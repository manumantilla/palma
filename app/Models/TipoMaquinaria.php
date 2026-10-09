<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMaquinaria extends Model
{
    protected $table = 'tipo_maquinaria';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];
}
