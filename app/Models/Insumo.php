<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Insumo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'insumos';

    /** Código que usa el formulario de insumos => abreviatura en unidades_medida. */
    public const ABREVIATURA_POR_CODIGO = ['kg' => 'kg', 'l' => 'L', 'unidad' => 'u'];

    protected $fillable = [
        'categoria_id',
        'nombre',
        'registro_ica',
        'ingrediente_principal',
        'unidad_base_id',
        'unidad_uso_id',
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
        'metadata',
    ];

    protected $casts = [
        // equipo_proteccion es texto libre (varchar 255): "Guantes, mascarilla, overol"
        'requiere_refrigeracion'=> 'boolean',
        'sensible_luz'          => 'boolean',
        'stock_minimo'          => 'decimal:2',
        'rei_horas'             => 'integer',
        'phi_dias'              => 'integer',
        'dias_aviso_vencimiento'=> 'integer',
        'metadata'              => 'array',
    ];

    // --- RELACIONES ---

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaInsumo::class, 'categoria_id');
    }

    // No se llama unidadBase(): chocaría con el accessor unidad_base de abajo
    public function unidadMedidaBase(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_base_id');
    }

    public function unidadMedidaUso(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_uso_id');
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
     * Código de unidad que esperan las vistas (kg / l / unidad), leído de unidades_medida.
     */
    public function getUnidadBaseAttribute(): ?string
    {
        $abreviatura = $this->unidadMedidaBase?->abreviatura;

        return array_search($abreviatura, self::ABREVIATURA_POR_CODIGO, true) ?: $abreviatura;
    }

    /**
     * Factor de conversión de la unidad base (vive en unidades_medida).
     */
    public function getFactorConversionAttribute(): ?string
    {
        return $this->unidadMedidaBase?->factor_conversion;
    }

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
