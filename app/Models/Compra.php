<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compras';
    
    protected $fillable = [
        'proveedor_id',
        'fecha',
        'numero_factura',
        'total',
        'iva',
        'fecha_pedido',
        'estado_pago',
        'observaciones',
        'user_id',
    ];
    
    protected $casts = [
        'fecha' => 'datetime',
        'fecha_pedido' => 'datetime',
        'total' => 'decimal:2',
        'iva' => 'decimal:2',
        'estado_pago' => 'string',
    ];
    
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(CompraItem::class, 'compra_id');
    }
}
