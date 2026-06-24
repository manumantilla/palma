<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FenologiaEtapa extends Model
{
    use HasFactory;

    protected $table = 'fenologia_etapas';

    protected $fillable = [
        'cultivo_id',
        'nombre',
        'orden',
        'duracion_dias_desde_inicio',
        'duracion_dias_estimada',
        'descripcion',
    ];

    /**
     * Obtener el cultivo al que pertenece esta etapa.
     */
    public function cultivo(): BelongsTo
    {
        return $this->belongsTo(Cultivo::class, 'cultivo_id');
    }

    /**
     * Obtener las labores de plantilla que dependen de esta etapa fenológica.
     */
    public function laboresPlantilla(): HasMany
    {
        return $this->hasMany(LaborPlantilla::class, 'fenologia_etapa_id');
    }
}