<?php
namespace App\Http\Controllers;

use App\Models\FenologiaEtapa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class FenologiaEtapaController extends Controller
{
    public function index()
    {
        try {
            $etapas = FenologiaEtapa::orderBy('orden', 'asc')->get();
            return view('fenologia_etapa.index', compact('etapas'));
        } catch (Exception $e) {
            Log::error('Error al listar etapas fenológicas: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Ocurrió un error al cargar las etapas.');
        }
    }

    public function create()
    {
        return view('fenologia_etapa.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'cultivo_id' => 'required|exists:cultivos,id',
                'nombre' => 'required|string|max:255',
                'orden' => 'required|integer',
                'duracion_dias_desde_inicio' => 'nullable|integer',
                'duracion_dias_estimada' => 'required|integer',
                'descripcion' => 'nullable|string',
            ]);

            $etapa = FenologiaEtapa::create($validated);
            Log::info("Etapa fenológica creada exitosamente: ID {$etapa->id}");

            return redirect()->route('fenologia-etapa.index')->with('success', 'Etapa creada correctamente.');
        } catch (Exception $e) {
            Log::error('Error al crear etapa fenológica: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'No se pudo crear la etapa. Verifique los datos.');
        }
    }

    public function show($id)
    {
        try {
            $etapa = FenologiaEtapa::findOrFail($id);
            return view('fenologia_etapa.show', compact('etapa'));
        } catch (Exception $e) {
            Log::error("Error al buscar etapa fenológica ID {$id}: " . $e->getMessage());
            return redirect()->route('fenologia-etapa.index')->with('error', 'Etapa no encontrada.');
        }
    }

    public function edit($id)
    {
        try {
            $etapa = FenologiaEtapa::findOrFail($id);
            return view('fenologia_etapa.edit', compact('etapa'));
        } catch (Exception $e) {
            Log::error("Error al buscar etapa fenológica para edición ID {$id}: " . $e->getMessage());
            return redirect()->route('fenologia-etapa.index')->with('error', 'Etapa no encontrada.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'cultivo_id' => 'required|exists:cultivos,id',
                'nombre' => 'required|string|max:255',
                'orden' => 'required|integer',
                'duracion_dias_desde_inicio' => 'nullable|integer',
                'duracion_dias_estimada' => 'required|integer',
                'descripcion' => 'nullable|string',
            ]);

            $etapa = FenologiaEtapa::findOrFail($id);
            $etapa->update($validated);
            
            Log::info("Etapa fenológica actualizada exitosamente: ID {$etapa->id}");

            return redirect()->route('fenologia-etapa.index')->with('success', 'Etapa actualizada correctamente.');
        } catch (Exception $e) {
            Log::error("Error al actualizar etapa fenológica ID {$id}: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'No se pudo actualizar la etapa.');
        }
    }

    public function destroy($id)
    {
        try {
            $etapa = FenologiaEtapa::findOrFail($id);
            $etapa->delete();
            
            Log::info("Etapa fenológica eliminada: ID {$id}");

            return redirect()->route('fenologia-etapa.index')->with('success', 'Etapa eliminada correctamente.');
        } catch (Exception $e) {
            Log::error("Error al eliminar etapa fenológica ID {$id}: " . $e->getMessage());
            return redirect()->route('fenologia-etapa.index')->with('error', 'No se pudo eliminar la etapa.');
        }
    }
}