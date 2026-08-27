<?php

namespace App\Http\Controllers;

use App\Models\RecepcionCampo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\SesionCosecha;
use App\Models\Trabajador;
use App\Models\LoteZonaManejo;
use App\Models\Arbol;
use Illuminate\Support\Facades\DB;
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
     * Muestra la vista con todo el paquete de datos necesario para almacenar en LocalStorage/IndexedDB.
     */
    public function prepararOffline($sesion)
    {
        $sesion = SesionCosecha::with(['lote', 'cicloProductivo'])->findOrFail($sesion);

        // Carga de catálogo liviano para trabajo en campo sin internet
        $trabajadores = Trabajador::where('activo', true)
            ->select('id', 'nombre', 'apellido', 'documento_identidad')
            ->get();

        $zonas = LoteZonaManejo::where('lote_id', $sesion->lote_id)
            ->select('id', 'nombre', 'codigo')
            ->get();

        $arboles = Arbol::where('lote_id', $sesion->lote_id)
            ->select('id', 'codigo', 'lote_zona_id')
            ->get();

        return view('cosecha.preparar_offline', compact('sesion', 'trabajadores', 'zonas', 'arboles'));
    }

    /**
     * Procesa la sincronización de recepciones y redirige con mensajes de estado.
     */
    public function store(Request $request)
    {
        $isBatch = $request->has('recepciones') && is_array($request->input('recepciones'));
        $rawCollection = $isBatch ? $request->input('recepciones') : [$request->all()];

        $validatedData = [];
        $errors = [];

        foreach ($rawCollection as $index => $item) {
            $validator = Validator::make($item, [
                'id' => 'required|uuid',
                'sesion_cosecha_id' => 'required|exists:sesiones_cosecha,id',
                'lote_zona_id' => 'nullable|exists:lotes_zonas_manejo,id',
                'trabajador_id' => 'required|exists:trabajadores,id',
                'arbol_id' => 'nullable|exists:arboles,id',
                'peso_bruto' => 'required|numeric|min:0.01',
                'tara_costal' => 'required|numeric|min:0',
                'peso_neto' => 'required|numeric|min:0',
                'peso_merma_campo' => 'nullable|numeric|min:0', // Lógica de mermas agregada
                'hora_pesaje' => 'required|date',
                'costal_codigo' => 'nullable|string|max:100',
                'numero_corte' => 'nullable|integer|min:1',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'foto_base64' => 'nullable|string',
                'client_updated_at' => 'required|date',
            ]);

            if ($validator->fails()) {
                $errors[] = "Fila #" . ($index + 1) . ": " . implode(', ', $validator->errors()->all());
                continue;
            }

            $datos = $validator->validated();

            // Verificación matemática de peso neto
            if (abs($datos['peso_neto'] - ($datos['peso_bruto'] - $datos['tara_costal'])) > 0.05) {
                $errors[] = "Fila #" . ($index + 1) . ": El peso neto no coincide con Bruto - Tara.";
                continue;
            }

            $validatedData[] = $datos;
        }

        if (!empty($errors)) {
            return redirect()->back()
                ->withInput()
                ->with('error_batch', $errors);
        }

        DB::beginTransaction();
        try {
            $procesados = 0;

            foreach ($validatedData as $item) {
                // Procesamiento de foto tomada offline en Base64
                $rutaFoto = null;
                if (!empty($item['foto_base64'])) {
                    $imageName = 'recepcion_' . $item['id'] . '_' . time() . '.jpg';
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $item['foto_base64']));
                    Storage::disk('public')->put('cosechas/' . $imageName, $imageData);
                    $rutaFoto = 'cosechas/' . $imageName;
                }

                // Generación de punto geométrico PostGIS si existen coordenadas
                $geometriaPoint = null;
                if (isset($item['latitude']) && isset($item['longitude'])) {
                    $geometriaPoint = DB::raw("ST_SetSRID(ST_MakePoint({$item['longitude']}, {$item['latitude']}), 4326)");
                }

                // Inserción idempotente
                RecepcionCampo::updateOrCreate(
                    ['id' => $item['id']],
                    [
                        'sesion_cosecha_id' => $item['sesion_cosecha_id'],
                        'lote_zona_id'      => $item['lote_zona_id'],
                        'trabajador_id'     => $item['trabajador_id'],
                        'arbol_id'          => $item['arbol_id'],
                        'peso_bruto'        => $item['peso_bruto'],
                        'tara_costal'       => $item['tara_costal'],
                        'peso_neto'         => $item['peso_neto'],
                        'peso_merma_campo'  => $item['peso_merma_campo'] ?? 0,
                        'hora_pesaje'       => $item['hora_pesaje'],
                        'costal_codigo'     => $item['costal_codigo'],
                        'numero_corte'      => $item['numero_corte'],
                        'foto_evidencia'    => $rutaFoto ?? DB::raw('foto_evidencia'),
                        'ubicacion_gps'     => $geometriaPoint,
                        'client_updated_at' => $item['client_updated_at'],
                        'synced_at'         => now(),
                    ]
                );
                $procesados++;
            }

            DB::commit();

            return redirect()->route('cosecha.preparar_offline', $validatedData[0]['sesion_cosecha_id'])
                ->with('success', "¡Sincronización exitosa! Se guardaron {$procesados} pesajes correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error grave al guardar en servidor: ' . $e->getMessage());
        }
    }
}