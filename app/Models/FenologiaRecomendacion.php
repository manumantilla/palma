<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FenologiaRecomendacion extends Model
{
    use SoftDeletes;

    protected $table = 'fenologia_recomendaciones';

    protected $fillable = [
        'fenologia_etapa_id',
        'tipo_evento_id',
        'titulo',
        'descripcion',
        'prioridad',
        'dias_offset',
        'ventana_ejecucion_dias',
        'genera_evento_automatico',
        'requiere_verificacion_campo',
        'instrucciones_tecnicas',
    ];

    protected $casts = [
        'genera_evento_automatico' => 'boolean',
        'requiere_verificacion_campo' => 'boolean',
        'dias_offset' => 'integer',
        'ventana_ejecucion_dias' => 'integer',
    ];

    public function etapa(): BelongsTo
    {
        return $this->belongsTo(FenologiaEtapa::class, 'fenologia_etapa_id');
    }

    public function tipoEvento(): BelongsTo
    {
        return $this->belongsTo(TipoEvento::class, 'tipo_evento_id');
    }
}