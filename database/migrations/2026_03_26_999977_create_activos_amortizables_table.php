<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activos_amortizables', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', ['cultivo_en_desarrollo', 'maquinaria', 'infraestructura', 'sistema_riego', 'otro']);

            $table->foreignId('maquinaria_id')->nullable()->constrained('maquinaria')->restrictOnDelete();
            $table->foreignId('lote_id')->nullable()->constrained('lotes')->restrictOnDelete();
            $table->foreignId('ciclo_productivo_id')->nullable()->constrained('ciclos_productivos')->restrictOnDelete();

            $table->string('cuenta_puc', 10)->nullable();

            $table->date('fecha_inicio_capitalizacion');
            $table->date('fecha_inicio_amortizacion')->nullable(); // palma: cuando entra a producción
            $table->decimal('valor_inicial', 16, 2)->default(0);  
            $table->decimal('valor_residual', 16, 2)->default(0);
            $table->unsignedSmallInteger('vida_util_meses'); 
            $table->enum('metodo', ['linea_recta', 'unidades_produccion'])->default('linea_recta');
            $table->decimal('unidades_estimadas_total', 16, 2)->nullable(); // horas máquina o kg totals

            $table->enum('estado', ['en_formacion', 'en_uso', 'totalmente_amortizado', 'dado_de_baja'])->default('en_formacion');
            $table->date('fecha_baja')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['tipo', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activos_amortizables');
    }
};