<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartaPorte extends Model
{
    protected $table = 'carta_porte';

    protected $fillable = [
        'despacho_id',
        'numero_carta_porte',
        'tipo_transportador',
        'transportador_id',
        'nombre_conductor',
        'cedula_conductor',
        'telefono_conductor',
        'placa_vehiculo',
        'tipo_vehiculo',
        'placa_trailer',
        'municipio_origen',
        'departamento_origen',
        'municipio_destino',
        'departamento_destino',
        'ruta_descripcion',
        'valor_flete',
        'quien_paga_flete',
        'forma_pago_flete',
        'peso_declarado_kg',
        'descripcion_carga',
        'numero_sello',
        'fecha_salida',
        'fecha_llegada_real',
        'observaciones',
    ];

    protected $casts = [
        'valor_flete'        => 'decimal:2',
        'peso_declarado_kg'  => 'decimal:2',
        'fecha_salida'       => 'datetime',
        'fecha_llegada_real' => 'datetime',
    ];

    public function despacho(): BelongsTo
    {
        return $this->belongsTo(Despacho::class, 'despacho_id');
    }

    public function transportador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transportador_id');
    }
}
