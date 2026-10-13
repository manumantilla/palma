<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LogicException;

/**
 * Renglón del kardex. Solo lo crea App\Services\InventarioService, que es quien bloquea el lote,
 * valida y actualiza cantidad_actual. Este modelo ya no modifica el inventario por su cuenta.
 */
class MovimientoStock extends Model
{
    use HasFactory;

    protected $table = 'movimientos_stock';

    public const ENTRADAS = ['entrada_saldo_inicial', 'entrada_compra', 'entrada_devolucion', 'ajuste_entrada'];

    public const SALIDAS = [
        'salida_aplicacion', 'salida_devolucion_prov',
        'ajuste_salida_merma', 'ajuste_salida_vencido', 'ajuste_salida_hurto',
    ];

    protected $fillable = [
        'lote_insumo_id',
        'tipo_movimiento',
        'cantidad',
        'costo_unitario',
        'valor_total',
        'stock_resultante',
        'saldo_valor',
        'movimientoable_id',
        'movimientoable_type',
        'fecha_movimiento',
        'observacion',
        'user_id',
    ];

    protected $casts = [
        'cantidad'         => 'decimal:4',
        'costo_unitario'   => 'decimal:4',
        'valor_total'      => 'decimal:2',
        'stock_resultante' => 'decimal:4',
        'saldo_valor'      => 'decimal:2',
        'fecha_movimiento' => 'date',
    ];

    protected static function booted(): void
    {
        // Libro de solo inserción: un error se corrige con un movimiento contrario, nunca editando
        static::updating(fn () => throw new LogicException('Un movimiento de kardex no se modifica; registre un ajuste.'));
        static::deleting(fn () => throw new LogicException('Un movimiento de kardex no se elimina; registre un ajuste.'));
    }

    public static function esEntrada(string $tipo): bool
    {
        return in_array($tipo, self::ENTRADAS, true);
    }

    //Relacion con el lote de insumo especifico afectado
    public function loteInsumo(): BelongsTo
    {
        return $this->belongsTo(LoteInsumo::class, 'lote_insumo_id');
    }

    //El usuario u operario que realizo el movimiento
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //Relacion polimorfica inversa
    public function movimientoable(): MorphTo
    {
        return $this->morphTo();
    }

    // ACCESSORS - FORMATO PARA LA VISTA
    public function getCantidadFormateadaAttribute(): string
    {
        $signo = self::esEntrada($this->tipo_movimiento) ? '+' : '-';
        return $signo . ' ' . number_format((float) $this->cantidad, 2);
    }

    // etiqueta amigable
    public function getTipoMovimientoTextoAttribute(): string
    {
        return match($this->tipo_movimiento){
            'entrada_saldo_inicial'  => 'Saldo Inicial',
            'entrada_compra'         => 'Compra a Proveedor',
            'entrada_devolucion'     => 'Reingreso/Devolución de Campo',
            'ajuste_entrada'         => 'Ajuste de Inventario (Sobrante)',
            'salida_aplicacion'      => 'Aplicación en Cultivo/Lote',
            'salida_devolucion_prov' => 'Devolución a Proveedor',
            'ajuste_salida_merma'    => 'Merma / Daño de Empaque',
            'ajuste_salida_vencido'  => 'Producto Caducado/Vencido',
            'ajuste_salida_hurto'    => 'Faltante no Justificado',
            default                  => 'Movimiento Desconocido',
        };
    }
}
