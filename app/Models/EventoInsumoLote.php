<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class EventoInsumoLote extends Model
{
    use HasFactory;

    protected $table = 'evento_insumo_lotes';

    protected $fillable = [
        'evento_insumo_id',
        'lote_insumo_id',
        'cantidad',
        'costo_unitario',
        'costo_total',
        'area_aplicada',
    ];

    protected $casts = [
        'cantidad'       => 'decimal:4',
        'costo_unitario' => 'decimal:4',
        'costo_total'    => 'decimal:2',
        'area_aplicada'  => 'decimal:2',
    ];

    // Relationships
    public function eventoInsumo()
    {
        return $this->belongsTo(EventoInsumo::class);
    }

    public function loteInsumo() {
        return $this->belongsTo(LoteInsumo::class, 'lote_insumo_id');
    }

    /** Salida del kardex que generó esta línea (una por línea). */
    public function movimientoStock(): MorphOne
    {
        return $this->morphOne(MovimientoStock::class, 'movimientoable');
    }
}
