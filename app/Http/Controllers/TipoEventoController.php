<?php
namespace App\Http\Controllers;

use App\Models\TipoEvento;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TipoEventoController extends Controller
{
    public function index()
    {
        $tipos = TipoEvento::orderBy('categoria')->get();
        return view('tipos_evento.index', compact('tipos'));
    }

    public function create()
    {
        $categorias = ['Mantenimiento', 'Fitosanitario', 'Cosecha', 'Fertilización', 'Logística'];
        return view('tipos_evento.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:tipos_evento,nombre',
            'categoria' => ['required', Rule::in(['Mantenimiento', 'Fitosanitario', 'Cosecha', 'Fertilización', 'Logística'])],
            'consume_insumos' => 'boolean',
            'consume_mano_obra' => 'boolean',
            'genera_ingreso' => 'boolean',
            'genera_movimiento_stock' => 'boolean',
            'requiere_area_ha' => 'boolean',
            'aplica_a_arbol' => 'boolean',
            'aplica_a_ciclo' => 'boolean',
            'periodo_reingreso_horas' => 'nullable|integer|min:0',
            'periodo_carencia_dias' => 'nullable|integer|min:0',
        ]);

        // Asegurar el formateo de los checkboxes ausentes en el request (bajan como false)
        $flags = ['consume_insumos', 'consume_mano_obra', 'genera_ingreso', 'genera_movimiento_stock', 'requiere_area_ha', 'aplica_a_arbol', 'aplica_a_ciclo'];
        foreach ($flags as $flag) {
            $validated[$flag] = $request->has($flag);
        }

        TipoEvento::create($validated);

        return redirect()->route('tipos-evento.index')->with('success', 'Tipo de evento agrícola configurado con éxito.');
    }
}