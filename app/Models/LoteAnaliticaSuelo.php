<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoteAnaliticaSuelo extends Model
{
    use HasFactory;

    protected $table = 'lotes_analiticas_suelo';

    protected $fillable = [
        'lote_id', 'fecha_muestreo', 'numero_laboratorio_ticket', 'ph',
        'conductividad_electrica_ds_m', 'materia_organica_porcentaje',
        'capacidad_intercambio_cationico_meq', 'textura_predominante',
        'porcentaje_arena', 'porcentaje_limo', 'porcentaje_arcilla', 'analista_user_id'
    ];

    protected $casts = [
        'fecha_muestreo'                      => 'date',
        'ph'                                  => 'decimal:2',
        'conductividad_electrica_ds_m'        => 'decimal:3',
        'materia_organica_porcentaje'         => 'decimal:2',
        'capacidad_intercambio_cationico_meq' => 'decimal:2',
        'porcentaje_arena'                    => 'decimal:2',
        'porcentaje_limo'                     => 'decimal:2',
        'porcentaje_arcilla'                  => 'decimal:2',
    ];

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function analista(): BelongsTo
    {
        return $this->belongsTo(User::class, 'analista_user_id');
    }
}