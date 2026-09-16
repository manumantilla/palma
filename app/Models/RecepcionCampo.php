<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RecepcionCampo extends Model
{
    use HasUuids;

    protected $table = 'recepciones_campo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',              
        'sesion_cosecha_id',           
        'lote_zona_id',
        'trabajador_id',
        'arbol_id',
        'peso_bruto',
        'tara_costal',
        'peso_neto',
        'peso_merma_campo',
        'hora_pesaje',
        'foto_evidencia',
        'costal_codigo',
        'numero_corte',
        'ubicacion_gps',      
        'client_updated_at', 
        'synced_at', 
        'estado_clasificacion'
    ];

    protected $casts = [
        'hora_pesaje'        => 'datetime',
        'client_updated_at'  => 'datetime',
        'synced_at'          => 'datetime',
        'peso_bruto'         => 'decimal:2',
        'tara_costal'        => 'decimal:2',
        'peso_neto'          => 'decimal:2',
        'peso_merma_campo'   => 'decimal:2',
    ];

    public function getLoteAttribute()
    {
        return $this->sesionCosecha?->lote;
    }

    /**
     * Obtiene directamente el ID del lote.
     */
    public function getLoteIdAttribute()
    {
        return $this->sesionCosecha?->ordenCosecha?->lote_cultivo_id;
    }

    public function sesionCosecha()
    {
        return $this->belongsTo(SesionCosecha::class, 'sesion_id');
    }

    public function zonaManejo()
    {
        return $this->belongsTo(LoteZonaManejo::class, 'lote_zona_id');
    }

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, 'trabajador_id');
    }

    public function arbol()
    {
        return $this->belongsTo(Arbol::class, 'arbol_id');
    }
}