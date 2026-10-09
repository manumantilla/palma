<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoMaquinaria extends Model
{
    protected $table = 'evento_maquinaria';

    protected $fillable = [
        'evento_id',
        'maquina_id',
        'trabajador_id',
        'estado',
        'horometro_inicial',
        'horometro_final',
        'horas_trabajadas',
        'tipo_combustible',
        'litros_consumidos',
        'costo_total',
        'observaciones',
    ];

    protected $casts = [
        'horometro_inicial' => 'decimal:2',
        'horometro_final'   => 'decimal:2',
        'horas_trabajadas'  => 'decimal:2',
        'litros_consumidos' => 'decimal:2',
        'costo_total'       => 'decimal:2',
    ];

    public function evento(): BelongsTo
    {
        return $this->belongsTo(EventoCampo::class, 'evento_id');
    }

    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquinaria::class, 'maquina_id');
    }

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'trabajador_id');
    }
}
