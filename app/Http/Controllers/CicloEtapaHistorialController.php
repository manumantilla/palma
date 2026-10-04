<?php
namespace App\Http\Controllers;
use App\Models\CicloEtapaHistorial;
use App\Models\CicloProductivo;
use App\Models\FenologiaEtapa;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class CicloEtapaHistorialController extends Controller
{

    public function index(Request $request): View
    {
        $query = CicloEtapaHistorial::with([
            'cicloProductivo.cultivo',
            'fenologiaEtapa.recomendaciones'
        ]);

        if ($request->filled('ciclo_productivo_id')) {
            $query->where('ciclo_productivo_id', $request->ciclo_productivo_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $etapas = $query->orderBy('fecha_inicio_estimada', 'asc')->get();

        return view('ciclo_etapas.index', compact('etapas'));
    }


    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ciclo_productivo_id'   => 'required|exists:ciclos_productivos,id',
            'fenologia_etapa_id'    => 'required|exists:fenologia_etapas,id',
            'fecha_inicio_estimada' => 'required|date',
            'fecha_fin_estimada'    => 'required|date|after_or_equal:fecha_inicio_estimada',
            'fecha_inicio_real'     => 'nullable|date',
            'fecha_fin_real'        => 'nullable|date|after_or_equal:fecha_inicio_real',
            'estado'                => ['nullable', Rule::in(['pendiente', 'en_progreso', 'completada', 'omitida'])],
            'motivo_desviacion'     => 'nullable|string|max:255',
            'observaciones'         => 'nullable|string',
        ]);

        CicloEtapaHistorial::create($validated);

        return redirect()->back()->with('success', 'Etapa registrada en el historial del ciclo.');
    }


    public function show(int $id): View
    {
        $etapaHistorial = CicloEtapaHistorial::with([
            'cicloProductivo',
            'fenologiaEtapa.recomendaciones'
        ])->findOrFail($id);

        return view('ciclo_etapas.show', compact('etapaHistorial'));
    }


    public function update(Request $request, int $id): RedirectResponse
    {
        $etapaHistorial = CicloEtapaHistorial::findOrFail($id);

        $validated = $request->validate([
            'fecha_inicio_estimada' => 'sometimes|required|date',
            'fecha_fin_estimada'    => 'sometimes|required|date|after_or_equal:fecha_inicio_estimada',
            'fecha_inicio_real'     => 'nullable|date',
            'fecha_fin_real'        => 'nullable|date|after_or_equal:fecha_inicio_real',
            'estado'                => ['sometimes', 'required', Rule::in(['pendiente', 'en_progreso', 'completada', 'omitida'])],
            'motivo_desviacion'     => 'nullable|string|max:255',
            'observaciones'         => 'nullable|string',
        ]);

        $etapaHistorial->update($validated);

        return redirect()->back()->with('success', 'Historial de etapa actualizado correctamente.');
    }


    public function destroy(int $id): RedirectResponse
    {
        $etapaHistorial = CicloEtapaHistorial::findOrFail($id);
        $etapaHistorial->delete();

        return redirect()->back()->with('success', 'Registro de historial eliminado correctamente.');
    }


    public function iniciarEtapa(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'fecha_inicio_real' => 'nullable|date',
            'observaciones'     => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $id) {
            $etapa = CicloEtapaHistorial::with('fenologiaEtapa.recomendaciones')->findOrFail($id);
            $fechaInicio = $request->fecha_inicio_real ?? now()->toDateString();

            $etapa->update([
                'estado'            => 'en_progreso',
                'fecha_inicio_real' => $fechaInicio,
                'observaciones'     => $request->observaciones ?? $etapa->observaciones,
            ]);

            foreach ($etapa->fenologiaEtapa->recomendaciones as $recomendacion) {
                if ($recomendacion->genera_evento_automatico) {
                    $fechaProgramada = Carbon::parse($fechaInicio)->addDays($recomendacion->dias_offset);

                    Evento::create([
                        'ciclo_productivo_id' => $etapa->ciclo_productivo_id,
                        'tipo_evento_id'      => $recomendacion->tipo_evento_id,
                        'titulo'              => $recomendacion->titulo,
                        'descripcion'         => $recomendacion->instrucciones_tecnicas ?? $recomendacion->descripcion,
                        'fecha_programada'    => $fechaProgramada,
                        'estado'              => 'programado',
                        'prioridad'           => $recomendacion->prioridad ?? 'Media'
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Etapa iniciada en campo y eventos automáticos programados.');
    }


    public function completarEtapa(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'fecha_fin_real'    => 'nullable|date',
            'motivo_desviacion' => 'nullable|string|max:255',
            'observaciones'     => 'nullable|string'
        ]);

        $etapaHistorial = CicloEtapaHistorial::findOrFail($id);
        $fechaFin = $request->fecha_fin_real ?? now()->toDateString();

        $motivo = $request->motivo_desviacion;
        if (!$motivo && Carbon::parse($fechaFin)->gt(Carbon::parse($etapaHistorial->fecha_fin_estimada))) {
            $diasRetraso = Carbon::parse($fechaFin)->diffInDays(Carbon::parse($etapaHistorial->fecha_fin_estimada));
            $motivo = "Etapa completada con {$diasRetraso} días de retraso respecto a lo planificado.";
        }

        $etapaHistorial->update([
            'estado'            => 'completada',
            'fecha_fin_real'    => $fechaFin,
            'motivo_desviacion' => $motivo ?? $etapaHistorial->motivo_desviacion,
            'observaciones'     => $request->observaciones ?? $etapaHistorial->observaciones,
        ]);

        return redirect()->back()->with('success', 'Etapa completada exitosamente.');
    }

 
    public function omitirEtapa(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'motivo_desviacion' => 'required|string|max:255',
            'observaciones'     => 'nullable|string'
        ]);

        $etapaHistorial = CicloEtapaHistorial::findOrFail($id);

        $etapaHistorial->update([
            'estado'            => 'omitida',
            'motivo_desviacion' => $request->motivo_desviacion,
            'observaciones'     => $request->observaciones ?? $etapaHistorial->observaciones,
        ]);

        return redirect()->back()->with('success', 'La etapa ha sido marcada como omitida.');
    }

 
    public function inicializarPlanFenologico(Request $request): RedirectResponse
    {
        $request->validate([
            'ciclo_productivo_id' => 'required|exists:ciclos_productivos,id',
            'fecha_inicio_ciclo'  => 'required|date'
        ]);

        $ciclo = CicloProductivo::findOrFail($request->ciclo_productivo_id);
        $fechaBase = Carbon::parse($request->fecha_inicio_ciclo);

        $etapasPlantilla = FenologiaEtapa::where('cultivo_id', $ciclo->cultivo_id)
            ->orderBy('orden', 'asc')
            ->get();

        if ($etapasPlantilla->isEmpty()) {
            return redirect()->back()->with('error', 'No existen etapas fenológicas configuradas para el cultivo de este ciclo.');
        }

        DB::transaction(function () use ($etapasPlantilla, $ciclo, $fechaBase) {
            $acumuladoDias = 0;

            foreach ($etapasPlantilla as $plantilla) {
                $inicioEstimado = (clone $fechaBase)->addDays($plantilla->duracion_dias_desde_inicio ?? $acumuladoDias);
                $finEstimado = (clone $inicioEstimado)->addDays($plantilla->duracion_dias_estimada);

                CicloEtapaHistorial::create([
                    'ciclo_productivo_id'   => $ciclo->id,
                    'fenologia_etapa_id'    => $plantilla->id,
                    'fecha_inicio_estimada' => $inicioEstimado->toDateString(),
                    'fecha_fin_estimada'    => $finEstimado->toDateString(),
                    'estado'                => 'pendiente',
                ]);

                $acumuladoDias += $plantilla->duracion_dias_estimada;
            }
        });

        return redirect()->back()->with('success', 'Plan fenológico inicializado correctamente para el ciclo productivo.');
    }
}