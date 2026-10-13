<?php

namespace App\Services;

use App\Exceptions\InventarioException;
use App\Models\CategoriaGasto;
use App\Models\EventoCampo;
use App\Models\EventoInsumo;
use App\Models\EventoInsumoLote;
use App\Models\Gasto;
use App\Models\Insumo;
use App\Models\UnidadMedida;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Registra lo que se le aplicó a un lote o a unos árboles en una labor de campo.
 * En una sola transacción: la aplicación, el detalle por lote de bodega, la salida del kardex
 * y el costo de producción. Si algo falla, no queda nada a medias.
 *
 * Por qué el costo se causa aquí y no en la compra: comprar 20 bultos de urea no es costo del
 * ciclo, es inventario (un activo). El costo nace cuando la urea se aplica, y se carga al lote,
 * al ciclo y a la etapa fenológica donde se usó.
 */
class AplicacionInsumoService
{
    public function __construct(private InventarioService $inventario)
    {
    }

    /**
     * $datos: insumo_id, cantidad, unidad_id, metodo_aplicacion, y opcionales
     *         area_aplicada (ha), observaciones, lotes [lote_insumo_id => cantidad en unidad_id].
     */
    public function registrar(EventoCampo $evento, array $datos, ?int $userId = null): EventoInsumo
    {
        $userId ??= Auth::id();

        return DB::transaction(function () use ($evento, $datos, $userId) {
            $insumo = Insumo::with('unidadMedidaBase')->findOrFail($datos['insumo_id']);
            $unidad = UnidadMedida::findOrFail($datos['unidad_id']);
            $base   = $insumo->unidadMedidaBase;
            $fecha  = Carbon::parse($evento->fecha_ejecucion ?? now());

            $cantidadBase = $this->inventario->convertir($datos['cantidad'], $unidad, $base);

            // Si el usuario eligió lotes, sus cantidades vienen en la misma unidad que la aplicación
            $manual = [];
            foreach ($datos['lotes'] ?? [] as $loteId => $cantidadLote) {
                $manual[$loteId] = $this->inventario->convertir($cantidadLote, $unidad, $base);
            }
            if ($manual !== [] && abs(array_sum($manual) - $cantidadBase) > 0.0001) {
                throw new InventarioException('La suma de lo sacado de cada lote no coincide con la cantidad aplicada.');
            }

            $reparto = $this->inventario->repartirSalida($insumo, $cantidadBase, $fecha, $manual);

            // Por árbol el área no aplica (la dosis es por planta); por lote/zona va en hectáreas
            $esPorArbol = $evento->arboles()->exists();
            $area = $esPorArbol ? null : ($datos['area_aplicada'] ?? null);

            $aplicacion = EventoInsumo::create([
                'evento_campo_id'   => $evento->id,
                'insumo_id'         => $insumo->id,
                'cantidad'          => $datos['cantidad'],
                'unidad_id'         => $unidad->id,
                'cantidad_base'     => $cantidadBase,
                'area_aplicada'     => $area,
                'metodo_aplicacion' => $datos['metodo_aplicacion'],
                'costo_total'       => 0,
                'observaciones'     => $datos['observaciones'] ?? null,
            ]);

            $costoTotal = 0.0;
            foreach ($reparto as ['lote' => $lote, 'cantidad' => $cantidad]) {
                $linea = EventoInsumoLote::create([
                    'evento_insumo_id' => $aplicacion->id,
                    'lote_insumo_id'   => $lote->id,
                    'cantidad'         => $cantidad,
                    'costo_unitario'   => $lote->costo_unitario,
                    'costo_total'      => round($cantidad * (float) $lote->costo_unitario, 2),
                    'area_aplicada'    => $area,
                ]);

                $this->inventario->mover(
                    $lote, 'salida_aplicacion', $cantidad, $linea,
                    "Aplicación en evento #{$evento->id}", $fecha, $userId
                );

                $costoTotal += (float) $linea->costo_total;
            }

            $aplicacion->update(['costo_total' => round($costoTotal, 2)]);

            if ($costoTotal > 0) {
                $this->causarCosto($evento, $aplicacion, $insumo, $unidad, $fecha, $userId);
            }

            return $aplicacion->load('lotes');
        });
    }

    private function causarCosto(EventoCampo $evento, EventoInsumo $aplicacion, Insumo $insumo, UnidadMedida $unidad, Carbon $fecha, ?int $userId): Gasto
    {
        $categoria = CategoriaGasto::where('origen_automatico', 'aplicacion_insumo')->firstOrFail();

        return Gasto::create([
            'origen'              => 'aplicacion_insumo',
            'gastable_type'       => $aplicacion->getMorphClass(),
            'gastable_id'         => $aplicacion->id,
            'categoria_gasto_id'  => $categoria->id,
            'clase_costo'         => $categoria->clase_costo_default,
            'imputacion'          => $categoria->imputacion_default,
            'cuenta_puc'          => $categoria->cuenta_puc,
            'lote_id'             => $evento->lote_id,
            'ciclo_productivo_id' => $evento->ciclo_productivo_id,
            'ciclo_etapa_id'      => $evento->ciclo_productivo_id ? $evento->ciclo_etapa_id : null,
            'tipo_soporte'        => 'comprobante_interno',
            'concepto'            => sprintf('Aplicación de %s (%s %s)', $insumo->nombre, rtrim(rtrim(number_format((float) $aplicacion->cantidad, 4, '.', ''), '0'), '.'), $unidad->abreviatura),
            'fecha'               => $fecha->toDateString(),
            'base_gravable'       => $aplicacion->costo_total,
            // Sale de bodega: la deuda con el proveedor ya está en la compra, esto no se vuelve a pagar
            'es_cuenta_por_pagar' => false,
            'estado'              => 'devengado',
            'user_id'             => $userId,
        ]);
    }
}
