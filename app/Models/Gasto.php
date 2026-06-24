<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    use HasFactory;

    protected $table = 'gastos';

    protected $fillable = [
        'gastable_id',
        'gastable_type',
        'ciclo_productivo_id',
        'categoria',
        'metodo_pago',
        'concepto',
        'monto',
        'fecha',
        'descripcion',
        'user_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];

    /**
     * Relación polimórfica: Retorna el modelo dueño de este gasto 
     * (p.ej. App\Models\LaborPlantilla, App\Models\Trabajador, etc.)
     */
    public function gastable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relación con el ciclo productivo (opcional, para costeo global)
     */
    public function cicloProductivo(): BelongsTo
    {
        return $this->belongsTo(CicloProductivo::class, 'ciclo_producto_id');
    }

    /**
     * Usuario del sistema que registró el movimiento
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}