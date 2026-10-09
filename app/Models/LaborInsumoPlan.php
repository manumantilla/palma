<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LaborInsumoPlan extends Model
{
    protected $table = 'labor_insumos_plan';

    protected $fillable = [
        'planificable_type',
        'planificable_id',
        'insumo_id',
        'dosis',
        'unidad_medida_id',
        'base_dosis',
        'es_alternativa',
        'notas',
    ];

    protected $casts = [
        'dosis'          => 'decimal:4',
        'es_alternativa' => 'boolean',
    ];

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }

    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id');
    }

    public function planificable(): MorphTo
    {
        return $this->morphTo();
    }
}
