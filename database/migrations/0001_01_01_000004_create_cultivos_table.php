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
        Schema::create('cultivos', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['perenne', 'transitorio'])->after('nombre');
            $table->string('nombre_cultivo')->unique();
            $table->string('descripcion')->nullable();
            
            $table->timestamps();
        });
        Schema::create('fenologia_etapas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cultivo_id')->constrained()->onDelete('cascade');
            $table->string('nombre'); // "Floración", "Cuajado", "Maduración", etc.
            $table->integer('orden');  // orden de aparición
            $table->integer('duracion_dias_desde_inicio')->nullable(); // días desde siembra (transitorio) o desde brotación (perenne)
            $table->integer('duracion_dias_estimada'); // cuánto dura esta etapa
            $table->text('descripcion')->nullable();
            $table->timestamps();   
        });
        Schema::create('labores_plantilla', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cultivo_id')->constrained()->onDelete('cascade');
            $table->string('nombre_labor'); // "Fertilización nitrogenada", "Poda sanitaria"
            $table->text('descripcion')->nullable();
            
            // Programación
            $table->enum('momento_tipo', ['dias_desde_siembra', 'etapa_fenologica', 'fecha_fija_anual']);
            $table->integer('dias_desde_siembra')->nullable();   // si momento_tipo = dias_desde_siembra
            $table->foreignId('fenologia_etapa_id')->nullable()->constrained('fenologia_etapas'); // si por etapa
            $table->integer('periodicidad_dias')->nullable();     // cada cuántos días se repite (ej. cada 30 días)
            $table->integer('duracion_estimada_horas')->nullable(); // duración de la labor por ha
            
            // Datos por defecto
            $table->foreignId('tipo_evento_id')->constrained('tipos_evento'); // reutiliza tu tabla de tipos de evento
            $table->boolean('requiere_insumos')->default(false);
            $table->boolean('requiere_mano_obra')->default(true);
            
            $table->timestamps();
        });

        DB::table('cultivos')->insert([
            ['tipo' => 'perenne', 'nombre_cultivo' => 'Palma de Aceite', 'descripcion' => 'Cultivo de palma para producción de aceite.'],
            ['tipo' => 'transitorio', 'nombre_cultivo' => 'Maíz', 'descripcion' => 'Cultivo anual de maíz para consumo humano y animal.'],
            ['tipo' => 'transitorio', 'nombre_cultivo' => 'Frijol', 'descripcion' => 'Cultivo anual de frijol para consumo humano.'],
            ['tipo' => 'perenne', 'nombre_cultivo' => 'Lulo', 'descripcion' => 'Cultivo de lulo para producción de jugo.'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cultivos');
    }
};
