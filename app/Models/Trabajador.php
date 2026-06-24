<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trabajador extends Model
{
    use HasFactory;

    protected $table = 'trabajadores';

    protected $fillable = [
        'user_id',
        'tipo_documento',
        'numero_documento',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'genero',
        'cargo',
        'fecha_ingreso',
        'fecha_retiro',
        'tipo_contrato',
        'salario_base',
        'forma_pago',
        'banco_numero_cuenta',
        'eps',
        'arl',
        'afp',
        'habilidades',
        'ubicacion_actual',
        'ultima_ubicacion_at',
        'activo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_ingreso'    => 'date',
        'fecha_retiro'     => 'date',
        'habilidades'      => 'array', // Castea automáticamente el JSON a un array de PHP
        'activo'           => 'boolean',
        'ultima_ubicacion_at' => 'datetime',
    ];

    /**
     * Obtener el usuario de Jetstream vinculado al trabajador (si aplica).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}