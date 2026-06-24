<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movimientos_stock', function (Blueprint $table) {
            $table->id();
            // 1. Corrección del nombre de la tabla (lotes_insumos)
            $table->foreignId('lote_insumo_id')->constrained('lotes_insumos')->onDelete('cascade');
            
            $table->enum('tipo_movimiento', [
                // --- ENTRADAS (Suman al inventario) ---
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
            $table->decimal('cantidad', 12, 2);
            $table->morphs('movimientoable'); // Polimorfismo: Compra, Aplicación, Ajuste, etc.
            $table->decimal('stock_resultante', 12, 2); // Excelente práctica para auditorías rápidas
            
            // 2. Campo para justificar ajustes o pérdidas
            $table->string('observacion', 255)->nullable(); 

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Índices vitales para reportes de consumo mensual
            $table->index(['tipo_movimiento', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_stock');
    }
};
