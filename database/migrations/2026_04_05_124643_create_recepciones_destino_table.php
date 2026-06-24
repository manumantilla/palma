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
        Schema::create('recepciones_destino', function (Blueprint $table) {
            $table->id();
            $table->foreignId('despacho_id')->constrained('despachos')->onDelete('cascade');

            $table->dateTime('fecha_recepcion');
            $table->string('recibido_por')->nullable();         // nombre del bodeguero/comisionista

            // Peso real en destino (diferente al declarado en carta porte)
            $table->decimal('peso_recibido_kg', 12, 2);
            $table->decimal('merma_transito_kg', 12, 2)
                ->nullable();                                 // se puede calcular vs peso_declarado

            // Estado de la carga al llegar
            $table->enum('estado_carga', [
                'buena',
                'con_novedad',       // algo llegó mal
                'rechazo_parcial',   // rechazaron parte
                'rechazo_total',     // rechazaron todo
            ])->default('buena');

            $table->text('novedad_descripcion')->nullable();
            $table->decimal('kg_rechazados', 10, 2)->default(0);
            $table->string('motivo_rechazo')->nullable();

            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recepciones_destino');
    }
};
