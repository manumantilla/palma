<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FenologiaEtapa extends Model
{
    protected $table = 'fenologia_etapas';

    protected $fillable = [
        'cultivo_id',
        'nombre',
        'orden',
        'duracion_dias_desde_inicio',
        'duracion_dias_estimada',
        'descripcion',
    ];

    public function cultivo(): BelongsTo
    {
        return $this->belongsTo(Cultivo::class);
    }

    public function recomendaciones(): HasMany
    {
        return $this->hasMany(FenologiaRecomendacion::class);
    }

    public function historiales(): HasMany
    {
        return $this->hasMany(CicloEtapaHistorial::class);
    }
}