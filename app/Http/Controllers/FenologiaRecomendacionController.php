<?php
namespace App\Http\Controllers;

use App\Models\FenologiaRecomendacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class FenologiaRecomendacionController extends Controller
{
    public function index()
    {
        try {
            $recomendaciones = FenologiaRecomendacion::with(['etapa', 'tipoEvento'])->get();
            return view('fenologia_recomendacion.index', compact('recomendaciones'));
        } catch (Exception $e) {
            Log::error('Error al listar recomendaciones fenológicas: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Ocurrió un error al cargar las recomendaciones.');
        }
    }

    public function create()
    {
        
        return view('fenologia_recomendacion.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'fenologia_etapa_id' => 'required|exists:fenologia_etapas,id',
                'tipo_evento_id' => 'nullable|exists:tipos_evento,id',
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'prioridad' => 'required|in:Baja,Media,Alta,Crítica',
                'dias_offset' => 'required|integer',
                'ventana_ejecucion_dias' => 'nullable|integer',
                'genera_evento_automatico' => 'boolean',
                'requiere_verificacion_campo' => 'boolean',
                'instrucciones_tecnicas' => 'nullable|string',
            ]);

            // Manejo de checkboxes no enviados en el request
            $validated['genera_evento_automatico'] = $request->has('genera_evento_automatico');
            $validated['requiere_verificacion_campo'] = $request->has('requiere_verificacion_campo');

            $recomendacion = FenologiaRecomendacion::create($validated);
            Log::info("Recomendación fenológica creada exitosamente: ID {$recomendacion->id}");

            return redirect()->route('fenologia-recomendacion.index')->with('success', 'Recomendación creada correctamente.');
        } catch (Exception $e) {
            Log::error('Error al crear recomendación fenológica: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'No se pudo crear la recomendación. Verifique los datos.');
        }
    }

    public function show($id)
    {
        try {
            $recomendacion = FenologiaRecomendacion::with(['etapa', 'tipoEvento'])->findOrFail($id);
            return view('fenologia_recomendacion.show', compact('recomendacion'));
        } catch (Exception $e) {
            Log::error("Error al buscar recomendación fenológica ID {$id}: " . $e->getMessage());
            return redirect()->route('fenologia-recomendacion.index')->with('error', 'Recomendación no encontrada.');
        }
    }

    public function edit($id)
    {
        try {
            $recomendacion = FenologiaRecomendacion::findOrFail($id);
            return view('fenologia_recomendacion.edit', compact('recomendacion'));
        } catch (Exception $e) {
            Log::error("Error al buscar recomendación fenológica para edición ID {$id}: " . $e->getMessage());
            return redirect()->route('fenologia-recomendacion.index')->with('error', 'Recomendación no encontrada.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'fenologia_etapa_id' => 'required|exists:fenologia_etapas,id',
                'tipo_evento_id' => 'nullable|exists:tipos_evento,id',
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'prioridad' => 'required|in:Baja,Media,Alta,Crítica',
                'dias_offset' => 'required|integer',
                'ventana_ejecucion_dias' => 'nullable|integer',
                'genera_evento_automatico' => 'boolean',
                'requiere_verificacion_campo' => 'boolean',
                'instrucciones_tecnicas' => 'nullable|string',
            ]);

            $validated['genera_evento_automatico'] = $request->has('genera_evento_automatico');
            $validated['requiere_verificacion_campo'] = $request->has('requiere_verificacion_campo');

            $recomendacion = FenologiaRecomendacion::findOrFail($id);
            $recomendacion->update($validated);
            
            Log::info("Recomendación fenológica actualizada exitosamente: ID {$recomendacion->id}");

            return redirect()->route('fenologia-recomendacion.index')->with('success', 'Recomendación actualizada correctamente.');
        } catch (Exception $e) {
            Log::error("Error al actualizar recomendación fenológica ID {$id}: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'No se pudo actualizar la recomendación.');
        }
    }

    public function destroy($id)
    {
        try {
            $recomendacion = FenologiaRecomendacion::findOrFail($id);
            $recomendacion->delete();
            
            Log::info("Recomendación fenológica eliminada: ID {$id}");

            return redirect()->route('fenologia-recomendacion.index')->with('success', 'Recomendación eliminada correctamente.');
        } catch (Exception $e) {
            Log::error("Error al eliminar recomendación fenológica ID {$id}: " . $e->getMessage());
            return redirect()->route('fenologia-recomendacion.index')->with('error', 'No se pudo eliminar la recomendación.');
        }
    }
}