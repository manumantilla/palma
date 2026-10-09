<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarifaRetencion extends Model
{
    protected $table = 'tarifas_retencion';

    protected $fillable = [
        'anio',
        'tipo',
        'concepto',
        'descripcion',
        'base_minima_uvt',
        'tarifa_pct',
        'municipio',
        'aplica_declarante',
    ];

    protected $casts = [
        'base_minima_uvt'   => 'decimal:2',
        'tarifa_pct'        => 'decimal:3',
        'aplica_declarante' => 'boolean',
    ];
}
