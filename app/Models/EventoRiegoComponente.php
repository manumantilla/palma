<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoRiegoComponente extends Model
{
    protected $table = 'evento_riego_componentes';

    protected $fillable = [
        'evento_riego_id',
        'componente_id',
        'estaba_activo',
    ];

    protected $casts = [
        'estaba_activo' => 'boolean',
    ];

    public function eventoRiego(): BelongsTo
    {
        return $this->belongsTo(EventoRiego::class, 'evento_riego_id');
    }

    public function componente(): BelongsTo
    {
        return $this->belongsTo(ComponenteRiego::class, 'componente_id');
    }
}
