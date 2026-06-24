<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventoInsumo extends Model
{
    use HasFactory;

    // Por convención Laravel busca 'evento_insumos', especificamos por si acaso
    protected $table = 'evento_insumos';

    protected $fillable = [
        'evento_campo_id',
        'insumo_id',
        'cantidad',
        'area_aplicada',
        'metodo_aplicacion',
        'unidad_medida',
        'costo_total',
        'observaciones',
    ];

    /**
     * Relación con el Evento de Campo.
     */
    public function eventoCampo(): BelongsTo
    {
        return $this->belongsTo(EventoCampo::class, 'evento_campo_id');
    }

    /**
     * Relación con el Insumo.
     */
    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }

    /**
     * Relación con los lotes específicos de este insumo aplicado.
     */
    public function detallesLotes(): HasMany
    {
        return $this->hasMany(EventoInsumoLote::class, 'evento_insumo_id');
    }
}