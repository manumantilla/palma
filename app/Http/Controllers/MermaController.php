<?php

namespace App\Http\Controllers;

use App\Models\Merma;
use App\Models\Contenedor;
use App\Models\RecepcionCampo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class MermaController extends Controller
{

    public function index(Request $request)
    {
        try {
            $mermas = Merma::with(['recepcionCampo.trabajador', 'contenedor'])
                ->filtrar($request->all())
                ->orderBy('fecha_registro', 'desc')
                ->paginate(15)
                ->withQueryString();

            return view('mermas.index', compact('mermas'));

        } catch (Exception $e) {
            Log::error("Error en MermaController@index: " . $e->getMessage());
            abort(500, 'Error al cargar el módulo de control de mermas.');
        }
    }

    /**
     * Registrar una merma (Desde mesa de clasificación o desde inventario)
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'recepcion_campo_id' => 'nullable|exists:recepciones_campo,id',
                'contenedor_id' => 'nullable|exists:contenedores,id',
                'fecha_registro' => 'required|date',
                'kilos_merma' => 'required|numeric|min:0.01',
                'motivo' => 'required|in:daño,perdida,robo,deshidratacion,consumo_interno,error,otro',
                'costo_estimado' => 'nullable|numeric|min:0',
                'destino_final' => 'nullable|string|max:100',
                'comentarios' => 'nullable|string'
            ]);

            // Validación de negocio obligatoria: Al menos debe estar amarrado a uno de los dos destinos
            if (empty($data['recepcion_campo_id']) && empty($data['contenedor_id'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Error: La merma debe asociarse a un costal de recepción o a un contenedor específico.');
            }

            // Lógica de impacto si la merma ocurre en un contenedor/tolva ya clasificado
            if (!empty($data['contenedor_id'])) {
                $contenedor = Contenedor::findOrFail($data['contenedor_id']);
                
                if ($contenedor->kilos_acumulados < $data['kilos_merma']) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "No puedes registrar una merma de {$data['kilos_merma']} kg en un contenedor que solo tiene {$contenedor->kilos_acumulados} kg disponibles.");
                }

                // Descontamos físicamente los kilos del contenedor y recalculamos su total automáticamente
                $contenedor->decrement('kilos_acumulados', $data['kilos_merma']);
                $contenedor->decrement('peso_total', $data['kilos_merma']);
            }

            Merma::create($data);

            return redirect()->back()->with('success', 'Merma registrada en el sistema. Inventarios actualizados.');

        } catch (Exception $e) {
            Log::error("Error en MermaController@store: " . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ocurrió un error inesperado al procesar el reporte de merma.');
        }
    }
}