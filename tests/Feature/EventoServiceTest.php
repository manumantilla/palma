<?php

namespace Tests\Feature;

use App\Exceptions\EventoException;
use App\Exceptions\InventarioException;
use App\Models\Arbol;
use App\Models\CicloEtapaHistorial;
use App\Models\CicloProductivo;
use App\Models\Cliente;
use App\Models\Cultivo;
use App\Models\EventoCampo;
use App\Models\EventoManoObra;
use App\Models\FenologiaEtapa;
use App\Models\Finca;
use App\Models\Gasto;
use App\Models\GastoPago;
use App\Models\Lote;
use App\Models\LoteZonaManejo;
use App\Models\OrdenCosecha;
use App\Models\RecepcionCampo;
use App\Models\SesionCosecha;
use App\Models\TipoEvento;
use App\Models\Trabajador;
use App\Models\User;
use App\Services\AplicacionInsumoService;
use App\Services\EventoService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use MatanYadaev\EloquentSpatial\Objects\MultiPolygon;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use Tests\TestCase;

/**
 * Labores de campo y mano de obra con casos reales de finca.
 */
class EventoServiceTest extends TestCase
{
    use RefreshDatabase;

    private EventoService $servicio;
    private User $usuario;
    private Lote $lote;
    private CicloProductivo $ciclo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->usuario = User::factory()->withPersonalTeam()->create();
        $this->actingAs($this->usuario);
        $this->servicio = app(EventoService::class);
        $this->lote = $this->nuevoLote();
        $this->ciclo = $this->nuevoCiclo($this->lote);
    }

    // ---------------- Congruencia del evento ----------------

    public function test_la_labor_hereda_el_lote_del_ciclo_y_guarda_el_gps(): void
    {
        $evento = $this->servicio->crear($this->datosLabor(['latitud' => 7.105, 'longitud' => -73.095]));

        $this->assertSame($this->lote->id, $evento->lote_id);
        $gps = $evento->refresh()->coordenada_gps;
        $this->assertInstanceOf(Point::class, $gps);
        $this->assertEqualsWithDelta(7.105, $gps->latitude, 0.00001);
    }

    public function test_rechaza_lote_zona_o_etapa_que_no_son_del_ciclo(): void
    {
        $otroLote = $this->nuevoLote();
        $zonaAjena = $this->zona($otroLote);
        $etapaAjena = $this->etapa($this->nuevoCiclo($otroLote));

        $this->assertLanza(fn () => $this->servicio->crear($this->datosLabor(['lote_id' => $otroLote->id])), 'lote no corresponde');
        $this->assertLanza(fn () => $this->servicio->crear($this->datosLabor(['zona_id' => $zonaAjena->id])), 'no pertenece al lote');
        $this->assertLanza(fn () => $this->servicio->crear($this->datosLabor(['ciclo_etapa_id' => $etapaAjena->id])), 'etapa fenológica');
        $this->assertSame(0, EventoCampo::count());
    }

    public function test_una_labor_nueva_no_arranca_completada(): void
    {
        $this->assertLanza(fn () => $this->servicio->crear($this->datosLabor(['estado' => 'Completado'])), 'arranca Pendiente');
    }

    public function test_por_arbol_asigna_solo_los_arboles_vivos_de_la_zona(): void
    {
        $norte = $this->zona($this->lote, 'Norte');
        $sur = $this->zona($this->lote, 'Sur');
        $vivo = $this->arbol(['lote_zona_manejo_id' => $norte->id, 'codigo_unico' => 'N-1']);
        $this->arbol(['lote_zona_manejo_id' => $norte->id, 'codigo_unico' => 'N-2', 'estado_vital' => 'muerto']);
        $this->arbol(['lote_zona_manejo_id' => $sur->id, 'codigo_unico' => 'S-1']);

        $evento = $this->servicio->crear($this->datosLabor(['zona_id' => $norte->id]), 'arbol');

        $this->assertSame([$vivo->id], $evento->arboles()->pluck('arboles.id')->all());
    }

    // ---------------- Mano de obra ----------------

    public function test_el_jornal_causa_su_costo_y_pone_la_labor_en_proceso(): void
    {
        $evento = $this->servicio->crear($this->datosLabor(['ciclo_etapa_id' => $this->etapa($this->ciclo)->id]));
        $pedro = $this->trabajador('Pedro', 'Ardila');

        $jornal = $this->servicio->registrarManoObra($evento, [
            'trabajador_id' => $pedro->id, 'tipo_labor' => 'jornal_dia_completo', 'fecha_labor' => '2026-10-10',
            'cantidad' => 2, 'valor_unitario' => 60000, 'area_ejecutada_ha' => 2.5,
        ]);

        $this->assertSame('120000.00', $jornal->costo_total);       // 2 × 60.000
        $this->assertSame('Pedro Ardila', $jornal->nombre_trabajador); // de la ficha del trabajador
        $this->assertSame('En Proceso', $evento->refresh()->estado);

        $gasto = $jornal->gasto;
        $this->assertSame('mano_obra', $gasto->origen);
        $this->assertSame('120000.00', $gasto->base_gravable);
        $this->assertTrue($gasto->es_cuenta_por_pagar);
        $this->assertSame($this->lote->id, $gasto->lote_id);
        $this->assertSame($evento->ciclo_etapa_id, $gasto->ciclo_etapa_id);
        $this->assertSame('directo', $gasto->imputacion);
    }

    public function test_el_valor_ajustado_a_mano_es_el_que_se_paga(): void
    {
        $jornal = $this->servicio->registrarManoObra($this->servicio->crear($this->datosLabor()), [
            'nombre_trabajador' => 'Don Chepe', 'tipo_labor' => 'destajo', 'unidad_destajo' => 'kg',
            'fecha_labor' => '2026-10-10', 'cantidad' => 1234.5, 'valor_unitario' => 85, 'costo_total' => 105000,
        ]);

        $this->assertSame(104932.5, $jornal->costo_calculado);
        $this->assertSame(67.5, $jornal->ajuste);
        $this->assertSame('105000.00', $jornal->gasto->base_gravable);
    }

    public function test_jornalero_de_paso_sin_documento(): void
    {
        $jornal = $this->servicio->registrarManoObra($this->servicio->crear($this->datosLabor()), [
            'nombre_trabajador' => 'El Mono', 'tipo_labor' => 'jornal_medio_dia',
            'fecha_labor' => '2026-10-10', 'cantidad' => 1, 'valor_unitario' => 30000,
        ]);

        $this->assertNull($jornal->cedula);
        $this->assertNull($jornal->trabajador_id);
    }

    public function test_no_acepta_jornales_futuros_ni_sin_nombre(): void
    {
        $evento = $this->servicio->crear($this->datosLabor());

        $this->assertLanza(fn () => $this->servicio->registrarManoObra($evento, $this->jornal(['fecha_labor' => now()->addDay()->toDateString()])), 'no han pasado');
        $this->assertLanza(fn () => $this->servicio->registrarManoObra($evento, $this->jornal(['nombre_trabajador' => ''])), 'nombre del trabajador');
    }

    public function test_labor_que_no_lleva_mano_de_obra(): void
    {
        $dron = TipoEvento::create(['nombre' => 'Vuelo de dron', 'categoria' => 'Mantenimiento', 'consume_mano_obra' => false]);
        $evento = $this->servicio->crear($this->datosLabor(['tipo_evento_id' => $dron->id]));

        $this->assertLanza(fn () => $this->servicio->registrarManoObra($evento, $this->jornal()), 'no lleva mano de obra');
    }

    public function test_labor_general_sin_cultivo_es_costo_indirecto(): void
    {
        $cerca = $this->servicio->crear(['tipo_evento_id' => $this->tipo('Deshierbe'), 'fecha_programada' => '2026-10-09']);
        $jornal = $this->servicio->registrarManoObra($cerca, $this->jornal());

        $this->assertNull($jornal->gasto->ciclo_productivo_id);
        $this->assertSame('indirecto', $jornal->gasto->imputacion);
    }

    // ---------------- Estados ----------------

    public function test_completar_y_cancelar(): void
    {
        $evento = $this->servicio->crear($this->datosLabor());
        $this->assertLanza(fn () => $this->servicio->completar($evento), 'al menos un jornal');

        $this->servicio->registrarManoObra($evento, $this->jornal(['fecha_labor' => '2026-10-08']));
        $this->servicio->registrarManoObra($evento, $this->jornal(['fecha_labor' => '2026-10-10']));

        // Con jornales ya no se cancela; se completa con el último día trabajado
        $this->assertLanza(fn () => $this->servicio->cancelar($evento, 'llovió'), 'complétela');
        $this->servicio->completar($evento);
        $this->assertSame('Completado', $evento->refresh()->estado);
        $this->assertSame('2026-10-10', $evento->fecha_ejecucion->toDateString());

        // Una labor sin consumos sí se cancela, y después no recibe jornales
        $otra = $this->servicio->crear($this->datosLabor());
        $this->servicio->cancelar($otra, 'Se adelantó la cosecha');
        $this->assertSame('Cancelado', $otra->refresh()->estado);
        $this->assertLanza(fn () => $this->servicio->registrarManoObra($otra, $this->jornal()), 'cancelada');
    }

    // ---------------- Destajo de cosecha ----------------

    public function test_destajo_toma_los_kilos_de_la_bascula_y_no_se_paga_dos_veces(): void
    {
        $luis = $this->trabajador('Luis', 'Pérez');
        $ana = $this->trabajador('Ana', 'Rueda');
        $sesion = $this->sesionCosecha();
        $this->pesaje($sesion, $luis, 600.5);
        $this->pesaje($sesion, $luis, 634);
        $this->pesaje($sesion, $ana, 410);                       // otro recolector: no se mezcla
        $this->pesaje($this->sesionCosecha(), $luis, 999);       // otra sesión: no se mezcla

        $this->assertSame(['kilos' => 1234.5, 'pesajes' => 2], $this->servicio->kilosPesados($sesion, $luis->id));

        $destajo = $this->servicio->registrarDestajoCosecha($sesion, ['trabajador_id' => $luis->id, 'valor_unitario' => 85]);

        $this->assertSame('1234.50', $destajo->cantidad);
        $this->assertSame('1234.50', $destajo->kilos_pesados);
        $this->assertSame('kg', $destajo->unidad_destajo);
        $this->assertSame($sesion->evento_campo_id, $destajo->evento_campo_id);
        $this->assertSame('2026-10-10', $destajo->fecha_labor->toDateString());

        // Segundo destajo del mismo recolector en la misma sesión: lo frena el servicio...
        $this->assertLanza(fn () => $this->servicio->registrarDestajoCosecha($sesion, ['trabajador_id' => $luis->id, 'valor_unitario' => 85]), 'ya tiene su destajo');

        // ...y si alguien se lo salta, la base de datos
        $this->assertLanza(fn () => EventoManoObra::create($destajo->only([
            'evento_campo_id', 'sesion_id', 'trabajador_id', 'nombre_trabajador', 'tipo_labor',
            'fecha_labor', 'cantidad', 'valor_unitario', 'costo_total',
        ])), 'evento_mano_obra_destajo_sesion_unico', UniqueConstraintViolationException::class);
    }

    public function test_un_jornal_no_puede_apuntar_a_la_sesion_de_otra_labor(): void
    {
        $sesion = $this->sesionCosecha();
        $otraLabor = $this->servicio->crear($this->datosLabor());

        $this->assertLanza(fn () => $this->servicio->registrarManoObra($otraLabor, $this->jornal(['sesion_id' => $sesion->id])), 'otra labor');
    }

    // ---------------- Edición, eliminación y pago ----------------

    public function test_editar_actualiza_el_gasto_y_eliminar_lo_anula(): void
    {
        $jornal = $this->servicio->registrarManoObra($this->servicio->crear($this->datosLabor()), $this->jornal());

        $this->servicio->actualizarManoObra($jornal, $this->jornal(['cantidad' => 3]));
        $this->assertSame('180000.00', $jornal->refresh()->costo_total);
        $this->assertSame('180000.00', $jornal->gasto->base_gravable);

        $gastoId = $jornal->gasto->id;
        $this->servicio->eliminarManoObra($jornal);
        $this->assertNull(EventoManoObra::find($jornal->id));
        $this->assertSame('anulado', Gasto::find($gastoId)->estado);
    }

    public function test_liquidar_paga_solo_la_semana_y_salda_la_cuenta(): void
    {
        $pedro = $this->trabajador('Pedro', 'Ardila');
        $evento = $this->servicio->crear($this->datosLabor());
        $lunes = $this->servicio->registrarManoObra($evento, $this->jornal(['trabajador_id' => $pedro->id, 'fecha_labor' => '2026-10-05']));
        $sabado = $this->servicio->registrarManoObra($evento, $this->jornal(['trabajador_id' => $pedro->id, 'fecha_labor' => '2026-10-10']));
        $anterior = $this->servicio->registrarManoObra($evento, $this->jornal(['trabajador_id' => $pedro->id, 'fecha_labor' => '2026-09-28']));

        $resultado = $this->servicio->liquidar($pedro->id, null, '2026-10-05', '2026-10-11', '2026-10-11');

        $this->assertSame(['registros' => 2, 'total' => 240000.0], $resultado);
        $this->assertSame('pagado', $lunes->refresh()->estado_pago);
        $this->assertSame('pagado', $sabado->refresh()->estado_pago);
        $this->assertSame('pendiente', $anterior->refresh()->estado_pago);
        $this->assertSame('pagado', $lunes->gasto->estado);
        $this->assertSame(2, GastoPago::count());

        // Lo pagado ya no se edita
        $this->assertLanza(fn () => $this->servicio->actualizarManoObra($lunes, $this->jornal()), 'ya fue pagado');
    }

    // ---------------- Insumos respetan el tipo de labor ----------------

    public function test_no_se_cargan_insumos_a_una_labor_que_no_los_usa(): void
    {
        $riego = $this->servicio->crear($this->datosLabor(['tipo_evento_id' => $this->tipo('Riego')]));

        $this->assertLanza(fn () => app(AplicacionInsumoService::class)->registrar($riego, [
            'insumo_id' => 1, 'cantidad' => 1, 'unidad_id' => 1, 'metodo_aplicacion' => 'foliar',
        ]), 'no lleva insumos', InventarioException::class);
    }

    // ---------------- Pantallas ----------------

    public function test_las_pantallas_de_mano_de_obra_cargan(): void
    {
        $evento = $this->servicio->crear($this->datosLabor());
        $jornal = $this->servicio->registrarManoObra($evento, $this->jornal());

        $this->get(route('mano-obra.index'))->assertOk()->assertSee('Juan Gómez');
        $this->get(route('mano-obra.create', ['evento' => $evento->id]))->assertOk();
        $this->get(route('mano-obra.edit', $jornal))->assertOk();
        $this->get(route('mano-obra.show', $jornal))->assertOk();
        $this->get(route('eventos_campo.show', $evento->id))->assertOk()->assertSee('Mano de Obra');

        $this->post(route('mano-obra.store'), [
            'evento_campo_id' => $evento->id, 'nombre_trabajador' => 'María Toloza', 'tipo_labor' => 'jornal_dia_completo',
            'fecha_labor' => '2026-10-10', 'cantidad' => 1, 'valor_unitario' => 60000, 'costo_total' => 65000,
        ])->assertRedirect(route('eventos_campo.show', $evento->id));
        $this->assertSame('65000.00', EventoManoObra::where('nombre_trabajador', 'María Toloza')->value('costo_total'));
    }

    // ------------------------------------------------------------------

    private function datosLabor(array $extra = []): array
    {
        return $extra + [
            'ciclo_productivo_id' => $this->ciclo->id,
            'tipo_evento_id'      => $this->tipo('Fertilización química'),
            'fecha_programada'    => '2026-10-08 06:00:00',
        ];
    }

    private function jornal(array $extra = []): array
    {
        return $extra + [
            'nombre_trabajador' => 'Juan Gómez', 'tipo_labor' => 'jornal_dia_completo',
            'fecha_labor' => '2026-10-10', 'cantidad' => 2, 'valor_unitario' => 60000,
        ];
    }

    private function tipo(string $nombre): int
    {
        return TipoEvento::where('nombre', $nombre)->value('id');
    }

    private function nuevoLote(): Lote
    {
        $finca = Finca::create(['nombre' => 'La Esperanza', 'ubicacion' => 'Sabana de Torres, Santander']);

        return Lote::create([
            'finca_id' => $finca->id, 'nombre_lote' => 'Lote ' . uniqid(), 'codigo_lote' => 'L-' . uniqid(),
            'area_hectareas_declaradas' => 10, 'altitud_mediana_msnm' => 120, 'pendiente_promedio_porcentaje' => 2.5,
            'tipo_suelo' => 'franco', 'ph_suelo' => 5.2,
            'geometria_gps' => MultiPolygon::fromWkt('MULTIPOLYGON(((-73.10 7.10, -73.09 7.10, -73.09 7.11, -73.10 7.11, -73.10 7.10)))', 4326),
        ]);
    }

    private function nuevoCiclo(Lote $lote): CicloProductivo
    {
        return CicloProductivo::create([
            'lote_id' => $lote->id, 'cultivo_id' => Cultivo::where('nombre_cultivo', 'Palma de Aceite')->value('id'),
            'tipo' => 'perenne', 'nombre_campana' => 'Campaña ' . uniqid(), 'fecha_inicio' => '2024-01-15',
            'fecha_estimada_cosecha' => '2027-01-15', 'fecha_estimada_fin_cosecha' => '2049-01-15',
            'modalidad_siembra' => 'plantula_vivero',
        ]);
    }

    private function zona(Lote $lote, string $nombre = 'Zona'): LoteZonaManejo
    {
        return LoteZonaManejo::create([
            'lote_id' => $lote->id, 'nombre_zona' => $nombre, 'codigo_zona' => 'Z-' . uniqid(), 'area_hectareas' => 2,
            'geometria_zona' => Polygon::fromWkt('POLYGON((-73.10 7.10, -73.095 7.10, -73.095 7.105, -73.10 7.10))', 4326),
        ]);
    }

    private function etapa(CicloProductivo $ciclo): CicloEtapaHistorial
    {
        $etapa = FenologiaEtapa::create([
            'cultivo_id' => $ciclo->cultivo_id, 'tipo_fase' => 'vida', 'nombre' => 'Establecimiento ' . uniqid(),
            'orden' => 3, 'duracion_dias_estimada' => 900,
        ]);

        return CicloEtapaHistorial::create([
            'ciclo_productivo_id' => $ciclo->id, 'fenologia_etapa_id' => $etapa->id,
            'fecha_inicio_estimada' => '2024-01-15', 'fecha_fin_estimada' => '2026-07-03',
        ]);
    }

    private function arbol(array $extra): Arbol
    {
        static $posicion = 0;
        $posicion++;

        return Arbol::create($extra + [
            'ciclo_productivo_id' => $this->ciclo->id, 'lote_id' => $this->lote->id,
            'fila_indice' => 1, 'posicion_indice' => $posicion,
        ]);
    }

    private function trabajador(string $nombres, string $apellidos): Trabajador
    {
        return Trabajador::create([
            'tipo_documento' => 'CC', 'numero_documento' => (string) random_int(10000000, 99999999),
            'nombres' => $nombres, 'apellidos' => $apellidos, 'cargo' => 'Cosechero',
            'fecha_ingreso' => '2025-01-10', 'tipo_contrato' => 'por_labores',
        ]);
    }

    private function sesionCosecha(): SesionCosecha
    {
        $cliente = Cliente::firstOrCreate(['nit' => '900123456'], [
            'razon_social' => 'Extractora Demo S.A.S.', 'direccion_fiscal' => 'Km 5 vía Puerto Wilches', 'municipio' => 'Puerto Wilches',
        ]);
        $orden = OrdenCosecha::create([
            'cliente_id' => $cliente->id, 'ciclo_productivo_id' => $this->ciclo->id, 'lote_cultivo_id' => $this->lote->id,
            'fecha_programada' => '2026-10-10', 'fecha_entrega' => '2026-10-11',
        ]);
        $evento = $this->servicio->crear($this->datosLabor(['tipo_evento_id' => $this->tipo('Cosecha')]));

        return SesionCosecha::create([
            'orden_cosecha_id' => $orden->id, 'evento_campo_id' => $evento->id,
            'responsable_id' => $this->usuario->id, 'fecha' => '2026-10-10',
        ]);
    }

    private function pesaje(SesionCosecha $sesion, Trabajador $trabajador, float $neto): void
    {
        RecepcionCampo::create([
            'sesion_cosecha_id' => $sesion->id, 'trabajador_id' => $trabajador->id,
            'peso_bruto' => $neto + 0.5, 'tara_costal' => 0.5, 'peso_neto' => $neto,
        ]);
    }

    /** Ejecuta en un savepoint para que un error de Postgres no aborte la transacción del test. */
    private function assertLanza(callable $accion, string $mensaje, string $clase = EventoException::class): void
    {
        try {
            DB::transaction($accion);
            $this->fail("Se esperaba {$clase}: {$mensaje}");
        } catch (\Throwable $e) {
            $this->assertInstanceOf($clase, $e, $e->getMessage());
            $this->assertStringContainsString($mensaje, $e->getMessage());
        }
    }
}
