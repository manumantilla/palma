<?php

namespace App\Http\Controllers;

use App\Models\RecepcionCampo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class RecepcionCampoController extends Controller
{
    /**
     * Listar recepciones con filtros dinámicos (Retorna Vista de Blade)
     */
    public function index(Request $request)
    {
        try {
            $recepciones = RecepcionCampo::with(['sesionCosecha', 'trabajador', 'zonaManejo'])
                ->filtrar($request->all())
                ->orderBy('hora_pesaje', 'desc')
                ->paginate(20)
                ->withQueryString();

            return view('recepciones.index', compact('recepciones'));
            
        } catch (Exception $e) {
            Log::error("Error en RecepcionCampoController@index: " . $e->getMessage());
            abort(500, 'Error al cargar el historial de pesajes en campo.');
        }
    }

    /**
     * Almacenar el costal con el peso neto calculado por el frontend
     */
    public function store(Request $request, Sesion $sesion)
    {
        try {
            $data = $request->validate([
                'sesion_id' => 'required|exists:sesiones_cosecha,id',
                'lote_zona_id' => 'nullable|exists:lotes_zonas_manejo,id',
                'trabajador_id' => 'required|exists:trabajadores,id',
                'arbol_id' => 'nullable|exists:arboles,id',
                'peso_bruto' => 'required|numeric|min:0.1',
                'tara_costal' => 'required|numeric|min:0',
                'peso_neto' => 'required|numeric|min:0.1', // Validamos el valor del frontend
                'costal_codigo' => 'nullable|string|max:50',
                'numero_corte' => 'nullable|integer',
                'foto_evidencia' => 'nullable|string'
            ]);

            RecepcionCampo::create($data);

            return redirect()->back()->with('success', 'Costal de fruta registrado y listo para clasificación.');

        } catch (Exception $e) {
            Log::error("Error en RecepcionCampoController@store: " . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ocurrió un problema al guardar el pesaje en el sistema.');
        }
    }
}