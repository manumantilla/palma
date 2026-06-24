<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrdenCosecha;
use App\Models\User;

class SesionCosecha extends Model
{
    protected $table = 'sesiones_cosecha';

    protected $fillable = [
        'orden_cosecha_id',
        'responsable_id',
        'fecha',
        'estado',
        'meta_kg_dia',
        'numero_recolectores',
        'hora_inicio',
        'hora_fin',
        'total_recolectado_kg'
    ];

    protected $casts = [
        'fecha' => 'date',
        'meta_kg_dia' => 'decimal:2',
        'total_recolectado_kg' => 'decimal:2',
    ];

    // --- RELACIONES ---

    public function ordenCosecha()
    {
        return $this->belongsTo(OrdenCosecha::class, 'orden_cosecha_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    // --- SCOPE PARA FILTROS DESDE EL REQUEST ---
    public function scopeFiltrar($query, array $filtros)
    {
        return $query->when($filtros['estado'] ?? null, function ($q, $estado) {
            $q->where('estado', $estado);
        })
        ->when($filtros['fecha'] ?? null, function ($q, $fecha) {
            $q->where('fecha', $fecha);
        })
        ->when($filtros['orden_id'] ?? null, function ($q, $ordenId) {
            $q->where('orden_cosecha_id', $ordenId);
        })
        ->when($filtros['responsable_id'] ?? null, function ($q, $responsableId) {
            $q->where('responsable_id', $responsableId);
        });
    }
}