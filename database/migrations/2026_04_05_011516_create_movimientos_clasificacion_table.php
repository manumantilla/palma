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
        Schema::create('movimientos_clasificacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recepcion_campo_id')->constrained('recepciones_campo')->onDelete('cascade');
            $table->foreignId('contenedor_id')->constrained('contenedores')->onDelete('cascade');
            $table->decimal('kilos_asignados', 10, 2);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_clasificacion');
    }
};
