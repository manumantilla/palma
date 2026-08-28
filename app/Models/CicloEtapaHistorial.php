<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CicloEtapaHistorial extends Model
{
    protected $table = 'ciclo_etapas_historial';

    protected $fillable = [
        'ciclo_productivo_id',
        'fenologia_etapa_id',
        'fecha_inicio_estimada',
        'fecha_inicio_real',
        'fecha_fin_estimada',
        'fecha_fin_real',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio_estimada' => 'date',
        'fecha_inicio_real' => 'date',
        'fecha_fin_estimada' => 'date',
        'fecha_fin_real' => 'date',
    ];

    public function cicloProductivo(): BelongsTo
    {
        return $this->belongsTo(CicloProductivo::class);
    }

    public function etapa(): BelongsTo
    {
        return $this->belongsTo(FenologiaEtapa::class, 'fenologia_etapa_id');
    }

    // Calcula el número de días de desfase respecto a la estimación
    public function getDesfaseDiasAttribute(): ?int
    {
        if (!$this->fecha_inicio_real) {
            return null;
        }

        return (int) $this->fecha_inicio_estimada->diffInDays($this->fecha_inicio_real, false);
    }

    // Inicia formalmente la etapa y dispara las labores automáticas
    public function iniciarEtapa(?string $fechaReal = null): void
    {
        $fecha = $fechaReal ? Carbon::parse($fechaReal) : now();

        $this->update([
            'fecha_inicio_real' => $fecha,
            'estado' => 'en_progreso',
        ]);

        // Disparar la automatización de eventos
        $this->generarEventosProgramados();
    }

    // Genera automáticamente los registros en eventos_campo basados en fenologia_recomendaciones
    public function generarEventosProgramados(): void
    {
        $recomendaciones = $this->etapa->recomendaciones()
            ->where('genera_evento_automatico', true)
            ->whereNotNull('tipo_evento_id')
            ->get();

        foreach ($recomendaciones as $rec) {
            $fechaProgramada = Carbon::parse($this->fecha_inicio_real)->addDays($rec->dias_offset);

            EventoCampo::create([
                'ciclo_productivo_id' => $this->ciclo_productivo_id,
                'lote_id'             => $this->cicloProductivo->lote_id ?? null,
                'tipo_evento_id'      => $rec->tipo_evento_id,
                'fecha_programada'    => $fechaProgramada,
                'estado'              => 'Pendiente',
                'observaciones'       => "Generado automáticamente por recomendación: {$rec->titulo}. {$rec->instrucciones_tecnicas}",
            ]);
        }
    }
}