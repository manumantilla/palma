<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoStock extends Model
{
    protected $table = 'movimientos_stock';
    
    use HasFactory;

    protected $fillable = [
        'lote_insumo_id',
        'tipo_movimiento',
        'cantidad',
        'movimientoable_id',
        'movimientoable_type',
        'stock_resultante',
        'observacion',
        'user_id',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'stock_resultante' => 'decimal:2'
    ];

    //Relacion con el lote de insumo especifico afectado
    public function loteInsumo(): BelongsTo
    {
        return $this->belongsTo(LoteInsumo::class, 'lote_insumo_id');
    }

    //El usuario u operario que realizo el movimiento
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //Relacion polimorfica inversa
    public function movimientoable()
    {
        return $this->morphTo();
    }

    // ACCESSORS - FORMATO PARA LA VISTA
    public function getCantidadFormateadaAttribute()
    {
        $entradas = ['entrada_compra', 'entrada_devolucion', 'ajuste_entrada'];
        $signo = in_array($this->tipo_movimiento, $entradas) ? '+' : '-';
        return $signo . ' ' . number_format($this->cantidad, 2);
    }

    // etiqueta amigable
    public function getTipoMovimientoTextoAttribute()
    {
        return match($this->tipo_movimiento){
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
    
    //Trigger logico de laravel 

    protected static function booted()
    {
        static::creating(function ($movimiento) {
            $lote = LoteInsumo::findOrFail($movimiento->lote_insumo_id);
            $tiposEntrada = ['entrada_compra', 'entrada_devolucion', 'ajuste_entrada'];

            if(in_array($movimiento->tipo_movimiento, $tiposEntrada)){
                //Suman stock
                $lote->cantidad_actual += $movimiento->cantidad;
            } else {
                $lote->cantidad_actual -= $movimiento->cantidad;
            }

            $lote->save();
            $movimiento->stock_resultante = $lote->cantidad_actual;
        });
    }

}
