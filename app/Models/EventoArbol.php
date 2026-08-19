<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoArbol extends Model
{
    use HasFactory;

    protected $table = 'evento_arbol';

    protected $fillable = [
        'evento_campo_id',
        'arbol_id',
        'novedad_arbol',
        'nota_individual',
    ];

    protected $casts = [
        'novedad_arbol' => 'string',
    ];

    // Relationships
    public function eventoCampo()
    {
        return $this->belongsTo(EventoCampo::class);
    }

    public function arbol()
    {
        return $this->belongsTo(Arbol::class);
    }
}