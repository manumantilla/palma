<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoInsumo extends Model
{
    use HasFactory;

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

    protected $casts = [
        'cantidad'      => 'decimal:2',
        'area_aplicada' => 'decimal:2',
        'costo_total'   => 'decimal:2',
    ];

    // Relationships
    public function eventoCampo()
    {
        return $this->belongsTo(EventoCampo::class);
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }

    public function eventoInsumoLotes()
    {
        return $this->hasMany(EventoInsumoLote::class);
    }
}