<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    protected $table = 'unidades_medida';

    protected $fillable = [
        'nombre',
        'abreviatura',
        'tipo',
        'factor_conversion',
    ];

    protected $casts = [
        'factor_conversion' => 'decimal:4',
    ];
}
