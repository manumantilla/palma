<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class EventoInsumo extends Model
{
    use HasFactory;

    protected $table = 'evento_insumos';

    protected $fillable = [
        'evento_campo_id',
        'insumo_id',
        'cantidad',
        'unidad_id',
        'cantidad_base',
        'area_aplicada',
        'metodo_aplicacion',
        'costo_total',
        'observaciones',
    ];

    protected $casts = [
        'cantidad'      => 'decimal:4',
        'cantidad_base' => 'decimal:4',
        'area_aplicada' => 'decimal:2',
        'costo_total'   => 'decimal:2',
    ];

    // Relationships
    public function eventoCampo()
    {
        return $this->belongsTo(EventoCampo::class);
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }

    // No se llama unidadMedida(): chocaría con el accessor unidad_medida de abajo
    public function unidad(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_id');
    }

    /** Abreviatura (kg, L, mL...) para las vistas que antes leían la columna enum unidad_medida. */
    public function getUnidadMedidaAttribute(): ?string
    {
        return $this->unidad?->abreviatura;
    }

    public function lotes() {
        return $this->hasMany(EventoInsumoLote::class, 'evento_insumo_id');
    }

    /** Costo causado en gastos por esta aplicación (origen aplicacion_insumo). */
    public function gastos(): MorphMany
    {
        return $this->morphMany(Gasto::class, 'gastable');
    }
}
