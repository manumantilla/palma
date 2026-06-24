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
        Schema::create('tipos_evento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->enum('categoria', ['Mantenimiento', 'Fitosanitario', 'Cosecha', 'Fertilización', 'Logística']);
            $table->boolean('consume_insumos')->default(false);
            $table->boolean('consume_mano_obra')->default(false);
            $table->boolean('genera_ingreso')->default(false);
            $table->boolean('genera_movimiento_stock')->default(false);

            $table->boolean('requiere_area_ha')->default(false);   // fumigación, riego, encalado
            $table->boolean('aplica_a_arbol')->default(false);     // poda, polinización (perennes)
            $table->boolean('aplica_a_ciclo')->default(true);

            // Para eventos fitosanitarios: período de reingreso al lote post-aplicación
            $table->unsignedSmallInteger('periodo_reingreso_horas')->nullable();
            $table->unsignedSmallInteger('periodo_carencia_dias')->nullable(); // Días antes de cosecha
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_evento');
    }
};
