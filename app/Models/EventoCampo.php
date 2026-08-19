<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class EventoCampo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'eventos_campo';

    protected $fillable = [
        'ciclo_productivo_id',
        'lote_id',
        'zona_id',
        'tipo_evento_id',
        'fecha_programada',
        'fecha_ejecucion',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_programada' => 'datetime',
        'fecha_ejecucion'  => 'datetime',
        'latitud'          => 'decimal:8',
        'longitud'         => 'decimal:8',
        'hora_inicio'      => 'datetime:H:i:s', // or just 'string'
        'hora_fin'         => 'datetime:H:i:s',
    ];

    // Relationships
    public function cicloProductivo()
    {
        return $this->belongsTo(CicloProductivo::class);
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function zona()
    {
        return $this->belongsTo(LoteZonaManejo::class, 'zona_id');
    }

    public function tipoEvento()
    {
        return $this->belongsTo(TipoEvento::class);
    }

    public function eventoArboles()
    {
        return $this->hasMany(EventoArbol::class);
    }

    public function eventoInsumos()
    {
        return $this->hasMany(EventoInsumo::class);
    }

    public function scopeFiltrar(Builder $query, array $filtros)
    {
        return $query
            ->when($filtros['ciclo_productivo_id'] ?? null, function ($q, $cicloId) {
                $q->where('ciclo_productivo_id', $cicloId);
            })
            ->when($filtros['lote_id'] ?? null, function ($q, $loteId) {
                $q->where('lote_id', $loteId);
            })
            ->when($filtros['zona_id'] ?? null, function ($q, $zonaId) {
                $q->where('zona_id', $zonaId);
            })
            ->when($filtros['tipo_evento_id'] ?? null, function ($q, $tipoId) {
                $q->where('tipo_evento_id', $tipoId);
            })
            ->when($filtros['estado'] ?? null, function ($q, $estado) {
                $q->where('estado', $estado);
            })
            ->when($filtros['fecha_inicio'] ?? null, function ($q, $fechaInicio) {
                $q->whereDate('fecha_programada', '>=', $fechaInicio);
            })
            ->when($filtros['fecha_fin'] ?? null, function ($q, $fechaFin) {
                $q->whereDate('fecha_programada', '<=', $fechaFin);
            });
    }

}
