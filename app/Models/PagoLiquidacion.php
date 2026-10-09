<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoLiquidacion extends Model
{
    protected $table = 'pagos_liquidacion';

    protected $fillable = [
        'liquidacion_id',
        'despacho_id',
        'valor_pagado',
        'fecha_pago',
        'medio_pago',
        'referencia_pago',
        'banco_origen',
        'registrado_por',
        'observaciones',
    ];

    protected $casts = [
        'valor_pagado' => 'decimal:2',
        'fecha_pago'   => 'datetime',
    ];

    public function liquidacion(): BelongsTo
    {
        return $this->belongsTo(LiquidacionDespacho::class, 'liquidacion_id');
    }

    public function despacho(): BelongsTo
    {
        return $this->belongsTo(Despacho::class, 'despacho_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
