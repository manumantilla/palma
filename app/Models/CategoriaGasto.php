<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaGasto extends Model
{
    protected $table = 'categorias_gasto';

    protected $fillable = [
        'codigo',
        'nombre',
        'cuenta_puc',
        'clase_costo_default',
        'imputacion_default',
        'origen_automatico',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
