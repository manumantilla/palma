<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CompraItem extends Model
{
    protected $table = 'compra_items';

    protected $fillable = [
        'compra_id', 'insumo_id', 'cantidad', 'precio_unitario', 'subtotal', 'fecha_vencimiento',
    ];

    protected function casts(): array
    {
        return [
            'cantidad'          => 'decimal:2',
            'precio_unitario'   => 'decimal:2',
            'subtotal'          => 'decimal:2',
            'fecha_vencimiento' => 'date',
        ];
    }

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class);
    }

    /** Entradas de inventario generadas al recibir este ítem (relación polimórfica). */
    public function movimientosStock(): MorphMany
    {
        return $this->morphMany(MovimientoStock::class, 'movimientoable');
    }

    /** Cantidad ya ingresada a inventario. */
    public function cantidadRecibida(): float
    {
        return (float) $this->movimientosStock()
            ->where('tipo_movimiento', 'entrada')
            ->sum('cantidad');
    }

    public function cantidadPendiente(): float
    {
        return round((float) $this->cantidad - $this->cantidadRecibida(), 2);
    }
}