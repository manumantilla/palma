<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Cada lote es una entrada física a bodega con su propio vencimiento y costo.
 * cantidad_actual solo la modifica App\Services\InventarioService (nunca un controlador).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes_insumos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insumo_id')->constrained('insumos')->restrictOnDelete();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->restrictOnDelete();
            // Null = saldo inicial, donación o ingreso sin factura
            $table->foreignId('compra_id')->nullable()->constrained('compras')->restrictOnDelete();
            $table->foreignId('compra_item_id')->nullable()->constrained('compra_items')->restrictOnDelete();
            //  $table->foreignId('bodega_id')->nullable()->constrained('bodegas');
            $table->string('codigo_lote');
            // Null para lo que no vence (herramientas, EPP). El servicio la exige si la categoría maneja_vencimiento
            $table->date('fecha_vencimiento')->nullable();
            $table->date('fecha_ingreso');
            // Siempre en la unidad base del insumo; 4 decimales para que 15 mL = 0.0150 L no se redondee a 0.02
            $table->decimal('cantidad_inicial', 14, 4);
            $table->decimal('cantidad_actual', 14, 4);
            $table->foreignId('unidad_id')->constrained('unidades_medida')->restrictOnDelete();
            $table->decimal('costo_unitario', 14, 4); // COP por unidad base, IVA no descontable incluido
            $table->enum('estado', ['activo', 'agotado', 'vencido', 'cuarentena'])->default('activo');
            $table->timestamps();

            // Orden FEFO: los lotes disponibles de un insumo, del que vence primero al último
            $table->index(['insumo_id', 'estado', 'fecha_vencimiento']);
        });

        DB::statement('ALTER TABLE lotes_insumos ADD CONSTRAINT lotes_insumos_cantidades_chk
            CHECK (cantidad_inicial > 0 AND cantidad_actual >= 0 AND costo_unitario >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes_insumos');
    }
};
