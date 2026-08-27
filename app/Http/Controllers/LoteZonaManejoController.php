<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\LoteZonaManejo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LoteZonaManejoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $zonas = LoteZonaManejo::with('lote')->latest()->paginate(15);
        return view('lote-zonas-manejo.index', compact('zonas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lotes = Lote::where('activo', true)->orderBy('nombre_lote')->get();
        return view('lote-zonas-manejo.create', compact('lotes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lote_id'           => 'required|exists:lotes,id',
            'nombre_zona'       => 'required|string|max:100',
            'codigo_zona'       => 'required|string|max:50|unique:lotes_zonas_manejo,codigo_zona',
            'area_hectareas'    => 'required|numeric|min:0',
            'geometria_zona'    => 'nullable|string', // Se puede validar WKT si se desea
        ]);

        try {
            DB::transaction(function () use ($request) {
                LoteZonaManejo::create($request->only([
                    'lote_id', 'nombre_zona', 'codigo_zona', 'area_hectareas', 'geometria_zona'
                ]));
            });

            return redirect()->route('lote-zonas-manejo.index')
                ->with('success', 'Zona de manejo creada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la zona: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(LoteZonaManejo $loteZonaManejo)
    {
        return view('lote-zonas-manejo.show', compact('loteZonaManejo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LoteZonaManejo $loteZonaManejo)
    {
        $lotes = Lote::where('activo', true)->orderBy('nombre_lote')->get();
        return view('lote-zonas-manejo.edit', compact('loteZonaManejo', 'lotes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LoteZonaManejo $loteZonaManejo)
    {
        $request->validate([
            'lote_id'           => 'required|exists:lotes,id',
            'nombre_zona'       => 'required|string|max:100',
            'codigo_zona'       => [
                'required',
                'string',
                'max:50',
                Rule::unique('lotes_zonas_manejo', 'codigo_zona')->ignore($loteZonaManejo->id),
            ],
            'area_hectareas'    => 'required|numeric|min:0',
            'geometria_zona'    => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $loteZonaManejo) {
                $loteZonaManejo->update($request->only([
                    'lote_id', 'nombre_zona', 'codigo_zona', 'area_hectareas', 'geometria_zona'
                ]));
            });

            return redirect()->route('lote-zonas-manejo.index')
                ->with('success', 'Zona de manejo actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la zona: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoteZonaManejo $loteZonaManejo)
    {
        try {
            DB::transaction(function () use ($loteZonaManejo) {
                $loteZonaManejo->delete();
            });

            return redirect()->route('lote-zonas-manejo.index')
                ->with('success', 'Zona de manejo eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar la zona: ' . $e->getMessage());
        }
    }
}