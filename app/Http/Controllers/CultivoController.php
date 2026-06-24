<?php

namespace App\Http\Controllers;

use App\Models\Cultivo;
use Illuminate\Http\Request;

class CultivoController extends Controller
{
    /**
     * Muestra el listado de todos los cultivos.
     */
    public function index()
    {
        $cultivos = Cultivo::all();
        return view('cultivos.index', compact('cultivos'));
    }

    /**
     * Muestra el formulario para crear un nuevo cultivo.
     */
    public function create()
    {
        return view('cultivos.form');
    }

    /**
     * Almacena un cultivo recién creado en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_cultivo' => 'required|string|unique:cultivos,nombre_cultivo|max:255',
            'tipo'           => 'required|in:perenne,transitorio',
            'descripcion'    => 'nullable|string',
        ]);

        Cultivo::create($validated);

        return redirect()->route('cultivos.index')
            ->with('success', '¡Cultivo creado con éxito!');
    }

    /**
     * Muestra el detalle de un cultivo específico (con sus etapas y labores).
     */
    public function show(Cultivo $cultivo)
    {
        // Cargamos las relaciones optimizadas para las pestañas de la vista
        $cultivo->load([
            'etapasFenologicas' => function ($query) {
                $query->orderBy('orden', 'asc');
            },
            'laboresPlantilla.etapaFenologica'
        ]);

        return view('cultivos.show', compact('cultivo'));
    }

    /**
     * Muestra el formulario para editar un cultivo existente.
     */
    public function edit(Cultivo $cultivo)
    {
        return view('cultivos.form', compact('cultivo'));
    }

    /**
     * Actualiza el cultivo en la base de datos.
     */
    public function update(Request $request, Cultivo $cultivo)
    {
        $validated = $request->validate([
            'nombre_cultivo' => 'required|string|max:255|unique:cultivos,nombre_cultivo,' . $cultivo->id,
            'tipo'           => 'required|in:perenne,transitorio',
            'descripcion'    => 'nullable|string',
        ]);

        $cultivo->update($validated);

        return redirect()->route('cultivos.index')
            ->with('success', '¡Cultivo actualizado con éxito!');
    }

    /**
     * Elimina el cultivo de la base de datos.
     */
    public function destroy(Cultivo $cultivo)
    {
        // Al tener onDelete('cascade') en tus migraciones, 
        // Laravel y la BD borrarán automáticamente sus etapas y labores.
        $cultivo->delete();

        return redirect()->route('cultivos.index')
            ->with('success', '¡Cultivo eliminado correctamente junto con todas sus dependencias!');
    }
}