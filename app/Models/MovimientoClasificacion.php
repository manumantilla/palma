<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MovimientoClasificacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'movimientos_clasificacion';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'operario_id',
        'recepcion_campo_id',
        'contenedor_id',
        'fecha_movimiento',
        'kilos_asignados',
        'observaciones',
        'client_updated_at',
        'synced_at',
    ];

    protected $casts = [
        'fecha_movimiento'  => 'date',
        'kilos_asignados'   => 'decimal:2',
        'client_updated_at' => 'datetime',
        'synced_at'         => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    
    public function operario()
    {
        return $this->belongsTo(User::class, 'operario_id');
    }

    public function recepcionCampo()
    {
        return $this->belongsTo(RecepcionCampo::class, 'recepcion_campo_id');
    }

    public function contenedor()
    {
        return $this->belongsTo(Contenedor::class, 'contenedor_id');
    }


    /**
     * Filtra los movimientos en un rango de fechas o en una fecha específica.
     */
    public function scopeFechas($query, $fechaInicio, $fechaFin = null)
    {
        if ($fechaInicio && $fechaFin) {
            return $query->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin]);
        } elseif ($fechaInicio) {
            return $query->where('fecha_movimiento', '>=', $fechaInicio);
        } elseif ($fechaFin) {
            return $query->where('fecha_movimiento', '<=', $fechaFin);
        }
        
        return $query;
    }

    public function scopePorContenedor($query, $contenedorId)
    {
        if ($contenedorId) {
            return $query->where('contenedor_id', $contenedorId);
        }
    }

    public function scopePorRecepcion($query, $recepcionId)
    {
        if ($recepcionId) {
            return $query->where('recepcion_campo_id', $recepcionId);
        }
    }

  
    public function scopePorOperario($query, $operarioId)
    {
        if ($operarioId) {
            return $query->where('operario_id', $operarioId);
        }
    }
}