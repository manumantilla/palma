<?php

namespace App\Http\Controllers;

use App\Models\CicloEtapaHistorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class CicloEtapaHistorialController extends Controller
{
    public function index()
    {
        try {
            $historiales = CicloEtapaHistorial::with(['cicloProductivo', 'etapa'])->get();
            return view('ciclo_etapa_historial.index', compact('historiales'));
        } catch (Exception $e) {
            Log::error('Error al listar el historial de etapas: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Ocurrió un error al cargar los historiales.');
        }
    }

    public function create()
    {
        return view('ciclo_etapa_historial.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'ciclo_productivo_id' => 'required|exists:ciclos_productivos,id',
                'fenologia_etapa_id' => 'required|exists:fenologia_etapas,id',
                'fecha_inicio_estimada' => 'required|date',
                'fecha_inicio_real' => 'nullable|date',
                'fecha_fin_estimada' => 'required|date',
                'fecha_fin_real' => 'nullable|date',
                'estado' => 'required|in:pendiente,en_progreso,completada,omitida',
            ]);

            $historial = CicloEtapaHistorial::create($validated);
            Log::info("Historial de etapa creado exitosamente: ID {$historial->id}");

            return redirect()->route('ciclo-etapa-historial.index')->with('success', 'Historial creado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al crear historial de etapa: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'No se pudo crear el registro. Verifique los datos.');
        }
    }

    public function show($id)
    {
        try {
            $historial = CicloEtapaHistorial::with(['cicloProductivo', 'etapa'])->findOrFail($id);
            return view('ciclo_etapa_historial.show', compact('historial'));
        } catch (Exception $e) {
            Log::error("Error al buscar historial de etapa ID {$id}: " . $e->getMessage());
            return redirect()->route('ciclo-etapa-historial.index')->with('error', 'Registro no encontrado.');
        }
    }

    public function edit($id)
    {
        try {
            $historial = CicloEtapaHistorial::findOrFail($id);
            return view('ciclo_etapa_historial.edit', compact('historial'));
        } catch (Exception $e) {
            Log::error("Error al buscar historial de etapa para edición ID {$id}: " . $e->getMessage());
            return redirect()->route('ciclo-etapa-historial.index')->with('error', 'Registro no encontrado.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'ciclo_productivo_id' => 'required|exists:ciclos_productivos,id',
                'fenologia_etapa_id' => 'required|exists:fenologia_etapas,id',
                'fecha_inicio_estimada' => 'required|date',
                'fecha_inicio_real' => 'nullable|date',
                'fecha_fin_estimada' => 'required|date',
                'fecha_fin_real' => 'nullable|date',
                'estado' => 'required|in:pendiente,en_progreso,completada,omitida',
            ]);

            $historial = CicloEtapaHistorial::findOrFail($id);
            $historial->update($validated);
            
            Log::info("Historial de etapa actualizado exitosamente: ID {$historial->id}");

            return redirect()->route('ciclo-etapa-historial.index')->with('success', 'Registro actualizado correctamente.');
        } catch (Exception $e) {
            Log::error("Error al actualizar historial de etapa ID {$id}: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'No se pudo actualizar el registro.');
        }
    }

    public function destroy($id)
    {
        try {
            $historial = CicloEtapaHistorial::findOrFail($id);
            $historial->delete();
            
            Log::info("Historial de etapa eliminado: ID {$id}");

            return redirect()->route('ciclo-etapa-historial.index')->with('success', 'Registro eliminado correctamente.');
        } catch (Exception $e) {
            Log::error("Error al eliminar historial de etapa ID {$id}: " . $e->getMessage());
            return redirect()->route('ciclo-etapa-historial.index')->with('error', 'No se pudo eliminar el registro.');
        }
    }
}