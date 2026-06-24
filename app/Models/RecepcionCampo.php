<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecepcionCampo extends Model
{
    protected $table = 'recepciones_campo';

    protected $fillable = [
        'sesion_id',
        'lote_zona_id',
        'trabajador_id',
        'arbol_id',
        'peso_bruto',
        'tara_costal',
        'peso_neto', // Recibido directamente desde el frontend
        'hora_pesaje',
        'foto_evidencia',
        'costal_codigo',
        'numero_corte',
        'estado_clasificacion'
    ];

    protected $casts = [
        'hora_pesaje' => 'datetime',
        'peso_bruto' => 'decimal:2',
        'tara_costal' => 'decimal:2',
        'peso_neto' => 'decimal:2',
    ];

    // --- RELACIONES ---
    public function sesionCosecha() { return $this->belongsTo(SesionCosecha::class, 'sesion_id'); }
    public function zonaManejo() { return $this->belongsTo(LoteZonaManejo::class, 'lote_zona_id'); }
    public function trabajador() { return $this->belongsTo(Trabajador::class, 'trabajador_id'); }
    public function arbol() { return $this->belongsTo(Arbol::class, 'arbol_id'); }
    public function movimientosClasificacion() { return $this->hasMany(MovimientoClasificacion::class, 'recepcion_campo_id'); }

    // --- SCOPE DE FILTRADO DINÁMICO ---
    public function scopeFiltrar($query, array $filtros)
    {
        return $query->when($filtros['sesion_id'] ?? null, function ($q, $sesionId) {
            $q->where('sesion_id', $sesionId);
        })
        ->when($filtros['trabajador_id'] ?? null, function ($q, $trabajadorId) {
            $q->where('trabajador_id', $trabajadorId);
        })
        ->when($filtros['estado_clasificacion'] ?? null, function ($q, $estado) {
            $q->where('estado_clasificacion', $estado);
        })
        ->when($filtros['costal_codigo'] ?? null, function ($q, $codigo) {
            $q->where('costal_codigo', 'LIKE', "%{$codigo}%");
        });
    }
}