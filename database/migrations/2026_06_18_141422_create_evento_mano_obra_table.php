<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evento_mano_obra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_campo_id')
                ->nullable()
                ->constrained('eventos_campo')
                ->onDelete('cascade');
            // Vincular a una sesion de cosecha
            $table->foreignUuid('sesion_id')->nullable()->constrained('sesiones_cosecha')->onDelete('set null'); // A veces esta mano de obra no corresponde a un evento
            $table->enum('tipo_labor', [
                'jornal_dia_completo',  
                'jornal_medio_dia',
                'hora_extra',
                'destajo',          
            ]);
            // En evento_mano_obra 
            // $table->foreignId('sesion_cosecha_id')->nullable()->constrained('sesiones_cosecha');

            // Puede ser un trabajador registrado en el sistema o externo
            $table->foreignId('trabajador_id')
                ->nullable()
                ->constrained('trabajadores')
                ->onDelete('set null');
            $table->string('nombre_trabajador')->nullable(); // para jornaleros externos sin cuenta
            $table->string('cedula');
            $table->decimal('cantidad', 12, 1);              // número de jornales o unidades destajo
            $table->string('unidad_destajo', 30)->nullable(); 
            $table->decimal('valor_unitario', 10, 2);        // valor del jornal o unidad ese día
            $table->decimal('costo_total',15,2);
            $table->enum('estado_pago', ['pendiente', 'pagado', 'anulado'])->default('pendiente');
            $table->datetime('fecha_pago')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->index(['trabajador_id', 'estado_pago'], 'idx_nomina_trabajador');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_mano_obra');
    }
};
