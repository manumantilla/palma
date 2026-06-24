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
        Schema::create('recepciones_campo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesion_id')->constrained('sesiones_cosecha')->onDelete('cascade');
            //cosecha
            // $table->foreignId('ciclo_id')->constrained('ciclos_productivos')->onDelete('cascade');
            $table->foreignId('lote_zona_id')->nullable()->constrained('lotes_zona_manejo')->onDelete('set null');
            $table->foreignId('trabajador_id')->constrained('trabajadores')->onDelete('restrict');
            $table->foreignId('arbol_id')->nullable()->constrained('arboles')->onDelete('set null');    
            // Peso y tara — tara variable por costal
            $table->decimal('peso_bruto', 10, 2);               // lo que entra con costal
            $table->decimal('tara_costal', 8, 2)->default(0);   // peso del costal vacío
            $table->decimal('peso_neto', 10, 2); 

            $table->timestamp('hora_pesaje')->useCurrent();
            $table->string('foto_evidencia')->nullable();
            
            // Trazabilidad
            $table->string('costal_codigo')->nullable();         // código o número del costal
            $table->integer('numero_corte')->nullable();         // 1er corte, 2do corte...
            $table->enum('estado_clasificacion', [
                'pendiente',
                'en_proceso',
                'clasificado'
            ])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recepciones_campo');
    }
};
