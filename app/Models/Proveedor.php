<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    use SoftDeletes;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre', 'nit', 'categoria_principal', 'activo',
        'contacto_principal', 'telefono', 'email', 'direccion',
        'tiene_credito', 'dias_plazo', 'limite_credito',
        'banco_1', 'cuenta_bancaria_1', 'banco_2', 'cuenta_bancaria_2', 'notas'
    ];

    public function lotesInsumos(): HasMany
    {
        return $this->hasMany(LoteInsumo::class, 'proveedor_id');
    }
}