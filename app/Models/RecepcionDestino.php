<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecepcionDestino extends Model
{
    protected $table = 'recepciones_destino';

    protected $fillable = [
        'despacho_id',
        'fecha_recepcion',
        'recibido_por',
        'peso_recibido_kg',
        'merma_transito_kg',
        'estado_carga',
        'novedad_descripcion',
        'kg_rechazados',
        'motivo_rechazo',
        'observaciones',
    ];

    protected $casts = [
        'fecha_recepcion'   => 'datetime',
        'peso_recibido_kg'  => 'decimal:2',
        'merma_transito_kg' => 'decimal:2',
        'kg_rechazados'     => 'decimal:2',
    ];

    public function despacho(): BelongsTo
    {
        return $this->belongsTo(Despacho::class, 'despacho_id');
    }
}
