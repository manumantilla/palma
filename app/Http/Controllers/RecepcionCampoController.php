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

    public function create(Request $request, $sesion_cosecha_id)
    {
        // 1. Validamos que la sesión de cosecha exista y esté activa antes de permitir registrar datos
        $sesion = SesionCosecha::with(['lote', 'cicloProductivo'])->findOrFail($sesion_cosecha_id);
        $trabajadores = Trabajador::where('activo', true)->get();

        // 2. Aquí cargarías la información necesaria para los selectores del frontend (UI local/caché)
        // Por ejemplo: Trabajadores activos, zonas del lote de la sesión, etc.
        return response()->json([
            'status' => 'success',
            'sesion_cosecha' => $sesion,
            'mensaje' => 'Sesión lista para captura de pesajes offline.'
        ]);
    }

    /**
     * Sincroniza o almacena las recepciones creadas en el cliente offline.
     * Soporta tanto la creación unitaria como el envío masivo al recuperar señal.
     */
    public function store(Request $request)
    {
        // Soportamos que nos envíen una sola recepción o un array de ellas (Bulk Sync)
        $isBatch = $request->has('recepciones') && is_array($request->input('recepciones'));
        $dataToValidate = $isBatch ? $request->input('recepciones') : [$request->all()];

        $validatedData = [];
        $errors = [];

        // Validamos cada registro individualmente
        foreach ($dataToValidate as $index => $item) {
            $validator = Validator::make($item, [
                'id' => 'required|uuid', // El UUID DEBE venir generado por el cliente offline
                'sesion_cosecha_id' => 'required|exists:sesiones_cosecha,id',
                'lote_zona_id' => 'nullable|exists:lotes_zonas_manejo,id',
                'trabajador_id' => 'required|exists:trabajadores,id',
                'arbol_id' => 'nullable|exists:arboles,id',
                'peso_bruto' => 'required|numeric|min:0.01',
                'tara_costal' => 'required|numeric|min:0',
                'peso_neto' => 'required|numeric|min:0', // Calculado en front, verificado aquí
                'hora_pesaje' => 'required|date_format:Y-m-d H:i:s',
                'costal_codigo' => 'nullable|string|max:100',
                'numero_corte' => 'nullable|integer|min:1',
                'client_updated_at' => 'required|date_format:Y-m-d H:i:s',
            ]);

            if ($validator->fails()) {
                $errors[$index] = $validator->errors();
                continue;
            }

            // Validación lógica de negocio: Peso Neto Real
            $datos = $validator->validated();
            $pesoNetoReal = $datos['peso_bruto'] - $datos['tara_costal'];
            
            // Tolerancia pequeña por redondeos en JS del celular/dispositivo (ej: 0.05 kg)
            if (abs($datos['peso_neto'] - $pesoNetoReal) > 0.05) {
                $errors[$index] = ["peso_neto" => ["El peso neto enviado ({$datos['peso_neto']}) no coincide con el cálculo bruto - tara."]];
                continue;
            }

            $validatedData[] = $datos;
        }

        if (!empty($errors)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Errores de validación en los datos enviados.',
                'errors' => $errors
            ], 422);
        }

        // Procesamos la inserción/actualización idempotente mediante una transacción
        DB::beginTransaction();
        try {
            $sincronizadosIds = [];

            foreach ($validatedData as $item) {
                // Usamos updateOrCreate para que sea idempotente (si la sincronización falla a mitad de camino y reintentan)
                $recepcion = RecepcionCampo::updateOrCreate(
                    ['id' => $item['id']], // Busca por el UUID del cliente
                    [
                        'sesion_cosecha_id' => $item['sesion_cosecha_id'],
                        'lote_zona_id'      => $item['lote_zona_id'],
                        'trabajador_id'     => $item['trabajador_id'],
                        'arbol_id'          => $item['arbol_id'],
                        'peso_bruto'        => $item['peso_bruto'],
                        'tara_costal'       => $item['tara_costal'],
                        'peso_neto'         => $item['peso_neto'],
                        'hora_pesaje'       => $item['hora_pesaje'],
                        'costal_codigo'     => $item['costal_codigo'],
                        'numero_corte'      => $item['numero_corte'],
                        'client_updated_at' => $item['client_updated_at'],
                        'synced_at'         => now(), // Marcamos la hora exacta de llegada al servidor
                    ]
                );

                $sincronizadosIds[] = $recepcion->id;
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Sincronización exitosa.',
                'synced_ids' => $sincronizadosIds
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'critical_error',
                'message' => 'Error al guardar los registros en el servidor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}