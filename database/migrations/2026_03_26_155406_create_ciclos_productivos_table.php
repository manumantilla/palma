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
        Schema::create('ciclos_productivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->onDelete('cascade');
            $table->foreignId('cultivo_id')->constrained('cultivos')->onDelete('cascade');
            $table->enum('estado', [
                'preparacion_suelo', 'siembra_establecimiento', 'desarrollo_vegetativo', 
                'floracion_llenado', 'cosecha_activa', 'receso_invernal_poda', 
                'concluido', 'siniestrado_perdida'
            ])->default('preparacion_suelo');

            $table->enum('tipo', ['perenne', 'transitorio'])->default('transitorio');
            $table->string('nombre_campana');

            $table->date('fecha_inicio');
            $table->date('fecha_estimada_cosecha');
            $table->date('fecha_real_inicio_cosecha')->nullable();
            $table->date('fecha_estimada_fin_cosecha');
            $table->date('fecha_real_fin_cosecha')->nullable();
            $table->date('fecha_finalizacion_ciclo')->nullable();

            $table->enum('modalidad_siembra', ['semilla_directa', 'plantula_vivero', 'estaca_esqueje', 'arbol_injertado']);

            $table->decimal('distancia_entre_hileras_metros', 5, 2)->nullable(); // Ej: 3.50 m
            $table->decimal('distancia_entre_plantas_metros', 5, 2)->nullable(); // Ej: 1.20 m
            $table->decimal('plantas_por_hectarea_real', 8, 2); // Calculado automáticamente por el sistema

            $table->foreignId('proveedor_material_vegetal_id')->nullable()->constrained('proveedores');
            $table->string('codigo_lote_vivero_origen', 100)->nullable(); // Pasaporte fitosanitario
            $table->string('registro_autorizacion_institucional', 100)->nullable(); // Ej: Registro ICA de la semilla
            $table->boolean('es_organico_certificado')->default(false);

            $table->foreignId('agronomo_responsable_id')->nullable()->constrained('users');


            $table->decimal('costo_acumulado_directo', 15, 2)->default(0.00); // Sumatoria de insumos + mano de obra
            $table->decimal('costo_acumulado_indirecto', 15, 2)->default(0.00);


            $table->timestamps();
        });
        Schema::create('ciclo_productivo_zona_manejo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciclo_productivo_id')->constrained('ciclos_productivos')->onDelete('cascade');
            $table->foreignId('zona_id')->constrained('lotes_zonas_manejo')->onDelete('cascade');
            
            // --- CAMPOS DE CONGELACIÓN (SNAPSHOT) ---
            $table->decimal('toneladas_producidas', 8, 2)->default(0.00); // Ej: 3.00 toneladas
            $table->decimal('area_hectareas_momento', 8, 4)->nullable(); // Cuánto medía la zona en ESE ciclo
            // Opcional (Toque Pro): Guardas el polígono exacto que se usó en ese ciclo por si se deforma el original
            $table->geometry('geometria_zona_momento', 'POLYGON', 4326)->nullable();
            $table->timestamps();
        });
        Schema::create('ciclo_etapas_historial', function (Blueprint $table){
            $table->id();
            $table->foreignId('ciclo_productivo_id')->constrained('ciclos_productivos')->onDelete('cascade');
            $table->foreignId('fenologia_etapa_id')->constrained('fenologia_etapas')->onDelete('cascade');
            $table->date('fecha_inicio_estimada');
            $table->date('fecha_inicio_real')->nullable();
            $table->date('fecha_fin_estimada');
            $table->date('fecha_fin_real')->nullable();
            $table->enum('estado', ['pendiente', 'en_progreso', 'completada', 'omitida'])->default('pendiente');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('ciclos_productivos');
        Schema::dropIfExists('ciclo_productivo_zona_manejo');
        Schema::dropIfExists('ciclo_etapas_historial'); 
    }
};
