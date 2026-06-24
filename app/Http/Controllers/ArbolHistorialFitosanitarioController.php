<?php

namespace App\Http\Controllers;

use App\Models\Arbol;
use Illuminate\Http\Request;

class ArbolHistorialFitosanitarioController extends Controller
{
    public function store(Request $request, Arbol $arbol)
    {
        $validated = $request->validate([
            'fecha_hallazgo'                => 'required|date|before_or_equal:today',
            'tipo_incidencia'               => 'required|in:plaga,enfermedad,deficiencia_nutricional,dano_mecanico',
            'agente_patogeno_nombre'         => 'required|string|max:150',
            'severidad_afectacion'          => 'required|in:leve,moderada,critica_cuarentena',
            'descripcion_sintomas'          => 'required|string',
            'evidencia_fotografica_url'     => 'nullable|url',
            'requiere_intervencion_quimica' => 'required|boolean',
        ]);

        $validated['usuario_evaluador_id'] = auth()->id() ?? 1; // Respaldo del evaluador autenticado

        $incidente = $arbol->historialFitosanitario()->create($validated);

        // Si la severidad es crítica, el árbol cambia su estado vital inmediatamente para alertar en los mapas
        if ($validated['severidad_afectacion'] === 'critica_cuarentena') {
            $arbol->update(['estado_vital' => 'enfermo_critico']);
        }

        return response()->json(['success' => true, 'incidente' => $incidente], 201);
    }
}