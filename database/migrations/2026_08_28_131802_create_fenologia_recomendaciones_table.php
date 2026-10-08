<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fenologia_recomendaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fenologia_etapa_id')->constrained('fenologia_etapas')->cascadeOnDelete();
            $table->foreignId('tipo_evento_id')->nullable()->constrained('tipos_evento')->nullOnDelete();
            // ej. polinización asistida solo para OxG
            $table->foreignId('material_genetico_id')->nullable()->constrained('materiales_geneticos')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descripcion');
            $table->enum('prioridad', ['Baja', 'Media', 'Alta', 'Crítica'])->default('Media');

            // Programación y automatización
            $table->integer('dias_offset')->default(0); // Días (+/-) desde el inicio de la etapa para activar la recomendación
            $table->unsignedSmallInteger('ventana_ejecucion_dias')->nullable(); // genera eventos_campo.fecha_limite
            $table->unsignedSmallInteger('periodicidad_dias')->nullable();      // repetir dentro de la etapa (ronda de cosecha c/12 días)
            $table->unsignedSmallInteger('repeticiones_maximas')->nullable();   // null = mientras dure la etapa
            $table->boolean('genera_evento_automatico')->default(false); // Determina si inserta automáticamente en eventos_campo
            $table->boolean('requiere_verificacion_campo')->default(true);

            // Condición opcional que evalúa Python antes de activar (umbral de plaga, lluvia, etc.)
            // ej: {"variable":"precipitacion_7d_mm","operador":">","valor":80}
            $table->jsonb('condicion_activacion')->nullable();

            // Recursos estándar (insumos y dosis en labor_insumos_plan)
            $table->decimal('jornales_por_ha', 6, 2)->nullable();
            $table->decimal('horas_maquina_por_ha', 6, 2)->nullable();
            $table->text('instrucciones_tecnicas')->nullable(); // técnica de aplicación o criterios de evaluación

            $table->softDeletes();
            $table->timestamps();

            $table->index(['fenologia_etapa_id', 'genera_evento_automatico']);
        });

        // Insumos planificados, estructurados (ya no en texto libre).
        // Sirve para recomendaciones y labores_plantilla -> presupuesto, verificación de stock
        // y validación de carencia (insumos.phi_dias) contra cosechas programadas.
        Schema::create('labor_insumos_plan', function (Blueprint $table) {
            $table->id();
            $table->morphs('planificable');       // FenologiaRecomendacion | LaborPlantilla
            $table->foreignId('insumo_id')->constrained('insumos')->restrictOnDelete();
            $table->decimal('dosis', 12, 4);
            $table->foreignId('unidad_medida_id')->constrained('unidades_medida');
            $table->enum('base_dosis', ['hectarea', 'planta', 'litro_mezcla', 'total'])->default('hectarea');
            $table->boolean('es_alternativa')->default(false); // producto sustituto
            $table->string('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labor_insumos_plan');
        Schema::dropIfExists('fenologia_recomendaciones');
    }
};
