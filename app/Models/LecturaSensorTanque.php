<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LecturaSensorTanque extends Model
{
    use HasUuids;

    protected $table = 'lecturas_sensores_tanque';

    protected $fillable = [
        'tanque_id',
        'lectura_distancia_cm',
        'porcentaje_volumen',
        'calculo_litros_actuales',
        'fecha_hora_lectura',
        'dispositivo_mac',
    ];

    protected $casts = [
        'lectura_distancia_cm'    => 'decimal:2',
        'porcentaje_volumen'      => 'decimal:2',
        'calculo_litros_actuales' => 'decimal:2',
        'fecha_hora_lectura'      => 'datetime',
    ];

    public function tanque(): BelongsTo
    {
        return $this->belongsTo(Tanque::class, 'tanque_id');
    }
}
