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
        Schema::create('mantenimientos_maquinaria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maquina_id')->constrained('maquinaria')->restrictOnDelete();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->restrictOnDelete(); // taller externo
            $table->enum('tipo_mantenimiento',['preventivo','correctivo','calibracion']);
            $table->date('fecha');
            $table->text('descripcion_trabajo');
            $table->decimal('costo_mano_obra_mecanico',12,2);
            $table->decimal('costo_materiales',12,2);
            $table->decimal('costo_total', 12, 2)->storedAs('costo_mano_obra_mecanico + costo_materiales');
            //hora proxima de mantenimiento
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos_maquinaria');
    }
};
