<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contenedor extends Model
{
    protected $table = 'contenedores';

    protected $fillable = [
        'sesion_id',
        'cliente_id',
        'orden_pedido_id',
        'estado',
        'nombre',
        'tipo_destino',
        'variedad',
        'calidad',
        'calibre_talla',
        'kilos_acumulados',
        'peso_tara',
        'peso_total' // Enviado o ajustado desde el frontend
    ];

    protected $casts = [
        'kilos_acumulados' => 'decimal:2',
        'peso_tara' => 'decimal:2',
        'peso_total' => 'decimal:2',
    ];

    // --- RELACIONES ---
    
    public function sesionCosecha()
    {
        return $this->belongsTo(SesionCosecha::class, 'sesion_id');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function ordenPedido()
    {
        return $this->belongsTo(OrdenCosecha::class, 'orden_pedido_id');
    }

    public function movimientosClasificacion()
    {
        return $this->hasMany(MovimientoClasificacion::class, 'contenedor_id');
    }

    // --- SCOPE DE FILTRADO DINÁMICO ---
    public function scopeFiltrar($query, array $filtros)
    {
        return $query->when($filtros['estado'] ?? null, function ($q, $estado) {
            $q->where('estado', $estado);
        })
        ->when($filtros['tipo_destino'] ?? null, function ($q, $destino) {
            $q->where('tipo_destino', $destino);
        })
        ->when($filtros['calidad'] ?? null, function ($q, $calidad) {
            $q->where('calidad', $calidad);
        })
        ->when($filtros['calibre_talla'] ?? null, function ($q, $calibre) {
            $q->where('calibre_talla', $calibre);
        })
        ->when($filtros['sesion_id'] ?? null, function ($q, $sesionId) {
            $q->where('sesion_id', $sesionId);
        })
        ->when($filtros['buscar'] ?? null, function ($q, $buscar) {
            $q->where('nombre', 'LIKE', "%{$buscar}%")
              ->orWhere('variedad', 'LIKE', "%{$buscar}%");
        });
    }
}