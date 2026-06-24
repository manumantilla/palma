<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoClasificacion extends Model
{
    //
    protected $table = 'movimientos_clasificacion';

    protected $fillable = [
        'recepcion_campo_id',
        'contenedor_id',
        'kilos_asignados',
        'observaciones'
    ];

    public function recepcionCampo()
    {
        return $this->belongsTo(RecepcionCampo::class, 'recepcion_campo_id');
    }

    public function contenedor()
    {
        return $this->belongsTo(Contenedor::class, 'contenedor_id');
    }
}
