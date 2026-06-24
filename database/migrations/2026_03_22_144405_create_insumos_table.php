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
            Schema::create('insumos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('categoria_id')->constrained('categorias_insumo')->onDelete('cascade');
                $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->onDelete('set null');
                $table->string('nombre');
                $table->string('ingrediente_principal')->nullable();
                $table->enum('unidad_base', ['kg', 'l', 'unidad'])->default('unidad');
                #$table->enum('unidad_uso', ['cc', 'gr', 'unidad'])->default('unidad');
                $table->decimal('factor_conversion', 8, 4)->default(1000);
                $table->enum('nivel_toxicidad', ['bajo', 'medio', 'alto'])->nullable();
                $table->enum('estado', ['activo', 'inactivo'])->default('activo');
                $table->integer('rei_horas')->nullable();
                $table->integer('phi_dias')->nullable();
                $table->string('clasificacion_toxicologica', 20)->nullable(); // 'Ia', 'Ib', 'II', 'III', 'IV'
                $table->string('equipo_proteccion', 255)->nullable();  // texto o JSON
                $table->string('franja_color', 20)->nullable(); // rojo, amarillo, azul, verde
                $table->string('almacenamiento_temp_min', 10)->nullable();
                $table->string('almacenamiento_temp_max', 10)->nullable();
                $table->string('almacenamiento_humedad', 20)->nullable();
                $table->boolean('requiere_refrigeracion')->default(false);
                $table->boolean('sensible_luz')->default(false);
                $table->decimal('stock_minimo', 12,2)->nullable();
                $table->integer('dias_aviso_vencimiento')->default(30);
                $table->timestamps();
            });
            Schema::create('insumo_componentes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('insumo_id')->constrained('insumos')->onDelete('cascade');
                $table->enum('tipo_componente', ['nutriente', 'activo', 'coadyuvante', 'carga', 'otros'])->default('nutriente');
                $table->string('componente');          // 'Nitrógeno', 'Potasio', 'Trichoderma', etc.
                $table->string('unidad');              // '%', 'UFC/g', 'mg/kg'
                $table->decimal('concentracion', 8, 4); // Ej: 46.00 para Urea (46% N)
                $table->timestamps();
            });
            Schema::create('lotes_insumos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('insumo_id')->constrained();
                $table->foreignId('proveedor_id')->nullable()->constrained('proveedores');
                $table->string('codigo_lote');
                $table->string('ubicacion_bodega')->nullable();
                $table->date('fecha_vencimiento');
                $table->date('fecha_ingreso');
                $table->decimal('cantidad_inicial', 12,2);
                $table->decimal('cantidad_actual', 12,2);
                $table->string('unidad', 20);
                $table->decimal('costo_unitario', 12,4);
                $table->enum('estado', ['activo', 'agotado', 'vencido', 'cuarentena'])->default('activo');
                $table->timestamps();
            });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos');
        Schema::dropIfExists('insumo_componentes');
        
        Schema::dropIfExists('lotes_insumos');
        
    }
};
