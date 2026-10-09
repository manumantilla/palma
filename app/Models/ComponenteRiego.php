<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComponenteRiego extends Model
{
    protected $table = 'componentes_riego';

    protected $fillable = [
        'sistema_riego_id',
        'tipo_componente',
        'nombre_identificador',
        'diametro_pulgadas',
        'presion_trabajo_psi',
        'caudal_estimado_litros_minuto',
        'activo',
    ];

    protected $casts = [
        'presion_trabajo_psi'           => 'decimal:2',
        'caudal_estimado_litros_minuto' => 'decimal:2',
        'activo'                        => 'boolean',
    ];

    public function sistemaRiego(): BelongsTo
    {
        return $this->belongsTo(SistemaRiego::class, 'sistema_riego_id');
    }
}
