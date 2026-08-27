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
        Schema::create('mermas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('recepcion_campo_id')->nullable()->constrained('recepciones_campo')->onDelete('set null');
            $table->foreignUuid('contenedor_id')->nullable()->constrained('contenedores')->onDelete('set null');
            $table->date('fecha_registro');
            $table->decimal('kilos_merma', 10, 2);
            $table->enum('motivo', [
                    'daño_mecanico',      // Golpes, magulladuras, cortes
                    'daño_fitosanitario', // Pudrición, hongos, plagas
                    'descarte_calidad',   // No da la talla, color o forma requerida
                    'deshidratacion',     // Pérdida de peso natural por agua
                    'perdida',            // Cayó al piso, irrecuperable
                    'robo',               
                    'consumo_interno',    // Muestreo de calidad (Brix, firmeza) o degustación
                    'error_bascula',      // Ajustes de inventario
                    'otro'
                ]);                        
            $table->decimal('costo_estimado', 10, 2)->nullable();
            $table->string('destino_final')->nullable();         // basura, abono, donación...
            $table->text('comentarios')->nullable();
            // METADATOS DE SINCRONIZACIÓN
            $table->timestamp('client_updated_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mermas');
    }
};
