<?php
namespace App\Http\Controllers;

use App\Models\EventoCampo;
use App\Models\CicloProductivo; 
use App\Models\Lote;
use App\Models\LoteZonaManejo;  
use App\Models\TipoEvento;      
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventoController extends Controller
{
    /**
     * READ (Index): Listar todos los eventos de campo
     */
    public function index(Request $request)
    {
        $eventos = EventoCampo::with(['tipoEvento', 'cicloProductivo', 'lote', 'zona'])
            ->filtrar($request->only([
                'ciclo_productivo_id', 
                'lote_id', 
                'zona_id', 
                'tipo_evento_id', 
                'estado', 
                'fecha_inicio', 
                'fecha_fin'
            ]))
            ->orderBy('fecha_programada', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Cargamos los datos para llenar los selects del formulario de filtros
        $ciclos = CicloProductivo::orderBy('nombre_campana')->get();
        $lotes = Lote::orderBy('nombre_lote')->get();
        $zonas = LoteZonaManejo::orderBy('nombre_zona')->get();
        $tiposEvento = TipoEvento::orderBy('nombre')->get();

        return view('eventos_campo.index', compact('eventos', 'ciclos', 'lotes', 'zonas', 'tiposEvento'));
    }

    /**
     * READ (Show): Ver el detalle de un evento específico
     */
    public function show($id)
    {
        $evento = EventoCampo::with(['tipoEvento', 'cicloProductivo', 'lote', 'zona', 'arboles'])->findOrFail($id);
        
        return view('eventos_campo.show', compact('evento'));
    }

    /**
     * Mostrar formulario para Evento General (Fase 1: SIN RELACIÓN A CULTIVO)
     */
    public function createGeneral()
    {
        // Solo necesitas los tipos de evento de catálogo
        $tiposEvento = DB::table('tipos_evento')->select('id', 'nombre')->get();

        return view('eventos_campo.create_general', compact('tiposEvento'));
    }


    public function createConCultivo(CicloProductivo $ciclo)
    {
        $tiposEvento = DB::table('tipos_evento')->select('id', 'nombre')->get();

        // 1. Buscamos los lotes que estén asociados a este ciclo productivo 
        // (Ajusta los nombres de las columnas según tus llaves foráneas)
        $lotes = DB::table('lotes')
            ->where('activo', true)
            ->where('ciclo_productivo_id', $ciclo->id) // O a través de la relación que tengas
            ->select('id', 'nombre_lote', 'codigo_lote')
            ->get();

        // 2. Si quieres ahorrarle un paso de AJAX al usuario, puedes mandarle de una vez 
        // todas las zonas de esos lotes para que JavaScript las filtre al cambiar de lote.
        $lotesIds = $lotes->pluck('id');
        $zonas = DB::table('lotes_zonas_manejo')
            ->whereIn('lote_id', $lotesIds)
            ->select('id', 'lote_id', 'nombre_zona')
            ->get();

        return view('eventos_campo.create_cultivo', compact('ciclo', 'tiposEvento', 'lotes', 'zonas'));
    }
    /**
     * CREATE (Store) - Fase 1: SIN RELACIÓN A UN CULTIVO
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo_evento_id'   => 'required|exists:tipos_evento,id',
            'fecha_programada' => 'required|date',
            'hora_inicio'      => 'nullable|date_format:H:i',
            'hora_fin'         => 'nullable|date_format:H:i|after:hora_inicio',
            'fecha_ejecucion'  => 'nullable|date',
            'latitud'          => 'nullable|numeric',
            'longitud'         => 'nullable|numeric',
            'estado'           => 'required|in:Pendiente,En Proceso,Completado,Cancelado',
            'observaciones'    => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Fuerza a que los campos relacionales vayan vacíos en este método
            $data = array_merge($request->all(), [
                'ciclo_productivo_id' => null,
                'lote_id'             => null,
                'zona_id'             => null
            ]);

            $evento = EventoCampo::create($data);
            
            DB::commit();
            return redirect()->route('eventos_campo.show', $evento->id)
                ->with('success', 'Evento General creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Error al crear el evento general: ' . $e->getMessage());
        }
    }

    /**
     * CREATE (Store With Cultivo) - Fase 1: CON RELACIÓN A UN CULTIVO Y ÁRBOLES
     */
    public function storeWithCultivo(Request $request, CicloProductivo $ciclo)
    {
        $request->validate([
            'lote_id'          => 'required|exists:lotes,id',
            'zona_id'          => 'nullable|exists:lotes_zonas_manejo,id',
            'tipo_evento_id'   => 'required|exists:tipos_evento,id',
            'fecha_programada' => 'required|date',
            'hora_inicio'      => 'nullable|date_format:H:i',
            'hora_fin'         => 'nullable|date_format:H:i|after:hora_inicio',
            'fecha_ejecucion'  => 'nullable|date',
            'latitud'          => 'nullable|numeric',
            'longitud'         => 'nullable|numeric',
            'estado'           => 'required|in:Pendiente,En Proceso,Completado,Cancelado',
            'observaciones'    => 'nullable|string',
            
            // Input controlador del alcance (Punto 2.1)
            'alcance'          => 'nullable|in:global,lote_zona,arbol', 
        ]);

        DB::beginTransaction();
        try {
            // Unificamos el ID del ciclo que viene por parámetro de ruta con la data del Request
            $data = array_merge($request->all(), [
                'ciclo_productivo_id' => $ciclo->id
            ]);

            $evento = EventoCampo::create($data);

            // Manejo automático de asignación masiva de árboles (Punto 2.1)
            if ($request->alcance === 'arbol') {
                $queryArboles = Arbol::query();

                if ($request->filled('zona_id')) {
                    $queryArboles->where('zona_id', $request->zona_id);
                } else {
                    $queryArboles->where('lote_id', $request->lote_id);
                }

                $arbolesIds = $queryArboles->pluck('id')->toArray();

                if (!empty($arbolesIds)) {
                    $pivotData = array_map(function($arbolId) use ($evento) {
                        return [
                            'evento_campo_id' => $evento->id,
                            'arbol_id'        => $arbolId,
                            'novedad_arbol'   => 'ninguna',
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }, $arbolesIds);

                    DB::table('evento_arbol')->insert($pivotData);
                }
            }

            DB::commit();
            return redirect()->route('eventos_campo.index')
                ->with('success', 'Evento del cultivo ' . $ciclo->nombre_campana . ' registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Error al registrar el evento de cultivo: ' . $e->getMessage());
        }
    }

    /**
     * UPDATE: Modificar datos de un evento existente
     */
    public function update(Request $request, $id)
    {
        $evento = EventoCampo::findOrFail($id);

        $request->validate([
            'tipo_evento_id'   => 'required|exists:tipos_evento,id',
            'fecha_programada' => 'required|date',
            'hora_inicio'      => 'nullable|date_format:H:i',
            'hora_fin'         => 'nullable|date_format:H:i|after:hora_inicio',
            'fecha_ejecucion'  => 'nullable|date',
            'latitud'          => 'nullable|numeric',
            'longitud'         => 'nullable|numeric',
            'estado'           => 'required|in:Pendiente,En Proceso,Completado,Cancelado',
            'observaciones'    => 'nullable|string',
            'ciclo_productivo_id' => 'nullable|exists:ciclos_productivos,id',
            'lote_id'          => 'nullable|exists:lotes,id',
            'zona_id'          => 'nullable|exists:lotes_zonas_manejo,id',
        ]);

        DB::beginTransaction();
        try {
            $evento->update($request->all());

            DB::commit();
            return redirect()->route('eventos_campo.show', $evento->id)
                ->with('success', 'Evento de campo actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Error al actualizar el evento: ' . $e->getMessage());
        }
    }

    /**
     * DESTROY: Eliminar lógicamente un evento (Usa SoftDeletes)
     */
    public function destroy($id)
    {
        $evento = EventoCampo::findOrFail($id);

        DB::beginTransaction();
        try {
            // Nota técnica: Tu migración de 'evento_arbol' tiene onDelete('cascade').
            // Al ser SoftDelete en la tabla padre, los pivotes se mantienen intactos por seguridad de trazabilidad histórica.
            $evento->delete();

            DB::commit();
            return redirect()->route('eventos_campo.index')
                ->with('success', 'El evento ha sido eliminado (archivado) correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al intentar eliminar el evento: ' . $e->getMessage());
        }
    }
}


