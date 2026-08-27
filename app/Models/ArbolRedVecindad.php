<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArbolRedVecindad extends Model
{
    use HasFactory;

    protected $table = 'arboles_red_vecindad';

    protected $fillable = [
        'arbol_origen_id',
        'arbol_destino_id',
        'distancia_metros',
        'probabilidad_contagio_base',
        'tipo_contacto',
    ];

    protected $casts = [
        'distancia_metros'           => 'decimal:2',
        'probabilidad_contagio_base' => 'decimal:4',
    ];

    // --- RELACIONES ---

    /**
     * Árbol que actúa como punto de origen de la conexión o contagio.
     */
    public function arbolOrigen(): BelongsTo
    {
        return $this->belongsTo(Arbol::class, 'arbol_origen_id');
    }

    /**
     * Árbol receptor o destino expuesto en la red.
     */
    public function arbolDestino(): BelongsTo
    {
        return $this->belongsTo(Arbol::class, 'arbol_destino_id');
    }
}