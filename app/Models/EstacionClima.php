<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MatanYadaev\EloquentSpatial\Objects\Point;

class EstacionClima extends Model
{
    protected $table = 'estaciones_clima';

    protected $fillable = [
        'finca_id',
        'nombre',
        'tipo',
        'codigo_externo',
        'ubicacion',
        'altitud_msnm',
        'activo',
    ];

    protected $casts = [
        'ubicacion'    => Point::class,
        'altitud_msnm' => 'decimal:2',
        'activo'       => 'boolean',
    ];

    public function finca(): BelongsTo
    {
        return $this->belongsTo(Finca::class, 'finca_id');
    }
}
