<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use MatanYadaev\EloquentSpatial\Objects\MultiPolygon;

class Lote extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lotes';

    protected $fillable = [
        'finca_id', 'nombre_lote', 'codigo_lote', 'area_hectareas_declaradas',
        'area_hectareas_gis', 'altitud_mediana_msnm', 'pendiente_promedio_porcentaje',
        'pendiente_terreno', 'tipo_suelo', 'ph_suelo', 'tiene_riego_instalado',
        'fuente_agua', 'tenencia', 'registro_ica', 'geometria_gps', 'activo'
    ];

    protected $casts = [
        'area_hectareas_declaradas'     => 'decimal:2',
        'area_hectareas_gis'            => 'decimal:4',
        'altitud_mediana_msnm'          => 'decimal:2',
        'pendiente_promedio_porcentaje' => 'decimal:2',
        'ph_suelo'                      => 'decimal:2',
        'tiene_riego_instalado'         => 'boolean',
        'activo'                        => 'boolean',
        'geometria_gps'                 => MultiPolygon::class,
    ];

    // --- RELACIONES ---

    public function finca(): BelongsTo
    {
        return $this->belongsTo(Finca::class, 'finca_id');
    }

    public function zonasManejo(): HasMany
    {
        return $this->hasMany(LoteZonaManejo::class, 'lote_id');
    }

    public function analiticasSuelo(): HasMany
    {
        return $this->hasMany(LoteAnaliticaSuelo::class, 'lote_id');
    }

    /**
     * Trae la analítica de suelo más reciente cargada en el sistema
     */
    public function ultimaAnaliticaSuelo(): HasOne
    {
        return $this->hasOne(LoteAnaliticaSuelo::class, 'lote_id')->latestOfMany('fecha_muestreo');
    }

    public function sistemasRiego(): HasMany
    {
        return $this->hasMany(LoteSistemaRiego::class, 'lote_id');
    }

    // --- SCOPES GEOGRÁFICOS (PostGIS raw query execution) ---

    /**
     * Scope para buscar si un punto GPS (Latitud/Longitud) cae dentro del polígono del lote
     */
    public function scopeContienePunto($query, $latitud, $longitud)
    {
        return $query->whereRaw("ST_Contains(geometria_gps, ST_GeomFromText('POINT(? ?)', 4326))", [$longitud, $latitud]);
    }
}