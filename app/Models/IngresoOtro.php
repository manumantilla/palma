<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IngresoOtro extends Model
{
    protected $table = 'ingresos_otros';

    protected $fillable = [
        'tipo',
        'cliente_id',
        'tercero_nombre',
        'lote_id',
        'ciclo_productivo_id',
        'concepto',
        'cuenta_puc',
        'numero_soporte',
        'fecha',
        'fecha_vencimiento',
        'valor_bruto',
        'valor_iva',
        'retefuente',
        'reteica',
        'valor_recibido',
        'estado',
        'user_id',
    ];

    protected $casts = [
        'fecha'             => 'date',
        'fecha_vencimiento' => 'date',
        'valor_bruto'       => 'decimal:2',
        'valor_iva'         => 'decimal:2',
        'retefuente'        => 'decimal:2',
        'reteica'           => 'decimal:2',
        'valor_neto'        => 'decimal:2',
        'valor_recibido'    => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function cicloProductivo(): BelongsTo
    {
        return $this->belongsTo(CicloProductivo::class, 'ciclo_productivo_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
