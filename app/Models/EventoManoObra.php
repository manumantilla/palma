<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoManoObra extends Model
{
    use HasFactory;

    protected $table = 'evento_mano_obra';

    protected $fillable = [
        'evento_campo_id',
        'sesion_id',
        'ciclo_id',
        'tipo_labor',
        'trabajador_id',
        'nombre_trabajador',
        'cedula',
        'cantidad',
        'unidad_destajo',
        'valor_unitario',
        'costo_total',
        'estado_pago',
        'fecha_pago',
        'observaciones',
    ];

    protected $casts = [
        'cantidad' => 'decimal:1',
        'valor_unitario' => 'decimal:2',
        'costo_total' => 'decimal:2',
        'fecha_pago' => 'datetime',
    ];

    // ==========================================
    // ⚙️ BOOT & EVENTOS (Cálculo Automático)
    // ==========================================
    // protected static function boot()
    // {
    //     parent::boot();

    //     // Garantiza que costo_total siempre sea correcto antes de guardar
    //     static::saving(function ($model) {
    //         $model->costo_total = $model->cantidad * $model->valor_unitario;
    //     });
    // }

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
     * Ciclo productivo al que se carga el jornal (null = labor general de la finca).
     */
    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(CicloProductivo::class, 'ciclo_id');
    }

    /**
     * Trabajador registrado formalmente en la plataforma.
     */
    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'trabajador_id');
    }

    // ==========================================
    // SCOPES DE CONSULTA RÁPIDA
    // ==========================================

    /**
     * Scope: Filtrar por jornales o registros pendientes de pago.
     * Uso: EventoManoObra::pendientesDePago()->get();
     */
    public function scopePendientesDePago($query)
    {
        return $query->where('estado_pago', 'pendiente');
    }

    /**
     * Scope: Filtrar por registros ya pagados.
     */
    public function scopePagados($query)
    {
        return $query->where('estado_pago', 'pagado');
    }

    /**
     * Scope: Obtener la mano de obra de un trabajador específico (por ID o por Cédula).
     * Uso: EventoManoObra::delTrabajador(5)->get();
     */
    public function scopeDelTrabajador($query, $trabajadorIdOrCedula)
    {
        return $query->where(function ($q) use ($trabajadorIdOrCedula) {
            $q->where('trabajador_id', $trabajadorIdOrCedula)
              ->orWhere('cedula', $trabajadorIdOrCedula);
        });
    }

    /**
     * Scope: Filtrar por tipo de labor (ej. 'destajo', 'jornal_dia_completo').
     */
    public function scopePorTipoLabor($query, string $tipoLabor)
    {
        return $query->where('tipo_labor', $tipoLabor);
    }

    /**
     * Scope: Filtrar registros de mano de obra en un rango de fechas.
     * Útil para la nómina semanal o quincenal.
     * Uso: EventoManoObra::enRangoFechas('2026-07-01', '2026-07-07')->get();
     */
    public function scopeEnRangoFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope: Filtrar mano de obra atribuida a un ciclo productivo específico.
     * Atraviesa la relación de EventoCampo para la analítica de costos por cultivo.
     * Uso: EventoManoObra::porCicloProductivo($cicloId)->get();
     */
    public function scopePorCicloProductivo($query, $cicloProductivoId)
    {
        return $query->whereHas('eventoCampo', function ($q) use ($cicloProductivoId) {
            $q->where('ciclo_productivo_id', $cicloProductivoId);
        });
    }

    /**
     * Scope: Filtrar por un Lote/Zona específico.
     * Uso: EventoManoObra::porZona($zonaId)->get();
     */
    public function scopePorZona($query, $zonaId)
    {
        return $query->whereHas('eventoCampo', function ($q) use ($zonaId) {
            $q->where('zona_id', $zonaId);
        });
    }

    // ==========================================
    // 💡 ATRIBUTOS ACCESORES (Helpers de Nombre)
    // ==========================================

    /**
     * Retorna el nombre del trabajador ya sea registrado o externo.
     * Uso: $manoObra->nombre_completo_trabajador
     */
    public function getNombreCompletoTrabajadorAttribute(): string
    {
        if ($this->trabajador) {
            return $this->trabajador->nombre . ' ' . $this->trabajador->apellido;
        }

        return $this->nombre_trabajador ?? 'Trabajador Ocasional';
    }
}