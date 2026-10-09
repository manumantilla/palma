<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Models\LoteZonaManejo;
use MatanYadaev\EloquentSpatial\Objects\Geometry;
class EventoCampo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'eventos_campo';

    protected $fillable = [
        'ciclo_productivo_id',
        'lote_id',
        'zona_id',
        'hora_inicio',
        'hora_fin',
        'tipo_evento_id',
        'origen',
        'origen_id',
        'numero_repeticion',
        'fecha_programada',
        'fecha_limite',
        'ciclo_etapa_id',
        'fecha_ejecucion',
        'coordenada_gps',
        'estado',
        'observaciones',
    ];
    protected $casts = [
        'hora_inicio'       => 'datetime:H:i:s',
        'hora_fin'          => 'datetime:H:i:s',
        'fecha_programada'  => 'datetime',
        'fecha_limite'      => 'datetime',
        'fecha_ejecucion'   => 'datetime',
        'numero_repeticion' => 'integer',
        // Columna GEOMETRY genérica (SRID 4326)
        'coordenada_gps'    => Geometry::class,
        'estado'            => 'string',
        'deleted_at'        => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    // ==========================================
    // RELACIONES DE PERTENENCIA (BelongsTo)
    // ==========================================
    
    public function cicloProductivo(): BelongsTo
    {
        return $this->belongsTo(CicloProductivo::class, 'ciclo_productivo_id');
    }

    public function cicloEtapa()
    {
        return $this->belongsTo(CicloEtapaHistorial::class, 'ciclo_etapa_id');
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }

    public function zona(): BelongsTo
    {
        return $this->belongsTo(LoteZonaManejo::class, 'zona_id');
    }

    public function tipoEvento(): BelongsTo
    {
        return $this->belongsTo(TipoEvento::class);
    }

    // ==========================================
    // RELACIONES MANY-TO-MANY (Pivot)
    // ==========================================

    public function arboles(): BelongsToMany
    {
        return $this->belongsToMany(Arbol::class, 'evento_arbol', 'evento_campo_id', 'arbol_id')
                    ->withPivot(['novedad_arbol', 'nota_individual'])
                    ->withTimestamps();
    }

    // ==========================================
    // RELACIONES ONE-TO-MANY (HasMany)
    // ==========================================

    public function eventoInsumos(): HasMany
    {
        return $this->hasMany(EventoInsumo::class, 'evento_campo_id');
    }

    public function eventoArboles()
    {
        return $this->hasMany(EventoArbol::class, 'evento_campo_id');
    }

    public function eventoManoObra(): HasMany
    {
        return $this->hasMany(EventoManoObra::class, 'evento_campo_id');
    }

    // ==========================================
    // ACCESSORS PARA CÁLCULOS FINANCIEROS
    // ==========================================

    public function getCostoTotalInsumosAttribute(): float
    {
        // 'costo_total' ya existe como columna en 'evento_insumos'
        return (float) $this->eventoInsumos->sum('costo_total');
    }

    public function getCostoTotalManoObraAttribute(): float
    {
        // Se calcula iterando en memoria la colección ya cargada (no genera queries)
        return (float) $this->eventoManoObra->sum(function ($item) {
            return $item->cantidad * $item->valor_unitario;
        });
    }

    public function getCostoTotalEventoAttribute(): float
    {
        return $this->costo_total_insumos + $this->costo_total_mano_obra;
    }

    public function scopeFiltrar(Builder $query, array $filtros)
    {
        return $query
            ->when($filtros['ciclo_productivo_id'] ?? null, function ($q, $cicloId) {
                $q->where('ciclo_productivo_id', $cicloId);
            })
            ->when($filtros['lote_id'] ?? null, function ($q, $loteId) {
                $q->where('lote_id', $loteId);
            })
            ->when($filtros['zona_id'] ?? null, function ($q, $zonaId) {
                $q->where('zona_id', $zonaId);
            })
            ->when($filtros['tipo_evento_id'] ?? null, function ($q, $tipoId) {
                $q->where('tipo_evento_id', $tipoId);
            })
            ->when($filtros['estado'] ?? null, function ($q, $estado) {
                $q->where('estado', $estado);
            })
            ->when($filtros['fecha_inicio'] ?? null, function ($q, $fechaInicio) {
                $q->whereDate('fecha_programada', '>=', $fechaInicio);
            })
            ->when($filtros['fecha_fin'] ?? null, function ($q, $fechaFin) {
                $q->whereDate('fecha_programada', '<=', $fechaFin);
            });
    }

}
