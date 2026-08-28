<?php
namespace App\Services;
use App\Models\CicloProductivo;
use App\Models\CicloEtapaHistorial;
use App\Models\FenologiaEtapa;
use App\Models\EventoCampo;
use App\Models\FenologiaRecomendacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CicloProductivoService
{
    public function crearCicloConCronograma(array $datosCiclo): CicloProductivo
    {
        return DB::transaction(function() use ($datosCiclo){
            // 1. Crear el ciclo productivo
            $ciclo = CicloProductivo::create($datosCiclo);
            // 2. Traer las etapas teoricas en orden 
            $etapas = FenologiaEtapa::where('cultivo_id', $ciclo->cultivo_id)
                ->orderBy('orden')
                ->get();

            $fechaBase = Carbon::parse($ciclo->fecha_inicio);

            // Proyectamos el historial de etapas
            foreach($etapas as $index => $etapa)
            {
                $inicioEstimado = $etapa->duracion_dias_desde_inicio !== null
                    ? Carbon::parse($ciclo->fecha_inicio)->addDays($etapa->duracion_dias_desde_inicio)
                    : $fechaBase->copy();
                $finEstimado = $inicioEstimado->copy()->addDays($etapa->duracion_dias_estimada);
                $esPrimerEtapa = ($index === 0);
                
                $historial = CicloEtapaHistorial::create([
                    'ciclo_productivo_id' => $ciclo->id,
                    'fenologia_etapa_id' => $etapa->id,
                    'fecha_inicio_estimada' => $inicioEstimado,
                    'fecha_fin_estimada' => $finEstimado,
                    'fecha_inicio_real' => $esPrimerEtapa ? $inicioEstimado : null,
                    'estado' => $esPrimerEtapa ? 'en_progreso' : 'pendiente',
                ]);

                if ($esPrimerEtapa){
                    $this->generarEventosDeEtapa($historial);
                }

                $fechaBase = $finEstimado;
            }

            return $ciclo;
        });
    }

    // Paso 2: Genera eventos en eventos_campo basados en las recomendaciones de la etapa
    
    private function generarEventosDeEtapa(CicloEtapaHistorial $etapaHistorial): void
    {
        $recomendaciones = FenologiaRecomendacion::where('fenologia_etapa_id', $etapaHistorial->fenologia_etapa_id)
            ->where('genera_evento_automatico', true)
            ->whereNotNull('tipo_evento_id')
            ->get();

        $fechaReferencia = Carbon::parse($etapaHistorial->fecha_inicio_real ?? $etapaHistorial->fecha_inicio_estimada);

        foreach ($recomendaciones as $rec) {
            $fechaProgramada = $fechaReferencia->copy()->addDays($rec->dias_offset);

            EventoCampo::create([
                'ciclo_productivo_id' => $etapaHistorial->ciclo_productivo_id,
                'lote_id'             => $etapaHistorial->cicloProductivo->lote_id ?? null,
                'tipo_evento_id'      => $rec->tipo_evento_id,
                'fecha_programada'    => $fechaProgramada,
                'estado'              => 'Pendiente',
                'observaciones'       => "Auto: {$rec->titulo}. {$rec->instrucciones_tecnicas}",
            ]);
        }
    }

    /**
     * Paso 3: Avanza a una nueva etapa, recalcula fechas subsecuentes y dispara eventos.
     */
    public function avanzarEtapa(int $cicloId, int $nuevaEtapaHistorialId, string $fechaInicioReal): CicloEtapaHistorial
    {
        return DB::transaction(function () use ($cicloId, $nuevaEtapaHistorialId, $fechaInicioReal) {
            $fechaReal = Carbon::parse($fechaInicioReal);

            // 1. Completar la etapa en progreso actual
            CicloEtapaHistorial::where('ciclo_productivo_id', $cicloId)
                ->where('estado', 'en_progreso')
                ->update([
                    'estado' => 'completada',
                    'fecha_fin_real' => $fechaReal,
                ]);

            // 2. Iniciar la nueva etapa
            $etapaActual = CicloEtapaHistorial::findOrFail($nuevaEtapaHistorialId);
            $etapaActual->update([
                'estado' => 'en_progreso',
                'fecha_inicio_real' => $fechaReal,
            ]);

            // 3. Generar las labores de la nueva etapa
            $this->generarEventosDeEtapa($etapaActual);

            // 4. Recalcular fechas estimadas de etapas futuras por el desfase
            $etapasSiguientes = CicloEtapaHistorial::where('ciclo_productivo_id', $cicloId)
                ->where('estado', 'pendiente')
                ->get();

            $fechaPivote = $fechaReal->copy()->addDays($etapaActual->etapa->duracion_dias_estimada);

            foreach ($etapasSiguientes as $siguiente) {
                $duracion = $siguiente->etapa->duracion_dias_estimada;

                $siguiente->update([
                    'fecha_inicio_estimada' => $fechaPivote->copy(),
                    'fecha_fin_estimada'    => $fechaPivote->copy()->addDays($duracion),
                ]);

                $fechaPivote->addDays($duracion);
            }

            return $etapaActual;
        });
    }

}