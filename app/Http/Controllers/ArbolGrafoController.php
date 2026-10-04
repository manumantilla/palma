<?php
namespace App\Http\Controllers;

use App\Models\Arbol;
use Illuminate\Http\Request;
use App\Models\CicloProductivo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
//logs
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;

class ArbolGrafoController extends Controller
{
    public function dashboard()
    {
        $ciclos = CicloProductivo::select('id', 'nombre_campana')->get();
        $arboles = Arbol::select('id', 'codigo_unico', 'ciclo_productivo_id')
            ->whereNull('deleted_at')
            ->limit(100)
            ->get();

        return view('arboles.grafo_dashboard', compact('ciclos', 'arboles'));
    }

public function simular(Request $request)
{
    $request->validate([
        'ciclo_productivo_id' => 'required|integer',
        'arbol_origen_id'     => 'required|integer',
        'max_distancia_m'     => 'required|numeric|min:1|max:200',
    ]);

    $pythonUrl = rtrim(
        config('services.python.url', 'http://python_service:8000'),
        '/'
    );

    Log::info('SIMULACION INICIADA', [
        'python_url' => $pythonUrl,
        'payload' => $request->all(),
    ]);

    try {

        Log::info('ANTES DE LLAMAR PYTHON');

        $response = Http::timeout(15)
            ->acceptJson()
            ->post("{$pythonUrl}/api/v1/graphs/simulate-contagion", [
                'ciclo_productivo_id' => (int) $request->ciclo_productivo_id,
                'arbol_origen_id'     => (int) $request->arbol_origen_id,
                'max_distancia_m'     => (float) $request->max_distancia_m,
            ]);

        Log::info('PYTHON RESPONDIO', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

    } catch (ConnectionException $e) {

        Log::error('PYTHON INACCESIBLE', [
            'url' => $pythonUrl,
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'error' => 'No se pudo conectar con Python.',
            'details' => $e->getMessage(),
        ], 503);

    } catch (\Throwable $e) {

        Log::error('ERROR SIMULACION', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'Error inesperado en el servidor.',
            'details' => $e->getMessage(),
        ], 500);
    }

    if ($response->failed()) {

        Log::error('PYTHON DEVOLVIO ERROR', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return response()->json([
            'error' => 'El motor de simulación devolvió un error.',
            'status' => $response->status(),
            'details' => $response->json() ?? $response->body(),
        ], $response->status());
    }

    return response()->json($response->json());
}
    /**
     * Vista principal del gemelo virtual (mapa + grafo).
     */
    public function index()
    {
        return view('grafo.index');
    }

    /**
     * Endpoint: nodos del grafo dentro del viewport actual.
     * Modo 1: grafo completo (<=10.000 árboles, sin clustering).
     *
     * GET /api/grafo/nodos?lote_id=3&min_lng=..&min_lat=..&max_lng=..&max_lat=..
     */
    public function nodos(Request $request)
    {
        $request->validate([
            'lote_id'  => 'nullable|integer',
            'min_lng'  => 'required|numeric',
            'min_lat'  => 'required|numeric',
            'max_lng'  => 'required|numeric',
            'max_lat'  => 'required|numeric',
        ]);

        $query = DB::table('arboles')
            ->select([
                'id',
                'lote_id',
                'lote_zona_manejo_id',
                'fila_indice',
                'posicion_indice',
                'estado_vital',
                'etapa_biologica',
                'produccion_acumulada_kg',
                'variedad',
                DB::raw('ST_X(coordenada_precision) as lng'),
                DB::raw('ST_Y(coordenada_precision) as lat'),
            ])
            ->whereRaw(
                'ST_Within(coordenada_precision, ST_MakeEnvelope(?, ?, ?, ?, 4326))',
                [$request->min_lng, $request->min_lat, $request->max_lng, $request->max_lat]
            );

        if ($request->filled('lote_id')) {
            $query->where('lote_id', $request->lote_id);
        }

        $total = $query->count();

        // Si supera el umbral, devolver flag para activar Modo 2 (clustering)
        if ($total > 10000) {
            return response()->json([
                'modo' => 'jerarquico',
                'total' => $total,
                'mensaje' => 'Demasiados nodos para grafo completo. Usar /api/grafo/clusters',
            ], 200);
        }

        $arboles = $query->get();

        return response()->json([
            'modo' => 'completo',
            'total' => $total,
            'nodos' => $arboles->map(fn($a) => $this->arbolToCytoscapeNode($a)),
            'edges' => $this->generarEdgesEspaciales($arboles),
        ]);
    }

    /**
     * Genera edges entre árboles vecinos según fila/posición (estructura de plantación).
     * Conecta cada árbol con su vecino inmediato en fila y columna.
     */
    private function generarEdgesEspaciales($arboles)
    {
        $edges = [];

        // Indexar por lote + fila + posición para búsqueda O(1)
        $index = [];
        foreach ($arboles as $a) {
            $index["{$a->lote_id}_{$a->fila_indice}_{$a->posicion_indice}"] = $a->id;
        }

        foreach ($arboles as $a) {
            // Vecino siguiente en la misma fila
            $vecinoFila = $index["{$a->lote_id}_{$a->fila_indice}_" . ($a->posicion_indice + 1)] ?? null;
            if ($vecinoFila) {
                $edges[] = [
                    'data' => [
                        'id' => "e_{$a->id}_{$vecinoFila}",
                        'source' => (string) $a->id,
                        'target' => (string) $vecinoFila,
                        'tipo' => 'fila',
                    ],
                ];
            }

            // Vecino siguiente en la fila adyacente (misma posición)
            $vecinoColumna = $index["{$a->lote_id}_" . ($a->fila_indice + 1) . "_{$a->posicion_indice}"] ?? null;
            if ($vecinoColumna) {
                $edges[] = [
                    'data' => [
                        'id' => "e_{$a->id}_{$vecinoColumna}",
                        'source' => (string) $a->id,
                        'target' => (string) $vecinoColumna,
                        'tipo' => 'columna',
                    ],
                ];
            }
        }

        return $edges;
    }

    /**
     * Convierte un árbol a formato nodo Cytoscape.js con posición geográfica.
     */
    private function arbolToCytoscapeNode($arbol)
    {
        return [
            'data' => [
                'id' => (string) $arbol->id,
                'lote_id' => $arbol->lote_id,
                'zona_id' => $arbol->lote_zona_manejo_id,
                'estado_vital' => $arbol->estado_vital,
                'etapa_biologica' => $arbol->etapa_biologica,
                'produccion' => $arbol->produccion_acumulada_kg,
                'variedad' => $arbol->variedad,
                'lat' => (float) $arbol->lat,
                'lng' => (float) $arbol->lng,
            ],
            // Posición inicial en el plano cytoscape (será recalculada
            // por la extensión de geo-mapping en el frontend)
            'position' => [
                'x' => $arbol->lng,
                'y' => -$arbol->lat,
            ],
        ];
    }

    /**
     * Modo 2: Clustering espacial para >10.000 árboles.
     * Agrupa por celdas (geohash/grid) y devuelve supernodos.
     *
     * GET /api/grafo/clusters?lote_id=3&min_lng=..&min_lat=..&max_lng=..&max_lat=..&precision=6
     */
    public function clusters(Request $request)
    {
        $request->validate([
            'lote_id'   => 'nullable|integer',
            'min_lng'   => 'required|numeric',
            'min_lat'   => 'required|numeric',
            'max_lng'   => 'required|numeric',
            'max_lat'   => 'required|numeric',
            'precision' => 'nullable|integer|min:3|max:9',
        ]);

        $precision = $request->input('precision', 6); // ~tamaño de celda

        $query = DB::table('arboles')
            ->select([
                DB::raw('ST_SnapToGrid(coordenada_precision, ?) as celda', [pow(10, -$precision)]),
                DB::raw('COUNT(*) as total_arboles'),
                DB::raw('AVG(produccion_acumulada_kg) as produccion_promedio'),
                DB::raw("SUM(CASE WHEN estado_vital != 'sano' THEN 1 ELSE 0 END) as total_alertas"),
                DB::raw('ST_X(ST_Centroid(ST_Collect(coordenada_precision))) as centro_lng'),
                DB::raw('ST_Y(ST_Centroid(ST_Collect(coordenada_precision))) as centro_lat'),
            ])
            ->whereRaw(
                'ST_Within(coordenada_precision, ST_MakeEnvelope(?, ?, ?, ?, 4326))',
                [$request->min_lng, $request->min_lat, $request->max_lng, $request->max_lat]
            )
            ->groupBy('celda');

        if ($request->filled('lote_id')) {
            $query->where('lote_id', $request->lote_id);
        }

        $clusters = $query->get();

        return response()->json([
            'modo' => 'jerarquico',
            'precision' => $precision,
            'clusters' => $clusters->map(fn($c, $i) => [
                'data' => [
                    'id' => "cluster_{$i}",
                    'tipo' => 'supernodo',
                    'total_arboles' => $c->total_alertas !== null ? (int) $c->total_arboles : 0,
                    'produccion_promedio' => round((float) $c->produccion_promedio, 2),
                    'total_alertas' => (int) $c->total_alertas,
                    'lat' => (float) $c->centro_lat,
                    'lng' => (float) $c->centro_lng,
                ],
                'position' => [
                    'x' => (float) $c->centro_lng,
                    'y' => -(float) $c->centro_lat,
                ],
            ]),
        ]);
    }

    /**
     * Detalle de un árbol individual (al hacer click en un nodo).
     * GET /api/grafo/arboles/{id}
     */
    public function detalle($id)
    {
        $arbol = DB::table('arboles')
            ->select([
                'id', 'lote_id', 'lote_zona_manejo_id', 'fila_indice', 'posicion_indice',
                'fecha_siembra', 'fecha_primera_cosecha', 'estado_vital', 'etapa_biologica',
                'produccion_acumulada_kg', 'ciclos_productivos_count', 'variedad', 'altitud',
                DB::raw('ST_X(coordenada_precision) as lng'),
                DB::raw('ST_Y(coordenada_precision) as lat'),
            ])
            ->where('id', $id)
            ->first();

        if (!$arbol) {
            return response()->json(['error' => 'Árbol no encontrado'], 404);
        }

        // Última métrica histórica (NDVI, altura, etc.)
        $ultimaMetrica = DB::table('arboles_metricas_historicas')
            ->where('arbol_id', $id)
            ->orderByDesc('created_at')
            ->first();

        // Últimas incidencias fitosanitarias
        $incidencias = DB::table('arboles_historial_fitosanitario')
            ->where('arbol_id', $id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return response()->json([
            'arbol' => $arbol,
            'ultima_metrica' => $ultimaMetrica,
            'incidencias_recientes' => $incidencias,
        ]);
    }

    // app/Http/Controllers/ArbolGrafoController.php

/**
 * Devuelve el centro y bounding box de todos los árboles
 * (o de un lote específico) para centrar el mapa al cargar.
 *
 * GET /api/grafo/extent?lote_id=3
 */
    public function extent(Request $request)
    {
        $request->validate([
            'lote_id' => 'nullable|integer',
        ]);

        $query = DB::table('arboles');

        if ($request->filled('lote_id')) {
            $query->where('lote_id', $request->lote_id);
        }

        $result = $query->select([
            DB::raw('ST_XMin(ST_Extent(coordenada_precision)) as min_lng'),
            DB::raw('ST_YMin(ST_Extent(coordenada_precision)) as min_lat'),
            DB::raw('ST_XMax(ST_Extent(coordenada_precision)) as max_lng'),
            DB::raw('ST_YMax(ST_Extent(coordenada_precision)) as max_lat'),
        ])->first();

        if (!$result || $result->min_lng === null) {
            return response()->json(['error' => 'No hay árboles registrados'], 404);
        }

        return response()->json([
            'min_lng' => (float) $result->min_lng,
            'min_lat' => (float) $result->min_lat,
            'max_lng' => (float) $result->max_lng,
            'max_lat' => (float) $result->max_lat,
            'centro' => [
                'lat' => ((float) $result->min_lat + (float) $result->max_lat) / 2,
                'lng' => ((float) $result->min_lng + (float) $result->max_lng) / 2,
            ],
        ]);
    }
}