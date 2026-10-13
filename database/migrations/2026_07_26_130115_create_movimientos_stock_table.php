<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Kardex valorizado. Es un libro de solo inserción: un movimiento nunca se edita ni se borra,
 * los errores se corrigen con un movimiento contrario. Lo escribe únicamente InventarioService.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_stock', function (Blueprint $table) {
            $table->id();
            // restrict: un lote con historial no se puede borrar
            $table->foreignId('lote_insumo_id')->constrained('lotes_insumos')->restrictOnDelete();

            $table->enum('tipo_movimiento', [
                // --- ENTRADAS (Suman al inventario) ---
                'entrada_saldo_inicial',   // Lo que ya había en bodega el día que la finca empezó a usar el sistema.
                'entrada_compra',          // Compra estándar a proveedor.
                'entrada_devolucion',      // El operario devolvió a bodega lo que le sobró de una labor.
                'ajuste_entrada',          // Se encontraron cajas de más en un conteo físico (sobrantes).

                // --- SALIDAS (Restan al inventario) ---
                'salida_aplicacion',       // Consumo real en el campo (Fumigación, fertilización, etc.).
                'salida_devolucion_prov',  // Devolución de producto defectuoso al proveedor.
                'ajuste_salida_merma',     // Pérdida por evaporación, derrame o daño físico del empaque.
                'ajuste_salida_vencido',   // Producto caducado que toca descartar de bodega.
                'ajuste_salida_hurto',     // Triste pero pasa; descuadres por robo o pérdida no justificada.
            ]);

            // Cantidades en la unidad del lote (unidad base del insumo), siempre positivas: el tipo da el signo
            $table->decimal('cantidad', 14, 4);
            $table->decimal('costo_unitario', 14, 4);
            $table->decimal('valor_total', 16, 2);

            // Saldo del lote después del movimiento, en cantidad y en pesos
            $table->decimal('stock_resultante', 14, 4);
            $table->decimal('saldo_valor', 16, 2);

            $table->morphs('movimientoable'); // Compra, CompraItem, EventoInsumoLote, LoteInsumo (ajustes)...
            $table->date('fecha_movimiento'); // cuándo pasó en bodega; created_at es cuándo se digitó
            $table->string('observacion', 255)->nullable();

            // restrict: no se borra un usuario que tiene movimientos auditables; se desactiva
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['tipo_movimiento', 'fecha_movimiento']);
            $table->index(['lote_insumo_id', 'id']);
        });

        DB::statement('ALTER TABLE movimientos_stock ADD CONSTRAINT movimientos_stock_valores_chk
            CHECK (cantidad > 0 AND costo_unitario >= 0 AND stock_resultante >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_stock');
    }
};
