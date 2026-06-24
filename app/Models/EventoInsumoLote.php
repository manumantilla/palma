<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoInsumoLote extends Model
{
    use HasFactory;

    protected $table = 'evento_insumo_lotes';

    protected $fillable = [
        'evento_insumo_id',
        'lote_insumo_id',
        'cantidad',
    ];

    /**
     * Relación con el registro padre de EventoInsumo.
     */
    public function eventoInsumo(): BelongsTo
    {
        return $this->belongsTo(EventoInsumo::class, 'evento_insumo_id');
    }

    /**
     * Relación con el lote de insumo (Inventario).
     */
    public function loteInsumo(): BelongsTo
    {
        // Asumo que tu modelo se llama LoteInsumo
        return $this->belongsTo(LoteInsumo::class, 'lote_insumo_id');
    }
}