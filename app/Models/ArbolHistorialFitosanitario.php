<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArbolHistorialFitosanitario extends Model
{
    use HasFactory;

    protected $table = 'arboles_historial_fitosanitario';

    protected $fillable = [
        'arbol_id', 'fecha_hallazgo', 'tipo_incidencia', 'agente_patogeno_nombre',
        'severidad_afectacion', 'descripcion_sintomas', 'evidencia_fotografica_url',
        'usuario_evaluador_id', 'requiere_intervencion_quimica', 'caso_controlado',
        'fecha_resolucion'
    ];

    protected $casts = [
        'fecha_hallazgo'                => 'datetime',
        'fecha_resolucion'              => 'datetime',
        'requiere_intervencion_quimica' => 'boolean',
        'caso_controlado'               => 'boolean',
    ];

    public function arbol(): BelongsTo
    {
        return $this->belongsTo(Arbol::class, 'arbol_id');
    }

    public function evaluador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_evaluador_id');
    }
}