<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValidacionRiego extends Model
{
    protected $table = 'validaciones_riego';

    protected $fillable = [
        'evento_riego_id',
        'tanque_id',
        'nivel_tanque_antes_litros',
        'nivel_tanque_despues_litros',
        'volumen_real_consumido_litros',
        'volumen_estimado_litros',
        'diferencia_litros',
        'porcentaje_error',
        'observaciones_auditoria',
    ];

    protected $casts = [
        'nivel_tanque_antes_litros'     => 'decimal:2',
        'nivel_tanque_despues_litros'   => 'decimal:2',
        'volumen_real_consumido_litros' => 'decimal:2',
        'volumen_estimado_litros'       => 'decimal:2',
        'diferencia_litros'             => 'decimal:2',
        'porcentaje_error'              => 'decimal:2',
    ];

    public function eventoRiego(): BelongsTo
    {
        return $this->belongsTo(EventoRiego::class, 'evento_riego_id');
    }

    public function tanque(): BelongsTo
    {
        return $this->belongsTo(Tanque::class, 'tanque_id');
    }
}
