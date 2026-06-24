<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\LoteSistemaRiego;
use Illuminate\Http\Request;

class LoteSistemaRiegoController extends Controller
{
    public function store(Request $request, Lote $lote)
    {
        $validated = $request->validate([
            'nombre_sistema'                => 'required|string|max:100',
            'tipo_riego'                    => 'required|in:goteo,microaspersion,aspersion,pivot_central,gravedad,subterraneo',
            'fuente_agua'                   => 'required|in:acueducto_distrito,pozo_profundo,rio_directo,embalse_almacenamiento,nacimiento',
            'caudal_diseno_litros_segundo'  => 'required|numeric|min:0.01',
            'presion_operacion_psi'         => 'nullable|numeric|min:0',
            'coeficiente_uniformidad'       => 'nullable|numeric|min:0|max:100',
            'espaciamiento_emisores_metros' => 'nullable|numeric|min:0',
            'descarga_emisor_litros_hora'   => 'nullable|numeric|min:0',
        ]);

        $lote->sistemasRiego()->create($validated);

        if (!$lote->tiene_riego_instalado) {
            $lote->update(['tiene_riego_instalado' => true]);
        }

        return redirect()->back()->with('success', 'Infraestructura hidráulica de fertirriego vinculada.');
    }
}