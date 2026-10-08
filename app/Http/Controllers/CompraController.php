<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\LoteInsumo;
use App\Models\MovimientoStock;
use App\Models\Proveedor;
use App\Models\CicloProductivo;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $compras = Compra::with(['proveedor', 'user'])
            ->withSum('pagos', 'monto')
            ->orderBy('fecha', 'desc')
            ->paginate(15);

        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        $ciclos = CicloProductivo::all();
        $insumos = Insumo::orderBy('nombre')->get();

        return view('compras.create', compact('proveedores', 'ciclos', 'insumos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proveedor_id'           => 'required|exists:proveedores,id',
            'fecha'                  => 'required|date',
            'tipo'                   => 'required|in:' . implode(',', Compra::TIPOS),
            'ciclo_productivo_id'    => 'nullable|exists:ciclos_productivos,id',
            'numero_factura'         => 'nullable|string|max:100',
            'plazo_pago_dias'        => 'nullable|integer|min:0',
            'descuento_pronto_pago'  => 'nullable|numeric|min:0',
            'porcentaje_iva_general' => 'nullable|numeric|min:0',
            'observaciones'          => 'nullable|string',
            'items'                  => 'required|array|min:1',
            'items.*.insumo_id'      => 'required_if:tipo,insumos|nullable|exists:insumos,id',
            'items.*.cantidad'       => 'required|numeric|min:0.01',
            'items.*.costo_unitario' => 'required|numeric|min:0',
            'items.*.codigo_lote'    => 'nullable|string|max:100',
            'items.*.fecha_vencimiento' => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // 1. Calcular subtotales e impuestos
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['cantidad'] * $item['costo_unitario'];
            }

            $porcentajeIva = $validated['porcentaje_iva_general'] ?? 0;
            $ivaTotal = $subtotal * ($porcentajeIva / 100);
            $total = $subtotal + $ivaTotal;

            // 2. Crear Registro de Compra
            $compra = Compra::create([
                'proveedor_id'           => $validated['proveedor_id'],
                'fecha'                  => $validated['fecha'],
                'tipo'                   => $validated['tipo'],
                'ciclo_productivo_id'    => $validated['ciclo_productivo_id'] ?? null,
                'estado'                 => 'recibida_total', // Directo a stock
                'numero_factura'         => $validated['numero_factura'] ?? null,
                'plazo_pago_dias'        => $validated['plazo_pago_dias'] ?? 0,
                'descuento_pronto_pago'  => $validated['descuento_pronto_pago'] ?? 0,
                'subtotal'               => $subtotal,
                'descuento_total'        => 0,
                'iva_total'              => $ivaTotal,
                'total'                  => $total,
                'porcentaje_iva_general' => $porcentajeIva,
                'estado_pago'            => 'pendiente',
                'observaciones'          => $validated['observaciones'] ?? null,
                'user_id'                => auth()->id(),
            ]);

            // 3. Procesar Ítems, Lotes KARDEX y Movimientos de Inventario
            foreach ($validated['items'] as $itemData) {
                $compra->items()->create([
                    'insumo_id'      => $itemData['insumo_id'] ?? null,
                    'cantidad'       => $itemData['cantidad'],
                    'costo_unitario' => $itemData['costo_unitario'],
                    'subtotal'       => $itemData['cantidad'] * $itemData['costo_unitario'],
                ]);

                // Generar Lote y Movimiento KARDEX únicamente si es compra de insumos
                if ($compra->tipo === 'insumos') {
                    $codigoLote = $itemData['codigo_lote'] ?? 'LOT-' . strtoupper(Str::random(8));

                    // A) Crear Lote Insumo
                    $lote = LoteInsumo::create([
                        'insumo_id'         => $itemData['insumo_id'],
                        'proveedor_id'      => $compra->proveedor_id,
                        'compra_id'         => $compra->id,
                        'codigo_lote'       => $codigoLote,
                        'fecha_vencimiento' => $itemData['fecha_vencimiento'] ?? null,
                        'fecha_ingreso'     => $compra->fecha,
                        'cantidad_inicial'  => $itemData['cantidad'],
                        'cantidad_actual'   => 0, // Evento booted() de MovimientoStock actualizará a la cantidad real
                        'costo_unitario'    => $itemData['costo_unitario'],
                        'estado'            => 'activo',
                    ]);

                    // B) Registrar Entrada en MovimientoStock (KARDEX)
                    MovimientoStock::create([
                        'lote_insumo_id'      => $lote->id,
                        'tipo_movimiento'     => 'entrada_compra',
                        'cantidad'            => $itemData['cantidad'],
                        'movimientoable_id'   => $compra->id,
                        'movimientoable_type' => Compra::class,
                        'observacion'         => "Entrada por compra Factura #" . ($compra->numero_factura ?? 'S/N'),
                        'user_id'             => $compra->user_id,
                    ]);
                }
            }
        });

        return redirect()
            ->route('compras.index')
            ->with('success', 'La compra fue registrada exitosamente y el inventario KARDEX ha sido actualizado.');
    }

    public function show($id)
    {
        $compra = Compra::with(['proveedor', 'items.insumo', 'pagos', 'user'])
            ->withSum('pagos', 'monto')
            ->findOrFail($id);

        return view('compras.show', compact('compra'));
    }
}