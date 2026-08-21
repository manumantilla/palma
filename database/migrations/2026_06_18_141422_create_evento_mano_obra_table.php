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
        Schema::create('evento_mano_obra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_campo_id')
                ->constrained('eventos_campo')
                ->onDelete('cascade');
            // Vincular a una sesion de cosecha
            $table->foreignUuid('sesion_id')->nullable()->constrained('sesiones_cosecha')->onDelete('set null'); // A veces esta mano de obra no corresponde a un evento
            $table->foreignId('ciclo_id')->nullable()->constrained('ciclos_productivos')->onDelete('set null'); // A veces las manos de obra corresponden a algo especifico a un cultivo y a veces arreglos normales  carreteras o cercas
            $table->enum('tipo_labor', [
                'jornal_dia_completo',   // 8 horas estándar
                'jornal_medio_dia',
                'hora_extra',
                'destajo',               // pago por unidad producida (racimos cosechados, huecos)
            ]);
            // En evento_mano_obra 
            // $table->foreignId('sesion_cosecha_id')->nullable()->constrained('sesiones_cosecha');

            // Puede ser un trabajador registrado en el sistema o externo
            $table->foreignId('trabajador_id')
                ->nullable()
                ->constrained('trabajadores')
                ->onDelete('set null');
            $table->string('nombre_trabajador')->nullable(); // para jornaleros externos sin cuenta

            $table->decimal('cantidad', 6, 1);              // número de jornales o unidades destajo
            $table->decimal('valor_unitario', 10, 2);        // valor del jornal o unidad ese día
            // valor_total = cantidad * valor_unitario — calculado, no almacenado
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento_mano_obra');
    }
};
