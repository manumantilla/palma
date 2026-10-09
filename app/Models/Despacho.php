<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Despacho extends Model
{
    protected $table = 'despachos';

    protected $fillable = [
        'numero_remision',
        'tipo_destino',
        'nombre_destino',
        'ciudad_destino',
        'departamento_destino',
        'cliente_id',
        'fecha_despacho',
        'fecha_estimada_llegada',
        'fecha_liquidado',
        'estado',
        'modalidad_precio',
        'precio_referencia_kg',
        'observaciones',
    ];

    protected $casts = [
        'fecha_despacho'         => 'datetime',
        'fecha_estimada_llegada' => 'datetime',
        'fecha_liquidado'        => 'datetime',
        'precio_referencia_kg'   => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
