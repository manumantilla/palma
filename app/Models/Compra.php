<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MatanYadaev\EloquentSpatial\Objects\Point;

class Compra extends Model
{
    protected $table = 'compras';

    public const TIPOS = [
        'insumos', 'maquinaria', 'servicios_tecnicos', 'transporte',
        'empaque', 'laboratorio', 'consultoria', 'otros',
    ];

    public const ESTADOS = [
        'borrador', 'confirmada', 'enviada', 'recibida_parcial', 'recibida_total', 'cancelada',
    ];

    /** Transiciones de estado permitidas (máquina de estados). */
    public const TRANSICIONES = [
        'borrador'         => ['confirmada', 'cancelada'],
        'confirmada'       => ['enviada', 'cancelada'],
        'enviada'          => ['recibida_parcial', 'recibida_total', 'cancelada'],
        'recibida_parcial' => ['recibida_total'],
        'recibida_total'   => [],
        'cancelada'        => [],
    ];

    public const METODOS_PAGO = ['efectivo', 'transferencia', 'cheque', 'tarjeta'];

    protected $fillable = [
        'proveedor_id', 'fecha', 'tipo', 'ciclo_productivo_id', 'estado',
        'factura_pdf_path', 'ciudad', 'departamento', 'plazo_pago_dias',
        'descuento_pronto_pago', 'numero_factura', 'subtotal', 'descuento_total',
        'iva_total', 'total', 'porcentaje_iva_general', 'fecha_pedido',
        'estado_pago', 'observaciones', 'user_id', 'ubicacion', 'fecha_vencimiento',
        'tipo_soporte', 'cufe', 'retefuente', 'reteica', 'reteiva',
    ];

    // La geometría cruda (WKB) no se serializa; se expone como lat/lng.
    protected $hidden = ['ubicacion'];

    protected function casts(): array
    {
        return [
            'fecha'                  => 'datetime',
            'fecha_pedido'           => 'datetime',
            'plazo_pago_dias'        => 'integer',
            'descuento_pronto_pago'  => 'decimal:2',
            'subtotal'               => 'decimal:2',
            'descuento_total'        => 'decimal:2',
            'iva_total'              => 'decimal:2',
            'total'                  => 'decimal:2',
            'porcentaje_iva_general' => 'decimal:2',
            'fecha_vencimiento'      => 'date',
            'retefuente'             => 'decimal:2',
            'reteica'                => 'decimal:2',
            'reteiva'                => 'decimal:2',
            'ubicacion'              => Point::class,
        ];
    }

    /* ---------------- Relaciones ---------------- */

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function cicloProductivo(): BelongsTo
    {
        return $this->belongsTo(CicloProductivo::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CompraItem::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(CompraPago::class);
    }

    /* ---------------- Scopes ---------------- */

    /** Agrega lat/lng leídos de PostGIS a la consulta. */
    public function scopeConCoordenadas(Builder $q): Builder
    {
        return $q->addSelect([
            'compras.*',
        ])->selectRaw('ST_Y(compras.ubicacion) AS lat, ST_X(compras.ubicacion) AS lng');
    }

    /* ---------------- Atributos calculados ---------------- */

    public function getTotalPagadoAttribute(): float
    {
        // Usa el valor de withSum si viene cargado, evita N+1.
        if (array_key_exists('pagos_sum_monto', $this->attributes)) {
            return (float) $this->attributes['pagos_sum_monto'];
        }

        return (float) $this->pagos()->sum('monto');
    }

    public function getSaldoPendienteAttribute(): float
    {
        return round((float) $this->total - $this->total_pagado, 2);
    }

    public function getFechaVencimientoPagoAttribute()
    {
        return $this->fecha?->copy()->addDays($this->plazo_pago_dias ?? 0);
    }

    public function esEditable(): bool
    {
        return $this->estado === 'borrador';
    }

    public function puedeTransicionarA(string $nuevo): bool
    {
        return in_array($nuevo, self::TRANSICIONES[$this->estado] ?? [], true);
    }
}