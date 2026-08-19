<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Contenedor extends Model
{
    use HasFactory;

    protected $table = 'contenedores';

    // Desactivamos el autoincremento porque usamos UUID
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'sesion_id',
        'cliente_id',
        'orden_pedido_id',
        'estado',
        'nombre',
        'variedad',
        'calidad',
        'tipo_destino',
        'calibre_talla',
        'kilos_acumulados',
        'peso_tara',
        'peso_total',
        'kilos_merma_acumulada',
        'client_updated_at',
        'synced_at',
    ];

    protected $casts = [
        'kilos_acumulados' => 'decimal:2',
        'peso_tara' => 'decimal:2',
        'peso_total' => 'decimal:2',
        'kilos_merma_acumulada' => 'decimal:2',
        'client_updated_at' => 'datetime',
        'synced_at' => 'datetime',
    ];

    // ==========================================
    // ⚙️ BOOT & EVENTOS (UUID + Auto-Pesaje)
    // ==========================================
    protected static function boot()
    {
        parent::boot();

        // Asignación de UUID si no viene presente
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });

        // Recálculo del peso bruto total antes de guardar
        static::saving(function ($model) {
            $model->peso_total = $model->kilos_acumulados + $model->peso_tara;
        });
    }

    // ==========================================
    // 🔗 RELACIONES ELOQUENT
    // ==========================================

    public function sesionCosecha(): BelongsTo
    {
        return $this->belongsTo(SesionCosecha::class, 'sesion_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function ordenPedido(): BelongsTo
    {
        return $this->belongsTo(OrdenCosecha::class, 'orden_pedido_id');
    }

    public function movimientosClasificacion(): HasMany
    {
        return $this->hasMany(MovimientoClasificacion::class, 'contenedor_id');
    }

    public function mermas(): HasMany
    {
        return $this->hasMany(Merma::class, 'contenedor_id');
    }

    // ==========================================
    // 💡 ACCESORES (Cálculos de Negocio)
    // ==========================================

    /**
     * Retorna los kilos netos comercializables (Acumulado - Mermas).
     * Uso: $contenedor->kilos_netos_comercializables
     */
    public function getKilosNetosComercializablesAttribute(): float
    {
        return max(0, $this->kilos_acumulados - $this->kilos_merma_acumulada);
    }

    // ==========================================
    // 🔍 SCOPE DE FILTROS PARA EL INDEX
    // ==========================================

    /**
     * Scope dinámico que procesa los filtros provenientes del Request HTTP.
     * Uso en Controller: Contenedor::filtrar($request->all())->paginate(15);
     */
    public function scopeFiltrar($query, array $filtros)
    {
        // 1. Búsqueda por texto (Nombre del contenedor o Variedad)
        if (!empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('variedad', 'LIKE', "%{$search}%");
            });
        }

        // 2. Filtro por Estado (abierta, cerrada, despachada)
        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        // 3. Filtro por Calidad (extra, primera, segunda, etc.)
        if (!empty($filtros['calidad'])) {
            $query->where('calidad', $filtros['calidad']);
        }

        // 4. Filtro por Tipo de Destino (exportacion, mercado_local, etc.)
        if (!empty($filtros['tipo_destino'])) {
            $query->where('tipo_destino', $filtros['tipo_destino']);
        }

        // 5. Filtro por Calibre/Talla
        if (!empty($filtros['calibre_talla'])) {
            $query->where('calibre_talla', $filtros['calibre_talla']);
        }

        // 6. Filtro por Sesión de Cosecha activa
        if (!empty($filtros['sesion_id'])) {
            $query->where('sesion_id', $filtros['sesion_id']);
        }

        // 7. Filtro por Cliente asignado
        if (!empty($filtros['cliente_id'])) {
            $query->where('cliente_id', $filtros['cliente_id']);
        }

        // 8. Filtro por Contenedores con Mermas Registradas (> 0 kg)
        if (isset($filtros['con_mermas']) && $filtros['con_mermas'] == '1') {
            $query->where('kilos_merma_acumulada', '>', 0);
        }

        // 9. Rango de Fechas de Creación
        if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
            $query->whereBetween('created_at', [$filtros['fecha_inicio'], $filtros['fecha_fin']]);
        }

        return $query;
    }
}