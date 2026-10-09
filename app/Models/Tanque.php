<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tanque extends Model
{
    protected $table = 'tanques';

    protected $fillable = [
        'nombre',
        'capacidad_litros',
        'altura_maxima_cm',
        'lote_id',
        'nivel_actual_litros',
        'tiene_sensor_iot',
        'activo',
    ];

    protected $casts = [
        'capacidad_litros'    => 'decimal:2',
        'altura_maxima_cm'    => 'decimal:2',
        'nivel_actual_litros' => 'decimal:2',
        'tiene_sensor_iot'    => 'boolean',
        'activo'              => 'boolean',
    ];

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }
}
