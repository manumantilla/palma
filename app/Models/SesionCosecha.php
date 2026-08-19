<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids; 
class SesionCosecha extends Model
{
    use HasFactory;
    use HasUuids;
    public $incrementing = false; 
    // Forzamos el nombre exacto de tu tabla
    protected $table = 'sesiones_cosecha';

    protected $fillable = [
        'orden_cosecha_id',
        'evento_campo_id',
        'responsable_id',
        'fecha',
        'estado',
        'meta_kg_dia',
        'numero_recolectores',
        'hora_inicio',
        'hora_fin',
        'total_recolectado_kg',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES (Relationships)
    |--------------------------------------------------------------------------
    */
    public function cicloProductivo()
    {
        return $this->hasOneThrough(
            CicloProductivo::class,
            SesionCosecha::class,
            'id',
            'id',
            'sesion_cosecha_id',
            'orden_cosecha_id'
        );
    }

    // Manera mas limpia 
    // public function getCicloProductivoAttribute()
    // {
    //     return $this->sesionCosecha?->ordenCosecha?->cicloProductivo;
    // }

    public function ordenCosecha(): BelongsTo
    {
        return $this->belongsTo(OrdenCosecha::class, 'orden_cosecha_id');
    }

    public function eventoCampo(): BelongsTo
    {
        return $this->belongsTo(EventoCampo::class, 'evento_campo_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES (Filtros dinámicos para el Index)
    |--------------------------------------------------------------------------
    */

    /**
     * Aplica filtros dinámicos a la consulta de sesiones de cosecha.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            // Filtrar por Estado (abierta / cerrada)
            ->when($filters['estado'] ?? null, function ($q, $estado) {
                $q->where('estado', $estado);
            })
            // Filtrar por Responsable
            ->when($filters['responsable_id'] ?? null, function ($q, $responsableId) {
                $q->where('responsable_id', $responsableId);
            })
            // Filtrar por Orden de Cosecha específica
            ->when($filters['orden_cosecha_id'] ?? null, function ($q, $ordenId) {
                $q->where('orden_cosecha_id', $ordenId);
            })
            // Filtrar por Evento de Campo específico
            ->when($filters['evento_campo_id'] ?? null, function ($q, $eventoId) {
                $q->where('evento_campo_id', $eventoId);
            })
            // Filtrar por un rango de fechas (Fecha Desde)
            ->when($filters['fecha_desde'] ?? null, function ($q, $desde) {
                $q->whereDate('fecha', '>=', $desde);
            })
            // Filtrar por un rango de fechas (Fecha Hasta)
            ->when($filters['fecha_hasta'] ?? null, function ($q, $hasta) {
                $q->whereDate('fecha', '<=', $hasta);
            });
    }
}