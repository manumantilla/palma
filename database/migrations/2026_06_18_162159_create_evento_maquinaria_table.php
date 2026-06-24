<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('evento_maquinaria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos_campo')->onDelete('cascade');
            $table->foreignId('maquina_id')->constrained('maquinaria')->onDelete('set null');
            $table->foreignId('trabajador_id')->constrained('users');
            $table->enum('estado',['planificada','en_ejecucion','terminada','cancelada'])->default('planificada');
            $table->decimal('horometro_inicial',12,2)->nullable();
            $table->decimal('horometro_final',12,2)->nullable();
            $table->decimal('horas_trabajadas',12,2)->nullable();
            $table->enum('tipo_combustible',['acpm','gasolina','mecanico']);
            $table->decimal('litros_consumidos',12,2)->nullable();
            $table->decimal('costo_total',12,2)->default(0);
            $table->text('observaciones')->nullable();        
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_maquinaria');
    }
};
