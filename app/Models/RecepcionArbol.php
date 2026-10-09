<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Pivote pesaje ↔ árbol. Tiene PK UUID propia, por eso extiende Pivot con HasUuids:
 * al hacer $recepcion->arboles()->attach(...) el id se genera solo.
 */
class RecepcionArbol extends Pivot
{
    use HasUuids;

    protected $table = 'recepcion_arboles';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'recepcion_campo_id',
        'arbol_id',
        'peso_estimado_kg',
    ];

    protected $casts = [
        'peso_estimado_kg' => 'decimal:2',
    ];

    public function recepcionCampo(): BelongsTo
    {
        return $this->belongsTo(RecepcionCampo::class, 'recepcion_campo_id');
    }

    public function arbol(): BelongsTo
    {
        return $this->belongsTo(Arbol::class, 'arbol_id');
    }
}
