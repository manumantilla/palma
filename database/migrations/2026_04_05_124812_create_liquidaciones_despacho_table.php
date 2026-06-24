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
        Schema::create('liquidaciones_despacho', function (Blueprint $table) {
            $table->id();
            $table->foreignId('despacho_id')->constrained('despachos')->onDelete('cascade');
            $table->foreignId('despacho_item_id')->constrained('despacho_items')->onDelete('cascade');
            $table->dateTime('fecha_liquidacion');
            $table->string('numero_identificacion')->nullable();
            $table->decimal('precio_unitario_kg',12,2);
            $table->decimal('valor_bruto_venta',12,2);
            $table->decimal('comision_porcentaje', 5, 2)->default(0);
            $table->decimal('valor_comision', 12, 2)->default(0);
            $table->decimal('valor_flete_descontado', 12, 2)->default(0);  // flete prorrateado
            $table->decimal('otros_descuentos', 12, 2)->default(0);
            $table->text('detalle_otros_descuentos')->nullable();
            $table->decimal('valor_neto_item', 15, 2); 
            $table->enum('estado_pago', ['pendiente', 'parcial', 'pagado'])->default('pendiente');
            $table->decimal('valor_pagado', 15, 2)->default(0);
            $table->decimal('saldo_pendiente', 15, 2)->default(0);
            $table->dateTime('fecha_pago')->nullable();
            $table->enum('medio_pago', ['efectivo', 'transferencia', 'cheque', 'otro'])->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        //Pagos
        Schema::create('pagos_liquidacion', function (Blueprint $table) {
            $table->id();
            
            // Puedes pagar una liquidación específica o todo el despacho de un tajo
            $table->foreignId('liquidacion_id')
                ->nullable()
                ->constrained('liquidaciones_despacho')
                ->onDelete('restrict'); // No borrar pagos huérfanos
            $table->foreignId('despacho_id')
                ->constrained('despachos')
                ->onDelete('restrict');

            // El pago en sí
            $table->decimal('valor_pagado', 15, 2);
            $table->dateTime('fecha_pago');
            $table->enum('medio_pago', ['efectivo', 'transferencia', 'cheque', 'otro']);
            $table->string('referencia_pago')->nullable(); // Nro transferencia, cheque, etc.
            $table->string('banco_origen')->nullable();

            // Auditoría
            $table->foreignId('registrado_por')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liquidaciones_despacho');
        Schema::dropIfExists('pagos_liquidacion');
        
    }
};
