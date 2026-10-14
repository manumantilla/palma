<?php
namespace App\Http\Controllers;

use App\Exceptions\EventoException;
use App\Models\CicloProductivo;
use App\Models\EventoCampo;
use App\Models\EventoManoObra;
use App\Models\SesionCosecha;
use App\Models\Trabajador;
use App\Services\EventoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Jornales y destajos. Toda la lógica (costo, gasto, validaciones) vive en EventoService.
 */
class EventoManoObraController extends Controller
{
    public function __construct(private EventoService $eventos)
    {
    }

    public function index(Request $request)
    {
        $registros = EventoManoObra::with(['eventoCampo.cicloProductivo', 'eventoCampo.tipoEvento', 'trabajador'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . $request->input('search') . '%';
                $query->where(fn ($q) => $q->where('nombre_trabajador', 'ilike', $search)->orWhere('cedula', 'ilike', $search));
            })
            ->when($request->filled('estado_pago'), fn ($q) => $q->where('estado_pago', $request->input('estado_pago')))
            ->when($request->filled('tipo_labor'), fn ($q) => $q->porTipoLabor($request->input('tipo_labor')))
            ->when($request->filled('ciclo_productivo_id'), fn ($q) => $q->porCicloProductivo($request->input('ciclo_productivo_id')))
            ->when($request->filled('fecha_inicio') && $request->filled('fecha_fin'),
                fn ($q) => $q->enRangoFechas($request->input('fecha_inicio'), $request->input('fecha_fin')))
            ->orderByDesc('fecha_labor')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $ciclos = CicloProductivo::select('id', 'nombre_campana')->orderBy('nombre_campana')->get();
        $trabajadores = Trabajador::select('id', 'nombres', 'apellidos', 'numero_documento')->orderBy('nombres')->get();

        return view('mano_obra.index', compact('registros', 'ciclos', 'trabajadores'));
    }

    public function create(Request $request)
    {
        return view('mano_obra.create', $this->datosFormulario() + [
            'eventoSeleccionado' => $request->integer('evento') ?: null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->reglas() + [
            'evento_campo_id' => 'required_without:sesion_id|nullable|exists:eventos_campo,id',
            'sesion_id'       => 'nullable|exists:sesiones_cosecha,id',
        ]);

        try {
            if (! empty($validated['sesion_id'])) {
                // Destajo de cosecha: el evento y los kilos salen de la sesión
                $registro = $this->eventos->registrarDestajoCosecha(SesionCosecha::findOrFail($validated['sesion_id']), $validated);
            } else {
                $registro = $this->eventos->registrarManoObra(EventoCampo::findOrFail($validated['evento_campo_id']), $validated);
            }
        } catch (EventoException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('eventos_campo.show', $registro->evento_campo_id)
            ->with('success', 'Mano de obra registrada.');
    }

    public function show(EventoManoObra $manoObra)
    {
        $registro = $manoObra->load(['eventoCampo.cicloProductivo', 'eventoCampo.tipoEvento', 'sesionCosecha', 'trabajador']);

        return view('mano_obra.show', compact('registro'));
    }

    public function edit(EventoManoObra $manoObra)
    {
        if ($manoObra->estado_pago === 'pagado') {
            return redirect()->route('mano-obra.index')->with('error', 'Un registro ya pagado no se edita.');
        }

        return view('mano_obra.edit', $this->datosFormulario() + ['registro' => $manoObra->load('eventoCampo.tipoEvento')]);
    }

    public function update(Request $request, EventoManoObra $manoObra)
    {
        $validated = $request->validate($this->reglas());

        try {
            $this->eventos->actualizarManoObra($manoObra, $validated);
        } catch (EventoException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('mano-obra.index')->with('success', 'Registro actualizado.');
    }

    public function destroy(EventoManoObra $manoObra)
    {
        try {
            $this->eventos->eliminarManoObra($manoObra);
        } catch (EventoException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('mano-obra.index')->with('success', 'Registro eliminado; su costo quedó anulado.');
    }

    /** Lo que pesó un trabajador en una sesión, para precargar el destajo. */
    public function kilos(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sesion'     => 'required|exists:sesiones_cosecha,id',
            'trabajador' => 'required|exists:trabajadores,id',
        ]);

        return response()->json($this->eventos->kilosPesados(
            SesionCosecha::findOrFail($validated['sesion']), (int) $validated['trabajador']
        ));
    }

    /**
     * Pago de la semana (o del rango que se indique) de un trabajador.
     */
    public function liquidarPagoSemanal(Request $request)
    {
        $validated = $request->validate([
            'trabajador_id' => 'nullable|required_without:cedula|exists:trabajadores,id',
            'cedula'        => 'nullable|required_without:trabajador_id|string|max:30',
            'desde'         => 'required|date',
            'hasta'         => 'required|date|after_or_equal:desde',
            'fecha_pago'    => 'required|date',
        ]);

        try {
            $resultado = $this->eventos->liquidar(
                $validated['trabajador_id'] ?? null, $validated['cedula'] ?? null,
                $validated['desde'], $validated['hasta'], $validated['fecha_pago']
            );
        } catch (EventoException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('mano-obra.index')->with('success', sprintf(
            'Liquidación lista: %d registro(s) por $%s.', $resultado['registros'], number_format($resultado['total'], 0, ',', '.')
        ));
    }

    // ------------------------------------------------------------------

    private function reglas(): array
    {
        return [
            'trabajador_id'     => 'nullable|exists:trabajadores,id',
            'nombre_trabajador' => 'required_without:trabajador_id|nullable|string|max:255',
            'cedula'            => 'nullable|string|max:30',
            'tipo_labor'        => 'required|in:' . implode(',', EventoManoObra::TIPOS_LABOR),
            'fecha_labor'       => 'required|date|before_or_equal:today',
            'cantidad'          => 'required|numeric|min:0.01',
            'unidad_destajo'    => 'nullable|required_if:tipo_labor,destajo|string|max:30',
            'valor_unitario'    => 'required|numeric|min:0',
            'costo_total'       => 'nullable|numeric|min:0',
            'area_ejecutada_ha' => 'nullable|numeric|min:0',
            'arboles_atendidos' => 'nullable|integer|min:0',
            'observaciones'     => 'nullable|string',
        ];
    }

    private function datosFormulario(): array
    {
        return [
            // Solo labores que llevan mano de obra y siguen abiertas
            'eventos' => EventoCampo::with(['tipoEvento:id,nombre', 'lote:id,nombre_lote'])
                ->whereHas('tipoEvento', fn ($q) => $q->where('consume_mano_obra', true))
                ->whereIn('estado', ['Pendiente', 'En Proceso'])
                ->orderByDesc('fecha_programada')
                ->get(['id', 'tipo_evento_id', 'lote_id', 'fecha_programada']),
            'sesiones' => SesionCosecha::where('estado', 'abierta')->orderByDesc('fecha')->get(['id', 'fecha', 'evento_campo_id']),
            'trabajadores' => Trabajador::where('activo', true)
                ->orderBy('nombres')
                ->get(['id', 'nombres', 'apellidos', 'numero_documento']),
        ];
    }
}
