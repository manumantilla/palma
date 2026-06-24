<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoArbol extends Model
{
    //
    protected $table = 'evento_arbol';

    protected $fillable = [
        'evento_campo_id',
        'arbol_id', 'novedad_arbol',
        'nota_individual'
    ];

    public function evento()
    {
        return $this->belongsTo(EventoCampo::class, 'evento_campo_id');
    }

    public function arbol()
    {
        return $this->belongsTo(Arbol::class, 'arbol_id');
    }


}
