<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoEvento extends Model
{
    use HasFactory;

    protected $table = 'tipos_evento';

    protected $fillable = [
        'nombre',
        'categoria',
        'consume_insumos',
        'consume_mano_obra',
        'genera_ingreso',
        'genera_movimiento_stock',
        'requiere_area_ha',
        'aplica_a_arbol',
        'aplica_a_ciclo',
        'periodo_reingreso_horas',
        'periodo_carencia_dias',
    ];

    protected $casts = [
        'consume_insumos'         => 'boolean',
        'consume_mano_obra'       => 'boolean',
        'genera_ingreso'          => 'boolean',
        'genera_movimiento_stock' => 'boolean',
        'requiere_area_ha'        => 'boolean',
        'aplica_a_arbol'          => 'boolean',
        'aplica_a_ciclo'          => 'boolean',
    ];

    /**
     * Obtener las labores de plantilla que usan este tipo de evento.
     */
    public function laboresPlantilla(): HasMany
    {
        return $this->hasMany(LaborPlantilla::class, 'tipo_evento_id');
    }
}