<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'razon_social',
        'nombre_comercial',
        'tipo_persona',
        'nit',
        'dv',
        'contacto_nombre',
        'contacto_telefono',
        'contacto_email',
        'direccion_fiscal',
        'municipio',
        'cupo_credito',
        'saldo_actual',
        'dias_credito',
        'estado_cuenta',
        'cultivos_interes',
        'categoria_cliente',
        'autoriza_factura_electronica',
        'email_recepcion_facturas',
        'codigo_postal',
    ];

    protected $casts = [
        'cultivos_interes'             => 'array', // Maneja el campo JSON de forma nativa
        'autoriza_factura_electronica' => 'boolean',
        'cupo_credito'                 => 'decimal:2',
        'saldo_actual'                 => 'decimal:2',
        'deleted_at'                   => 'datetime',
    ];
}