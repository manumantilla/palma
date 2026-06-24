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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social');
            $table->string('nombre_comercial')->nullable();
            $table->enum('tipo_persona', ['natural', 'juridica'])->default('natural');
            $table->string('nit')->unique();
            $table->string('dv', 1)->nullable(); 
            $table->string('contacto_nombre')->nullable();
            $table->string('contacto_telefono')->nullable();
            $table->string('contacto_email')->nullable();
            $table->text('direccion_fiscal');
            $table->text('municipio');
            $table->decimal('cupo_credito', 16, 2)->default(0); // Aumentado por si manejas grandes volúmenes
            $table->decimal('saldo_actual', 16, 2)->default(0); 
            $table->integer('dias_credito')->default(0);
            $table->enum('estado_cuenta', ['activo', 'suspendido', 'mora', 'castigado'])->default('activo');
            $table->json('cultivos_interes')->nullable(); 
            $table->enum('categoria_cliente', ['mayorista', 'minorista', 'exportacion', 'industrial'])->default('minorista');
            
            // Facturación Electrónica (Campos requeridos por la DIAN)
            $table->boolean('autoriza_factura_electronica')->default(true);
            $table->string('email_recepcion_facturas')->nullable();
            $table->string('codigo_postal')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Siempre usa softDeletes para clientes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
