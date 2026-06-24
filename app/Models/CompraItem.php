<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraItem extends Model
{
    protected $table = 'compra_items';
    
    protected $fillable = [
        'compra_id',
        'insumo_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'fecha_vencimiento',
    ];
    
    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'fecha_vencimiento' => 'date',
    ];
    
    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }
    
    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }
    
    // Relación polimórfica: este item puede ser el origen de un movimiento de stock (entrada)
    public function movimientosStock(): MorphMany
    {
        return $this->morphMany(MovimientoStock::class, 'movimientoable');
    }
}
