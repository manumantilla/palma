<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FenologiaEtapa extends Model
{
    protected $table = 'fenologia_etapas';

    protected $fillable = [
        'cultivo_id',
        'material_genetico_id',
        'etapa_padre_id',
        'tipo_fase',
        'nombre',
        'descripcion',
        'orden',
        'bbch_inicio',
        'bbch_fin',
        'duracion_dias_desde_inicio',
        'duracion_dias_estimada',
        'duracion_dias_min',
        'duracion_dias_max',
        'grados_dia_requeridos',
        'temperatura_base_c',
        'periodicidad_dias',
        'mes_inicio_tipico',
        'es_cosechable',
        'es_critica',
    ];

    protected $casts = [
        'orden'                 => 'integer',
        'bbch_inicio'           => 'integer',
        'bbch_fin'              => 'integer',
        'grados_dia_requeridos' => 'decimal:1',
        'temperatura_base_c'    => 'decimal:1',
        'es_cosechable'         => 'boolean',
        'es_critica'            => 'boolean',
    ];

    public function cultivo(): BelongsTo
    {
        return $this->belongsTo(Cultivo::class);
    }

    /** Null = la etapa aplica a todos los materiales del cultivo. */
    public function materialGenetico(): BelongsTo
    {
        return $this->belongsTo(MaterialGenetico::class, 'material_genetico_id');
    }

    public function padre(): BelongsTo
    {
        return $this->belongsTo(self::class, 'etapa_padre_id');
    }

    public function subetapas(): HasMany
    {
        return $this->hasMany(self::class, 'etapa_padre_id');
    }

    public function recomendaciones(): HasMany
    {
        return $this->hasMany(FenologiaRecomendacion::class);
    }

    public function historiales(): HasMany
    {
        return $this->hasMany(CicloEtapaHistorial::class);
    }
}
