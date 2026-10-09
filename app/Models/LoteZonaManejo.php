<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class LoteZonaManejo extends Model
{
    use HasFactory;

    protected $table = 'lotes_zonas_manejo';

    protected $fillable = [
        'lote_id',
        'nombre_zona',
        'codigo_zona',
        'area_hectareas',
        'geometria_zona',
    ];

    protected $casts = [
        'area_hectareas' => 'decimal:4',
        // Acepta un Polygon o DB::raw("ST_GeomFromText(...)"); un WKT plano debe convertirse con Polygon::fromWkt()
        'geometria_zona' => Polygon::class,
    ];

    // Relación con el lote
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }
}
