<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class EventoCampo extends Model
{
    //
    

    protected $table = 'eventos_campo';

    protected $fillable = [
        'ciclo_productivo_id',
        'lote_id',
        'zona_id',
        'tipo_evento_id',
        'fecha_programada',
        'fecha_ejecucion',
        'hora_inicio',
        'hora_fin',
        'latitud',
        'longitud',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_programada' => 'datetime',
        'fecha_ejecucion'  => 'datetime',
        'latitud'          => 'float',
        'longitud'         => 'float',
    ];

    public function cicloProductivo()
    {
        return $this->belongsTo(cicloProductivo::class, 'ciclo_productivo_id');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function zona()
    {
        return $this->belongsTo(LoteZonaManejo::class, 'zona_id');
    }

    public function tipoEvento()
    {
        return $this->belongsTo(TipoEvento::class, 'tipo_evento_id');
    }

    public function eventoArboles()
    {
        return $this->hasMany(EventoArbol::class, 'evento_campo_id');
    }

    public function arboles()
    {
        return $this->belongsToMany(
            Arbol::class,
            'evento_arbol',
            'evento_campo_id',
            'arbol_id',
        )->withPivot(['novedad_arbol', 'nota_individual'])->withTimestamps();
    }

    public function insumos()
    {
        return $this->hasMany(EventoInsumo::class, 'evento_campo_id');
    }

    public function manoObra()
    {
        return $this->hasMany(EventoManoObra::class, 'evento_campo_id');
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
