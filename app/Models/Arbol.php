<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Arbol extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'arboles';

    protected $fillable = [
        'ciclo_productivo_id', 'lote_id', 'lote_zona_manejo_id', 'codigo_unico',
        'fila_indice', 'posicion_indice', 'altitud', 'estado_vital', 'etapa_biologica',
        'fecha_baja_muerte', 'motivo_baja', 'altitud_ortometrica_msnm', 'fecha_siembra',
        'fecha_primera_cosecha', 'variedad', 'fecha_muerte', 'causa_muerte',
        'es_reemplazo', 'fecha_reemplazo', 'produccion_acumulada_kg', 
        'ciclos_productivos_count', 'observaciones'
    ];

    protected $casts = [
        'fila_indice'               => 'integer',
        'posicion_indice'           => 'integer',
        'altitud'                   => 'decimal:2',
        'altitud_ortometrica_msnm'  => 'decimal:2',
        'fecha_baja_muerte'         => 'date',
        'fecha_siembra'             => 'date',
        'fecha_primera_cosecha'     => 'date',
        'fecha_muerte'              => 'date',
        'fecha_reemplazo'           => 'date',
        'es_reemplazo'              => 'boolean',
        'produccion_acumulada_kg'   => 'decimal:2',
        'ciclos_productivos_count'  => 'integer',
    ];

    // --- RELACIONES ---

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function zonaManejo(): BelongsTo
    {
        return $this->belongsTo(LoteZonaManejo::class, 'lote_zona_manejo_id');
    }

    public function cicloProductivo(): BelongsTo
    {
        return $this->belongsTo(CicloProductivo::class, 'ciclo_productivo_id');
    }
    
    public function metricasHistoricas(): HasMany
    {
        return $this->hasMany(ArbolMetricaHistorica::class, 'arbol_id');
    }

    public function historialFitosanitario(): HasMany
    {
        return $this->hasMany(ArbolHistorialFitosanitario::class, 'arbol_id');
    }

    // Un arbol puede pertenecer a muchos eventos
    public function eventos()
    {
        return $this->belongsToMany(EventoCampo::class, 'evento_arbol', 'arbol_id', 'evento_campo_id')
                    ->withPivot('novedad_arbol', 'nota_individual');
    }

    // --- MUTATORS (Setters) ---

    /**
     * Mutator para asignar la geometría espacial PostGIS desde un String WKT (POINT(long lat))
     */
    public function setCoordenadaPrecisionAttribute($value)
    {
        if (is_string($value) && str_starts_with(strtoupper($value), 'POINT')) {
            $this->attributes['coordenada_precision'] = DB::raw("ST_GeomFromText('{$value}', 4326)");
        } else {
            $this->attributes['coordenada_precision'] = $value;
        }
    }

    // --- GETTERS / ACCESSORS ---

    /**
     * Calcula dinámicamente la edad fisiológica en meses sin guardar datos estáticos
     */
    public function getEdadMesesAttribute(): int
    {
        if (!$this->fecha_siembra) return 0;
        
        $fechaFin = $this->fecha_baja_muerte ?? $this->fecha_muerte ?? now();
        return (int) $this->fecha_siembra->diffInMonths($fechaFin);
    }

    /**
     * Retorna las coordenadas en un formato array limpio [long, lat] para mapas GIS
     */
    public function getCoordenadasGpsAttribute(): ?array
    {
        if (!$this->id) return null;

        $punto = DB::table('arboles')
            ->selectRaw('ST_X(coordenada_precision) as lng, ST_Y(coordenada_precision) as lat')
            ->where('id', $this->id)
            ->first();

        return $punto ? [$punto->lng, $punto->lat] : null;
    }

    /**
     * Label estético para control de sanidad vegetal
     */
    public function getEstadoVitalBadgeAttribute(): string
    {
        return match ($this->estado_vital) {
            'excelente'       => '🟢 Excelente',
            'con_estres'      => '🟡 Estrés / Alerta',
            'enfermo_critico' => '🔴 Crítico / Enfermo',
            'muerto'          => '⚫ Muerto',
            'erradicado'      => '🚫 Erradicado',
            default           => 'Desconocido',
        };
    }

    //soft delete
    public function delete()
    {
        parent::delete();
    }


    /**
     * Conexiones salientes de la red donde este árbol es el origen.
     */
    public function redVecindadOrigen(): HasMany
    {
        return $this->hasMany(ArbolRedVecindad::class, 'arbol_origen_id');
    }

    /**
     * Conexiones entrantes de la red donde este árbol es el destino.
     */
    public function redVecindadDestino(): HasMany
    {
        return $this->hasMany(ArbolRedVecindad::class, 'arbol_destino_id');
    }

    /**
     * Relación directa Muchos a Muchos con los árboles vecinos que puede infectar/afectar.
     */
    public function arbolesVecinos(): BelongsToMany
    {
        return $this->belongsToMany(
            Arbol::class,
            'arboles_red_vecindad',
            'arbol_origen_id',
            'arbol_destino_id'
        )->withPivot(['distancia_metros', 'probabilidad_contagio_base', 'tipo_contacto'])
        ->withTimestamps();
    }
}