<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Jornales y destajos de una labor. Lo escribe App\Services\EventoService.
 * El ciclo, lote y etapa se heredan del evento (no se repiten aquí). Solo valor pagado:
 * en el campo casi todo es jornal informal, así que no se modelan prestaciones.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evento_mano_obra', function (Blueprint $table) {
            $table->id();
            // Toda labor pertenece a un evento; lo que no es de un cultivo (una cerca) va en un evento general.
            // restrict: un jornal pagado no desaparece con su evento
            $table->foreignId('evento_campo_id')->constrained('eventos_campo')->restrictOnDelete();
            // Destajo de cosecha: la sesión de donde salen los kilos pesados
            $table->foreignUuid('sesion_id')->nullable()->constrained('sesiones_cosecha')->restrictOnDelete();
            $table->enum('tipo_labor', [
                'jornal_dia_completo',
                'jornal_medio_dia',
                'hora_extra',
                'destajo',
            ]);

            // Trabajador registrado o jornalero de paso (a veces sin documento)
            $table->foreignId('trabajador_id')->nullable()->constrained('trabajadores')->restrictOnDelete();
            $table->string('nombre_trabajador');
            $table->string('cedula', 30)->nullable();

            $table->date('fecha_labor'); // el día trabajado; la nómina filtra por aquí, no por created_at
            $table->decimal('cantidad', 12, 2);              // jornales, horas o unidades de destajo (kg, racimos...)
            $table->string('unidad_destajo', 30)->nullable();
            $table->decimal('valor_unitario', 12, 2);        // valor del jornal o de la unidad ese día
            // Lo que se acordó pagar. Arranca en cantidad × valor_unitario y el supervisor lo sube o baja a mano
            $table->decimal('costo_total', 14, 2);
            $table->decimal('kilos_pesados', 12, 2)->nullable(); // lo que marcó la báscula al registrar el destajo

            // Avance: cuánto rindió este registro
            $table->decimal('area_ejecutada_ha', 10, 2)->nullable();
            $table->unsignedInteger('arboles_atendidos')->nullable();

            $table->enum('estado_pago', ['pendiente', 'pagado'])->default('pendiente');
            $table->date('fecha_pago')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['trabajador_id', 'estado_pago'], 'idx_nomina_trabajador');
            $table->index(['fecha_labor', 'estado_pago']);
            $table->index('evento_campo_id');
        });

        DB::statement('ALTER TABLE evento_mano_obra ADD CONSTRAINT evento_mano_obra_valores_chk
            CHECK (cantidad > 0 AND valor_unitario >= 0 AND costo_total >= 0
                   AND (area_ejecutada_ha IS NULL OR area_ejecutada_ha >= 0))');

        // Un recolector tiene un solo destajo por sesión: el pesaje no se paga dos veces
        DB::statement("CREATE UNIQUE INDEX evento_mano_obra_destajo_sesion_unico ON evento_mano_obra
            (sesion_id, trabajador_id) WHERE tipo_labor = 'destajo' AND sesion_id IS NOT NULL");
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_mano_obra');
    }
};
