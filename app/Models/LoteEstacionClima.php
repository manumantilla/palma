<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoteEstacionClima extends Model
{
    protected $table = 'lotes_estacion_clima';

    protected $fillable = [
        'lote_id',
        'estacion_clima_id',
        'es_principal',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
    ];

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function estacionClima(): BelongsTo
    {
        return $this->belongsTo(EstacionClima::class, 'estacion_clima_id');
    }
}
