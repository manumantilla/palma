<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsumoComponente extends Model
{
    use HasFactory;

    protected $table = 'insumo_componentes';

    protected $fillable = [
        'insumo_id',
        'tipo_componente',
        'componente',
        'unidad',
        'concentracion',
    ];

    protected $casts = [
        'concentracion' => 'decimal:4',
    ];

    // --- RELACIONES ---

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }

    // --- ACCESSORS ---

    /**
     * Devuelve la concentración formateada limpia (Ej: "46% - Nitrógeno" o "1x10^9 UFC/g - Trichoderma")
     */
    public function getFichaResumenAttribute(): string
    {
        $valor = global_format_numeric_if_decimal($this->concentracion); // Helper o formateo nativo
        return rtrim(rtrim(number_format($this->concentracion, 2), '0'), '.') . "{$this->unidad} de {$this->componente} ({$this->tipo_componente})";
    }
}