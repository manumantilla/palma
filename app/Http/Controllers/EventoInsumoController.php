<?php

namespace App\Http\Controllers;

use App\Models\EventoCampo;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventoInsumoController extends Controller
{
    /**
     * Muestra la vista para registrar el consumo de insumos de un evento
     */
    public function create(EventoCampo $evento)
    {
        // Traemos los insumos que tienen stock disponible en el Kardex (lotes_insumos)
        // Agrupamos o listamos los lotes de insumos activos con su stock > 0
        $insumosConLotes = Insumo::whereHas('lotes', function($query) {
                $query->where('cantidad_actual', '>', 0);
            })
            ->with(['lotes' => function($query) {
                $query->where('cantidad_actual', '>', 0)->select('id', 'insumo_id', 'codigo_lote', 'cantidad_actual', 'costo_unitario');
            }])
            ->get();

        return view('eventos_insumos.create', compact('evento', 'insumosConLotes'));
    }

    /**
     * Almacena el consumo de insumos y descuenta del Kardex por lotes
     */
    public function store(Request $request, EventoCampo $evento)
    {
        // Validar la estructura del formulario (Array de insumos gastados)
        $request->validate([
            'insumos' => 'required|array|min:1',
            'insumos.*.insumo_id' => 'required|exists:insumos,id',
            'insumos.*.metodo_aplicacion' => 'required|in:terrestre,foliar,dron,fertirriego,drench',
            'insumos.*.unidad_medida' => 'required|in:kg,litros,unidades',
            'insumos.*.area_aplicada' => 'nullable|numeric|min:0',
            'insumos.*.observaciones' => 'nullable|string',
            
            // Lotes seleccionados para cada insumo (Kardex)
            'insumos.*.lotes' => 'required|array|min:1',
            'insumos.*.lotes.*.lote_insumo_id' => 'required|exists:lotes_insumos,id',
            'insumos.*.lotes.*.cantidad' => 'required|numeric|distinct|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->insumos as $insumoData) {
                
                // 1. Calcular el costo total sumando (cantidad * costo_unitario del lote)
                $costoTotalInsumo = 0;
                $cantidadTotalInsumo = 0;
                $costoTotalEvento = 0;

                // Verificación previa de stock disponible en los lotes solicitados
                foreach ($insumoData['lotes'] as $loteReq) {
                    $loteInventario = DB::table('lotes_insumos')
                        ->where('id', $loteReq['lote_insumo_id'])
                        ->lockForUpdate() // Bloqueo de fila en Postgres para evitar condiciones de carrera
                        ->first();

                    if (!$loteInventario || $loteInventario->stock < $loteReq['cantidad']) {
                        throw new \Exception("Stock insuficiente en el lote de insumo ID: {$loteReq['lote_insumo_id']}. Disponible: " . ($loteInventario->stock ?? 0));
                    }

                    $costoTotalInsumo += ($loteReq['cantidad'] * $loteInventario->costo_unitario);
                    $cantidadTotalInsumo += $loteReq['cantidad'];
                }

                // 2. Insertar en la tabla maestra: 'evento_insumos'
                // Determinamos si el evento es por árbol para forzar null en area_aplicada
                $esPorArbol = DB::table('evento_arbol')->where('evento_campo_id', $evento->id)->exists();

                $eventoInsumoId = DB::table('evento_insumos')->insertGetId([
                    'evento_campo_id'   => $evento->id,
                    'insumo_id'         => $insumoData['insumo_id'],
                    'cantidad'          => $cantidadTotalInsumo,
                    'area_aplicada'     => $esPorArbol ? null : ($insumoData['area_aplicada'] ?? null),
                    'metodo_aplicacion' => $insumoData['metodo_aplicacion'],
                    'unidad_medida'     => $insumoData['unidad_medida'],
                    'costo_total'       => $costoTotalInsumo,
                    'observaciones'     => $insumoData['observaciones'] ?? null,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                // 3. Insertar desglose por lotes y restar stock del inventario (Kardex)
                foreach ($insumoData['lotes'] as $loteReq) {
                    
                    // Guardar registro de la distribución del lote en el evento
                    DB::table('evento_insumo_lotes')->insert([
                        'evento_insumo_id' => $eventoInsumoId,
                        'lote_insumo_id'   => $loteReq['lote_insumo_id'],
                        'cantidad'         => $loteReq['cantidad'],
                        'area_aplicada'    => $esPorArbol ? null : ($insumoData['area_aplicada'] ?? null),
                    ]);

                    // DISMINUIR EL KARDEX FÍSICO
                    DB::table('lotes_insumos')
                        ->where('id', $loteReq['lote_insumo_id'])
                        ->decrement('stock', $loteReq['cantidad']);

                    //Crear el gastable
                    $gasto = $evento->gastos()->create([
                        'ciclo_productivo_id' => $evento->ciclo_productivo_id,
                        'categoria' => 'insumos',
                        'naturaleza' => 'costo_produccion',
                        'numero_soporte' => null,
                        'comprobante_archivo' => null,
                        'metodop_pago' => null,
                        'concepto' => 'Consumo de insumos en evento: ' . $evento,
                        'monto' => $loteReq['cantidad'] * $loteInventario->costo_unitario,
                        'fecha' => now(),
                        'descripcion' => 'Consumo de insumos en evento: ' . $evento . ' - Insumo ID: ' . $insumoData['insumo_id'] . ' - Lote ID: ' . $loteReq['lote_insumo_id'],
                    ]);

                    //Guardar el movimiento
                    MovimientoStock::create([
                        'lote_insumo_id' => $loteReq['lote_insumo_id'],
                        'tipo_movimiento' => 'salida_aplicacion',
                        'cantidad' => $loteReq['cantidad'],
                        'movimientoable_id' => $gasto->id,
                        'movimientoable_type' => get_class($gasto),
                        'stock_resultante' => $loteInventario->stock - $loteReq['cantidad'],
                        'observacion' => 'Salida de insumo por evento de campo: ' . $evento->tipoEvento->nombre,
                    ]);
                }
            }

            // Opcional: Cambiar el estado del evento si pasa de programado a ejecución
            DB::table('eventos_campo')
                ->where('id', $evento->id)
                ->update(['estado' => 'En Proceso', 'updated_at' => now()]);

            DB::commit();

            return redirect()->route('eventos_campo.show', $evento->id)
                ->with('success', 'Insumos del Kardex asignados y descontados correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Fallo en la asignación: ' . $e->getMessage());
        }
    }
}