<?php

namespace App\Http\Controllers;

use App\Models\MovimientoClasificacion;
use App\Models\RecepcionCampo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class MovimientoClasificacionController extends Controller
{
    /**
     * Listar movimientos de clasificación (Retorna Vista de Blade)
     */
    public function index(Request $request)
    {
        try {
            $movimientos = MovimientoClasificacion::with(['recepcionCampo', 'contenedor'])
                ->filtrar($request->all())
                ->orderBy('created_at', 'desc')
                ->paginate(20)
                ->withQueryString();

            return view('clasificaciones.index', compact('movimientos'));
            
        } catch (Exception $e) {
            Log::error("Error en MovimientoClasificacionController@index: " . $e->getMessage());
            abort(500, 'Error al cargar los flujos de clasificación.');
        }
    }

    /**
     * Registrar distribución de kilos en contenedores
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'recepcion_campo_id' => 'required|exists:recepciones_campo,id',
                'contenedor_id' => 'required|exists:contenedores,id',
                'kilos_asignados' => 'required|numeric|min:0.01',
                'observaciones' => 'nullable|string'
            ]);

            $recepcion = RecepcionCampo::findOrFail($data['recepcion_campo_id']);

            // Consultar cuánta fruta de este costal ya fue enviada a mesas de clasificación
            $kilosYaClasificados = MovimientoClasificacion::where('recepcion_campo_id', $recepcion->id)
                ->sum('kilos_asignados');
                
            // La capacidad se mide basándose en el neto definitivo guardado
            $capacidadDisponible = $recepcion->peso_neto - $kilosYaClasificados;

            // Alerta si intentan clasificar más fruta de la que físicamente existe en el costal
            if ($data['kilos_asignados'] > $capacidadDisponible) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Operación rechazada: Solo quedan {$capacidadDisponible} kg disponibles para asignar en este costal.");
            }

            MovimientoClasificacion::create($data);

            // Actualización inteligente del flujo de estado
            $totalProcesado = $kilosYaClasificados + $data['kilos_asignados'];
            
            // Tolerancia de 100 gramos por mermas físicas o calibración
            if ($totalProcesado >= ($recepcion->peso_neto - 0.1)) {
                $recepcion->update(['estado_clasificacion' => 'clasificado']);
            } else {
                $recepcion->update(['estado_clasificacion' => 'en_proceso']);
            }

            return redirect()->back()->with('success', 'Kilos asignados al contenedor con éxito.');

        } catch (Exception $e) {
            Log::error("Error en MovimientoClasificacionController@store: " . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'No se pudo procesar el movimiento de la fruta.');
        }
    }
}