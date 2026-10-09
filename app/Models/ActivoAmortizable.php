<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivoAmortizable extends Model
{
    protected $table = 'activos_amortizables';

    protected $fillable = [
        'nombre',
        'tipo',
        'maquinaria_id',
        'lote_id',
        'ciclo_productivo_id',
        'cuenta_puc',
        'fecha_inicio_capitalizacion',
        'fecha_inicio_amortizacion',
        'valor_inicial',
        'valor_residual',
        'vida_util_meses',
        'metodo',
        'unidades_estimadas_total',
        'estado',
        'fecha_baja',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio_capitalizacion' => 'date',
        'fecha_inicio_amortizacion'   => 'date',
        'valor_inicial'               => 'decimal:2',
        'valor_residual'              => 'decimal:2',
        'unidades_estimadas_total'    => 'decimal:2',
        'fecha_baja'                  => 'date',
    ];

    public function maquinaria(): BelongsTo
    {
        return $this->belongsTo(Maquinaria::class, 'maquinaria_id');
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function cicloProductivo(): BelongsTo
    {
        return $this->belongsTo(CicloProductivo::class, 'ciclo_productivo_id');
    }
}
