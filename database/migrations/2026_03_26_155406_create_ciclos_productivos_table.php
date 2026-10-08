<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            // restrict: borrar un lote no debe borrar en cascada su historial productivo y financiero
            $table->foreignId('lote_id')->constrained('lotes')->restrictOnDelete();
            $table->foreignId('cultivo_id')->constrained('cultivos')->restrictOnDelete();
            $table->foreignId('material_genetico_id')->nullable()->constrained('materiales_geneticos')->restrictOnDelete();

            // Estado ADMINISTRATIVO del ciclo. La etapa agronómica sale de ciclo_etapas_historial
            $table->enum('estado', ['planificado', 'activo', 'en_receso', 'concluido', 'siniestrado'])->default('planificado');

            $table->enum('tipo', ['perenne', 'transitorio'])->default('transitorio');
            $table->string('nombre_campana');

            $table->date('fecha_inicio');                 // siembra en campo (transitorio) o trasplante (perenne)
            $table->date('fecha_estimada_cosecha');
            $table->date('fecha_real_inicio_cosecha')->nullable();
            $table->date('fecha_estimada_fin_cosecha');
            $table->date('fecha_real_fin_cosecha')->nullable();
            $table->date('fecha_finalizacion_ciclo')->nullable();

            $table->enum('modalidad_siembra', ['semilla_directa', 'plantula_vivero', 'estaca_esqueje', 'arbol_injertado']);

            $table->decimal('distancia_entre_hileras_metros', 5, 2)->nullable(); // Ej: 3.50 m
            $table->decimal('distancia_entre_plantas_metros', 5, 2)->nullable(); // Ej: 1.20 m
            $table->decimal('plantas_por_hectarea_real', 8, 2); // Calculado automáticamente por el sistema
            // Palma: tresbolillo (triángulo) -> densidad = 10000 / (d² · 0.866)
            $table->enum('arreglo_espacial', ['cuadro', 'rectangulo', 'tresbolillo'])->default('rectangulo');

            $table->foreignId('proveedor_material_vegetal_id')->nullable()->constrained('proveedores');
            $table->string('codigo_lote_vivero_origen', 100)->nullable(); // Pasaporte fitosanitario
            $table->string('registro_autorizacion_institucional', 100)->nullable(); // Ej: Registro ICA de la semilla
            $table->boolean('es_organico_certificado')->default(false);

            $table->foreignId('agronomo_responsable_id')->nullable()->constrained('users');

            $table->decimal('costo_acumulado_directo', 15, 2)->default(0.00); // Sumatoria de insumos + mano de obra
            $table->decimal('costo_acumulado_indirecto', 15, 2)->default(0.00);

            $table->timestamps();

            $table->index(['lote_id', 'estado']);
        });

        Schema::create('ciclo_productivo_zona_manejo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciclo_productivo_id')->constrained('ciclos_productivos')->cascadeOnDelete();
            $table->foreignId('zona_id')->constrained('lotes_zonas_manejo')->cascadeOnDelete();
            $table->decimal('toneladas_producidas', 8, 2)->default(0.00);
            $table->decimal('area_hectareas_momento', 8, 4)->nullable();
            $table->geometry('geometria_zona_momento', 'POLYGON', 4326)->nullable();
            $table->timestamps();
        });

        // Plan vs real de etapas por ciclo.
        // - Admite etapas simultáneas (perennes: fase de vida + varias cohortes de racimo)
        // - numero_repeticion: cohorte N de una etapa 'repetitiva' o temporada N
        // - zona_id: una zona del lote puede ir adelantada/atrasada
        Schema::create('ciclo_etapas_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciclo_productivo_id')->constrained('ciclos_productivos')->cascadeOnDelete();
            $table->foreignId('fenologia_etapa_id')->constrained('fenologia_etapas')->restrictOnDelete();
            $table->foreignId('historial_padre_id')->nullable()->constrained('ciclo_etapas_historial')->cascadeOnDelete();
            $table->foreignId('zona_id')->nullable()->constrained('lotes_zonas_manejo')->restrictOnDelete();
            $table->unsignedInteger('numero_repeticion')->default(1);

            $table->date('fecha_inicio_estimada');
            $table->date('fecha_fin_estimada');
            $table->date('fecha_inicio_real')->nullable();
            $table->date('fecha_fin_real')->nullable();

            // Quién calculó la estimación (Python la irá refinando)
            $table->enum('fuente_estimacion', ['plantilla', 'grados_dia', 'modelo', 'manual'])->default('plantilla');
            $table->decimal('grados_dia_acumulados', 8, 1)->nullable();
            $table->decimal('porcentaje_avance', 5, 2)->nullable();   // desde observaciones de campo

            $table->enum('estado', ['pendiente', 'en_progreso', 'completada', 'omitida'])->default('pendiente');
            $table->string('motivo_desviacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Requerido por la FK compuesta de gastos (la etapa debe ser del mismo ciclo)
            $table->unique(['id', 'ciclo_productivo_id'], 'ciclo_etapas_id_ciclo_unique');
            $table->index(['ciclo_productivo_id', 'estado']);
            $table->index(['estado', 'fecha_inicio_estimada']);
        });

        // Evita duplicar la misma etapa/cohorte (PG15: NULLS NOT DISTINCT trata zona null como valor)
        DB::statement('CREATE UNIQUE INDEX ciclo_etapas_unica_idx ON ciclo_etapas_historial
            (ciclo_productivo_id, fenologia_etapa_id, zona_id, numero_repeticion) NULLS NOT DISTINCT');
        DB::statement('ALTER TABLE ciclo_etapas_historial ADD CONSTRAINT ciclo_etapas_fechas_chk CHECK (
            fecha_fin_estimada >= fecha_inicio_estimada
            AND (fecha_inicio_real IS NULL OR fecha_fin_real IS NULL OR fecha_fin_real >= fecha_inicio_real)
            AND numero_repeticion >= 1)');
    }

    public function down(): void
    {
        Schema::dropIfExists('ciclo_etapas_historial');
        Schema::dropIfExists('ciclo_productivo_zona_manejo');
        Schema::dropIfExists('ciclos_productivos');
    }
};
