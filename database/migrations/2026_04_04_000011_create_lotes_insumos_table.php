<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
         Schema::create('lotes_insumos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('insumo_id')->constrained();
                $table->foreignId('proveedor_id')->nullable()->constrained('proveedores');
                $table->foreignId('compra_id')->constrained('compras')->nullable();
              //  $table->foreignId('bodega_id')->nullable()->constrained('bodegas');
                $table->string('codigo_lote');
                $table->date('fecha_vencimiento');
                $table->date('fecha_ingreso');
                $table->decimal('cantidad_inicial', 12,2);
                $table->decimal('cantidad_actual', 12,2);
                $table->foreignId('unidad_id')->constrained('unidades_medida')->after('cantidad_actual');
                $table->decimal('costo_unitario', 12,4);
                $table->enum('estado', ['activo', 'agotado', 'vencido', 'cuarentena'])->default('activo');
                $table->timestamps();
            });
    }


    public function down(): void
    {
        Schema::dropIfExists('lotes_insumos');
    }
};
