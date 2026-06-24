<?php

namespace App\Http\Controllers;

use App\Models\Cultivo;
use App\Models\LaborPlantilla;
use App\Models\TipoEvento; // Asumiendo que existe el modelo para 'tipos_evento'
use Illuminate\Http\Request;

class LaborPlantillaController extends Controller
{
    public function create(Request $request)
    {
        $cultivo = Cultivo::with('etapasFenologicas')->findOrFail($request->get('cultivo_id'));
        
        // Traemos los tipos de evento para el select por defecto
        // Si no tienes el modelo creado aún, puedes usar: \DB::table('tipos_evento')->get()
        $tiposEvento = TipoEvento::all(); 

        return view('labores_plantilla.form', compact('cultivo', 'tiposEvento'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cultivo_id'              => 'required|exists:cultivos,id',
            'nombre_labor'            => 'required|string|max:255',
            'descripcion'             => 'nullable|string',
            'momento_tipo'            => 'required|in:dias_desde_siembra,etapa_fenologica,fecha_fija_anual',
            'dias_desde_siembra'      => 'required_if:momento_tipo,dias_desde_siembra|nullable|integer|min:0',
            'fenologia_etapa_id'      => 'required_if:momento_tipo,etapa_fenologica|nullable|exists:fenologia_etapas,id',
            'periodicidad_dias'       => 'nullable|integer|min:1',
            'duracion_estimada_horas' => 'nullable|integer|min:1',
            'tipo_evento_id'          => 'required|exists:tipos_evento,id',
            'requiere_insumos'        => 'boolean',
            'requiere_mano_obra'      => 'boolean',
        ]);

        // Ajustar los booleanos si no vienen en el request (los checkboxes no viajan si están apagados)
        $validated['requiere_insumos'] = $request->has('requiere_insumos');
        $validated['requiere_mano_obra'] = $request->has('requiere_mano_obra');

        LaborPlantilla::create($validated);

        return redirect()->route('cultivos.show', $validated['cultivo_id'])
            ->with('success', '¡Plantilla de labor guardada exitosamente!');
    }

    public function edit(LaborPlantilla $laborPlantilla)
    {
        $cultivo = Cultivo::with('etapasFenologicas')->findOrFail($laborPlantilla->cultivo_id);
        $tiposEvento = TipoEvento::all();

        return view('labores_plantilla.form', [
            'labor' => $laborPlantilla,
            'cultivo' => $cultivo,
            'tiposEvento' => $tiposEvento
        ]);
    }

    public function update(Request $request, LaborPlantilla $laborPlantilla)
    {
        $validated = $request->validate([
            'nombre_labor'            => 'required|string|max:255',
            'descripcion'             => 'nullable|string',
            'momento_tipo'            => 'required|in:dias_desde_siembra,etapa_fenologica,fecha_fija_anual',
            'dias_desde_siembra'      => 'required_if:momento_tipo,dias_desde_siembra|nullable|integer|min:0',
            'fenologia_etapa_id'      => 'required_if:momento_tipo,etapa_fenologica|nullable|exists:fenologia_etapas,id',
            'periodicidad_dias'       => 'nullable|integer|min:1',
            'duracion_estimada_horas' => 'nullable|integer|min:1',
            'tipo_evento_id'          => 'required|exists:tipos_evento,id',
        ]);

        $validated['requiere_insumos'] = $request->has('requiere_insumos');
        $validated['requiere_mano_obra'] = $request->has('requiere_mano_obra');

        $laborPlantilla->update($validated);

        return redirect()->route('cultivos.show', $laborPlantilla->cultivo_id)
            ->with('success', '¡Plantilla de labor actualizada!');
    }

    public function destroy(LaborPlantilla $laborPlantilla)
    {
        $cultivoId = $laborPlantilla->cultivo_id;
        $laborPlantilla->delete();

        return redirect()->route('cultivos.show', $cultivoId)
            ->with('success', 'Labor eliminada de la plantilla.');
    }
}