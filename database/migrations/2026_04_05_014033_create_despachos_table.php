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
    Schema::create('despachos', function (Blueprint $table) {
        $table->id();
        $table->string('numero_remision')->unique(); // Ej: REM-2026-0001
        
        // Destino comercial
        $table->enum('tipo_destino', [
            'central_mayorista', 
            'mercado_local',     
            'venta_detal',       
            'exportacion',       
            'consignacion',      
        ]);
        $table->string('nombre_destino');              
        $table->string('ciudad_destino')->nullable();
        $table->string('departamento_destino')->nullable();

        // Actores comerciales (cartera por cliente y control de cupo_credito)
        $table->foreignId('cliente_id')->nullable()->constrained('clientes')->restrictOnDelete();
        // $table->foreignId('comisionista_id')->nullable()->constrained('users')->onDelete('set null');

        // Tiempos logísticos
        $table->dateTime('fecha_despacho');
        $table->dateTime('fecha_estimada_llegada')->nullable();
        $table->dateTime('fecha_liquidado')->nullable(); // NUEVO: Para auditoría de tiempos financieros

        // Estados de control
        $table->enum('estado', [
            'preparando', 
            'en_transito',
            'recibido',   
            'liquidado',  
            'novedad',    
        ])->default('preparando');

        // Estrategia de precios
        $table->enum('modalidad_precio', [
            'precio_fijo',    
            'precio_mercado', 
            'precio_minimo',  
        ])->default('precio_mercado');

        $table->decimal('precio_referencia_kg', 10, 2)->nullable(); 
        $table->text('observaciones')->nullable();
        $table->timestamps();

        $table->index(['cliente_id', 'estado']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('despachos');
    }
};
