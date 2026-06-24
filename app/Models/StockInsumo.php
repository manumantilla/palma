<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockInsumo extends Model
{
    protected $table = 'stock_insumos';
    
    protected $fillable = [
        'insumo_id',
        'cantidad_disponible',
        'unidad_base',
    ];
    
    protected $casts = [
        'cantidad_disponible' => 'decimal:2',
    ];
    
    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }
    
    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoStock::class, 'stock_insumo_id');
    }       
}
