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
    Schema::create('despacho_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('despacho_id')->constrained('despachos')->onDelete('cascade');
        $table->foreignId('contenedor_id')->nullable()->constrained('contenedores')->onDelete('set null');
        
        // NUEVO: Origen a nivel de ítem por si se despacha directo del campo o contenedores puros de un lote
        $table->foreignId('ciclo_productivo_id')->nullable()->constrained('ciclos_productivos')->onDelete('set null');
        //+$table->foreignId('lote_id')->nullable()->constrained('lotes')->onDelete('restrict');

        // Atributos de la fruta (se llenan si contenedor_id es null, sino, se heredan por software)
        $table->string('variedad')->nullable();
        $table->enum('calidad', ['extra', 'primera', 'segunda', 'industria', 'sin_clasificar'])->nullable();
        
        // Logística de empaque
        $table->enum('tipo_empaque', ['bulto', 'costal', 'canastilla', 'caja', 'paca', 'granel']);
        $table->integer('cantidad_unidades');               
        $table->decimal('peso_promedio_unidad', 8, 2)->nullable();
        
        // Pesos con tu excelente columna calculada por la DB
        $table->decimal('peso_bruto_total', 12, 2);        
        $table->decimal('tara_total', 8, 2)->default(0);   
        $table->decimal('peso_neto_total', 12, 2)->storedAs('peso_bruto_total - tara_total');

        // Precios y Cierre Financiero
        $table->decimal('precio_unitario_kg', 10, 2)->nullable(); // Precio pactado de salida
        $table->decimal('precio_liquidado_kg', 10, 2)->nullable(); // NUEVO: Lo que pagaron real en la plaza

        $table->decimal('descuento_kg', 8, 2)->default(0);
        $table->text('motivo_descuento')->nullable();

        $table->text('descripcion')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('despacho_items');
    }
};
