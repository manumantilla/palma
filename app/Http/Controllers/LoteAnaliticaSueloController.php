<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\LoteAnaliticaSuelo;
use Illuminate\Http\Request;

class LoteAnaliticaSueloController extends Controller
{
    public function store(Request $request, Lote $lote)
    {
        $validated = $request->validate([
            'fecha_muestreo'                      => 'required|date|before_or_equal:today',
            'numero_laboratorio_ticket'           => 'nullable|string|max:50',
            'ph'                                  => 'required|numeric|min:0|max:14',
            'conductividad_electrica_ds_m'        => 'nullable|numeric|min:0',
            'materia_organica_porcentaje'         => 'nullable|numeric|min:0|max:100',
            'capacidad_intercambio_cationico_meq' => 'nullable|numeric|min:0',
            'textura_predominante'                => 'required|in:arenoso,arenoso_franco,franco_arenoso,franco,limoso,franco_limoso,franco_arcilloso_arenoso,franco_arcilloso_limoso,franco_arcilloso,arcilloso_arenoso,arcilloso_limoso,arcilloso',
            'porcentaje_arena'                    => 'nullable|numeric|min:0|max:100',
            'porcentaje_limo'                     => 'nullable|numeric|min:0|max:100',
            'porcentaje_arcilla'                  => 'nullable|numeric|min:0|max:100',
        ]);

        $validated['analista_user_id'] = auth()->id();

        // El modelo ejecutará de forma automática el update o inserción respetando el index unique ['lote_id', 'fecha_muestreo']
        $lote->analiticasSuelo()->create($validated);

        // Opcional y recomendado: Actualizamos el pH espejo en la tabla lote para consultas rápidas
        $lote->update(['ph_suelo' => $validated['ph']]);

        return redirect()->back()->with('success', 'Registro analítico de suelo incorporado al histórico del lote.');
    }
}