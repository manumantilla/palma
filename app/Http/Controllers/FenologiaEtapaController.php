<?php

namespace App\Http\Controllers;

use App\Models\Cultivo;
use App\Models\FenologiaEtapa;
use Illuminate\Http\Request;

class FenologiaEtapaController extends Controller
{
    /**
     * Muestra el formulario para crear una etapa vinculada a un cultivo.
     */
    public function create(Request $request)
    {
        // Forzamos a que venga un cultivo_id válido por la URL
        $cultivo = Cultivo::findOrFail($request->get('cultivo_id'));

        return view('fenologia_etapas.form', compact('cultivo'));
    }

    /**
     * Guarda la nueva etapa fenológica.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cultivo_id'                 => 'required|exists:cultivos,id',
            'nombre'                     => 'required|string|max:255',
            'orden'                      => 'required|integer|min:1',
            'duracion_dias_desde_inicio' => 'nullable|integer|min:0',
            'duracion_dias_estimada'     => 'required|integer|min:1',
            'descripcion'                => 'nullable|string',
        ]);

        FenologiaEtapa::create($validated);

        // Redirige directo al detalle del cultivo, directo a la pestaña que corresponde
        return redirect()->route('cultivos.show', $validated['cultivo_id'])
            ->with('success', '¡Etapa fenológica agregada con éxito!');
    }

    /**
     * Muestra el formulario para editar.
     */
    public function edit(FenologiaEtapa $fenologiaEtapa)
    {
        $cultivo = $fenologiaEtapa->cultivo;
        return view('fenologia_etapas.form', [
            'etapa'   => $fenologiaEtapa,
            'cultivo' => $cultivo
        ]);
    }

    /**
     * Actualiza la etapa.
     */
    public function update(Request $request, FenologiaEtapa $fenologiaEtapa)
    {
        $validated = $request->validate([
            'nombre'                     => 'required|string|max:255',
            'orden'                      => 'required|integer|min:1',
            'duracion_dias_desde_inicio' => 'nullable|integer|min:0',
            'duracion_dias_estimada'     => 'required|integer|min:1',
            'descripcion'                => 'nullable|string',
        ]);

        $fenologiaEtapa->update($validated);

        return redirect()->route('cultivos.show', $fenologiaEtapa->cultivo_id)
            ->with('success', '¡Etapa fenológica actualizada!');
    }

    /**
     * Elimina la etapa.
     */
    public function destroy(FenologiaEtapa $fenologiaEtapa)
    {
        $cultivoId = $fenologiaEtapa->cultivo_id;
        $fenologiaEtapa->delete();

        return redirect()->route('cultivos.show', $cultivoId)
            ->with('success', 'Etapa eliminada correctamente.');
    }
}