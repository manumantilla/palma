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
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->id();
            // Vinculación con Jetstream
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Identificación (Indispensable)
            $table->enum('tipo_documento', ['CC', 'CE', 'NIT', 'PPT'])->default('CC');
            $table->string('numero_documento')->unique();
            
            $table->string('nombres');
            $table->string('apellidos');
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['M', 'F', 'Otro'])->nullable();

            // Laboral
            $table->string('cargo'); 
            $table->date('fecha_ingreso');
            $table->date('fecha_retiro')->nullable();
            $table->enum('tipo_contrato', ['indefinido', 'fijo', 'por_labores', 'aprendizaje']);
            
            // Financiero - Sugerencia: 12,2 es poco si manejas pesos colombianos devaluados en totales grandes
            // pero para salarios unitarios está bien.
            $table->decimal('salario_base', 14, 2)->nullable();
            $table->enum('forma_pago', ['jornal', 'destajo', 'mixto'])->default('jornal');
            $table->text('banco_numero_cuenta')->nullable();
            
            // Seguridad social
            $table->string('eps')->nullable();
            $table->string('arl')->nullable();
            $table->string('afp')->nullable();

            // Habilidades y eficiencia
            $table->json('habilidades')->nullable(); 
            
            // Geolocalización
            $table->geography('ubicacion_actual', 'point', 4326)->nullable(); // Estándar para GPS
            $table->timestamp('ultima_ubicacion_at')->nullable();

            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index(['activo', 'cargo']);
            $table->index('numero_documento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajadores');
    }
};
