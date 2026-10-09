<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class LaborPlantilla extends Model
{
    use HasFactory;

    protected $table = 'labores_plantilla';

    protected $fillable = [
        'cultivo_id',
        'material_genetico_id',
        'nombre_labor',
        'descripcion',
        'momento_tipo',
        'dias_desde_siembra',
        'fenologia_etapa_id',
        'mes_fijo',
        'dia_fijo',
        'periodicidad_dias',
        'ventana_ejecucion_dias',
        'duracion_estimada_horas',
        'tipo_evento_id',
        'requiere_insumos',
        'requiere_mano_obra',
        'jornales_por_ha',
        'horas_maquina_por_ha',
        'prioridad',
        'activo',
    ];

    /**
     * Convierte automáticamente los booleanos de la base de datos.
     */
    protected $casts = [
        'requiere_insumos' => 'boolean',
        'requiere_mano_obra' => 'boolean',
        'activo' => 'boolean',
        'jornales_por_ha' => 'decimal:2',
        'horas_maquina_por_ha' => 'decimal:2',
    ];

    public function materialGenetico(): BelongsTo
    {
        return $this->belongsTo(MaterialGenetico::class, 'material_genetico_id');
    }

    /** Insumos y dosis planificados para esta labor. */
    public function insumosPlan(): MorphMany
    {
        return $this->morphMany(LaborInsumoPlan::class, 'planificable');
    }

    /**
     * Obtener el cultivo al que pertenece esta plantilla de labor.
     */
    public function cultivo(): BelongsTo
    {
        return $this->belongsTo(Cultivo::class, 'cultivo_id');
    }

    /**
     * Obtener la etapa fenológica vinculada a esta labor (si aplica).
     */
    public function etapaFenologica(): BelongsTo
    {
        return $this->belongsTo(FenologiaEtapa::class, 'fenologia_etapa_id');
    }

    /**
     * Obtener el tipo de evento asociado a la labor.
     */
    public function tipoEvento(): BelongsTo
    {
        return $this->belongsTo(TipoEvento::class, 'tipo_evento_id');
    }
}