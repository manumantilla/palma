<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('ordenes_cosecha', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('ciclo_productivo_id')->constrained('ciclos_productivos');
            $table->foreignId('lote_cultivo_id')->nullable()->constrained('lotes')->onDelete('restrict');
            $table->foreignId('lote_zona_id')->nullable()->constrained('lotes_zona_manejo')->onDelete('set null');
            $table->date('fecha_programada');
            $table->date('fecha_entrega');
            $table->foreignId('responsable_id')->nullable()->constrained('users')->onDelete('set null');        
            $table->decimal('cantidad_solicitada_kg', 12, 2)->nullable();
            $table->string('variedad_requerida')->nullable();
            $table->decimal('cantidad_planificada_kg', 12, 2)->nullable();
            $table->decimal('cantidad_recolectada_kg', 12, 2)->default(0);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->enum('estado', [
                'borrador',
                'confirmada',
                'en_proceso',
                'completada',
                'cancelada'
            ])->default('borrador');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_cosecha');
    }
};
