<?php

namespace Tests\Feature;

use App\Exceptions\InventarioException;
use App\Models\CategoriaInsumo;
use App\Models\CicloProductivo;
use App\Models\Cultivo;
use App\Models\EventoCampo;
use App\Models\Finca;
use App\Models\Gasto;
use App\Models\Insumo;
use App\Models\Lote;
use App\Models\LoteInsumo;
use App\Models\MovimientoStock;
use App\Models\UnidadMedida;
use App\Models\User;
use App\Services\AplicacionInsumoService;
use App\Services\InventarioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use LogicException;
use MatanYadaev\EloquentSpatial\Objects\MultiPolygon;
use Tests\TestCase;

/**
 * Casos de bodega y campo reales contra InventarioService y AplicacionInsumoService.
 */
class InventarioServiceTest extends TestCase
{
    use RefreshDatabase;

    private InventarioService $inventario;
    private CategoriaInsumo $fungicidas;
    private CategoriaInsumo $herramientas;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
        $this->inventario = app(InventarioService::class);
        $this->fungicidas = CategoriaInsumo::create(['nombre' => 'Fungicidas', 'maneja_vencimiento' => true, 'maneja_toxicidad' => true]);
        $this->herramientas = CategoriaInsumo::create(['nombre' => 'Herramientas', 'maneja_vencimiento' => false, 'maneja_toxicidad' => false]);
    }

    public function test_una_entrada_suma_una_sola_vez_y_queda_valorizada(): void
    {
        $lote = $this->ingresar($this->fungicida(), 20, 48500, '2027-06-30');

        $this->assertSame('20.0000', $lote->cantidad_actual);
        $this->assertSame(1, $lote->movimientos()->count());

        $mov = $lote->movimientos()->first();
        $this->assertSame('entrada_saldo_inicial', $mov->tipo_movimiento);
        $this->assertSame('20.0000', $mov->stock_resultante);
        $this->assertSame('970000.00', $mov->valor_total);
        $this->assertSame('970000.00', $mov->saldo_valor);
    }

    public function test_aplicacion_toma_por_fefo_entre_lotes_y_causa_el_costo(): void
    {
        $insumo = $this->fungicida();
        $viejo = $this->ingresar($insumo, 10, 50000, '2026-12-31', 'LOT-VIEJO');
        $nuevo = $this->ingresar($insumo, 10, 55000, '2027-06-30', 'LOT-NUEVO');

        $aplicacion = app(AplicacionInsumoService::class)->registrar($this->evento(), [
            'insumo_id' => $insumo->id, 'cantidad' => 15, 'unidad_id' => $this->unidad('L'),
            'metodo_aplicacion' => 'foliar', 'area_aplicada' => 5,
        ]);

        // Primero se acaba el que vence antes
        $this->assertSame('0.0000', $viejo->refresh()->cantidad_actual);
        $this->assertSame('agotado', $viejo->estado);
        $this->assertSame('5.0000', $nuevo->refresh()->cantidad_actual);

        // 10 × 50.000 + 5 × 55.000, cada litro a su propio costo
        $this->assertSame('775000.00', $aplicacion->costo_total);
        $this->assertSame(2, $aplicacion->lotes()->count());
        $this->assertSame(2, MovimientoStock::where('tipo_movimiento', 'salida_aplicacion')->count());

        $gasto = Gasto::where('gastable_id', $aplicacion->id)->firstOrFail();
        $this->assertSame('aplicacion_insumo', $gasto->origen);
        $this->assertSame('775000.00', $gasto->base_gravable);
        $this->assertFalse($gasto->es_cuenta_por_pagar);
    }

    public function test_mililitros_se_descuentan_convertidos_a_litros(): void
    {
        $insumo = $this->fungicida();
        $lote = $this->ingresar($insumo, 1, 120000, '2027-06-30');

        $aplicacion = app(AplicacionInsumoService::class)->registrar($this->evento(), [
            'insumo_id' => $insumo->id, 'cantidad' => 500, 'unidad_id' => $this->unidad('mL'),
            'metodo_aplicacion' => 'drench',
        ]);

        $this->assertSame('0.5000', $lote->refresh()->cantidad_actual);
        $this->assertSame('0.5000', $aplicacion->cantidad_base);
        $this->assertSame('60000.00', $aplicacion->costo_total);
    }

    public function test_no_convierte_litros_a_kilos(): void
    {
        $this->expectException(InventarioException::class);
        $this->inventario->convertir(5, UnidadMedida::where('abreviatura', 'L')->first(), UnidadMedida::where('abreviatura', 'kg')->first());
    }

    public function test_el_saldo_nunca_queda_negativo_y_no_deja_rastro(): void
    {
        $lote = $this->ingresar($this->fungicida(), 5, 48500, '2027-06-30');

        $this->assertLanza(fn () => $this->inventario->mover($lote, 'ajuste_salida_merma', 6, $lote), 'Stock insuficiente');

        $this->assertSame('5.0000', $lote->refresh()->cantidad_actual);
        $this->assertSame(1, $lote->movimientos()->count());
    }

    public function test_falta_de_stock_total_cancela_toda_la_aplicacion(): void
    {
        $insumo = $this->fungicida();
        $lote = $this->ingresar($insumo, 4, 48500, '2027-06-30');

        $this->assertLanza(fn () => app(AplicacionInsumoService::class)->registrar($this->evento(), [
            'insumo_id' => $insumo->id, 'cantidad' => 10, 'unidad_id' => $this->unidad('L'), 'metodo_aplicacion' => 'foliar',
        ]), 'No hay suficiente');

        $this->assertSame('4.0000', $lote->refresh()->cantidad_actual);
        $this->assertSame(0, DB::table('evento_insumos')->count());
        $this->assertSame(0, Gasto::count());
    }

    public function test_lo_vencido_o_en_cuarentena_no_sale_al_campo_pero_si_se_puede_descartar(): void
    {
        $insumo = $this->fungicida();
        $vencido = $this->ingresar($insumo, 3, 48500, '2026-09-30', 'LOT-VENCIDO');
        $sospechoso = $this->ingresar($insumo, 3, 48500, '2027-06-30', 'LOT-SELLO-ROTO');
        $this->inventario->cambiarEstado($sospechoso, 'cuarentena');

        $this->assertLanza(fn () => $this->inventario->mover($vencido, 'salida_aplicacion', 1, $vencido, null, '2026-10-10'), 'vencido');
        $this->assertLanza(fn () => $this->inventario->mover($sospechoso, 'salida_aplicacion', 1, $sospechoso, null, '2026-10-10'), 'cuarentena');

        // FEFO tampoco los ofrece
        $this->assertSame(0.0, $this->inventario->disponibleParaCampo($insumo->id, '2026-10-10'));

        // Pero sí se pueden sacar de bodega como descarte o devolución
        $this->inventario->mover($vencido, 'ajuste_salida_vencido', 3, $vencido, 'Entregado a Campo Limpio', '2026-10-10');
        $this->inventario->mover($sospechoso, 'salida_devolucion_prov', 3, $sospechoso, 'Posible producto adulterado', '2026-10-10');
        $this->assertSame('0.0000', $vencido->refresh()->cantidad_actual);
        $this->assertSame('0.0000', $sospechoso->refresh()->cantidad_actual);
    }

    public function test_un_lote_vencido_no_vuelve_a_activo(): void
    {
        $lote = $this->ingresar($this->fungicida(), 3, 48500, '2026-09-30');

        $this->assertLanza(fn () => $this->inventario->cambiarEstado($lote, 'activo'), 'venció');
    }

    public function test_no_hay_salidas_con_fecha_anterior_al_ingreso(): void
    {
        $lote = $this->ingresar($this->fungicida(), 3, 48500, '2027-06-30');

        $this->assertLanza(fn () => $this->inventario->mover($lote, 'salida_aplicacion', 1, $lote, null, '2026-08-15'), 'entró a bodega');
    }

    public function test_herramienta_sin_vencimiento_y_fungicida_lo_exige(): void
    {
        $machete = Insumo::create([
            'categoria_id' => $this->herramientas->id, 'nombre' => 'Machete 22"',
            'unidad_base_id' => $this->unidad('u'), 'estado' => 'activo',
        ]);
        $lote = $this->ingresar($machete, 12, 28000, null);
        $this->assertNull($lote->fecha_vencimiento);
        $this->assertSame('12.0000', $lote->cantidad_actual);

        $this->assertLanza(fn () => $this->ingresar($this->fungicida(), 5, 48500, null), 'fecha de vencimiento');
    }

    public function test_la_devolucion_reactiva_un_lote_agotado(): void
    {
        $lote = $this->ingresar($this->fungicida(), 2, 48500, '2027-06-30');
        $this->inventario->mover($lote, 'salida_aplicacion', 2, $lote, null, '2026-10-05');
        $this->assertSame('agotado', $lote->refresh()->estado);

        // Sobró medio litro en la bomba y se devolvió a bodega
        $this->inventario->mover($lote, 'entrada_devolucion', 0.5, $lote, null, '2026-10-05');
        $this->assertSame('activo', $lote->refresh()->estado);
        $this->assertSame('0.5000', $lote->cantidad_actual);
    }

    public function test_el_kardex_no_se_edita_ni_se_borra(): void
    {
        $mov = $this->ingresar($this->fungicida(), 5, 48500, '2027-06-30')->movimientos()->first();

        $this->assertLanza(fn () => $mov->update(['cantidad' => 50]), 'no se modifica', LogicException::class);
        $this->assertLanza(fn () => $mov->delete(), 'no se elimina', LogicException::class);
    }

    public function test_costo_promedio_ponderado_del_saldo(): void
    {
        $insumo = $this->fungicida();
        $this->ingresar($insumo, 10, 50000, '2026-12-31');
        $this->ingresar($insumo, 30, 54000, '2027-06-30');

        // (10 × 50.000 + 30 × 54.000) / 40
        $this->assertSame(53000.0, $this->inventario->costoPromedio($insumo->id));
    }

    // ------------------------------------------------------------------

    private function fungicida(): Insumo
    {
        return Insumo::create([
            'categoria_id' => $this->fungicidas->id, 'nombre' => 'Fungicida ' . uniqid(),
            'unidad_base_id' => $this->unidad('L'), 'estado' => 'activo', 'phi_dias' => 14, 'rei_horas' => 24,
        ]);
    }

    private function ingresar(Insumo $insumo, float $cantidad, float $costo, ?string $vence, ?string $codigo = null): LoteInsumo
    {
        return $this->inventario->ingresarLote([
            'insumo_id' => $insumo->id, 'cantidad' => $cantidad, 'costo_unitario' => $costo,
            'fecha_ingreso' => '2026-09-01', 'fecha_vencimiento' => $vence, 'codigo_lote' => $codigo ?? 'LOT-' . uniqid(),
        ], 'entrada_saldo_inicial');
    }

    private function unidad(string $abreviatura): int
    {
        return UnidadMedida::where('abreviatura', $abreviatura)->value('id');
    }

    private function evento(): EventoCampo
    {
        $finca = Finca::create(['nombre' => 'La Esperanza', 'ubicacion' => 'Sabana de Torres, Santander']);
        $lote = Lote::create([
            'finca_id' => $finca->id, 'nombre_lote' => 'Lote 1', 'codigo_lote' => 'L-' . uniqid(),
            'area_hectareas_declaradas' => 10, 'altitud_mediana_msnm' => 120, 'pendiente_promedio_porcentaje' => 2.5,
            'tipo_suelo' => 'franco', 'ph_suelo' => 5.2,
            'geometria_gps' => MultiPolygon::fromWkt('MULTIPOLYGON(((-73.10 7.10, -73.09 7.10, -73.09 7.11, -73.10 7.11, -73.10 7.10)))', 4326),
        ]);
        $ciclo = CicloProductivo::create([
            'lote_id' => $lote->id, 'cultivo_id' => Cultivo::where('nombre_cultivo', 'Palma de Aceite')->value('id'),
            'tipo' => 'perenne', 'nombre_campana' => 'Campaña A', 'fecha_inicio' => '2024-01-15',
            'fecha_estimada_cosecha' => '2027-01-15', 'fecha_estimada_fin_cosecha' => '2049-01-15',
            'modalidad_siembra' => 'plantula_vivero',
        ]);

        return EventoCampo::create([
            'ciclo_productivo_id' => $ciclo->id, 'lote_id' => $lote->id, 'tipo_evento_id' => 1,
            'fecha_programada' => '2026-10-10 06:00:00', 'fecha_ejecucion' => '2026-10-10 07:00:00',
        ]);
    }

    /** Ejecuta en un savepoint para que el error de Postgres no aborte la transacción del test. */
    private function assertLanza(callable $accion, string $mensaje, string $clase = InventarioException::class): void
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
