l<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoteSistemaRiego extends Model
{
    use HasFactory;

    protected $table = 'lotes_sistemas_riego';

    protected $fillable = [
        'lote_id', 'nombre_sistema', 'tipo_riego', 'fuente_agua',
        'caudal_diseno_litros_segundo', 'presion_operacion_psi',
        'coeficiente_uniformidad', 'espaciamiento_emisores_metros',
        'descarga_emisor_litros_hora', 'activo'
    ];

    protected $casts = [
        'caudal_diseno_litros_segundo' => 'decimal:2',
        'presion_operacion_psi'        => 'decimal:2',
        'coeficiente_uniformidad'      => 'decimal:2',
        'espaciamiento_emisores_metros'=> 'decimal:2',
        'descarga_emisor_litros_hora'  => 'decimal:2',
        'activo'                       => 'boolean',
    ];

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }
}