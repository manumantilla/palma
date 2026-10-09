<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DespachoItem extends Model
{
    protected $table = 'despacho_items';

    protected $fillable = [
        'despacho_id',
        'contenedor_id',
        'ciclo_productivo_id',
        'variedad',
        'calidad',
        'tipo_empaque',
        'cantidad_unidades',
        'peso_promedio_unidad',
        'peso_bruto_total',
        'tara_total',
        'precio_unitario_kg',
        'precio_liquidado_kg',
        'descuento_kg',
        'motivo_descuento',
        'descripcion',
    ];

    protected $casts = [
        'peso_promedio_unidad' => 'decimal:2',
        'peso_bruto_total'     => 'decimal:2',
        'tara_total'           => 'decimal:2',
        'peso_neto_total'      => 'decimal:2',
        'precio_unitario_kg'   => 'decimal:2',
        'precio_liquidado_kg'  => 'decimal:2',
        'descuento_kg'         => 'decimal:2',
    ];

    public function despacho(): BelongsTo
    {
        return $this->belongsTo(Despacho::class, 'despacho_id');
    }

    public function contenedor(): BelongsTo
    {
        return $this->belongsTo(Contenedor::class, 'contenedor_id');
    }

    public function cicloProductivo(): BelongsTo
    {
        return $this->belongsTo(CicloProductivo::class, 'ciclo_productivo_id');
    }
}
