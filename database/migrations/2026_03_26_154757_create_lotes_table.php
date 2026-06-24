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
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finca_id')->constrained('fincas')->onDelete('cascade');
            $table->string('nombre_lote');
            $table->string('codigo_lote')->unique();
            $table->decimal('area_hectareas_declaradas', 8,2);
            $table->decimal('area_hectareas_gis', 10, 4)->nullable();
            $table->decimal('altitud_mediana_msnm', 7, 2);
            $table->decimal('pendiente_promedio_porcentaje', 5, 2); // Ej: 12.50%
            $table->enum('pendiente_terreno', ['plano', 'ondulado', 'escarpado', 'muy_escarpado'])->default('ondulado');
            $table->enum('tipo_suelo', ['arenoso', 'arcilloso', 'limoso', 'franco', 'franco_arenoso', 'franco_arcilloso']);            
            $table->decimal('ph_suelo',5,2);
            $table->boolean('tiene_riego_instalado')->default(false);
            $table->enum('fuente_agua', ['acueducto', 'pozo', 'rio', 'nacimiento', 'lluvia'])->nullable();
            $table->enum('tenencia', ['propio', 'arrendado', 'comodato'])->default('propio');
            $table->string('registro_ica', 100)->nullable(); // Si el lote está certificado para exportar aguacate
            // GEOMETRÍA AVANZADA: MultiPolygon con SRID 4326 (WGS 84)
            // Soporta lotes que se componen de parcelas separadas físicamente bajo un mismo código
            $table->geometry('geometria_gps', 'MULTIPOLYGON', 4326)->nullable();            
            // POST GIS
            $table->boolean('activo')->default(true);
            $table->softDeletes();
            $table->timestamps();
            $table->index(['finca_id', 'activo']);
            $table->index('codigo_lote');
        });
        DB::statement('CREATE INDEX lotes_geometria_gps_spatial_index ON lotes USING gist (geometria_gps);');

        // 2. TABLA SUB-MAESTRA: ZONAS DE MANEJO / AGRICULTURA DE PRECISIÓN
        Schema::create('lotes_zonas_manejo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->cascadeOnDelete();
            $table->string('nombre_zona', 100); // Ej: "Zona Norte - Alta Productividad"
            $table->string('codigo_zona', 50)->unique();
            $table->decimal('area_hectareas', 8, 4);
            
            // Polígono específico de la zona de manejo para segmentación de NDVI / Drones / Sensores
            $table->geometry('geometria_zona', 'POLYGON', 4326)->nullable();
            $table->timestamps();
            
            $table->index('lote_id'); 
        });
        
        DB::statement('CREATE INDEX zonas_geometria_spatial_index ON lotes_zonas_manejo USING gist (geometria_zona);');

        // 3. TABLA HISTÓRICA: ANALÍTICAS Y CARACTERIZACIÓN DE SUELO (Auditoría temporal)
        Schema::create('lotes_analiticas_suelo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->cascadeOnDelete();
            $table->date('fecha_muestreo');
            $table->string('numero_laboratorio_ticket', 50)->nullable(); // Trazabilidad física del papel
            
            // Propiedades Químicas
            $table->decimal('ph', 4, 2);
            $table->decimal('conductividad_electrica_ds_m', 6, 3)->nullable();
            $table->decimal('materia_organica_porcentaje', 5, 2)->nullable();
            $table->decimal('capacidad_intercambio_cationico_meq', 6, 2)->nullable(); // CIC
            
            // Textura Física (Clasificación USDA)
            $table->enum('textura_predominante', [
                'arenoso', 'arenoso_franco', 'franco_arenoso', 'franco', 
                'limoso', 'franco_limoso', 'franco_arcilloso_arenoso', 
                'franco_arcilloso_limoso', 'franco_arcilloso', 'arcilloso_arenoso', 
                'arcilloso_limoso', 'arcilloso'
            ]);
            $table->decimal('porcentaje_arena', 5, 2)->nullable();
            $table->decimal('porcentaje_limo', 5, 2)->nullable();
            $table->decimal('porcentaje_arcilla', 5, 2)->nullable();
            
            $table->foreignId('analista_user_id')->nullable()->constrained('users');
            $table->timestamps();

            // El índice compuesto evita duplicar registros de muestreo en un mismo día
            $table->unique(['lote_id', 'fecha_muestreo']);
        });

        // 4. TABLA HIDRÁULICA: SISTEMAS DE RIEGO Y FERTIRRIEGO
        Schema::create('lotes_sistemas_riego', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->cascadeOnDelete();
            $table->string('nombre_sistema', 100); // Ej: "Sector Goteo Bloque A"
            $table->enum('tipo_riego', ['goteo', 'microaspersion', 'aspersion', 'pivot_central', 'gravedad', 'subterraneo']);
            $table->enum('fuente_agua', ['acueducto_distrito', 'pozo_profundo', 'rio_directo', 'embalse_almacenamiento', 'nacimiento']);
            
            // Datos técnicos para fertirriego de precisión
            $table->decimal('caudal_diseno_litros_segundo', 8, 2);
            $table->decimal('presion_operacion_psi', 6, 2)->nullable();
            $table->decimal('coeficiente_uniformidad', 5, 2)->nullable(); // Porcentaje de eficiencia (Ej: 92.50%)
            $table->decimal('espaciamiento_emisores_metros', 4, 2)->nullable();
            $table->decimal('descarga_emisor_litros_hora', 5, 2)->nullable();
            
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes_sistemas_riego');
        Schema::dropIfExists('lotes_analiticas_suelo');
        Schema::dropIfExists('lotes_zonas_manejo');
        Schema::dropIfExists('lotes');
    }
};
