<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialGenetico extends Model
{
    protected $table = 'materiales_geneticos';

    protected $fillable = [
        'cultivo_id',
        'nombre',
        'tipo',
        'casa_comercial',
        'registro_ica',
        'caracteristicas',
        'activo',
    ];

    protected $casts = [
        'caracteristicas' => 'array',
        'activo'          => 'boolean',
    ];

    public function cultivo(): BelongsTo
    {
        return $this->belongsTo(Cultivo::class, 'cultivo_id');
    }
}
