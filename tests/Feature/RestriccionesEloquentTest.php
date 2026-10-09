<?php

namespace Tests\Feature;

use App\Models\Arbol;
use App\Models\CategoriaGasto;
use App\Models\CicloEtapaHistorial;
use App\Models\CicloProductivo;
use App\Models\Cliente;
use App\Models\Cultivo;
use App\Models\EventoCampo;
use App\Models\FenologiaEtapa;
use App\Models\Finca;
use App\Models\Gasto;
use App\Models\Insumo;
use App\Models\Lote;
use App\Models\LoteZonaManejo;
use App\Models\Merma;
use App\Models\OrdenCosecha;
use App\Models\RecepcionCampo;
use App\Models\SesionCosecha;
use App\Models\Trabajador;
use App\Models\UnidadMedida;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use MatanYadaev\EloquentSpatial\Objects\Geometry;
use MatanYadaev\EloquentSpatial\Objects\MultiPolygon;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use Tests\TestCase;

/**
 * Guarda registros reales con Eloquent para comprobar que los CHECK, UNIQUE, FK compuestas,
 * columnas generadas y casts espaciales se comportan como se espera.
 */
class RestriccionesEloquentTest extends TestCase
{
    use RefreshDatabase;

    private User $usuario;

    protected function setUp(): void
    {
        parent::setUp();
        $this->usuario = User::factory()->create();
    }

    public function test_geometrias_se_guardan_y_leen_con_sus_casts(): void
    {
        $lote = $this->lote();
        $this->assertInstanceOf(MultiPolygon::class, $lote->refresh()->geometria_gps);
        $this->assertSame(4326, $lote->geometria_gps->srid);

        $zona = LoteZonaManejo::create([
            'lote_id' => $lote->id,
            'nombre_zona' => 'Zona norte',
            'codigo_zona' => 'Z-01',
            'area_hectareas' => 1.25,
            'geometria_zona' => Polygon::fromWkt('POLYGON((-73.10 7.10, -73.095 7.10, -73.095 7.105, -73.10 7.10))', 4326),
        ]);
        $this->assertInstanceOf(Polygon::class, $zona->refresh()->geometria_zona);

        // geography(point) en trabajadores
        $trabajador = $this->trabajador(['ubicacion_actual' => new Point(7.119, -73.122, 4326)]);
        $punto = $trabajador->refresh()->ubicacion_actual;
        $this->assertInstanceOf(Point::class, $punto);
        $this->assertEqualsWithDelta(7.119, $punto->latitude, 0.00001);
        $this->assertEqualsWithDelta(-73.122, $punto->longitude, 0.00001);

        // Árbol: el mutator acepta WKT y el cast devuelve la geometría
        $arbol = Arbol::create([
            'ciclo_productivo_id' => $this->ciclo($lote)->id,
            'lote_id' => $lote->id,
            'codigo_unico' => 'A-01-01',
            'fila_indice' => 1,
            'posicion_indice' => 1,
            'coordenada_precision' => 'POINT(-73.1 7.1)',
        ]);
        $this->assertInstanceOf(Geometry::class, $arbol->refresh()->coordenada_precision);
    }

    public function test_densidad_en_tresbolillo(): void
    {
        $ciclo = $this->ciclo($this->lote(), ['arreglo_espacial' => 'tresbolillo', 'distancia_entre_plantas_metros' => 9]);

        // Palma a 9 m en triángulo: 10000 / (81 · 0.866) ≈ 142.6 palmas/ha
        $this->assertEqualsWithDelta(142.56, (float) $ciclo->plantas_por_hectarea_real, 0.05);
    }

    public function test_gasto_calcula_columnas_generadas(): void
    {
        $gasto = Gasto::create($this->datosGasto([
            'base_gravable' => 1000000,
            'porcentaje_iva' => 19,
            'valor_iva' => 190000,
            'retefuente' => 25000,
            'reteica' => 9660,
        ]))->refresh();

        $this->assertSame('1190000.00', $gasto->total);
        $this->assertSame('1155340.00', $gasto->neto_a_pagar);
        // IVA no descontable => hace parte del costo
        $this->assertSame('1190000.00', $gasto->costo_cop);
    }

