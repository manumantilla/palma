<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maquinaria extends Model
{
    use SoftDeletes;

    protected $table = 'maquinaria';

    protected $fillable = [
        'codigo_interno',
        'nombre',
        'tipo_maquinaria_id',
        'marca',
        'modelo',
        'placa',
        'serial',
        'fecha_compra',
        'valor_compra',
        'vida_util_anios',
        'fuente_energia',
        'capacidad_tanque',
        'consumo_hora',
        'horometro_actual',
        'kilometraje',
        'estado',
        'responsable_id',
        'observaciones',
    ];

    protected $casts = [
        'fecha_compra'     => 'date',
        'valor_compra'     => 'decimal:2',
        'capacidad_tanque' => 'decimal:2',
        'consumo_hora'     => 'decimal:2',
    ];

    public function tipoMaquinaria(): BelongsTo
    {
        return $this->belongsTo(TipoMaquinaria::class, 'tipo_maquinaria_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
}
