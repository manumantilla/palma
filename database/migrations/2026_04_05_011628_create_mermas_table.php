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
            $table->id();
            $table->foreignId('recepcion_campo_id')->nullable()->constrained('recepciones_campo')->onDelete('set null');
            $table->foreignId('contenedor_id')->nullable()->constrained('contenedores')->onDelete('set null');
            $table->date('fecha_registro');
            $table->decimal('kilos_merma', 10, 2);
            $table->enum('motivo', [
                'daño',
                'perdida',
                'robo',
                'deshidratacion',
                'consumo_interno',
                'error',
                'otro'
            ]);                             
            $table->decimal('costo_estimado', 10, 2)->nullable();
            $table->string('destino_final')->nullable();         // basura, abono, donación...
            $table->text('comentarios')->nullable();
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
