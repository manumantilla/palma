<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * cantidad_actual y estado (activo/agotado) los mantiene InventarioService.
 * No asignar cantidad_actual a mano: el lote se crea en 0 y la entrada del kardex lo llena.
 */
class LoteInsumo extends Model
{
    protected $table = 'lotes_insumos';

    protected $fillable = [
        'insumo_id',
        'proveedor_id',
        'compra_id',
        'compra_item_id',
        'codigo_lote',
        'fecha_vencimiento',
        'fecha_ingreso',
        'cantidad_inicial',
        'cantidad_actual',
        'unidad_id',
        'costo_unitario',
        'estado'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_ingreso' => 'date',
        'cantidad_inicial' => 'decimal:4',
        'cantidad_actual' => 'decimal:4',
        'costo_unitario' => 'decimal:4',
    ];

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    public function compraItem(): BelongsTo
    {
        return $this->belongsTo(CompraItem::class);
    }

    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoStock::class, 'lote_insumo_id')->orderBy('id');
    }

    /** Vencido a una fecha dada, aunque el estado todavía diga 'activo'. */
    public function vencidoAl($fecha): bool
    {
        return $this->fecha_vencimiento !== null && $this->fecha_vencimiento->lt($fecha);
    }

    /** Lotes de los que se puede sacar producto para el campo, en orden FEFO. */
    public function scopeDisponiblesFefo(Builder $query, int $insumoId, $fecha): Builder
    {
        return $query->where('insumo_id', $insumoId)
            ->where('estado', 'activo')
            ->where('cantidad_actual', '>', 0)
            ->where(fn ($q) => $q->whereNull('fecha_vencimiento')->orWhereDate('fecha_vencimiento', '>=', $fecha))
            ->orderByRaw('fecha_vencimiento ASC NULLS LAST')
            ->orderBy('fecha_ingreso')
            ->orderBy('id');
    }
}
