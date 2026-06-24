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
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->morphs('gastable');
            $table->foreignId('ciclo_productivo_id')->nullable()->constrained('ciclos_productivos')->onDelete('set null');
            $table->enum('categoria', [
                'insumos', 'mano_obra', 'maquinaria', 'transporte', 'servicios_publicos', 
                'arriendos', 'mantenimiento', 'administrativos', 'impuestos', 'seguros', 'otros'
            ])->default('otros');
            $table->enum('naturaleza', [
                'costo_produccion',
                'gasto_operacional',
                'gasto_financiero',
                'inversion'
            ])->default('gasto_operacional');
            $table->string('numero_soporte')->nullable(); // Factura electrónica, recibo de caja, cuenta de cobro.
            $table->string('comprobante_archivo')->nullable(); // Ruta de almacenamiento del PDF o foto del recibo en S3/Storage.

            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta'])->nullable();
            $table->string('concepto');
            $table->decimal('monto', 14,2);
            $table->date('fecha');
            $table->text('descripcion')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
