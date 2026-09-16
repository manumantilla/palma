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
    Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            
            // Información General
            $table->string('nombre');
            $table->string('nit')->unique();
            $table->string('categoria_principal')->nullable(); // Ej: Agroquímicos, Maquinaria, Empaques
            $table->boolean('activo')->default(true);
            
            // Contacto
            $table->string('contacto_principal')->nullable(); // Nombre del vendedor o asesor
            $table->string('telefono')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('direccion')->nullable();
            
            // Condiciones Comerciales y Financieras
            $table->boolean('tiene_credito')->default(false);
            $table->integer('dias_plazo')->default(0); // Días para pagar la factura (ej: 30, 60, 90)
            $table->decimal('limite_credito', 15, 2)->nullable(); // Tope máximo de deuda permitida
            
            // Datos Bancarios (Corrigiendo el typo "cuente")
            $table->string('banco_1')->nullable();
            $table->string('cuenta_bancaria_1')->nullable();
            $table->string('banco_2')->nullable();
            $table->string('cuenta_bancaria_2')->nullable();
            
            $table->text('notas')->nullable();
            
            // Fundamental para no romper el historial de compras si dejas de usar un proveedor
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