    public function test_gasto_rechaza_etapa_de_otro_ciclo_o_sin_ciclo(): void
    {
        $lote = $this->lote();
        $cicloA = $this->ciclo($lote);
        $cicloB = $this->ciclo($lote, ['nombre_campana' => 'Campaña B']);
        $etapaDeB = $this->etapaHistorial($cicloB);

        $this->assertRechazado(QueryException::class, fn () => Gasto::create($this->datosGasto([
            'ciclo_productivo_id' => $cicloA->id,
            'ciclo_etapa_id' => $etapaDeB->id,
        ])));

        // Sin ciclo: la FK compuesta (MATCH SIMPLE) no lo detectaría; lo detiene el CHECK
        $this->assertRechazado(QueryException::class, fn () => Gasto::create($this->datosGasto([
            'ciclo_productivo_id' => null,
            'ciclo_etapa_id' => $etapaDeB->id,
        ])), 'gastos_etapa_requiere_ciclo_chk');

        $ok = Gasto::create($this->datosGasto([
            'ciclo_productivo_id' => $cicloB->id,
            'ciclo_etapa_id' => $etapaDeB->id,
        ]));
        $this->assertTrue($ok->exists);
    }

    public function test_historial_no_duplica_etapa_aunque_zona_sea_null(): void
    {
        $ciclo = $this->ciclo($this->lote());
        $etapa = $this->etapaHistorial($ciclo);

        $this->assertRechazado(UniqueConstraintViolationException::class, fn () => CicloEtapaHistorial::create([
            'ciclo_productivo_id' => $ciclo->id,
            'fenologia_etapa_id' => $etapa->fenologia_etapa_id,
            'fecha_inicio_estimada' => '2026-01-01',
            'fecha_fin_estimada' => '2026-02-01',
        ]));

        // Otra cohorte (numero_repeticion 2) sí se permite
        $cohorte = CicloEtapaHistorial::create([
            'ciclo_productivo_id' => $ciclo->id,
            'fenologia_etapa_id' => $etapa->fenologia_etapa_id,
            'numero_repeticion' => 2,
            'fecha_inicio_estimada' => '2026-01-15',
            'fecha_fin_estimada' => '2026-02-15',
        ]);
        $this->assertTrue($cohorte->exists);
    }

    public function test_eventos_automaticos_no_se_duplican_pero_los_manuales_si(): void
    {
        $ciclo = $this->ciclo($this->lote());
        $etapa = $this->etapaHistorial($ciclo);
        $auto = [
            'ciclo_productivo_id' => $ciclo->id,
            'ciclo_etapa_id' => $etapa->id,
            'tipo_evento_id' => 1,
            'origen' => 'recomendacion',
            'origen_id' => 7,
            'fecha_programada' => '2026-03-01 07:00:00',
            'coordenada_gps' => new Point(7.1, -73.1, 4326),
        ];

        EventoCampo::create($auto);
        $this->assertRechazado(UniqueConstraintViolationException::class, fn () => EventoCampo::create($auto));

        $manual = ['origen' => 'manual', 'origen_id' => null] + $auto;
        EventoCampo::create($manual);
        EventoCampo::create($manual);
        $this->assertSame(3, EventoCampo::count());
    }

