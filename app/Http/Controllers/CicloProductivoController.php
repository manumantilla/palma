<?php

namespace App\Http\Controllers;

use App\Models\CicloProductivo;
use App\Models\Lote;
use App\Models\Cultivo;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
class CicloProductivoController extends Controller
{
    /**
     * Despliega el listado aplicando filtros avanzados de ingeniería agronómica
     */
    public function index(Request $request)
    {
        // Iniciamos la consulta base cargando relaciones estructurales
        $query = CicloProductivo::with(['lote', 'cultivo', 'agronomo']);

        // 1. Filtro por Finca (a través de la relación de Lotes)
        if ($request->filled('finca_id')) {
            $query->whereHas('lote', function ($q) use ($request) {
                $q->where('finca_id', $request->finca_id);
            });
        }

        // 2. Filtro por Lote específico
        if ($request->filled('lote_id')) {
            $query->where('lote_id', $request->lote_id);
        }

        // 3. Filtro por Cultivo / Variedad
        if ($request->filled('cultivo_id')) {
            $query->where('cultivo_id', $request->cultivo_id);
        }

        // 4. Filtro por Estado Fenológico u Operativo
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // 5. Filtro por Tipo de Longevidad (Perenne o Transitorio)
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // 6. Alerta de Certificación Orgánica
        if ($request->has('organico') && $request->organico == '1') {
            $query->where('es_organico_certificado', true);
        }

        // 7. Rango de Fechas: Ventana estimada de inicio de cosechas (Mesa de Control Logística)
        if ($request->filled('cosecha_desde') && $request->filled('cosecha_hasta')) {
            $query->whereBetween('fecha_estimada_cosecha', [
                $request->cosecha_desde, 
                $request->cosecha_hasta
            ]);
        }

        // Ejecutamos la paginación para mantener alta velocidad de carga en bases de datos masivas
        $ciclos = $query->orderBy('fecha_inicio', 'desc')->paginate(20);

        // Data auxiliar para alimentar los dropdowns de los filtros en la vista
        $lotes = Lote::where('activo', true)->orderBy('nombre_lote')->get();
        $cultivos = Cultivo::orderBy('nombre_cultivo')->get(); // Suponiendo tabla de catálogos nativa

        return view('ciclos_productivos.index', compact('ciclos', 'lotes', 'cultivos'));
    }

    public function create()
    {
        $lotes = Lote::where('activo', true)->get();
        $cultivos = Cultivo::all();
        $proveedores = Proveedor::all();
        $agronomos = User::all(); // O la lógica de roles que uses

        return view('ciclos_productivos.create', compact('lotes', 'cultivos', 'proveedores', 'agronomos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lote_id'                             => 'required|exists:lotes,id',
            'cultivo_id'                          => 'required|exists:cultivos,id',
            'estado'                              => 'nullable|in:preparacion_suelo,siembra_establecimiento,desarrollo_vegetativo,floracion_llenado,cosecha_activa,receso_invernal_poda,concluido,siniestrado_perdida',
            'tipo'                                => 'required|in:perenne,transitorio',
            'nombre_campana'                      => 'required|string|max:150',
            'fecha_inicio'                        => 'required|date',
            'fecha_estimada_cosecha'              => 'required|date|after_or_equal:fecha_inicio',
            'fecha_estimada_fin_cosecha'          => 'required|date|after_or_equal:fecha_estimada_cosecha',
            'modalidad_siembra'                   => 'required|in:semilla_directa,plantula_vivero,estaca_esqueje,arbol_injertado',
            'distancia_entre_hileras_metros'      => 'nullable|numeric|min:0.05',
            'distancia_entre_plantas_metros'      => 'nullable|numeric|min:0.05',
            'proveedor_material_vegetal_id'       => 'nullable|exists:proveedores,id',
            'codigo_lote_vivero_origen'           => 'nullable|string|max:100',
            'registro_autorizacion_institucional' => 'nullable|string|max:100',
            'es_organico_certificado'             => 'nullable|boolean',
            'agronomo_responsable_id'             => 'nullable|exists:users,id',
        ]);

        // Forzar el booleano por si el request envía texto o checkbox vacío
        $validated['es_organico_certificado'] = $request->has('es_organico_certificado');

        try {
            DB::beginTransaction();

            // Los campos monetarios nacen en cero y las plantas_por_hectarea_real se autocalculan en el modelo
            CicloProductivo::create($validated);

            DB::commit();

            return redirect()->route('ciclos-productivos.index')
                ->with('success', 'Campaña y ciclo productivo aperturado con éxito.');

        } catch (Exception $e) {
            DB::rollBack();
            
            // Reportamos el error exacto en los logs internos de Laravel
            Log::error('Fallo al aperturar ciclo productivo: ' . $e->getMessage(), [
                'input' => $request->except(['_token'])
            ]);

            return back()
                ->withErrors(['database_error' => 'Ocurrió un error interno en el servidor. No se pudo guardar el ciclo productivo.'])
                ->withInput();
        }
    }

