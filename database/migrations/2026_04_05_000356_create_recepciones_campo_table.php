<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('recepciones_campo', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sesion_cosecha_id');
            $table->foreign('sesion_cosecha_id')->references('id')->on('sesiones_cosecha')->onDelete('cascade');
            //cosecha
            // $table->foreignId('ciclo_id')->constrained('ciclos_productivos')->onDelete('cascade');
            $table->foreignId('lote_zona_id')->nullable()->constrained('lotes_zona_manejo')->onDelete('set null');
            $table->foreignId('trabajador_id')->constrained('trabajadores')->onDelete('restrict');
            $table->foreignId('arbol_id')->nullable()->constrained('arboles')->onDelete('set null');    
            // Peso y tara — tara variable por costal: aunque en el campo colombiano ocurren muchas cosas no podemso hacerla storeAs es mejor que en el frotned se recomiende el peso neto pero la persona lo confirme
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

            // METADATOS DE SINCRONIZACIÓN
            $table->timestamp('client_updated_at')->nullable();
            $table->timestamp('synced_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recepciones_campo');
    }
};
