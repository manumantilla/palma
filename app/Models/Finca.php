<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Finca extends Model
{
    protected $table = 'fincas';

    protected $fillable = [
        'nombre',
        'ubicacion',
    ];

    public function lotes(): HasMany
    {
        return $this->hasMany(Lote::class, 'finca_id');
    }

    public function estacionesClima(): HasMany
    {
        return $this->hasMany(EstacionClima::class, 'finca_id');
    }
}
