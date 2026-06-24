<?php

namespace App\Http\Controllers;

use App\Models\Arbol;
use Illuminate\Http\Request;

class ArbolMetricasHistoricasController extends Controller
{
    public function store(Request $request, Arbol $arbol)
    {
        $validated = $request->validate([
            'fecha_medicion'               => 'required|date_format:Y-m-d H:i:s',
            'altura_metros'                => 'nullable|numeric|min:0',
            'diametro_tronco_cm'           => 'nullable|numeric|min:0',
            'diametro_copa_proyeccion_m'   => 'nullable|numeric|min:0',
            'volumen_copa_calculado_m3'    => 'nullable|numeric|min:0',
            'indice_ndvi_medido'           => 'nullable|numeric|between:-1,1',
            'indice_ndre_medido'           => 'nullable|numeric|between:-1,1',
            'temperatura_canopia_celsius'  => 'nullable|numeric',
            'codigo_escala_bbch'           => 'nullable|integer',
            'origen_datos'                 => 'required|in:manual,dron_lidar,satelite_sentinel,sensor_iot',
        ]);

        $metrica = $arbol->metricasHistoricas()->create($validated);

        // Disparador agronómico: Si el NDVI es muy bajo, degradamos automáticamente el estado vital del árbol principal
        if (isset($validated['indice_ndvi_medido']) && $validated['indice_ndvi_medido'] < 0.3) {
            $arbol->update(['estado_vital' => 'con_estres']);
        }

        return response()->json(['success' => true, 'metrica' => $metrica], 201);
    }
}