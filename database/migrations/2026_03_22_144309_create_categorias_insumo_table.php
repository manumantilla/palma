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
        Schema::create('categorias_insumo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('maneja_vencimiento')->default(false);
            $table->boolean('maneja_toxicidad')->default(false);
            $table->timestamps();
        });
        DB::table('categorias_insumo')->insert([
        [
            'nombre' => 'Pesticidas y Químicos',
            'maneja_vencimiento' => true,
            'maneja_toxicidad' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nombre' => 'Semillas y Plantones',
            'maneja_vencimiento' => true,
            'maneja_toxicidad' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nombre' => 'Herramientas y Maquinaria',
            'maneja_vencimiento' => false,
            'maneja_toxicidad' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nombre' => 'Fertilizantes Orgánicos',
            'maneja_vencimiento' => true,
            'maneja_toxicidad' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nombre' => 'Equipo de Protección',
            'maneja_vencimiento' => false,
            'maneja_toxicidad' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_insumo');
    }
};
