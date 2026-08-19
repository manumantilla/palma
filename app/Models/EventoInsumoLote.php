<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoInsumoLote extends Model
{
    use HasFactory;

    protected $table = 'evento_insumo_lotes';

    protected $fillable = [
        'evento_insumo_id',
        'lote_insumo_id',
        'cantidad',
        'precio',
        'area_aplicada',
    ];

    protected $casts = [
        'cantidad'      => 'decimal:2',
        'precio'        => 'decimal:2',
        'area_aplicada' => 'decimal:2',
    ];

    // Relationships
    public function eventoInsumo()
    {
        return $this->belongsTo(EventoInsumo::class);
    }

    public function loteInsumo()
    {
        return $this->belongsTo(LoteInsumo::class);
    }
}