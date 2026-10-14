<?php
namespace App\Http\Controllers;

use App\Exceptions\EventoException;
use App\Models\EventoCampo;
use App\Models\CicloProductivo;
use App\Models\Lote;
use App\Models\LoteZonaManejo;
use App\Models\TipoEvento;
use App\Services\EventoService;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function __construct(private EventoService $eventos)
    {
    }

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
        $evento = EventoCampo::with([
            'tipoEvento',
            'cicloProductivo',
            'lote',
            'zona',
            'eventoArboles.arbol', // Carga árboles intervenidos
            'eventoInsumos.insumo', // Carga los insumos
            'eventoInsumos.lotes',  // Carga los lotes del Kardex usados
            'eventoManoObra',       // Jornales y destajos
        ])->findOrFail($id);

        return view('eventos_campo.show', compact('evento'));
    }

    /**
     * Labor general de la finca, sin cultivo (cercas, caminos, bodega...).
     */
    public function createGeneral()
    {
        $tiposEvento = TipoEvento::select('id', 'nombre')->orderBy('nombre')->get();

        return view('eventos_campo.create_general', compact('tiposEvento'));
    }

    public function createConCultivo(CicloProductivo $ciclo)
    {
        $tiposEvento = TipoEvento::select('id', 'nombre')->orderBy('nombre')->get();
        // Solo las zonas del lote de este ciclo
        $zonas = $ciclo->lote->zonasManejo()->select('id', 'nombre_zona')->orderBy('nombre_zona')->get();

        return view('eventos_campo.create_cultivo', compact('ciclo', 'tiposEvento', 'zonas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->reglas());

        try {
            // Labor general: sin ciclo, lote ni zona
            $evento = $this->eventos->crear(array_merge($validated, [
                'ciclo_productivo_id' => null, 'lote_id' => null, 'zona_id' => null, 'ciclo_etapa_id' => null,
            ]));
        } catch (EventoException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('eventos_campo.show', $evento->id)
            ->with('success', 'Labor general creada.');
    }

    public function storeWithCultivo(Request $request, CicloProductivo $ciclo)
    {
        $validated = $request->validate($this->reglas() + [
            'lote_id'        => 'nullable|exists:lotes,id',
            'zona_id'        => 'nullable|exists:lotes_zonas_manejo,id',
            'ciclo_etapa_id' => 'nullable|exists:ciclo_etapas_historial,id',
            'alcance'        => 'nullable|in:global,lote_zona,arbol',
        ]);

        try {
            $this->eventos->crear(['ciclo_productivo_id' => $ciclo->id] + $validated, $validated['alcance'] ?? null);
        } catch (EventoException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('eventos_campo.index')
            ->with('success', 'Labor del cultivo ' . $ciclo->nombre_campana . ' registrada.');
    }

    public function update(Request $request, EventoCampo $evento)
    {
        $validated = $request->validate([
            'tipo_evento_id'      => 'required|exists:tipos_evento,id',
            'fecha_programada'    => 'required|date',
            'fecha_limite'        => 'nullable|date|after_or_equal:fecha_programada',
            'hora_inicio'         => 'nullable|date_format:H:i',
            'hora_fin'            => 'nullable|date_format:H:i|after:hora_inicio',
            'latitud'             => 'nullable|numeric|between:-90,90',
            'longitud'            => 'nullable|numeric|between:-180,180',
            'observaciones'       => 'nullable|string',
            'ciclo_productivo_id' => 'nullable|exists:ciclos_productivos,id',
            'lote_id'             => 'nullable|exists:lotes,id',
            'zona_id'             => 'nullable|exists:lotes_zonas_manejo,id',
            'ciclo_etapa_id'      => 'nullable|exists:ciclo_etapas_historial,id',
        ]);

        try {
            $this->eventos->actualizar($evento, $validated);
        } catch (EventoException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('eventos_campo.show', $evento->id)
            ->with('success', 'Labor actualizada.');
    }

    public function completar(Request $request, EventoCampo $evento)
    {
        $validated = $request->validate(['fecha_ejecucion' => 'nullable|date|before_or_equal:today']);

        try {
            $this->eventos->completar($evento, $validated['fecha_ejecucion'] ?? null);
        } catch (EventoException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Labor completada.');
    }

    public function cancelar(Request $request, EventoCampo $evento)
    {
        $validated = $request->validate(['motivo' => 'required|string|max:255']);

        try {
            $this->eventos->cancelar($evento, $validated['motivo']);
        } catch (EventoException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Labor cancelada.');
    }

    /** Archiva (soft delete) solo lo que no tiene consumos; lo demás se completa o se cancela. */
    public function destroy(EventoCampo $evento)
    {
        if ($evento->eventoManoObra()->exists() || $evento->eventoInsumos()->exists()) {
            return back()->with('error', 'La labor tiene insumos o jornales registrados y no se puede eliminar.');
        }

        $evento->delete();

        return redirect()->route('eventos_campo.index')
            ->with('success', 'La labor fue archivada.');
    }

    private function reglas(): array
    {
        return [
            'tipo_evento_id'   => 'required|exists:tipos_evento,id',
            'fecha_programada' => 'required|date',
            'fecha_limite'     => 'nullable|date|after_or_equal:fecha_programada',
            'hora_inicio'      => 'nullable|date_format:H:i',
            'hora_fin'         => 'nullable|date_format:H:i|after:hora_inicio',
            'latitud'          => 'nullable|numeric|between:-90,90',
            'longitud'         => 'nullable|numeric|between:-180,180',
            'estado'           => 'required|in:Pendiente,En Proceso',
            'observaciones'    => 'nullable|string',
        ];
    }
}
