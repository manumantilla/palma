<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\CicloProductivo; // Asumiendo que existe para el select del modal
use Illuminate\Http\Request;

class GastoController extends Controller
{
    /**
     * Lista general de egresos/gastos de la empresa
     */
    public function index()
    {
        $gastos = Gasto::with(['gastable', 'usuario'])->orderBy('fecha', 'desc')->get();
        return view('gastos.index', compact('gastos'));
    }

    /**
     * Almacena el gasto sin importar de qué vista provenga (Polimórfico)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'gastable_type'     => 'required|string', // Ej: "App\Models\LaborPlantilla"
            'gastable_id'       => 'required|integer',
            'ciclo_producto_id' => 'nullable|exists:ciclos_productivos,id',
            'categoria'         => 'required|in:insumos,mano_obra,maquinaria,transporte,servicios_publicos,arriendos,mantenimiento,administrativos,impuestos,seguros,otros',
            'metodo_pago'       => 'nullable|in:efectivo,transferencia,tarjeta',
            'concepto'          => 'required|string|max:255',
            'monto'             => 'required|numeric|min:0',
            'fecha'             => 'required|date',
            'descripcion'       => 'nullable|string',
        ]);

        // Asignamos automáticamente el ID del usuario logueado en Jetstream
        $validated['user_id'] = auth()->id();

        Gasto::create($validated);

        return redirect()->back()->with('success', '¡Gasto registrado y cargado al costo operativo!');
    }

    /**
     * Formulario de edición (Por si necesitan corregir un monto)
     */
    public function edit(Gasto $gasto)
    {
        $ciclos = CicloProductivo::all(); // Por si quieren reasociar el ciclo
        return view('gastos.edit', compact('gasto', 'ciclos'));
    }

    /**
     * Actualiza los datos del gasto
     */
    public function update(Request $request, Gasto $gasto)
    {
        $validated = $request->validate([
            'ciclo_producto_id' => 'nullable|exists:ciclos_productivos,id',
            'categoria'         => 'required|string',
            'metodo_pago'       => 'nullable|string',
            'concepto'          => 'required|string|max:255',
            'monto'             => 'required|numeric|min:0',
            'fecha'             => 'required|date',
            'descripcion'       => 'nullable|string',
        ]);

        $gasto->update($validated);

        return redirect()->route('gastos.index')->with('success', 'Gasto actualizado correctamente.');
    }

    /**
     * Elimina un registro de gasto
     */
    public function destroy(Gasto $gasto)
    {
        $gasto->delete();
        return redirect()->back()->with('success', 'Gasto eliminado del sistema.');
    }
}