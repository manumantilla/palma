<?php

namespace App\Http\Controllers;

use App\Models\LoteInsumo;
use App\Models\Insumo;
use App\Models\Proveedor;
use App\Models\MovimientoStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoteInsumoController extends Controller
{
    public function index()
    {
        // Listado enfocado en el almacenista: control de vencimientos y estados críticos
        $lotes = LoteInsumo::with(['insumo', 'proveedor'])
            ->whereIn('estado', ['activo', 'cuarentena', 'vencido'])
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        return view('lotes_insumo.index', compact('lotes'));
    }

    public function create()
    {
        $insumos = Insumo::where('estado', 'activo')->orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre')->get(); // Asumiendo que existe la tabla proveedores
        return view('lotes_insumo.create', compact('insumos', 'proveedores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'insumo_id'         => 'required|exists:insumos,id',
            'proveedor_id'      => 'nullable|exists:proveedores,id',
            'codigo_lote'       => 'required|string|max:100',
            'ubicacion_bodega'  => 'nullable|string|max:100',
            'fecha_vencimiento' => 'required|date|after:fecha_ingreso',
            'fecha_ingreso'     => 'required|date',
            'cantidad_inicial'  => 'required|numeric|min:0.01',
            'unidad'            => 'required|string|max:20',
            'costo_unitario'    => 'required|numeric|min:0',
            'estado'            => 'required|in:activo,cuarentena',
            'observacion_kardex'=> 'nullable|string|max:255'
        ]);

        // La cantidad actual al ingresar por primera vez es igual a la inicial
        $validated['cantidad_actual'] = $validated['cantidad_inicial'];

        DB::transaction(function () use ($validated) {
            // 1. Creamos el lote físico en la bodega
            $lote = LoteInsumo::create($validated);

            // 2. REGISTRO AUTOMÁTICO EN EL KARDEX
            // El trigger 'booted' que programamos en MovimientoStock procesará esto,
            // pero como ya seteamos la cantidad_actual arriba, solo sirve de bitácora perfecta de entrada.
            MovimientoStock::create([
                'lote_insumo_id'    => $lote->id,
                'tipo_movimiento'   => 'entrada_compra',
                'cantidad'          => $lote->cantidad_inicial,
                'movimientoable_id' => $lote->id, // Polimórfico: se apunta a sí mismo o a una tabla compras si existiera
                'movimientoable_type'=> LoteInsumo::class,
                'observacion'       => $validated['observacion_kardex'] ?? 'Ingreso inicial por compra de lote.',
                'user_id'           => auth()->id(),
            ]);
        });

        return redirect()->route('lotes_insumo.index')->with('success', 'Lote ingresado a bodega y asentado en el Kardex.');
    }

    /**
     * Permite al almacenista cambiar estados (Ej: Pasar a cuarentena o registrar mermas manuales)
     */
    public function updateEstado(Request $request, LoteInsumo $lote)
    {
        $request->validate([
            'estado' => 'required|in:activo,cuarentena,vencido,agotado'
        ]);

        $lote->update(['estado' => $request->estado]);

        return redirect()->back()->with('success', 'Estado del lote modificado correctamente.');
    }

    /**
     * Ajuste manual de stock (Mermas, hurtos, etc.) desde la oficina de control
     */
    public function ajustarStockManual(Request $request, LoteInsumo $lote)
    {
        $validated = $request->validate([
            'tipo_movimiento' => 'required|in:ajuste_entrada,ajuste_salida_merma,ajuste_salida_hurto,ajuste_salida_vencido',
            'cantidad'        => 'required|numeric|min:0.01',
            'observacion'     => 'required|string|max:255'
        ]);

        DB::transaction(function () use ($lote, $validated) {
            // Generamos el movimiento del Kardex, el cual actualizará la cantidad_actual del lote solito
            MovimientoStock::create([
                'lote_insumo_id'      => $lote->id,
                'tipo_movimiento'     => $validated['tipo_movimiento'],
                'cantidad'            => $validated['cantidad'],
                'movimientoable_id'   => $lote->id,
                'movimientoable_type' => LoteInsumo::class,
                'observacion'         => $validated['observacion'],
                'user_id'             => auth()->id()
            ]);
        });

        return redirect()->back()->with('success', 'Inventario ajustado y auditado con éxito.');
    }
}