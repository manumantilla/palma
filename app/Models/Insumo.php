<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Insumo extends Model
{
    use HasFactory;

    protected $table = 'insumos';

    protected $fillable = [
        'categoria_id',
        'nombre',
        'ingrediente_principal',
        'unidad_base',
        'factor_conversion',
        'nivel_toxicidad',
        'estado',
        'rei_horas',
        'phi_dias',
        'clasificacion_toxicologica',
        'equipo_proteccion',
        'franja_color',
        'almacenamiento_temp_min',
        'almacenamiento_temp_max',
        'almacenamiento_humedad',
        'requiere_refrigeracion',
        'sensible_luz',
        'stock_minimo',
        'dias_aviso_vencimiento',
    ];

    protected $casts = [
        'equipo_proteccion'     => 'array', // Guarda múltiples EPPs como ['Guantes', 'Careta']
        'requiere_refrigeracion'=> 'boolean',
        'sensible_luz'          => 'boolean',
        'factor_conversion'     => 'decimal:4',
        'stock_minimo'          => 'decimal:2',
        'rei_horas'             => 'integer',
        'phi_dias'              => 'integer',
        'dias_aviso_vencimiento'=> 'integer',
    ];

    // --- RELACIONES ---

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaInsumo::class, 'categoria_id');
    }

    public function componentes(): HasMany
    {
        return $this->hasMany(InsumoComponente::class, 'insumo_id');
    }

    public function lotes(): HasMany
    {
        return $this->hasMany(LoteInsumo::class, 'insumo_id');
    }

    // --- ACCESSORS Y LÓGICA DE NEGOCIO (AGRO) ---

    /**
     * Calcula el stock total sumando todos los lotes activos/cuarentena
     */
    public function getStockTotalAttribute(): float
    {
        return (float) $this->lotes()
            ->whereIn('estado', ['activo', 'cuarentena'])
            ->sum('cantidad_actual');
    }

    /**
     * Alerta si el insumo cayó por debajo del stock mínimo configurado
     */
    public function getRequiereReabastecimientoAttribute(): bool
    {
        if (is_null($this->stock_minimo)) return false;
        return $this->stock_total <= $this->stock_minimo;
    }
}