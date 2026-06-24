<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class LoteInsumo extends Model
{
    use HasFactory;

    protected $table = 'lotes_insumos';

    protected $fillable = [
        'insumo_id',
        'proveedor_id',
        'codigo_lote',
        'ubicacion_bodega',
        'fecha_vencimiento',
        'fecha_ingreso',
        'cantidad_inicial',
        'cantidad_actual',
        'unidad',
        'costo_unitario',
        'estado',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_ingreso'     => 'date',
        'cantidad_inicial'  => 'decimal:2',
        'cantidad_actual'   => 'decimal:2',
        'costo_unitario'    => 'decimal:4',
    ];

    // --- RELACIONES ---

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    // --- ACCESSORS FINANCIEROS Y DE TRAZABILIDAD ---

    /**
     * Calcula automáticamente el valor monetario actual que representa este lote en bodega
     */
    public function getValorInventarioAttribute(): float
    {
        return (float) ($this->cantidad_actual * $this->costo_unitario);
    }

    /**
     * Calcula los días restantes antes de que expire el químico/biológico
     */
    public function getDiasParaVencerAttribute(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->fecha_vencimiento, false);
    }

    /**
     * Determina si el lote ya entró en la ventana crítica de vencimiento preestablecida
     */
    public function getEstaProximoAVencerAttribute(): bool
    {
        if ($this->estado === 'vencido' || $this->estado === 'agotado') {
            return false;
        }

        $diasMargen = $this->insumo->dias_aviso_vencimiento ?? 30;
        return $this->dias_para_vencer <= $diasMargen && $this->dias_para_vencer > 0;
    }

    // --- MUTATORS AUTOMÁTICOS (Booting state validation) ---

    /**
     * Evento Eloquent para que si al actualizar la cantidad_actual llega a 0, 
     * el estado mute automáticamente a 'agotado'.
     */
    protected static function booted()
    {
        static::saving(function ($lote) {
            if ($lote->cantidad_actual <= 0) {
                $lote->estado = 'agotado';
            }
        });
    }
}