<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('estaciones_clima', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finca_id')->nullable()->constrained('fincas')->nullOnDelete();
            $table->string('nombre');
            $table->enum('tipo', ['propia_iot', 'manual', 'ideam', 'open_meteo', 'nasa_power']);
            $table->string('codigo_externo', 50)->nullable();   // código IDEAM o id del proveedor
            $table->geometry('ubicacion', 'POINT', 4326)->nullable();
            $table->decimal('altitud_msnm', 7, 2)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
        DB::statement('CREATE INDEX estaciones_clima_ubicacion_spatial_index ON estaciones_clima USING gist (ubicacion);');

        Schema::create('lotes_estacion_clima', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->cascadeOnDelete();
            $table->foreignId('estacion_clima_id')->constrained('estaciones_clima')->cascadeOnDelete();
            $table->boolean('es_principal')->default(true);
            $table->timestamps();

            $table->unique(['lote_id', 'estacion_clima_id']);
        });

        Schema::create('clima_diario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estacion_clima_id')->constrained('estaciones_clima')->cascadeOnDelete();
            $table->date('fecha');
            $table->decimal('temp_min_c', 4, 1)->nullable();
            $table->decimal('temp_max_c', 4, 1)->nullable();
            $table->decimal('temp_media_c', 4, 1)->nullable();
            $table->decimal('precipitacion_mm', 6, 1)->nullable();
            $table->decimal('humedad_relativa_pct', 5, 2)->nullable();
            $table->decimal('radiacion_mj_m2', 6, 2)->nullable();
            $table->decimal('velocidad_viento_ms', 5, 2)->nullable();
            $table->decimal('et0_mm', 5, 2)->nullable();          // evapotranspiración de referencia
            $table->decimal('horas_sol', 4, 1)->nullable();
            $table->enum('fuente', ['medido', 'api', 'estimado', 'interpolado'])->default('medido');
            $table->timestamps();

            $table->unique(['estacion_clima_id', 'fecha']);
            $table->index('fecha');
        });

        DB::statement('ALTER TABLE clima_diario ADD CONSTRAINT clima_diario_valores_chk CHECK (
            (temp_min_c IS NULL OR temp_max_c IS NULL OR temp_max_c >= temp_min_c)
            AND (precipitacion_mm IS NULL OR precipitacion_mm >= 0)
            AND (humedad_relativa_pct IS NULL OR humedad_relativa_pct BETWEEN 0 AND 100))');
    }

    public function down(): void
    {
        Schema::dropIfExists('clima_diario');
        Schema::dropIfExists('lotes_estacion_clima');
        Schema::dropIfExists('estaciones_clima');
    }
};
