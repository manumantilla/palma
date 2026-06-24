<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cultivo extends Model
{
    use HasFactory;

    protected $table = 'cultivos';

    protected $fillable = [
        'tipo',
        'nombre_cultivo',
        'descripcion',
    ];

    /**
     * Obtener las etapas fenológicas asociadas al cultivo.
     */
    public function etapasFenologicas(): HasMany
    {
        return $this->hasMany(FenologiaEtapa::class, 'cultivo_id');
    }

    /**
     * Obtener las plantillas de labores asociadas al cultivo.
     */
    public function laboresPlantilla(): HasMany
    {
        return $this->hasMany(LaborPlantilla::class, 'cultivo_id');
    }
}