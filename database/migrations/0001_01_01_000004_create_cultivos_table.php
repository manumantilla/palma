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
        Schema::create('cultivos', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['perenne', 'transitorio']);
            $table->string('nombre_cultivo')->unique();
            $table->string('nombre_cientifico')->nullable();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        // Variedad / híbrido / clon. Palma: E. guineensis vs híbrido OxG tienen fenología y labores distintas
        Schema::create('materiales_geneticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cultivo_id')->constrained('cultivos')->cascadeOnDelete();
            $table->string('nombre');                     // "Híbrido OxG Coari x La Mé", "Castilla", "ICA V-305"
            $table->enum('tipo', ['variedad', 'hibrido', 'clon', 'linea', 'criollo'])->default('variedad');
            $table->string('casa_comercial')->nullable();
            $table->string('registro_ica', 100)->nullable();
            $table->jsonb('caracteristicas')->nullable(); // tolerancias (PC, sequía), potencial t/ha, etc.
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['cultivo_id', 'nombre']);
        });

        // Plan teórico del cultivo.
        // tipo_fase:
        //   secuencial -> transitorios: una etapa tras otra (maíz, frijol)
        //   vida       -> perennes: fases de años (vivero, inmadura, producción...)
        //   repetitiva -> cohortes que se repiten y se superponen (racimo de palma, floración
        //                 continua del lulo). Corren mientras el ciclo esté en una fase 'vida'
        //                 con es_cosechable = true.
        //   temporada  -> ciclos anuales anclados al régimen de lluvias (café, aguacate)
        // duracion_dias_desde_inicio: desfase desde el inicio del ciclo (secuencial/vida)
        //                             o desde el inicio de la cohorte (repetitiva).
        Schema::create('fenologia_etapas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cultivo_id')->constrained('cultivos')->cascadeOnDelete();
            // Null si aplica a todos los materiales, ej. lulo castilla y la selva con las mismas etapas
            $table->foreignId('material_genetico_id')->nullable()->constrained('materiales_geneticos')->cascadeOnDelete();
            $table->foreignId('etapa_padre_id')->nullable()->constrained('fenologia_etapas')->cascadeOnDelete(); // sub-etapas (V1..Vn)

            $table->enum('tipo_fase', ['secuencial', 'vida', 'repetitiva', 'temporada'])->default('secuencial');
            $table->string('nombre'); // "Floración", "Cuajado", "Maduración", etc.
            $table->text('descripcion')->nullable();
            $table->unsignedSmallInteger('orden');  // orden de aparición

            // Escala BBCH (00-99)
            $table->unsignedSmallInteger('bbch_inicio')->nullable();
            $table->unsignedSmallInteger('bbch_fin')->nullable();

            // Duración en días calendario, con rango para que Python haga escenarios
            $table->integer('duracion_dias_desde_inicio')->nullable();
            $table->integer('duracion_dias_estimada');
            $table->integer('duracion_dias_min')->nullable();
            $table->integer('duracion_dias_max')->nullable();

            // Tiempo térmico: más preciso que días en Colombia por la altitud
            $table->decimal('grados_dia_requeridos', 7, 1)->nullable();
            $table->decimal('temperatura_base_c', 4, 1)->nullable();   // maíz ~10 °C

            // Solo para 'repetitiva' y 'temporada'
            $table->unsignedSmallInteger('periodicidad_dias')->nullable(); // palma adulta: nueva cohorte ~cada 15 días
            $table->unsignedSmallInteger('mes_inicio_tipico')->nullable(); // 1-12

            $table->boolean('es_cosechable')->default(false); // en esta etapa se cosecha
            $table->boolean('es_critica')->default(false);    // sensible a estrés hídrico/térmico
            $table->timestamps();

            $table->index(['cultivo_id', 'material_genetico_id', 'tipo_fase', 'orden'], 'fenologia_etapas_lookup_idx');
        });

        DB::statement('ALTER TABLE fenologia_etapas ADD CONSTRAINT fenologia_bbch_rango_chk
            CHECK ((bbch_inicio IS NULL OR bbch_inicio <= 99) AND (bbch_fin IS NULL OR bbch_fin <= 99)
                   AND (bbch_inicio IS NULL OR bbch_fin IS NULL OR bbch_inicio <= bbch_fin))');
        DB::statement('ALTER TABLE fenologia_etapas ADD CONSTRAINT fenologia_duracion_rango_chk
            CHECK (duracion_dias_estimada > 0
                   AND (duracion_dias_min IS NULL OR duracion_dias_min <= duracion_dias_estimada)
                   AND (duracion_dias_max IS NULL OR duracion_dias_max >= duracion_dias_estimada))');
        DB::statement("ALTER TABLE fenologia_etapas ADD CONSTRAINT fenologia_repetitiva_chk
            CHECK (tipo_fase <> 'repetitiva' OR periodicidad_dias IS NOT NULL)");
        DB::statement("ALTER TABLE fenologia_etapas ADD CONSTRAINT fenologia_temporada_chk
            CHECK (tipo_fase <> 'temporada' OR (mes_inicio_tipico IS NOT NULL AND mes_inicio_tipico BETWEEN 1 AND 12))");

        Schema::create('labores_plantilla', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cultivo_id')->constrained('cultivos')->cascadeOnDelete();
            $table->foreignId('material_genetico_id')->nullable()->constrained('materiales_geneticos')->cascadeOnDelete();
            $table->string('nombre_labor'); // "Fertilización nitrogenada", "Poda sanitaria"
            $table->text('descripcion')->nullable();

            // Programación
            $table->enum('momento_tipo', ['dias_desde_siembra', 'etapa_fenologica', 'fecha_fija_anual']);
            $table->integer('dias_desde_siembra')->nullable();   // si momento_tipo = dias_desde_siembra
            $table->foreignId('fenologia_etapa_id')->nullable()->constrained('fenologia_etapas')->nullOnDelete(); // si por etapa
            $table->unsignedSmallInteger('mes_fijo')->nullable(); // si fecha_fija_anual
            $table->unsignedSmallInteger('dia_fijo')->nullable();
            $table->integer('periodicidad_dias')->nullable();     // cada cuántos días se repite (ej. cada 30 días)
            $table->unsignedSmallInteger('ventana_ejecucion_dias')->nullable();
            $table->integer('duracion_estimada_horas')->nullable();

            // Recursos estándar, para el presupuesto del ciclo (insumos en labor_insumos_plan)
            $table->foreignId('tipo_evento_id')->constrained('tipos_evento');
            $table->boolean('requiere_insumos')->default(false);
            $table->boolean('requiere_mano_obra')->default(true);
            $table->decimal('jornales_por_ha', 6, 2)->nullable();
            $table->decimal('horas_maquina_por_ha', 6, 2)->nullable();

            $table->enum('prioridad', ['Baja', 'Media', 'Alta', 'Crítica'])->default('Media');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Ojo: en un CHECK, NULL cuenta como "pasa"; por eso los IS NOT NULL explícitos.
        DB::statement("ALTER TABLE labores_plantilla ADD CONSTRAINT labores_momento_chk CHECK (
            (momento_tipo = 'dias_desde_siembra' AND dias_desde_siembra IS NOT NULL) OR
            (momento_tipo = 'etapa_fenologica'   AND fenologia_etapa_id IS NOT NULL) OR
            (momento_tipo = 'fecha_fija_anual'   AND mes_fijo IS NOT NULL AND dia_fijo IS NOT NULL
                                                 AND mes_fijo BETWEEN 1 AND 12 AND dia_fijo BETWEEN 1 AND 31)
        )");

        $now = now();
        DB::table('cultivos')->insert([
            ['tipo' => 'perenne',     'nombre_cultivo' => 'Palma de Aceite', 'nombre_cientifico' => 'Elaeis guineensis / E. oleifera x E. guineensis', 'descripcion' => 'Cultivo de palma para producción de aceite.', 'created_at' => $now, 'updated_at' => $now],
            ['tipo' => 'transitorio', 'nombre_cultivo' => 'Maíz',            'nombre_cientifico' => 'Zea mays',           'descripcion' => 'Cultivo anual de maíz para consumo humano y animal.', 'created_at' => $now, 'updated_at' => $now],
            ['tipo' => 'transitorio', 'nombre_cultivo' => 'Frijol',          'nombre_cientifico' => 'Phaseolus vulgaris', 'descripcion' => 'Cultivo anual de frijol para consumo humano.', 'created_at' => $now, 'updated_at' => $now],
            ['tipo' => 'perenne',     'nombre_cultivo' => 'Lulo',            'nombre_cientifico' => 'Solanum quitoense',  'descripcion' => 'Cultivo de lulo para producción de jugo.', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labores_plantilla');
        Schema::dropIfExists('fenologia_etapas');
        Schema::dropIfExists('materiales_geneticos');
        Schema::dropIfExists('cultivos');
    }
};
