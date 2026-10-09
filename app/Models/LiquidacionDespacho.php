<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiquidacionDespacho extends Model
{
    protected $table = 'liquidaciones_despacho';

    protected $fillable = [
        'despacho_id',
        'despacho_item_id',
        'fecha_liquidacion',
        'fecha_vencimiento',
        'numero_identificacion',
        'precio_unitario_kg',
        'valor_bruto_venta',
        'comision_porcentaje',
        'valor_comision',
        'valor_flete_descontado',
        'otros_descuentos',
        'detalle_otros_descuentos',
        'retefuente',
        'reteica',
        'cuota_fomento',
        'valor_neto_item',
        'estado_pago',
        'valor_pagado',
        'saldo_pendiente',
        'fecha_pago',
        'medio_pago',
        'observaciones',
    ];

    protected $casts = [
        'fecha_liquidacion'      => 'datetime',
        'fecha_vencimiento'      => 'date',
        'precio_unitario_kg'     => 'decimal:2',
        'valor_bruto_venta'      => 'decimal:2',
        'comision_porcentaje'    => 'decimal:2',
        'valor_comision'         => 'decimal:2',
        'valor_flete_descontado' => 'decimal:2',
        'otros_descuentos'       => 'decimal:2',
        'retefuente'             => 'decimal:2',
        'reteica'                => 'decimal:2',
        'cuota_fomento'          => 'decimal:2',
        'valor_neto_item'        => 'decimal:2',
        'valor_pagado'           => 'decimal:2',
        'saldo_pendiente'        => 'decimal:2',
        'fecha_pago'             => 'datetime',
    ];

    public function despacho(): BelongsTo
    {
        return $this->belongsTo(Despacho::class, 'despacho_id');
    }

    public function despachoItem(): BelongsTo
    {
        return $this->belongsTo(DespachoItem::class, 'despacho_item_id');
    }
}
