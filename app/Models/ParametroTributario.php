<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParametroTributario extends Model
{
    protected $table = 'parametros_tributarios';

    protected $fillable = [
        'anio',
        'uvt',
        'smmlv',
        'auxilio_transporte',
        'pct_cesantias',
        'pct_intereses_cesantias',
        'pct_prima',
        'pct_vacaciones',
        'pct_salud_empleador',
        'pct_pension_empleador',
        'pct_arl',
        'pct_caja_compensacion',
        'pct_icbf',
        'pct_sena',
        'pct_cuota_fomento',
        'pct_gmf',
    ];

    protected $casts = [
        'uvt'                     => 'decimal:2',
        'smmlv'                   => 'decimal:2',
        'auxilio_transporte'      => 'decimal:2',
        'pct_cesantias'           => 'decimal:2',
        'pct_intereses_cesantias' => 'decimal:2',
        'pct_prima'               => 'decimal:2',
        'pct_vacaciones'          => 'decimal:2',
        'pct_salud_empleador'     => 'decimal:2',
        'pct_pension_empleador'   => 'decimal:2',
        'pct_arl'                 => 'decimal:3',
        'pct_caja_compensacion'   => 'decimal:2',
        'pct_icbf'                => 'decimal:2',
        'pct_sena'                => 'decimal:2',
        'pct_cuota_fomento'       => 'decimal:2',
        'pct_gmf'                 => 'decimal:3',
    ];
}
