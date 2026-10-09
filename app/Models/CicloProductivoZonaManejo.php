<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class CicloProductivoZonaManejo extends Model
{
    protected $table = 'ciclo_productivo_zona_manejo';

    protected $fillable = [
        'ciclo_productivo_id',
        'zona_id',
        'toneladas_producidas',
        'area_hectareas_momento',
        'geometria_zona_momento',
    ];

    protected $casts = [
        'toneladas_producidas'   => 'decimal:2',
        'area_hectareas_momento' => 'decimal:4',
        'geometria_zona_momento' => Polygon::class,
    ];

    public function cicloProductivo(): BelongsTo
    {
        return $this->belongsTo(CicloProductivo::class, 'ciclo_productivo_id');
    }

    public function zona(): BelongsTo
    {
        return $this->belongsTo(LoteZonaManejo::class, 'zona_id');
    }
}
