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
        Schema::create('contenedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesion_id')->constrained('sesiones_cosecha')->onDelete('cascade');
            $table->foreignId('cliente_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('orden_pedido_id')->nullable()->constrained('ordenes_cosecha')->onDelete('set null');
            $table->enum('estado',['abierta','cerrada','despachada'])->default('abierta');
            $table->string('nombre');                            // Ej: "Tolva Extra Grande #1"
            $table->enum('tipo_destino', [
                'exportacion',
                'mercado_local',
                'industria',
                'consumo_interno',
                'descarte'
            ]);
        
            $table->string('variedad')->nullable();              // hass, papelillo, etc.
        
            // Clasificación — se define cuando se llena la tolva en planta
            $table->enum('calidad', [
                'extra',
                'primera',
                'segunda',
                'industria',
                'descarte'
            ])->nullable();
        
            $table->enum('calibre_talla', [
                'pequeño',
                'mediano',
                'grande',
                'jumbo'
            ])->nullable();
        
            $table->decimal('kilos_acumulados', 12, 2)->default(0);
            $table->decimal('peso_tara', 8, 2)->default(0);     // tara del contenedor/pallete
            $table->decimal('peso_total', 12, 2)->default(0);   // kilos_acumulados + peso_tara
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contenedores');
    }
};
