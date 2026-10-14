<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Jornal o destajo de una labor. Se crea, edita y liquida con App\Services\EventoService.
 * El ciclo se hereda del evento: eventoCampo->ciclo_productivo_id.
 */
class EventoManoObra extends Model
{
    use HasFactory;

    protected $table = 'evento_mano_obra';

    public const TIPOS_LABOR = ['jornal_dia_completo', 'jornal_medio_dia', 'hora_extra', 'destajo'];

    protected $fillable = [
        'evento_campo_id',
        'sesion_id',
        'tipo_labor',
        'trabajador_id',
        'nombre_trabajador',
        'cedula',
        'fecha_labor',
        'cantidad',
        'unidad_destajo',
        'valor_unitario',
        'costo_total',
        'kilos_pesados',
        'area_ejecutada_ha',
        'arboles_atendidos',
        'estado_pago',
        'fecha_pago',
        'observaciones',
    ];

    protected $casts = [
        'fecha_labor'       => 'date',
        'cantidad'          => 'decimal:2',
        'valor_unitario'    => 'decimal:2',
        'costo_total'       => 'decimal:2',
        'kilos_pesados'     => 'decimal:2',
        'area_ejecutada_ha' => 'decimal:2',
        'arboles_atendidos' => 'integer',
        'fecha_pago'        => 'date',
    ];

    // ==========================================
    //  RELACIONES ELOQUENT
    // ==========================================

    /**
     * Evento macro al que pertenece la mano de obra.
     */
    public function eventoCampo(): BelongsTo
    {
        return $this->belongsTo(EventoCampo::class, 'evento_campo_id');
    }

    /**
     * Sesión de cosecha en caso de que sea trabajo a destajo/recolección.
     */
    public function sesionCosecha(): BelongsTo
    {
        return $this->belongsTo(SesionCosecha::class, 'sesion_id');
    }

    /**
     * Trabajador registrado formalmente en la plataforma.
     */
    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'trabajador_id');
    }

    /** Costo causado en gastos por este registro (origen mano_obra). */
    public function gasto(): MorphOne
    {
        return $this->morphOne(Gasto::class, 'gastable');
    }

    // ==========================================
    // SCOPES DE CONSULTA RÁPIDA
    // ==========================================

    public function scopePendientesDePago($query)
    {
        return $query->where('estado_pago', 'pendiente');
    }

    public function scopePagados($query)
    {
        return $query->where('estado_pago', 'pagado');
    }

    /**
     * Scope: la mano de obra de un trabajador, por ID o por documento.
     */
    public function scopeDelTrabajador($query, $trabajadorIdOrCedula)
    {
        return $query->where(function ($q) use ($trabajadorIdOrCedula) {
            $q->where('cedula', (string) $trabajadorIdOrCedula);
            if (ctype_digit((string) $trabajadorIdOrCedula)) {
                $q->orWhere('trabajador_id', (int) $trabajadorIdOrCedula);
            }
        });
    }

    public function scopePorTipoLabor($query, string $tipoLabor)
    {
        return $query->where('tipo_labor', $tipoLabor);
    }

    /**
     * Scope: por el día trabajado (no por cuándo se digitó). Para la nómina semanal.
     */
    public function scopeEnRangoFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_labor', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope: mano de obra cargada a un ciclo productivo (a través de su evento).
     */
    public function scopePorCicloProductivo($query, $cicloProductivoId)
    {
        return $query->whereHas('eventoCampo', function ($q) use ($cicloProductivoId) {
            $q->where('ciclo_productivo_id', $cicloProductivoId);
        });
    }

    public function scopePorZona($query, $zonaId)
    {
        return $query->whereHas('eventoCampo', function ($q) use ($zonaId) {
            $q->where('zona_id', $zonaId);
        });
    }

    // ==========================================
    // ATRIBUTOS
    // ==========================================

    /** cantidad × valor unitario: lo que daría la cuenta sin ajustes. */
    public function getCostoCalculadoAttribute(): float
    {
        return round((float) $this->cantidad * (float) $this->valor_unitario, 2);
    }

    /** Lo que el supervisor subió (+) o bajó (−) sobre el cálculo. */
    public function getAjusteAttribute(): float
    {
        return round((float) $this->costo_total - $this->costo_calculado, 2);
    }

    public function getNombreCompletoTrabajadorAttribute(): string
    {
        if ($this->trabajador) {
            return trim($this->trabajador->nombres . ' ' . $this->trabajador->apellidos);
        }

        return $this->nombre_trabajador ?? 'Trabajador ocasional';
    }
}
