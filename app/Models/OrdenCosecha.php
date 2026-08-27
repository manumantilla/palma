<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class OrdenCosecha extends Model
{
    use HasFactory;

    // Forzamos el nombre correcto de la tabla
    protected $table = 'ordenes_cosecha';

    protected $fillable = [
        'cliente_id',
        'ciclo_productivo_id',
        'lote_cultivo_id',
        'lote_zona_id',
        'fecha_programada',
        'fecha_entrega',
        'responsable_id',
        'cantidad_solicitada_kg',
        'variedad_requerida',
        'cantidad_planificada_kg',
        'cantidad_recolectada_kg',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'notas',
    ];

    protected $casts = [
        'fecha_programada' => 'date',
        'fecha_entrega'    => 'date',
        'fecha_inicio'     => 'date',
        'fecha_fin'        => 'date',
    ];



    /**
     * Relación con el Cliente (Usuario)
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    /**
     * Relación con el Responsable (Usuario)
     */
    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Relación con el Ciclo Productivo
     */
    public function cicloProductivo(): BelongsTo
    {
        return $this->belongsTo(CicloProductivo::class, 'ciclo_productivo_id');
    }

    public function loteCultivo(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_cultivo_id');
    }


    public function loteZona(): BelongsTo
    {
        return $this->belongsTo(LoteZonaManejo::class, 'lote_zona_id');
    }

    // Relacion para el nombre del cutlivo
    public function getNombreCultivoAttribute(): string
    {
        // El operador ?-> (null-safe) evita el error si alguna relación viene nula
        return $this->cicloProductivo?->cultivo?->nombre ?? 'N/A';
    }
}