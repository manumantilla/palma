<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merma extends Model
{
    protected $table = 'mermas';

    protected $fillable = [
        'recepcion_campo_id',
        'contenedor_id',
        'fecha_registro',
        'kilos_merma',
        'motivo',
        'costo_estimado',
        'destino_final',
        'comentarios'
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'kilos_merma' => 'decimal:2',
        'costo_estimado' => 'decimal:2'
    ];

    // --- RELACIONES ---

    // Merma detectada en el costal recién llegado del lote
    public function recepcionCampo()
    {
        return $this->belongsTo(RecepcionCampo::class, 'recepcion_campo_id');
    }

    // Merma detectada por almacenamiento, daño o deshidratación en tolva
    public function contenedor()
    {
        return $this->belongsTo(Contenedor::class, 'contenedor_id');
    }

    // --- SCOPE DE FILTRADO DINÁMICO ---
    public function scopeFiltrar($query, array $filtros)
    {
        return $query->when($filtros['motivo'] ?? null, function ($q, $motivo) {
            $q->where('motivo', $motivo);
        })
        ->when($filtros['fecha'] ?? null, function ($q, $fecha) {
            $q->where('fecha_registro', $fecha);
        })
        ->when($filtros['contenedor_id'] ?? null, function ($q, $contenedorId) {
            $q->where('contenedor_id', $contenedorId);
        })
        ->when($filtros['recepcion_id'] ?? null, function ($q, $recepcionId) {
            $q->where('recepcion_campo_id', $recepcionId);
        });
    }
}