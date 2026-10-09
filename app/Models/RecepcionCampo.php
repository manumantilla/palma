<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MatanYadaev\EloquentSpatial\Objects\Point;

class RecepcionCampo extends Model
{
    use HasUuids;

    protected $table = 'recepciones_campo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'sesion_cosecha_id',
        'lote_zona_id',
        'trabajador_id',
        'peso_bruto',
        'tara_costal',
        'peso_neto',
        'hora_pesaje',
        'ubicacion_gps',
        'foto_evidencia',
        'metodo_pesaje',
        'costal_codigo',
        'numero_corte',
        'estado_clasificacion',
        'client_updated_at',
        'synced_at',
    ];

    protected $casts = [
        'hora_pesaje'        => 'datetime',
        'client_updated_at'  => 'datetime',
        'synced_at'          => 'datetime',
        'peso_bruto'         => 'decimal:2',
        'tara_costal'        => 'decimal:2',
        'peso_neto'          => 'decimal:2',
        'numero_corte'       => 'integer',
        'ubicacion_gps'      => Point::class,
    ];

    public function getLoteAttribute()
    {
        return $this->sesionCosecha?->lote;
    }

    /**
     * Obtiene directamente el ID del lote.
     */
    public function getLoteIdAttribute()
    {
        return $this->sesionCosecha?->ordenCosecha?->lote_cultivo_id;
    }

    public function sesionCosecha(): BelongsTo
    {
        return $this->belongsTo(SesionCosecha::class, 'sesion_cosecha_id');
    }

    public function zonaManejo(): BelongsTo
    {
        return $this->belongsTo(LoteZonaManejo::class, 'lote_zona_id');
    }

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'trabajador_id');
    }

    /**
     * Árboles de los que salió el pesaje (pivote recepcion_arboles con PK UUID).
     * Uso: $recepcion->arboles()->attach($arbolId, ['peso_estimado_kg' => 12.5]);
     */
    public function arboles(): BelongsToMany
    {
        return $this->belongsToMany(Arbol::class, 'recepcion_arboles', 'recepcion_campo_id', 'arbol_id')
            ->using(RecepcionArbol::class)
            ->withPivot('id', 'peso_estimado_kg')
            ->withTimestamps();
    }

    /**
     * La merma en campo se registra en la tabla mermas (recepcion_campo_id),
     * no en una columna de la recepción.
     */
    public function mermas(): HasMany
    {
        return $this->hasMany(Merma::class, 'recepcion_campo_id');
    }

    public function movimientosClasificacion(): HasMany
    {
        return $this->hasMany(MovimientoClasificacion::class, 'recepcion_campo_id');
    }
}
