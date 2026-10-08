<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'nit',
        'categoria_principal',
        'activo',
        'contacto_principal',
        'telefono',
        'email',
        'direccion',
        'tiene_credito',
        'dias_plazo',
        'limite_credito',
        'banco_1',
        'cuenta_bancaria_1',
        'banco_2',
        'cuenta_bancaria_2',
        'notas',
    ];

    protected $casts = [
        'activo'        => 'boolean',
        'tiene_credito' => 'boolean',
        'dias_plazo'    => 'integer',
        'limite_credito' => 'decimal:2',
    ];

    /**
     * Relación uno a muchos: Un proveedor tiene muchas compras.
     */
    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class, 'proveedor_id');
    }
}