    public function test_checks_de_fenologia_y_labores(): void
    {
        $palma = Cultivo::where('nombre_cultivo', 'Palma de Aceite')->value('id');

        $this->assertRechazado(QueryException::class, fn () => FenologiaEtapa::create([
            'cultivo_id' => $palma, 'tipo_fase' => 'secuencial', 'nombre' => 'X', 'orden' => 1,
            'bbch_inicio' => 150, 'duracion_dias_estimada' => 5,
        ]), 'fenologia_bbch_rango_chk');

        $this->assertRechazado(QueryException::class, fn () => FenologiaEtapa::create([
            'cultivo_id' => $palma, 'tipo_fase' => 'repetitiva', 'nombre' => 'X', 'orden' => 1,
            'duracion_dias_estimada' => 5,
        ]), 'fenologia_repetitiva_chk');

        $this->assertRechazado(QueryException::class, fn () => \App\Models\LaborPlantilla::create([
            'cultivo_id' => $palma, 'nombre_labor' => 'Fertilización anual',
            'momento_tipo' => 'fecha_fija_anual', 'tipo_evento_id' => 1,
        ]), 'labores_momento_chk');
    }

    public function test_recepcion_con_pivote_uuid_y_merma_con_soft_delete(): void
    {
        $lote = $this->lote();
        $ciclo = $this->ciclo($lote);
        $trabajador = $this->trabajador();
        $cliente = Cliente::create([
            'razon_social' => 'Extractora Demo S.A.S.', 'nit' => '900123456', 'direccion_fiscal' => 'Km 5 vía Puerto Wilches',
            'municipio' => 'Puerto Wilches',
        ]);
        $orden = OrdenCosecha::create([
            'cliente_id' => $cliente->id, 'ciclo_productivo_id' => $ciclo->id, 'lote_cultivo_id' => $lote->id,
            'fecha_programada' => '2026-03-01', 'fecha_entrega' => '2026-03-02',
        ]);
        $this->assertSame($cliente->id, $orden->cliente->id);

        $evento = EventoCampo::create(['ciclo_productivo_id' => $ciclo->id, 'tipo_evento_id' => 1, 'fecha_programada' => '2026-03-01 06:00:00']);
        $sesion = SesionCosecha::create([
            'orden_cosecha_id' => $orden->id, 'evento_campo_id' => $evento->id,
            'responsable_id' => $this->usuario->id, 'fecha' => '2026-03-01',
        ]);

        $recepcion = RecepcionCampo::create([
            'sesion_cosecha_id' => $sesion->id, 'trabajador_id' => $trabajador->id,
            'peso_bruto' => 52.5, 'tara_costal' => 0.5, 'peso_neto' => 52,
            'ubicacion_gps' => new Point(7.1, -73.1, 4326),
        ]);
        $this->assertTrue($recepcion->sesionCosecha->is($sesion));

        $arbol = Arbol::create([
            'ciclo_productivo_id' => $ciclo->id, 'lote_id' => $lote->id, 'codigo_unico' => 'A-02-01',
            'fila_indice' => 2, 'posicion_indice' => 1,
        ]);
        $recepcion->arboles()->attach($arbol->id, ['peso_estimado_kg' => 26]);
        $pivote = $recepcion->arboles()->first()->pivot;
        $this->assertMatchesRegularExpression('/^[0-9a-f-]{36}$/', $pivote->id);

        $merma = Merma::create([
            'recepcion_campo_id' => $recepcion->id, 'operario_id' => $this->usuario->id,
            'fecha_registro' => '2026-03-01', 'kilos_merma' => 1.5, 'motivo' => 'daño_mecanico',
        ]);
        $merma->delete();
        $this->assertSoftDeleted($merma);
        $this->assertSame(0, $recepcion->mermas()->count());
    }

    public function test_insumo_expone_codigo_de_unidad_para_las_vistas(): void
    {
        $insumo = Insumo::create([
            'categoria_id' => 1,
            'nombre' => 'Cloruro de potasio',
            'unidad_base_id' => UnidadMedida::where('abreviatura', 'kg')->value('id'),
            'estado' => 'activo',
            'equipo_proteccion' => 'Guantes, mascarilla',
        ]);

        $this->assertSame('kg', $insumo->refresh()->unidad_base);
        $this->assertSame('1.0000', $insumo->factor_conversion);
        $this->assertSame('Guantes, mascarilla', $insumo->equipo_proteccion);
    }

