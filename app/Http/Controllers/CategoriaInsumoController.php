<?php

namespace App\Http\Controllers;

use App\Models\CategoriaInsumo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoriaInsumoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CategoriaInsumo::query();
        
        // Búsqueda por nombre
        if ($request->filled('search')) {
            $query->where('nombre', 'LIKE', "%{$request->search}%");
        }
        
        // Filtro por manejo de vencimiento
        if ($request->filled('maneja_vencimiento')) {
            $query->where('maneja_vencimiento', $request->maneja_vencimiento);
        }
        
        // Filtro por manejo de toxicidad
        if ($request->filled('maneja_toxicidad')) {
            $query->where('maneja_toxicidad', $request->maneja_toxicidad);
        }
        
        // Ordenamiento
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        // Paginación
        $categorias = $query->paginate(15)->withQueryString();
        
        return view('categorias_insumo.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categorias_insumo.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias_insumo,nombre',
            'maneja_vencimiento' => 'sometimes|boolean',
            'maneja_toxicidad' => 'sometimes|boolean',
        ]);
        
        // Convertir checkbox a boolean
        $validated['maneja_vencimiento'] = $request->has('maneja_vencimiento');
        $validated['maneja_toxicidad'] = $request->has('maneja_toxicidad');
        
        CategoriaInsumo::create($validated);
        
        return redirect()->route('categorias-insumo.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CategoriaInsumo $categorias_insumo)
    {
        return view('categorias_insumo.show', compact('categorias_insumo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategoriaInsumo $categorias_insumo)
    {
        return view('categorias_insumo.form', compact('categorias_insumo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CategoriaInsumo $categorias_insumo)
    {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categorias_insumo', 'nombre')->ignore($categorias_insumo->id)
            ],
            'maneja_vencimiento' => 'sometimes|boolean',
            'maneja_toxicidad' => 'sometimes|boolean',
        ]);
        
        // Convertir checkbox a boolean
        $validated['maneja_vencimiento'] = $request->has('maneja_vencimiento');
        $validated['maneja_toxicidad'] = $request->has('maneja_toxicidad');
        
        $categorias_insumo->update($validated);
        
        return redirect()->route('categorias-insumo.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoriaInsumo $categorias_insumo)
    {
        // Verificar si tiene insumos asociados
        if ($categorias_insumo->insumos()->count() > 0) {
            return redirect()->route('categorias-insumo.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene insumos asociados.');
        }
        
        $categorias_insumo->delete();
        
        return redirect()->route('categorias-insumo.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }
    
    /**
     * API: Obtener todas las categorías (para selects dinámicos)
     */
    public function getCategorias(Request $request)
    {
        $query = CategoriaInsumo::query();
        
        if ($request->filled('search')) {
            $query->where('nombre', 'LIKE', "%{$request->search}%");
        }
        
        if ($request->filled('con_vencimiento')) {
            $query->where('maneja_vencimiento', true);
        }
        
        if ($request->filled('con_toxicidad')) {
            $query->where('maneja_toxicidad', true);
        }
        
        $categorias = $query->orderBy('nombre')->get();
        
        return response()->json($categorias);
    }
}