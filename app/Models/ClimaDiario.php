<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClimaDiario extends Model
{
    protected $table = 'clima_diario';

    protected $fillable = [
        'estacion_clima_id',
        'fecha',
        'temp_min_c',
        'temp_max_c',
        'temp_media_c',
        'precipitacion_mm',
        'humedad_relativa_pct',
        'radiacion_mj_m2',
        'velocidad_viento_ms',
        'et0_mm',
        'horas_sol',
        'fuente',
    ];

    protected $casts = [
        'fecha'                => 'date',
        'temp_min_c'           => 'decimal:1',
        'temp_max_c'           => 'decimal:1',
        'temp_media_c'         => 'decimal:1',
        'precipitacion_mm'     => 'decimal:1',
        'humedad_relativa_pct' => 'decimal:2',
        'radiacion_mj_m2'      => 'decimal:2',
        'velocidad_viento_ms'  => 'decimal:2',
        'et0_mm'               => 'decimal:2',
        'horas_sol'            => 'decimal:1',
    ];

    public function estacionClima(): BelongsTo
    {
        return $this->belongsTo(EstacionClima::class, 'estacion_clima_id');
    }
}