    public function test_categorias_gasto_sembradas(): void
    {
        $this->assertSame('7205', CategoriaGasto::where('codigo', 'mano_obra_directa')->value('cuenta_puc'));
    }

    // ------------------------------------------------------------------ helpers

    /** Ejecuta en un savepoint y exige que falle con la excepción (y constraint) indicada. */
    private function assertRechazado(string $excepcion, callable $accion, ?string $constraint = null): void
    {
        try {
            DB::transaction($accion);
        } catch (\Throwable $e) {
            $this->assertInstanceOf($excepcion, $e, $e->getMessage());
            if ($constraint) {
                $this->assertStringContainsString($constraint, $e->getMessage());
            }

            return;
        }
        $this->fail("Se esperaba $excepcion" . ($constraint ? " ($constraint)" : '') . ' y la operación se guardó.');
    }

    private function lote(): Lote
    {
        $finca = Finca::create(['nombre' => 'La Esperanza', 'ubicacion' => 'Sabana de Torres, Santander']);

        return Lote::create([
            'finca_id' => $finca->id,
            'nombre_lote' => 'Lote 1',
            'codigo_lote' => 'L-' . uniqid(),
            'area_hectareas_declaradas' => 10,
            'altitud_mediana_msnm' => 120,
            'pendiente_promedio_porcentaje' => 2.5,
            'tipo_suelo' => 'franco',
            'ph_suelo' => 5.2,
            'geometria_gps' => MultiPolygon::fromWkt('MULTIPOLYGON(((-73.10 7.10, -73.09 7.10, -73.09 7.11, -73.10 7.11, -73.10 7.10)))', 4326),
        ]);
    }

    private function ciclo(Lote $lote, array $extra = []): CicloProductivo
    {
        return CicloProductivo::create($extra + [
            'lote_id' => $lote->id,
            'cultivo_id' => Cultivo::where('nombre_cultivo', 'Palma de Aceite')->value('id'),
            'tipo' => 'perenne',
            'nombre_campana' => 'Campaña A',
            'fecha_inicio' => '2024-01-15',
            'fecha_estimada_cosecha' => '2027-01-15',
            'fecha_estimada_fin_cosecha' => '2049-01-15',
            'modalidad_siembra' => 'plantula_vivero',
        ]);
    }

    private function etapaHistorial(CicloProductivo $ciclo): CicloEtapaHistorial
    {
        $etapa = FenologiaEtapa::create([
            'cultivo_id' => $ciclo->cultivo_id, 'tipo_fase' => 'vida', 'nombre' => 'Establecimiento ' . uniqid(),
            'orden' => 3, 'duracion_dias_estimada' => 900,
        ]);

        return CicloEtapaHistorial::create([
            'ciclo_productivo_id' => $ciclo->id,
            'fenologia_etapa_id' => $etapa->id,
            'fecha_inicio_estimada' => '2024-01-15',
            'fecha_fin_estimada' => '2026-07-03',
        ]);
    }

    private function trabajador(array $extra = []): Trabajador
    {
        return Trabajador::create($extra + [
            'tipo_documento' => 'CC',
            'numero_documento' => (string) random_int(10000000, 99999999),
            'nombres' => 'Luis',
            'apellidos' => 'Pérez',
            'cargo' => 'Cosechero',
            'fecha_ingreso' => '2025-01-10',
            'tipo_contrato' => 'por_labores',
        ]);
    }

    private function datosGasto(array $extra = []): array
    {
        return $extra + [
            'categoria_gasto_id' => CategoriaGasto::where('codigo', 'insumos_agricolas')->value('id'),
            'concepto' => 'Fertilizante',
            'fecha' => '2026-02-01',
            'base_gravable' => 100000,
            'user_id' => $this->usuario->id,
        ];
    }
}
