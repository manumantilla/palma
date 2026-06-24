<?php

namespace App\Http\Controllers;

use App\Models\Arbol;
use App\Models\Lote;
use App\Models\LoteZonaManejo;
use App\Models\CicloProductivo;
use App\Models\ArbolMetricaHistorica;
use App\Models\ArbolHistorialFitosanitario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Exception;

class ArbolController extends Controller
{
    /**
     * Muestra el catálogo de árboles con buscador avanzado y filtros indexados.
     */
    public function index(Request $request)
    {
        // 1. Iniciamos la consulta con Eager Loading para evitar el problema de queries N+1
        $query = Arbol::with(['lote', 'zonaManejo', 'cicloProductivo']);

        // 2. Filtro por búsqueda de texto (Código Único del Árbol)
        if ($request->filled('search')) {
            $query->where('codigo_unico', 'ILIKE', '%' . $request->search . '%'); // ILIKE para Postgres (Insensible a mayúsculas)
        }

        // 3. Filtros Estructurales y Catastrales
        if ($request->filled('lote_id')) {
            $query->where('lote_id', $request->lote_id);
        }
        if ($request->filled('lote_zona_manejo_id')) {
            $query->where('lote_zona_manejo_id', $request->lote_zona_manejo_id);
        }

        // 4. Filtros de Estado y Agronómicos
        if ($request->filled('estado_vital')) {
            $query->where('estado_vital', $request->estado_vital);
        }
        if ($request->filled('etapa_biologica')) {
            $query->where('etapa_biologica', $request->etapa_biologica);
        }
        if ($request->filled('variedad')) {
            $query->where('variedad', $request->variedad);
        }

        // 5. Paginación estricta debido al alto volumen de datos
        $arboles = $query->orderBy('fila_indice')
                         ->orderBy('posicion_indice')
                         ->paginate(100) 
                         ->withQueryString(); // Mantiene los filtros activos al cambiar de página

        // Datos complementarios para llenar los selectores de los filtros en la vista
        $lotes = Lote::where('activo', true)->orderBy('nombre_lote')->get();
        $zonas = LoteZonaManejo::orderBy('nombre_zona')->get();

        return view('arboles.index', compact('arboles', 'lotes', 'zonas'));
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        $lotes = Lote::where('activo', true)->get();
        $zonas = LoteZonaManejo::all();
        $ciclos = CicloProductivo::orderBy('id', 'desc')->get(); // Ajustar según tu lógica de ciclos

        return view('arboles.create', compact('lotes', 'zonas', 'ciclos'));
    }

    /**
     * Almacena un árbol con validación PostGIS.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ciclo_productivo_id'      => 'required|exists:ciclos_productivos,id',
            'lote_id'                  => 'required|exists:lotes,id',
            'lote_zona_manejo_id'      => 'nullable|exists:lotes_zonas_manejo,id',
            'codigo_unico'             => 'required|string|unique:arboles,codigo_unico',
            'fila_indice'              => 'required|integer|min:0',
            'posicion_indice'          => 'required|integer|min:0',
            'altitud'                  => 'nullable|numeric',
            'estado_vital'             => 'required|in:excelente,con_estres,enfermo_critico,muerto,erradicado',
            'etapa_biologica'          => 'required|in:vivero,establecimiento,desarrollo_inmaduro,produccion_madura,senescencia',
            'fecha_siembra'            => 'nullable|date',
            'variedad'                 => 'nullable|string|max:100',
            'latitude'                 => 'nullable|numeric|between:-90,90',
            'longitude'                => 'nullable|numeric|between:-180,180',
            'altitud_ortometrica_msnm' => 'nullable|numeric',
            'observaciones'            => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $arbol = new Arbol($validated);

            // Procesar punto geométrico PostGIS SRID 4326 (WGS 84)
            if ($request->filled('latitude') && $request->filled('longitude')) {
                $lat = $validated['latitude'];
                $lng = $validated['longitude'];
                // Ojo: En WKT de PostGIS el orden estándar es POINT(Longitud Latitud)
                $arbol->coordenada_precision = DB::raw("ST_GeomFromText('POINT({$lng} {$lat})', 4326)");
            }

            $arbol->save();
            DB::commit();

            return redirect()->route('arboles.index')->with('success', 'Árbol geo-referenciado con éxito.');

        } catch (Exception $e) {
            DB::rollBack();
            // Registramos el error internamente para debug, pero protegemos la UX del usuario
            Log::error('Error registrando árbol: ' . $e->getMessage(), ['request' => $request->all()]);

            return back()->withErrors([
                'error_database' => 'Ocurrió un fallo en el servidor o la coordenada GPS tiene un formato inválido.'
            ])->withInput();
        }
    }

    /**
     * Ficha técnica del Árbol (Muestra analíticas IoT/Drones e historial fitosanitario).
     */
    public function show(Arbol $arbol)
    {
        // Carga relacional con ordenamiento cronológico inverso para el historial
        $arbol->load([
            'lote', 
            'zonaManejo', 
            'metricasHistoricas' => function($q) { $q->orderBy('fecha_medicion', 'desc'); },
            'historialFitosanitario' => function($q) { $q->orderBy('fecha_hallazgo', 'desc'); }
        ]);

        return view('arboles.show', compact('arbol'));
    }

