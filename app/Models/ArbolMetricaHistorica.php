<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArbolMetricaHistorica extends Model
{
    use HasFactory;

    protected $table = 'arboles_metricas_historicas';

    protected $fillable = [
        'arbol_id', 'fecha_medicion', 'altura_metros', 'diametro_tronco_cm',
        'diametro_copa_proyeccion_m', 'volumen_copa_calculado_m3',
        'indice_ndvi_medido', 'indice_ndre_medido', 'temperatura_canopia_celsius',
        'codigo_escala_bbch', 'origen_datos'
    ];

    protected $casts = [
        'fecha_medicion'               => 'datetime',
        'altura_metros'                => 'decimal:2',
        'diametro_tronco_cm'           => 'decimal:2',
        'diametro_copa_proyeccion_m'   => 'decimal:2',
        'volumen_copa_calculado_m3'    => 'decimal:2',
        'indice_ndvi_medido'           => 'decimal:3',
        'indice_ndre_medido'           => 'decimal:3',
        'temperatura_canopia_celsius'  => 'decimal:2',
        'codigo_escala_bbch'           => 'integer',
    ];

    public function arbol(): BelongsTo
    {
        return $this->belongsTo(Arbol::class, 'arbol_id');
    }
}