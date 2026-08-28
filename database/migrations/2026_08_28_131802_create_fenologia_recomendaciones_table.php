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
            $table->foreignId('fenologia_etapa_id')->constrained('fenologia_etapas')->onDelete('cascade');
            $table->foreignId('tipo_evento_id')->nullable()->constrained('tipos_evento')->onDelete('set null');
    
            $table->string('titulo');
            $table->text('descripcion');
            $table->enum('prioridad', ['Baja', 'Media', 'Alta', 'Crítica'])->default('Media');
            // Programación y automatización
            $table->integer('dias_offset')->default(0); // Días (+/-) desde el inicio de la etapa para activar la recomendación
            $table->unsignedSmallInteger('ventana_ejecucion_dias')->nullable(); // Días permitidos para completar la tarea
            $table->boolean('genera_evento_automatico')->default(false); // Determina si inserta automáticamente en eventos_campo
            $table->boolean('requiere_verificacion_campo')->default(true); 
            // Parámetros técnicos específicos
            $table->text('instrucciones_tecnicas')->nullable(); // Dosis sugeridas, técnica de aplicación o criterios de evaluación
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fenologia_recomendaciones');
    }
};
