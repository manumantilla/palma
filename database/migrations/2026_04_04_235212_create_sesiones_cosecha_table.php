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
        Schema::create('sesiones_cosecha', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_cosecha_id')->constrained('ordenes_cosecha')->onDelete('cascade');
            $table->foreignId('evento_id')->constrained('eventos_campo')->onDelete('cascade');
            // $table->foreignId('evento_campo_id')->constrained('eventos_campo')->unique();
            $table->foreignId('responsable_id')->constrained('users')->onDelete('restrict');
            $table->date('fecha');
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->decimal('meta_kg_dia', 10, 2)->nullable();
            $table->integer('numero_recolectores')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->decimal('total_recolectado_kg', 12,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesiones_cosecha');
    }
};
