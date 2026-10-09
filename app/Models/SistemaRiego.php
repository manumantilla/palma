<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SistemaRiego extends Model
{
    protected $table = 'sistemas_riego';

    protected $fillable = [
        'tanque_id',
        'lote_id',
        'nombre_sistema',
        'tipo_riego',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function tanque(): BelongsTo
    {
        return $this->belongsTo(Tanque::class, 'tanque_id');
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }
}
