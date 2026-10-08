<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Monitoreo fenológico real en campo: cómo sabe el sistema en qué etapa está el cultivo
        // (hoy solo se sabe porque alguien hace clic en "avanzar etapa").
        // Palma: aquí va el censo de inflorescencias/racimos que alimenta el pronóstico de cosecha.
        // UUID + metadatos de sincronización: se captura en el celular sin señal, igual que la cosecha.
        Schema::create('observaciones_fenologicas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('ciclo_productivo_id')->constrained('ciclos_productivos')->cascadeOnDelete();
            $table->foreignId('ciclo_etapa_id')->nullable()->constrained('ciclo_etapas_historial')->nullOnDelete();
            $table->foreignId('fenologia_etapa_id')->nullable()->constrained('fenologia_etapas')->nullOnDelete();
            $table->foreignId('zona_id')->nullable()->constrained('lotes_zonas_manejo')->nullOnDelete();
            $table->foreignId('arbol_id')->nullable()->constrained('arboles')->nullOnDelete(); // observación individual

            $table->dateTime('fecha_observacion');
            $table->unsignedSmallInteger('bbch_codigo')->nullable();

            // Muestreo: % de plantas en la etapa observada
            $table->unsignedInteger('plantas_muestreadas')->nullable();
            $table->unsignedInteger('plantas_en_etapa')->nullable();
            $table->decimal('porcentaje_en_etapa', 5, 2)->storedAs(
                'CASE WHEN plantas_muestreadas > 0 THEN ROUND(plantas_en_etapa * 100.0 / plantas_muestreadas, 2) END'
            );

            // Conteos específicos del cultivo, ej. palma:
            // {"inflorescencias_femeninas":3,"inflorescencias_masculinas":2,"racimos_verdes":8,"racimos_maduros":1}
            $table->jsonb('conteos')->nullable();

            $table->geometry('coordenada_gps', 'POINT', 4326)->nullable();
            $table->string('foto_evidencia')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();

            // METADATOS DE SINCRONIZACIÓN
            $table->timestamp('client_updated_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['ciclo_productivo_id', 'fecha_observacion']);
            $table->index(['arbol_id', 'fecha_observacion']);
        });

        DB::statement('ALTER TABLE observaciones_fenologicas ADD CONSTRAINT observaciones_fenologicas_valores_chk CHECK (
            (bbch_codigo IS NULL OR bbch_codigo <= 99)
            AND (plantas_muestreadas IS NULL OR plantas_en_etapa IS NULL OR plantas_en_etapa <= plantas_muestreadas))');
    }

    public function down(): void
    {
        Schema::dropIfExists('observaciones_fenologicas');
    }
};
