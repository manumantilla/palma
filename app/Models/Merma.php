<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Merma extends Model
{
    use HasFactory, SoftDeletes; // Asegúrate de tener $table->softDeletes() en tu migración

    protected $table = 'mermas';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'operario_id',
        'recepcion_campo_id',
        'contenedor_id',
        'fecha_registro',
        'kilos_merma',
        'motivo',
        'costo_estimado',
        'destino_final',
        'comentarios',
        'client_updated_at',
        'synced_at',
    ];

    protected $casts = [
        'fecha_registro'    => 'datetime',
        'kilos_merma'       => 'decimal:2',
        'costo_estimado'    => 'decimal:2',
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


    public function scopeFechas($query, $fechaInicio, $fechaFin = null)
    {
        if ($fechaInicio && $fechaFin) {
            return $query->whereBetween('fecha_registro', [$fechaInicio, $fechaFin]);
        } elseif ($fechaInicio) {
            return $query->whereDate('fecha_registro', '>=', $fechaInicio);
        } elseif ($fechaFin) {
            return $query->whereDate('fecha_registro', '<=', $fechaFin);
        }
        
        return $query;
    }

    /**
     * Filtra por motivo específico (Ideal para reportes de "Daño Fitosanitario" o "Robo").
     */
    public function scopePorMotivo($query, $motivo)
    {
        if ($motivo) {
            return $query->where('motivo', $motivo);
        }
    }

    /**
     * Filtra solo las mermas que ocurrieron en la etapa de Recepción.
     */
    public function scopeDesdeRecepcion($query)
    {
        return $query->whereNotNull('recepcion_campo_id');
    }

    /**
     * Filtra solo las mermas que ocurrieron en la etapa de Almacenamiento/Proceso (Contenedores).
     */
    public function scopeDesdeContenedor($query)
    {
        return $query->whereNotNull('contenedor_id');
    }

    /**
     * Filtra por el destino final de la merma (Ej: 'abono', 'basura').
     */
    public function scopePorDestino($query, $destino)
    {
        if ($destino) {
            return $query->where('destino_final', 'like', "%{$destino}%");
        }
    }
}