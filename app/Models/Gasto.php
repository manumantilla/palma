<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Registro único de costos y gastos.
 *
 * total, neto_a_pagar y costo_cop son columnas generadas por Postgres (storedAs):
 * no van en $fillable y solo tienen valor después de guardar + refresh().
 */
class Gasto extends Model
{
    use HasFactory;

    protected $table = 'gastos';

    protected $fillable = [
        'origen',
        'gastable_type',
        'gastable_id',
        'categoria_gasto_id',
        'clase_costo',
        'imputacion',
        'lote_id',
        'ciclo_productivo_id',
        'ciclo_etapa_id',
        'capitalizable',
        'activo_amortizable_id',
        'proveedor_id',
        'trabajador_id',
        'tercero_nombre',
        'tercero_documento',
        'tipo_soporte',
        'numero_soporte',
        'cufe',
        'comprobante_archivo',
        'cuenta_puc',
        'concepto',
        'descripcion',
        'fecha',
        'fecha_vencimiento',
        'moneda',
        'tasa_cambio',
        'base_gravable',
        'porcentaje_iva',
        'valor_iva',
        'iva_descontable',
        'retefuente',
        'reteica',
        'reteiva',
        'es_cuenta_por_pagar',
        'valor_pagado',
        'estado',
        'motivo_anulacion',
        'anulado_por',
        'anulado_at',
        'user_id',
    ];

    protected $casts = [
        'fecha'               => 'date',
        'fecha_vencimiento'   => 'date',
        'anulado_at'          => 'datetime',
        'capitalizable'       => 'boolean',
        'iva_descontable'     => 'boolean',
        'es_cuenta_por_pagar' => 'boolean',
        'tasa_cambio'         => 'decimal:4',
        'base_gravable'       => 'decimal:2',
        'porcentaje_iva'      => 'decimal:2',
        'valor_iva'           => 'decimal:2',
        'retefuente'          => 'decimal:2',
        'reteica'             => 'decimal:2',
        'reteiva'             => 'decimal:2',
        'valor_pagado'        => 'decimal:2',
        // Generadas por la BD (solo lectura)
        'total'               => 'decimal:2',
        'neto_a_pagar'        => 'decimal:2',
        'costo_cop'           => 'decimal:2',
    ];

    /**
     * Documento que originó el gasto (EventoInsumo, EventoManoObra, CartaPorte...).
     */
    public function gastable(): MorphTo
    {
        return $this->morphTo();
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaGasto::class, 'categoria_gasto_id');
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function cicloProductivo(): BelongsTo
    {
        return $this->belongsTo(CicloProductivo::class, 'ciclo_productivo_id');
    }

    /**
     * Etapa fenológica a la que se carga el costo. La BD garantiza (FK compuesta + CHECK)
     * que pertenezca al mismo ciclo_productivo_id.
     */
    public function cicloEtapa(): BelongsTo
    {
        return $this->belongsTo(CicloEtapaHistorial::class, 'ciclo_etapa_id');
    }

    public function activoAmortizable(): BelongsTo
    {
        return $this->belongsTo(ActivoAmortizable::class, 'activo_amortizable_id');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'trabajador_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(GastoPago::class, 'gasto_id');
    }

    public function anuladoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'anulado_por');
    }

    /**
     * Usuario del sistema que registró el movimiento
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
