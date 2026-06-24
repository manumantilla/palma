<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arboles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciclo_productivo_id')->constrained('ciclos_productivos')->onDelete('cascade');
            $table->foreignId('lote_id')->constrained('lotes')->onDelete('cascade'); // redundante pero útil para queries directas
            $table->foreignId('lote_zona_manejo_id')->nullable()->constrained('lotes_zonas_manejo')->nullOnDelete();
            // Identificación física
            $table->string('codigo_unico')->unique();           // Ej: "A-01-03" (fila 1, posición 3)

            $table->integer('fila_indice')->index();
            $table->integer('posicion_indice')->index();
             $table->decimal('altitud', 8, 2)->nullable();

            $table->enum('estado_vital', ['excelente', 'con_estres', 'enfermo_critico', 'muerto', 'erradicado'])->default('excelente');
            $table->enum('etapa_biologica', ['vivero', 'establecimiento', 'desarrollo_inmaduro', 'produccion_madura', 'senescencia'])->default('establecimiento');

            $table->date('fecha_baja_muerte')->nullable();
            $table->string('motivo_baja')->nullable(); // Ej: Ceratocystis fimbriata, Rayo, Mecanización
            // GEO-REFERENCIACIÓN POSTGIS EXCLUSIVA: POINT SRID 4326
            $table->geometry('coordenada_precision', 'GEOMETRY', 4326)->nullable();
            $table->decimal('altitud_ortometrica_msnm', 6, 2)->nullable();

            // Datos agronómicos
            $table->date('fecha_siembra')->nullable();
            $table->date('fecha_primera_cosecha')->nullable();// o calculado desde fecha_siembra
            $table->string('variedad')->nullable();            // si hay mezcla de variedades en el lote


            $table->date('fecha_muerte')->nullable();
            $table->string('causa_muerte')->nullable();       // helada, plaga, volcamiento, etc.
            $table->boolean('es_reemplazo')->default(false);
            $table->date('fecha_reemplazo')->nullable();

            // Productividad
            $table->decimal('produccion_acumulada_kg', 12, 2)->default(0);
            $table->integer('ciclos_productivos_count')->default(0); // cuántas cosechas ha tenido

            $table->text('observaciones')->nullable();  
            $table->softDeletes();
            $table->timestamps();
        });

        DB::statement('CREATE INDEX arboles_coordenada_spatial_index ON arboles USING gist (coordenada_precision);');

        // 2. TABLA AUXILIAR: MÉTRICAS ALOMÉTRICAS Y AGRICULTURA DE PRECISIÓN (Drones / IoT)
        Schema::create('arboles_metricas_historicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arbol_id')->constrained('arboles')->cascadeOnDelete();
            $table->dateTime('fecha_medicion');
            
            // Dimensiones Físicas
            $table->decimal('altura_metros', 4, 2)->nullable();
            $table->decimal('diametro_tronco_cm', 5, 2)->nullable(); // Tomado con sensores o dendrómetros
            $table->decimal('diametro_copa_proyeccion_m', 4, 2)->nullable();
            $table->decimal('volumen_copa_calculado_m3', 6, 2)->nullable();
            
            // Índices de Sensores y Teledetección (Drones / Satélites / NDVI)
            $table->decimal('indice_ndvi_medido', 4, 3)->nullable(); // Rango de -1.000 a 1.000
            $table->decimal('indice_ndre_medido', 4, 3)->nullable();
            $table->decimal('temperatura_canopia_celsius', 4, 2)->nullable();
            
            // Estado Fenológico BBCH
            $table->integer('codigo_escala_bbch')->nullable(); // Sistema universal internacional de estados fenológicos
            
            $table->string('origen_datos')->default('manual'); // manual, dron_lidar, satelite_sentinel, sensor_iot
            $table->timestamps();

            $table->index(['arbol_id', 'fecha_medicion']);
        });

        // 3. TABLA AUXILIAR: HISTORIAL FITOSANITARIO Y MONITOREOS INDIVIDUALES
        Schema::create('arboles_historial_fitosanitario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arbol_id')->constrained('arboles')->cascadeOnDelete();
            $table->dateTime('fecha_hallazgo');
            
            // Clasificación del Incidente
            $table->enum('tipo_incidencia', ['plaga', 'enfermedad', 'deficiencia_nutricional', 'dano_mecanico']);
            $table->string('agente_patogeno_nombre', 150); // Ej: Phytophthora cinnamomi o Arañita Roja
            $table->enum('severidad_afectacion', ['leve', 'moderada', 'critica_cuarentena']);
            
            // Seguimiento y Evidencias para Auditorías GlobalG.A.P.
            $table->text('descripcion_sintomas');
            $table->string('evidencia_fotografica_url')->nullable();
            $table->foreignId('usuario_evaluador_id')->constrained('users');
            
            // Cierre de Caso
            $table->boolean('requiere_intervencion_quimica')->default(false);
            $table->boolean('caso_controlado')->default(false);
            $table->dateTime('fecha_resolucion')->nullable();
            
            $table->timestamps();
            
            $table->index(['arbol_id', 'tipo_incidencia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arboles');
        Schema::dropIfExists('arboles_historial_fitosanitario');
        Schema::dropIfExists('arboles_metricas_historicas');
    }
};
