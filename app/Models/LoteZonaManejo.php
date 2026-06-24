<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoteZonaManejo extends Model
{
    use HasFactory;

    protected $table = 'lotes_zonas_manejo';

    protected $fillable = [
        'lote_id', 'nombre_zona', 'codigo_zona', 'area_hectareas', 'geometria_zona'
    ];

    protected $casts = [
        'area_hectareas' => 'decimal:4',
    ];

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }
}