    public function obtenerGrafoYEstadisticas(CicloProductivo $cicloProductivo): JsonResponse
    {
        // 1. Obtener árboles con datos espaciales (ST_X/ST_Y) en una sola consulta
        $arboles = DB::table('arboles')
            ->selectRaw('
                id, 
                codigo_unico, 
                estado_vital, 
                etapa_biologica,
                fecha_siembra, 
                produccion_acumulada_kg,
                ST_Y(coordenada_precision) as lat,
                ST_X(coordenada_precision) as lng
            ')
            ->where('ciclo_productivo_id', $cicloProductivo->id)
            ->whereNull('deleted_at')
            ->get();

        if ($arboles->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'El ciclo productivo no tiene árboles asignados.',
                'total_arboles' => 0
            ], 404);
        }

        $arbolesIds = $arboles->pluck('id');

        // 2. Extraer las aristas (red de vecindad) para Dijkstra / Grafos
        $aristasGrafo = DB::table('arboles_red_vecindad')
            ->whereIn('arbol_origen_id', $arbolesIds)
            ->select(
                'arbol_origen_id as origen',
                'arbol_destino_id as destino',
                'distancia_metros as peso_distancia',
                'probabilidad_contagio_base as peso_contagio',
                'tipo_contacto'
            )
            ->get();

        // 3. Formatear nodos para Leaflet y Visualización de Grafos
        $nodosLeaflet = $arboles->map(function ($arbol) {
            return [
                'id' => $arbol->id,
                'codigo' => $arbol->codigo_unico,
                'lat' => (float) $arbol->lat,
                'lng' => (float) $arbol->lng,
                'estado_vital' => $arbol->estado_vital,
                'produccion_kg' => (float) $arbol->produccion_acumulada_kg,
            ];
        });

        // 4. Métricas y Estadísticas integradas
        $conteoEstados = $arboles->groupBy('estado_vital')->map->count();
        $totalArboles = $arboles->count();
        
        $estadisticas = [
            'total_arboles' => $totalArboles,
            'produccion_total_kg' => round($arboles->sum('produccion_acumulada_kg'), 2),
            'produccion_promedio_kg' => round($arboles->avg('produccion_acumulada_kg'), 2),
            'desglose_estado_vital' => $conteoEstados,
            'porcentaje_saludables' => round((($conteoEstados->get('excelente', 0) / $totalArboles) * 100), 2),
            'total_conexiones_red' => $aristasGrafo->count(),
        ];

        return response()->json([
            'success' => true,
            'ciclo_id' => $cicloProductivo->id,
            'estadisticas' => $estadisticas,
            'leaflet' => [
                'marcadores' => $nodosLeaflet,
            ],
            'grafo_dijkstra' => [
                'nodos' => $arbolesIds->values(),
                'aristas' => $aristasGrafo,
            ],
        ]);
    }

    public function vistaMapa(CicloProductivo $cicloProductivo)
    {
        return view('ciclos_productivos.grafo', compact('cicloProductivo'));
    }
    public function show(CicloProductivo $cicloProductivo)
    {
        $cicloProductivo->load([
            'lote',
            'cultivo',
            'proveedorMaterial',
            'agronomo',
        ]);
 
        // Conteo real de árboles asociados al lote (si la tabla arboles existe)
        $totalArboles = DB::table('arboles')
            ->where('lote_id', $cicloProductivo->lote_id)
            ->count();
 
        $arbolesPorEstado = DB::table('arboles')
            ->where('lote_id', $cicloProductivo->lote_id)
            ->select('estado_vital', DB::raw('count(*) as total'))
            ->groupBy('estado_vital')
            ->pluck('total', 'estado_vital');
 
        // Área del lote en hectáreas (si el lote tiene geometría con área calculable)
        $areaHectareas = DB::table('lotes')
            ->where('id', $cicloProductivo->lote_id)
            ->select(DB::raw('ST_Area(geometria_gps::geography) / 10000 as hectareas'))
            ->value('hectareas');
 
        return view('ciclos_productivos.show', [
            'cicloProductivo' => $cicloProductivo,
            'totalArboles' => $totalArboles,
            'arbolesPorEstado' => $arbolesPorEstado,
            'areaHectareas' => $areaHectareas ? round($areaHectareas, 2) : null,
        ]);
}

    public function update(Request $request, CicloProductivo $cicloProductivo)
    {
        $validated = $request->validate([
            'estado'                     => 'required|in:preparacion_suelo,siembra_establecimiento,desarrollo_vegetativo,floracion_llenado,cosecha_activa,receso_invernal_poda,concluido,siniestrado_perdida',
            'fecha_real_inicio_cosecha'  => 'nullable|date',
            'fecha_real_fin_cosecha'      => 'nullable|date|after_or_equal:fecha_real_inicio_cosecha',
            'fecha_finalizacion_ciclo'   => 'nullable|date|after_or_equal:fecha_inicio',
            'agronomo_responsable_id'     => 'nullable|exists:users,id',
        ]);

        $cicloProductivo->update($validated);

        return redirect()->route('ciclos.index')->with('success', 'Fase operativa del ciclo actualizada.');
    }
}