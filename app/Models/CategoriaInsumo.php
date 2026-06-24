<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaInsumo extends Model
{
    use HasFactory;

    protected $table = 'categorias_insumo';

    protected $fillable = [
        'nombre',
        'maneja_vencimiento',
        'maneja_toxicidad',
    ];

    protected $casts = [
        'maneja_vencimiento' => 'boolean',
        'maneja_toxicidad' => 'boolean',
    ];

    // Relación con insumos (asumiendo que tienes una tabla insumos)
    public function insumos()
    {
        return $this->hasMany(Insumo::class, 'categoria_id');
    }

    // Scope para filtrar categorías que manejan vencimiento
    public function scopeConVencimiento($query)
    {
        return $query->where('maneja_vencimiento', true);
    }

    // Scope para filtrar categorías que manejan toxicidad
    public function scopeConToxicidad($query)
    {
        return $query->where('maneja_toxicidad', true);
    }

    // Scope para búsqueda por nombre
    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombre', 'LIKE', "%{$termino}%");
    }
}