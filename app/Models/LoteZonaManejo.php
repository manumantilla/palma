<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoteZonaManejo extends Model
{
    use HasFactory;

    protected $table = 'lotes_zonas_manejo';

    protected $fillable = [
        'lote_id',
        'nombre_zona',
        'codigo_zona',
        'area_hectareas',
        'geometria_zona',
    ];

    // Relación con el lote
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }
}