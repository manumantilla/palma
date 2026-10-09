<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    /**
     * Movimientos de todos los lotes de compra de este insumo.
     * movimientos_stock se registra por lote (lote_insumo_id), no por stock_insumo.
     */
    public function movimientos(): HasManyThrough
    {
        return $this->hasManyThrough(
            MovimientoStock::class,
            LoteInsumo::class,
            'insumo_id',      // lotes_insumos.insumo_id
            'lote_insumo_id', // movimientos_stock.lote_insumo_id
            'insumo_id',      // stock_insumos.insumo_id
            'id'              // lotes_insumos.id
        );
    }
}
