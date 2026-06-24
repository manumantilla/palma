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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('cascade');
            $table->dateTime('fecha');
            $table->enum('tipo', [
                'insumos', 
                'maquinaria', 
                'servicios_tecnicos', 
                'transporte', 
                'empaque', 
                'laboratorio', 
                'consultoria', 
                'otros'
            ])->default('insumos');
             $table->foreignId('ciclo_productivo_id')->nullable()->constrained('ciclos_productivos');
            $table->enum('estado', ['borrador', 'confirmada', 'enviada', 'recibida_parcial', 'recibida_total', 'cancelada'])->default('borrador');
            $table->string('factura_pdf_path')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('departamento')->nullable();
            $table->geometry('ubicacion', 'POINT', 4326)->nullable();
            $table->integer('plazo_pago_dias')->default(0);
            $table->decimal('descuento_pronto_pago', 5,2)->nullable(); // porcentaje
            $table->string('numero_factura');
            $table->decimal('subtotal', 12,2)->nullable(); // antes de IVA y descuentos
            $table->decimal('descuento_total', 12,2)->default(0);
            $table->decimal('iva_total', 12,2)->default(0);
            $table->decimal('total', 12,2); // subtotal - descuento + iva
            $table->decimal('porcentaje_iva_general', 5,2)->nullable(); // si aplica a toda la factura
            $table->dateTime('fecha_pedido')->nullable();
            $table->enum('estado_pago', ['pendiente', 'pagado'])->default('pendiente');
            $table->string('observaciones')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('compras_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained();
            
            $table->decimal('monto', 12,2);
            $table->date('fecha_pago');
            $table->enum('metodo', ['efectivo', 'transferencia', 'cheque', 'tarjeta'])->default('transferencia');
            $table->string('referencia_transaccion')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
