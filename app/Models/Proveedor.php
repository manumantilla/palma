<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
       protected $table = 'proveedores';
    
    protected $fillable = [
        'nombre',
        'nit',
        'telefono',
        'email',
        'direccion',
        'tiene_credito',
        'cuente_banco_1',
        'cuente_banco_2',
    ];
    
    protected $casts = [
        'tiene_credito' => 'boolean',
    ];
    
    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class, 'proveedor_id');
    }
}
