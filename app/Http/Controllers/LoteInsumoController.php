<?php

namespace App\Http\Controllers;

use App\Exceptions\InventarioException;
use App\Models\LoteInsumo;
use App\Models\Insumo;
use App\Models\Proveedor;
use App\Services\InventarioService;
use Illuminate\Http\Request;
use App\Models\Compra;

class LoteInsumoController extends Controller
{
    public function __construct(private InventarioService $inventario)
    {
    }

    public function index()
    {
        // Listado enfocado en el almacenista: control de vencimientos y estados críticos
        $lotes = LoteInsumo::with(['insumo', 'proveedor'])
            ->whereIn('estado', ['activo', 'cuarentena', 'vencido'])
            ->orderByRaw('fecha_vencimiento ASC NULLS LAST')
            ->get();

        return view('lotes_insumo.index', compact('lotes'));
    }

    public function create()
    {
        $insumos = Insumo::where('estado', 'activo')->orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre')->get(); // Asumiendo que existe la tabla proveedores
        $compras = Compra::all();
        return view('lotes_insumo.create', compact('insumos', 'proveedores', 'compras'));
    }

    /**
     * Ingreso manual de un lote. Lo que viene con factura entra por CompraController;
     * aquí entra el saldo inicial (lo que ya había en bodega al empezar a usar el sistema)
     * o un ingreso sin compra asociada.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'insumo_id'         => 'required|exists:insumos,id',
            'proveedor_id'      => 'nullable|exists:proveedores,id',
            'compra_id'         => 'nullable|exists:compras,id',
            'codigo_lote'       => 'required|string|max:100',
            'fecha_ingreso'     => 'required|date|before_or_equal:today',
            'fecha_vencimiento' => 'nullable|date|after:fecha_ingreso',
            'cantidad_inicial'  => 'required|numeric|min:0.0001',
            'costo_unitario'    => 'required|numeric|min:0',
            'estado'            => 'required|in:activo,cuarentena',
            'observacion_kardex'=> 'nullable|string|max:255'
        ]);

        try {
            $this->inventario->ingresarLote(
                ['cantidad' => $validated['cantidad_inicial']] + $validated,
                empty($validated['compra_id']) ? 'entrada_saldo_inicial' : 'entrada_compra',
                null,
                $validated['observacion_kardex'] ?? 'Ingreso manual de lote.'
            );
        } catch (InventarioException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('lotes_insumo.index')->with('success', 'Lote ingresado a bodega y asentado en el Kardex.');
    }

    /**
     * Cuarentena, vencido o de vuelta a activo. 'agotado' no se pone a mano: lo pone el kardex.
     */
    public function updateEstado(Request $request, LoteInsumo $lote)
    {
        $request->validate([
            'estado' => 'required|in:activo,cuarentena,vencido'
        ]);

        try {
            $this->inventario->cambiarEstado($lote, $request->estado);
        } catch (InventarioException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Estado del lote modificado correctamente.');
    }

    /**
     * Ajuste manual de stock (Mermas, hurtos, etc.) desde la oficina de control
     */
    public function ajustarStockManual(Request $request, LoteInsumo $lote)
    {
        $validated = $request->validate([
            'tipo_movimiento' => 'required|in:ajuste_entrada,entrada_devolucion,ajuste_salida_merma,ajuste_salida_hurto,ajuste_salida_vencido,salida_devolucion_prov',
            'cantidad'        => 'required|numeric|min:0.0001',
            'fecha'           => 'nullable|date|before_or_equal:today',
            'observacion'     => 'required|string|max:255'
        ]);

        try {
            $this->inventario->mover(
                $lote, $validated['tipo_movimiento'], $validated['cantidad'], $lote,
                $validated['observacion'], $validated['fecha'] ?? null
            );
        } catch (InventarioException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Inventario ajustado y auditado con éxito.');
    }
}
