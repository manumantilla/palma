<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoRiego extends Model
{
    protected $table = 'eventos_riego';

    protected $fillable = [
        'sistema_riego_id',
        'responsable_id',
        'fecha_hora_inicio',
        'fecha_hora_fin',
        'duracion_total_minutos',
        'presion_promedio_psi',
        'caudal_estimado_litros_minuto',
        'volumen_estimado_litros',
        'estado',
    ];

    protected $casts = [
        'fecha_hora_inicio'             => 'datetime',
        'fecha_hora_fin'                => 'datetime',
        'presion_promedio_psi'          => 'decimal:2',
        'caudal_estimado_litros_minuto' => 'decimal:2',
        'volumen_estimado_litros'       => 'decimal:2',
    ];

    public function sistemaRiego(): BelongsTo
    {
        return $this->belongsTo(SistemaRiego::class, 'sistema_riego_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
}
