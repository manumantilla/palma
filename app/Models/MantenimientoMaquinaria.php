<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MantenimientoMaquinaria extends Model
{
    protected $table = 'mantenimientos_maquinaria';

    protected $fillable = [
        'maquina_id',
        'proveedor_id',
        'tipo_mantenimiento',
        'fecha',
        'descripcion_trabajo',
        'costo_mano_obra_mecanico',
        'costo_materiales',
    ];

    protected $casts = [
        'fecha'                    => 'date',
        'costo_mano_obra_mecanico' => 'decimal:2',
        'costo_materiales'         => 'decimal:2',
        'costo_total'              => 'decimal:2',
    ];

    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquinaria::class, 'maquina_id');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }
}
