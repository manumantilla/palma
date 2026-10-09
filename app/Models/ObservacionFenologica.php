<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MatanYadaev\EloquentSpatial\Objects\Point;

class ObservacionFenologica extends Model
{
    use HasUuids;

    protected $table = 'observaciones_fenologicas';

    protected $fillable = [
        'ciclo_productivo_id',
        'ciclo_etapa_id',
        'fenologia_etapa_id',
        'zona_id',
        'arbol_id',
        'fecha_observacion',
        'bbch_codigo',
        'plantas_muestreadas',
        'plantas_en_etapa',
        'conteos',
        'coordenada_gps',
        'foto_evidencia',
        'observaciones',
        'user_id',
        'client_updated_at',
        'synced_at',
    ];

    protected $casts = [
        'fecha_observacion'   => 'datetime',
        'porcentaje_en_etapa' => 'decimal:2',
        'conteos'             => 'array',
        'coordenada_gps'      => Point::class,
        'client_updated_at'   => 'datetime',
        'synced_at'           => 'datetime',
    ];

    public function cicloProductivo(): BelongsTo
    {
        return $this->belongsTo(CicloProductivo::class, 'ciclo_productivo_id');
    }

    public function cicloEtapa(): BelongsTo
    {
        return $this->belongsTo(CicloEtapaHistorial::class, 'ciclo_etapa_id');
    }

    public function fenologiaEtapa(): BelongsTo
    {
        return $this->belongsTo(FenologiaEtapa::class, 'fenologia_etapa_id');
    }

    public function zona(): BelongsTo
    {
        return $this->belongsTo(LoteZonaManejo::class, 'zona_id');
    }

    public function arbol(): BelongsTo
    {
        return $this->belongsTo(Arbol::class, 'arbol_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
