<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaborPlantilla extends Model
{
    use HasFactory;

    protected $table = 'labores_plantilla';

    protected $fillable = [
        'cultivo_id',
        'nombre_labor',
        'descripcion',
        'momento_tipo',
        'dias_desde_siembra',
        'fenologia_etapa_id',
        'periodicidad_dias',
        'duracion_estimada_horas',
        'tipo_evento_id',
        'requiere_insumos',
        'requiere_mano_obra',
    ];

    /**
     * Convierte automáticamente los booleanos de la base de datos.
     */
    protected $casts = [
        'requiere_insumos' => 'boolean',
        'requiere_mano_obra' => 'boolean',
    ];

    /**
     * Obtener el cultivo al que pertenece esta plantilla de labor.
     */
    public function cultivo(): BelongsTo
    {
        return $this->belongsTo(Cultivo::class, 'cultivo_id');
    }

    /**
     * Obtener la etapa fenológica vinculada a esta labor (si aplica).
     */
    public function etapaFenologica(): BelongsTo
    {
        return $this->belongsTo(FenologiaEtapa::class, 'fenologia_etapa_id');
    }

    /**
     * Obtener el tipo de evento asociado a la labor.
     */
    public function tipoEvento(): BelongsTo
    {
        return $this->belongsTo(TipoEvento::class, 'tipo_evento_id');
    }
}