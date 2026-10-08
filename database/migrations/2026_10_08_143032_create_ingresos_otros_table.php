<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingresos_otros', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['venta_subproducto', 'incentivo_icr', 'subsidio', 'arrendamiento', 'reembolso', 'otro']);
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->restrictOnDelete();
            $table->string('tercero_nombre')->nullable();
            $table->foreignId('lote_id')->nullable()->constrained('lotes')->restrictOnDelete();
            $table->foreignId('ciclo_productivo_id')->nullable()->constrained('ciclos_productivos')->restrictOnDelete();

            $table->string('concepto');
            $table->string('cuenta_puc', 10)->nullable();  // 42xx no operacionales, 41xx operacionales
            $table->string('numero_soporte', 60)->nullable();
            $table->date('fecha');
            $table->date('fecha_vencimiento')->nullable();

            $table->decimal('valor_bruto', 14, 2);
            $table->decimal('valor_iva', 14, 2)->default(0);
            $table->decimal('retefuente', 14, 2)->default(0);
            $table->decimal('reteica', 14, 2)->default(0);
            $table->decimal('valor_neto', 14, 2)->storedAs('valor_bruto + valor_iva - retefuente - reteica');
            $table->decimal('valor_recibido', 14, 2)->default(0);

            $table->enum('estado', ['pendiente', 'parcial', 'recibido', 'anulado'])->default('pendiente');
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['fecha', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingresos_otros');
    }
};