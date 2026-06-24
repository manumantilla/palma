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
        Schema::create('bitacoras', function (Blueprint $table) {
            $table->id();
            $table->morphs('bitacorable');
            $table->enum('tipo', [
                'observacion',      // nota general del agrónomo u operario
                'alerta',           // algo que requiere atención pero no es urgente
                'incidente',        // evento negativo: helada, plaga, accidente
                'decision',         // se tomó una decisión agronómica relevante
                'condicion_clima',  // lluvia, viento, temperatura — afecta labores
                'visita_tecnica',   // visita de ICA, certificadora, asesor externo
            ]);
            $table->enum('prioridad', ['baja', 'media', 'alta', 'critica'])->default('baja');
            $table->string('titulo')->nullable();
            $table->text('contenido');
            $table->enum('estado', [
                'abierto',
                'en_proceso',
                'resuelto',
            ])->default('abierto');
            $table->string('archivo_adjunto')->nullable(); // ruta del archivo 
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
    }
};
