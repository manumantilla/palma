<?php

namespace App\Http\Controllers;

use App\Models\Finca;
use App\Models\Lote;
use App\Models\LoteZonaManejo;
use App\Models\LoteAnaliticaSuelo;
use App\Models\LoteSistemaRiego;
use App\Models\Arbol;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoteController extends Controller
{
    /**
     * Listado de todos los lotes.
     */

public function index(Request $request)
{
    $query = Lote::with(['finca', 'ultimaAnaliticaSuelo']);

    // Filtro por finca
    if ($request->filled('finca_id')) {
        $query->where('finca_id', $request->finca_id);
    }

    // Búsqueda por nombre (like)
    if ($request->filled('nombre_lote')) {
        $query->where('nombre_lote', 'like', '%' . $request->nombre_lote . '%');
    }

    // Búsqueda por código (like)
    if ($request->filled('codigo_lote')) {
        $query->where('codigo_lote', 'like', '%' . $request->codigo_lote . '%');
    }

    // Rango de área declarada
    if ($request->filled('area_min')) {
        $query->where('area_hectareas_declaradas', '>=', $request->area_min);
    }
    if ($request->filled('area_max')) {
        $query->where('area_hectareas_declaradas', '<=', $request->area_max);
    }

    // Rango de altitud
    if ($request->filled('altitud_min')) {
        $query->where('altitud_mediana_msnm', '>=', $request->altitud_min);
    }
    if ($request->filled('altitud_max')) {
        $query->where('altitud_mediana_msnm', '<=', $request->altitud_max);
    }

    // Rango de pendiente
    if ($request->filled('pendiente_min')) {
        $query->where('pendiente_promedio_porcentaje', '>=', $request->pendiente_min);
    }
    if ($request->filled('pendiente_max')) {
        $query->where('pendiente_promedio_porcentaje', '<=', $request->pendiente_max);
    }

    // Tipo de suelo (exacto)
    if ($request->filled('tipo_suelo')) {
        $query->where('tipo_suelo', $request->tipo_suelo);
    }

    // Rango de pH
    if ($request->filled('ph_min')) {
        $query->where('ph_suelo', '>=', $request->ph_min);
    }
    if ($request->filled('ph_max')) {
        $query->where('ph_suelo', '<=', $request->ph_max);
    }

    // Tiene riego instalado (boolean)
    if ($request->filled('tiene_riego_instalado')) {
        $query->where('tiene_riego_instalado', $request->tiene_riego_instalado);
    }

    // Fuente de agua (like)
    if ($request->filled('fuente_agua')) {
        $query->where('fuente_agua', 'like', '%' . $request->fuente_agua . '%');
    }

    // Tenencia (exacto)
    if ($request->filled('tenencia')) {
        $query->where('tenencia', $request->tenencia);
    }

    // Activo (boolean)
    if ($request->filled('activo')) {
        $query->where('activo', $request->activo);
    }

    // Ordenar por nombre por defecto
    $query->orderBy('nombre_lote');

    $lotes = $query->paginate(15)->withQueryString();

    // Datos para los selects dinámicos
    $fincas = Finca::orderBy('nombre')->pluck('nombre', 'id');
    $tiposSuelo = Lote::distinct()->pluck('tipo_suelo')->filter()->values();
    $tenencias = Lote::distinct()->pluck('tenencia')->filter()->values();

    return view('lotes_gis.index', compact('lotes', 'fincas', 'tiposSuelo', 'tenencias'));
}

    /**
     * Formulario de creación del Lote Maestro.
     */
    public function create()
    {
        $fincas = Finca::orderBy('nombre')->get(); // Ajusta según tu modelo Finca
        return view('lotes_gis.create', compact('fincas'));
    }

    /**
     * Guarda el Lote Maestro procesando la geometría PostGIS.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'finca_id'                      => 'required|exists:fincas,id',
            'nombre_lote'                   => 'required|string|max:255',
            'codigo_lote'                   => 'required|string|unique:lotes,codigo_lote',
            'area_hectareas_declaradas'     => 'required|numeric|min:0.01',
            'area_hectareas_gis'            => 'nullable|numeric|min:0.01',
            'altitud_mediana_msnm'          => 'required|numeric|min:0',
            'pendiente_promedio_porcentaje' => 'required|numeric|between:0,100',
            'pendiente_terreno'             => 'required|in:plano,ondulado,escarpado,muy_escarpado',
            'tipo_suelo'                    => 'required|in:arenoso,arcilloso,limoso,franco,franco_arenoso,franco_arcilloso',
            'ph_suelo'                      => 'required|numeric|between:0,14',
            'tiene_riego_instalado'         => 'boolean',
            'fuente_agua'                   => 'nullable|required_if:tiene_riego_instalado,1|in:acueducto,pozo,rio,nacimiento,lluvia',
            'tenencia'                      => 'required|in:propio,arrendado,comodato',
            'registro_ica'                  => 'nullable|string|max:100',
            'geometria_gps'                 => 'nullable|string', // Espera un texto WKT estándar de mapas
        ]);

        // Forzar conversión booleana por si el formulario envía texto
        $validated['tiene_riego_instalado'] = $request->has('tiene_riego_instalado');

        DB::transaction(function () use ($validated) {
            // Separamos la geometría para procesarla nativamente con PostGIS
            $wktGeometria = $validated['geometria_gps'] ?? null;
            unset($validated['geometria_gps']);

            $lote = new Lote($validated);

            if ($wktGeometria) {
                // ST_GeomFromText convierte el string WKT en geometría binaria con SRID 4326
                $lote->geometria_gps = DB::raw("ST_GeomFromText('" . addslashes($wktGeometria) . "', 4326)");
            }

            $lote->save();
        });

        return redirect()->route('lotes-gis.index')->with('success', 'Lote registrado con éxito en la base de datos geoespacial.');
    }

    /**
     * Dashboard del Lote: Concentra toda la información y sub-tablas.
     */
    public function show(Lote $loteGis)
    {
        // Traemos el lote con todo su ecosistema técnico para mostrarlo segmentado en la vista
        $loteGis = Lote::select('*')
            ->selectRaw('ST_AsGeoJSON(geometria_gps) as geometria_geojson')
            ->findOrFail($loteGis->id);

        return view('lotes_gis.show', compact('loteGis'));
    }

    /**
     * Sub-recurso: Almacena una nueva Zona de Manejo (Agricultura de Precisión)
     */
    public function storeZona(Request $request, Lote $loteGis)
    {
        $validated = $request->validate([
            'nombre_zona'    => 'required|string|max:100',
            'codigo_zona'    => 'required|string|max:50|unique:lotes_zonas_manejo,codigo_zona',
            'area_hectareas' => 'required|numeric|min:0.0001',
            'geometria_zona' => 'nullable|string', // Texto WKT POLYGON
        ]);

        DB::transaction(function () use ($validated, $loteGis) {
            $wktZona = $validated['geometria_zona'] ?? null;
            unset($validated['geometria_zona']);

            $zona = new LoteZonaManejo($validated);
            $zona->lote_id = $loteGis->id;

            if ($wktZona) {
                $zona->geometria_zona = DB::raw("ST_GeomFromText('" . addslashes($wktZona) . "', 4326)");
            }

            $zona->save();
        });

        return back()->with('success', 'Nueva zona de manejo y segmentación cartográfica guardada.');
    }

    /**
     * Sub-recurso: Almacena una nueva Analítica de Suelo (Historial temporal)
     */
    public function storeAnalitica(Request $request, Lote $loteGis)
    {
        $validated = $request->validate([
            'fecha_muestreo'                     => 'required|date|before_or_equal:today',
            'numero_laboratorio_ticket'          => 'nullable|string|max:50',
            'ph'                                 => 'required|numeric|between:0,14',
            'conductividad_electrica_ds_m'       => 'nullable|numeric|min:0',
            'materia_organica_porcentaje'        => 'nullable|numeric|between:0,100',
            'capacidad_intercambio_cationico_meq' => 'nullable|numeric|min:0',
            'textura_predominante'               => 'required|in:arenoso,arenoso_franco,franco_arenoso,franco,limoso,franco_limoso,franco_arcilloso_arenoso,franco_arcilloso_limoso,franco_arcilloso,arcilloso_arenoso,arcilloso_limoso,arcilloso',
            'porcentaje_arena'                   => 'nullable|numeric|between:0,100',
            'porcentaje_limo'                    => 'nullable|numeric|between:0,100',
            'porcentaje_arcilla'                 => 'nullable|numeric|between:0,100',
        ]);

        // Validación condicional manual para asegurar que los porcentajes físicos sumen 100% si se envían
        if ($request->filled(['porcentaje_arena', 'porcentaje_limo', 'porcentaje_arcilla'])) {
            $suma = $request->porcentaje_arena + $request->porcentaje_limo + $request->porcentaje_arcilla;
            if ($suma != 100) {
                return back()->withErrors(['porcentaje_arena' => 'La suma de los porcentajes de Arena, Limo y Arcilla debe ser exactamente 100%. Actual: ' . $suma . '%'])->withInput();
            }
        }

        // Controlar el índice único compuesto directamente para dar un mensaje amigable
        $existeMuestreo = LoteAnaliticaSuelo::where('lote_id', $loteGis->id)
            ->where('fecha_muestreo', $validated['fecha_muestreo'])
            ->exists();

        if ($existeMuestreo) {
            return back()->withErrors(['fecha_muestreo' => 'Ya existe un reporte de laboratorio registrado para este lote en la fecha seleccionada.'])->withInput();
        }

        $analitica = new LoteAnaliticaSuelo($validated);
        $analitica->lote_id = $loteGis->id;
        $analitica->analista_user_id = auth()->id();
        $analitica->save();

        return back()->with('success', 'Análisis de laboratorio asentado en la bitácora histórica.');
    }

    /**
     * Sub-recurso: Almacena un Sistema de Riego / Fertirrieigo
     */
    public function storeRiego(Request $request, Lote $loteGis)
    {
        $validated = $request->validate([
            'nombre_sistema'                => 'required|string|max:100',
            'tipo_riego'                    => 'required|in:goteo,microaspersion,aspersion,pivot_central,gravedad,subterraneo',
            'fuente_agua'                   => 'required|in:acueducto_distrito,pozo_profundo,rio_directo,embalse_almacenamiento,nacimiento',
            'caudal_diseno_litros_segundo'  => 'required|numeric|min:0.01',
            'presion_operacion_psi'         => 'nullable|numeric|min:0',
            'coeficiente_uniformidad'       => 'nullable|numeric|between:0,100',
            'espaciamiento_emisores_metros' => 'nullable|numeric|min:0.01',
            'descarga_emisor_litros_hora'   => 'nullable|numeric|min:0.01',
        ]);

        $riego = new LoteSistemaRiego($validated);
        $riego->lote_id = $loteGis->id;
        $riego->activo = true;
        $riego->save();

        return back()->with('success', 'Especificaciones del sistema hidráulico añadidas.');
    }

    public function mapa()
    {
        // Extraemos los datos básicos y decodificamos el punto PostGIS
        $arboles = Arbol::select(
                'id',
                'codigo_unico',
                'fila_indice',
                'posicion_indice',
                'estado_vital',
                'etapa_biologica',
                // PostGIS: ST_Y es Latitud, ST_X es Longitud
                DB::raw('ST_Y(coordenada_precision::geometry) as lat'),
                DB::raw('ST_X(coordenada_precision::geometry) as lng')
            )
            ->whereNotNull('coordenada_precision') // Solo árboles con GPS
            ->get();

        // Enviamos la colección a la vista
        return view('lotes_gis.mapa', compact('arboles'));
    }
}