    /**
     * SUB-RECURSO: Registra una métrica alométrica o de teledetección (Drones / NDVI).
     */
    public function storeMetrica(Request $request, Arbol $arbol)
    {
        $validated = $request->validate([
            'fecha_medicion'               => 'required|date',
            'altura_metros'                => 'nullable|numeric|min:0',
            'diametro_tronco_cm'           => 'nullable|numeric|min:0',
            'diametro_copa_proyeccion_m'   => 'nullable|numeric|min:0',
            'volumen_copa_calculado_m3'    => 'nullable|numeric|min:0',
            'indice_ndvi_medido'           => 'nullable|numeric|between:-1,1',
            'indice_ndre_medido'           => 'nullable|numeric|between:-1,1',
            'temperatura_canopia_celsius' => 'nullable|numeric',
            'codigo_escala_bbch'           => 'nullable|integer',
            'origen_datos'                 => 'required|in:manual,dron_lidar,satelite_sentinel,sensor_iot',
        ]);

        try {
            $metrica = new ArbolMetricaHistorica($validated);
            $metrica->arbol_id = $arbol->id;
            $metrica->save();

            return back()->with('success', 'Métrica de precisión añadida al histórico.');

        } catch (Exception $e) {
            Log::error('Error en métrica de árbol ID ' . $arbol->id . ': ' . $e->getMessage());
            return back()->withErrors(['error_metrica' => 'No se pudo guardar la medición alométrica.'])->withInput();
        }
    }

    /**
     * SUB-RECURSO: Registra una incidencia fitosanitaria con carga de evidencia (GlobalG.A.P.).
     */
    public function storeFitosanitario(Request $request, Arbol $arbol)
    {
        $validated = $request->validate([
            'fecha_hallazgo'               => 'required|date|before_or_equal:today',
            'tipo_incidencia'              => 'required|in:plaga,enfermedad,deficiencia_nutricional,dano_mecanico',
            'agente_patogeno_nombre'       => 'required|string|max:150',
            'severidad_afectacion'         => 'required|in:leve,moderada,critica_cuarentena',
            'descripcion_sintomas'         => 'required|string',
            'evidencia_fotografica'        => 'nullable|image|max:4096', // Máx 4MB para fotos de campo
            'requiere_intervencion_quimica'=> 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $historial = new ArbolHistorialFitosanitario($validated);
            $historial->arbol_id = $arbol->id;
            $historial->usuario_evaluador_id = Auth::id();
            $historial->requiere_intervencion_quimica = $request->has('requiere_intervencion_quimica');
            $historial->caso_controlado = false;

            // Manejo seguro del archivo de imagen
            if ($request->hasFile('evidencia_fotografica')) {
                // Se guarda en el disco público dentro de una carpeta estructurada por lote
                $path = $request->file('evidencia_fotografica')->store("evidencias_fitosanitarias/lote_{$arbol->lote_id}", 'public');
                $historial->evidencia_fotografica_url = Storage::url($path);
            }

            $historial->save();

            // Si la afectación es crítica, mutamos el estado vital del árbol automáticamente
            if ($validated['severidad_afectacion'] === 'critica_cuarentena') {
                $arbol->update(['estado_vital' => 'enfermo_critico']);
            }

            DB::commit();
            return back()->with('success', 'Incidencia fitosanitaria registrada. Alerta de sanidad vegetal activa.');

        } catch (Exception $e) {
            DB::rollBack();
            // Si el archivo se subió pero la DB falló, borramos el archivo huérfano para no llenar el disco
            if (isset($path)) { Storage::disk('public')->delete($path); }

            Log::error('Error fitosanitario árbol ID ' . $arbol->id . ': ' . $e->getMessage());
            return back()->withErrors(['error_fito' => 'Fallo al procesar el reporte de sanidad vegetal.'])->withInput();
        }
    }
}