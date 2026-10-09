<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CicloProductivo extends Model
{
    use HasFactory;

    protected $table = 'ciclos_productivos';

    protected $fillable = [
        
        'lote_id', 'cultivo_id', 'material_genetico_id', 'estado', 'tipo', 'nombre_campana', 'arreglo_espacial',
        'fecha_inicio', 'fecha_estimada_cosecha', 'fecha_real_inicio_cosecha',
        'fecha_estimada_fin_cosecha', 'fecha_real_fin_cosecha', 'fecha_finalizacion_ciclo',
        'modalidad_siembra', 'distancia_entre_hileras_metros', 'distancia_entre_plantas_metros',
        'plantas_por_hectarea_real', 'proveedor_material_vegetal_id', 'codigo_lote_vivero_origen',
        'registro_autorizacion_institucional', 'es_organico_certificado',
        'agronomo_responsable_id', 'costo_acumulado_directo', 'costo_acumulado_indirecto'
    ];

    protected $casts = [
        'fecha_inicio'                     => 'date',
        'fecha_estimada_cosecha'           => 'date',
        'fecha_real_inicio_cosecha'        => 'date',
        'fecha_estimada_fin_cosecha'       => 'date',
        'fecha_real_fin_cosecha'           => 'date',
        'fecha_finalizacion_ciclo'         => 'date',
        'distancia_entre_hileras_metros'   => 'decimal:2',
        'distancia_entre_plantas_metros'   => 'decimal:2',
        'plantas_por_hectarea_real'        => 'decimal:2',
        'es_organico_certificado'          => 'boolean',
        'costo_acumulado_directo'          => 'decimal:2',
        'costo_acumulado_indirecto'        => 'decimal:2',
    ];

    // --- RELACIONES CORRECCIONALES ---

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function cultivo(): BelongsTo
    {
        return $this->belongsTo(Cultivo::class, 'cultivo_id');
    }

    // --RELACIONES DE APOYO
    //Nombre cultivo
    public function nombreCultivo()
    {
        return $this->cultivo ? $this->cultivo->nombre_cultivo : 'n/a';
    }

    public function materialGenetico(): BelongsTo
    {
        return $this->belongsTo(MaterialGenetico::class, 'material_genetico_id');
    }

    public function proveedorMaterial(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_material_vegetal_id');
    }

    
    public function agronomo()
    {
        return $this->belongsTo(User::class, 'agronomo_responsable_id');
    }

    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class, 'ciclo_productivo_id');
    }

    // --- AUTOMATIZACIÓN AGRONÓMICA (Eventos de Ciclo de Vida) ---

    protected static function booted()
    {
        /**
         * Antes de insertar o actualizar, calculamos matemáticamente la densidad por Ha
         * Fórmula: 10,000 m² / (Distancia Hileras * Distancia Plantas)
         */
        static::saving(function ($ciclo) {
            $plantas = (float) $ciclo->distancia_entre_plantas_metros;
            if ($ciclo->arreglo_espacial === 'tresbolillo' && $plantas > 0) {
                // Triángulo equilátero (palma): la distancia entre hileras es d · sen(60°)
                $ciclo->plantas_por_hectarea_real = 10000 / ($plantas * $plantas * 0.866);
            } elseif ($ciclo->distancia_entre_hileras_metros > 0 && $plantas > 0) {
                $marco_plantacion = $ciclo->distancia_entre_hileras_metros * $plantas;
                $ciclo->plantas_por_hectarea_real = 10000 / $marco_plantacion;
            } else {
                $ciclo->plantas_por_hectarea_real = 0.00;
            }
        });
    }

    // --- GETTERS / ACCESSORS (Atributos Dinámicos de Control) ---

    /**
     * Retorna el costo consolidado de la producción actual (Directo + Indirecto)
     */
    public function getCostoTotalInvertidoAttribute(): float
    {
        return (float) ($this->costo_acumulado_directo + $this->costo_acumulado_indirecto);
    }

    /**
     * Calcula dinámicamente cuántos días lleva el cultivo en el terreno
     */
    public function getDiasEnCampoAttribute(): int
    {
        if (!$this->fecha_inicio) return 0;
        
        $fechaFin = $this->fecha_finalizacion_ciclo ?? now();
        return (int) $this->fecha_inicio->diffInDays($fechaFin);
    }

    /**
     * Traduce los estados del ENUM a labels limpios para tus interfaces web
     */
    public function getEstadoBadgeTextoAttribute(): string
    {
        // Estado administrativo; la etapa agronómica sale de etapasHistorial()
        return match ($this->estado) {
            'planificado' => 'Planificado',
            'activo'      => 'Activo',
            'en_receso'   => 'En receso',
            'concluido'   => 'Ciclo Concluido ✅',
            'siniestrado' => 'Pérdida por Siniestro 🚨',
            default       => 'Desconocido',
        };
    }

    public function etapasHistorial()
    {
        return $this->hasMany(CicloEtapaHistorial::class, 'ciclo_productivo_id')->orderBy('fecha_inicio_estimada', 'asc');
    }
}