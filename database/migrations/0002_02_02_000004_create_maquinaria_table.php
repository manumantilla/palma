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
        Schema::create('tipo_maquinaria', function(Blueprint $table){
            $table->id();
            $table->string('nombre');
            $table->string('descripcion');

            $table->timestamps();
        });
        Schema::create('maquinaria', function (Blueprint $table) {
            $table->id();
            // Identificación
            $table->string('codigo_interno')->unique();
            $table->string('nombre');
            $table->foreignId('tipo_maquinaria_id')
                ->constrained('tipo_maquinaria')
                ->restrictOnDelete();
            $table->string('marca');
            $table->string('modelo')->nullable();
            $table->string('placa')->nullable();
            $table->string('serial')->nullable()->unique();
            // Compra
            $table->date('fecha_compra')->nullable();
            $table->decimal('valor_compra',15,2)->nullable();
            $table->integer('vida_util_anios')->nullable();
            // Operación
            $table->enum('fuente_energia',[
                'diesel',
                'gasolina',
                'electrica',
                'manual',
                'hibrida'
            ]);
            $table->decimal('capacidad_tanque',8,2)->nullable();
            $table->decimal('consumo_hora',8,2)->nullable();
            // Control uso
            $table->integer('horometro_actual')
                ->default(0);
            $table->integer('kilometraje')
                ->nullable();
            // Estado
            $table->enum('estado',[
                'activo',
                'mantenimiento',
                'averiado',
                'alquilado',
                'vendido',
                'retirado'
            ])->default('activo');
            $table->foreignId('responsable_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->text('observaciones')
                ->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {

        Schema::dropIfExists('tipo_maquinaria');
        Schema::dropIfExists('maquinaria');
    }
};
