<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class FenologiaRecomendacion extends Model
{
    use SoftDeletes;

    protected $table = 'fenologia_recomendaciones';

    protected $fillable = [
        'fenologia_etapa_id',
        'tipo_evento_id',
        'material_genetico_id',
        'titulo',
        'descripcion',
        'prioridad',
        'dias_offset',
        'ventana_ejecucion_dias',
        'periodicidad_dias',
        'repeticiones_maximas',
        'genera_evento_automatico',
        'requiere_verificacion_campo',
        'condicion_activacion',
        'jornales_por_ha',
        'horas_maquina_por_ha',
        'instrucciones_tecnicas',
    ];

    protected $casts = [
        'genera_evento_automatico' => 'boolean',
        'requiere_verificacion_campo' => 'boolean',
        'dias_offset' => 'integer',
        'ventana_ejecucion_dias' => 'integer',
        'periodicidad_dias' => 'integer',
        'repeticiones_maximas' => 'integer',
        'condicion_activacion' => 'array',
        'jornales_por_ha' => 'decimal:2',
        'horas_maquina_por_ha' => 'decimal:2',
    ];

    public function etapa(): BelongsTo
    {
        return $this->belongsTo(FenologiaEtapa::class, 'fenologia_etapa_id');
    }

    public function tipoEvento(): BelongsTo
    {
        return $this->belongsTo(TipoEvento::class, 'tipo_evento_id');
    }

    /** Null = aplica a todos los materiales (ej. polinización asistida solo para OxG). */
    public function materialGenetico(): BelongsTo
    {
        return $this->belongsTo(MaterialGenetico::class, 'material_genetico_id');
    }

    /** Insumos y dosis planificados para esta recomendación. */
    public function insumosPlan(): MorphMany
    {
        return $this->morphMany(LaborInsumoPlan::class, 'planificable');
    }
}