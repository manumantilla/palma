<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        // Schema::create('sys_tipos_riego')
        Schema::create('tanques', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('capacidad_litros',15,2);
            $table->decimal('altura_maxima_cm', 9,2);
            // Ubicacion con PostGIS
            $table->foreignId('lote_id')->nullable()->constrained('lotes')->onDelete('set null');
            $table->decimal('nivel_actual_litros', 12, 2)->default(0);
            $table->boolean('tiene_sensor_iot')->default(false);
            $table->boolean('activo')->default(true)->index();  
            $table->timestamps();
        });
        Schema::create('sistemas_riego', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanque_id')->constrained('tanques')->onDelete('cascade');
            $table->foreignId('lote_id')->nullable()->constrained('lotes')->onDelete('set null'); // Lote destino que recibe el beneficio
            $table->string('nombre_sistema', 100); // Ej: "Sector Goteo Variedad Hass"
            $table->enum('tipo_riego', ['goteo', 'microaspersion', 'aspersion', 'pivot_central', 'gravedad', 'subterraneo'])->index();
            
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });
        Schema::create('componentes_riego', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sistema_riego_id')->constrained('sistemas_riego')->onDelete('cascade');      
            $table->enum('tipo_componente', ['manguera', 'bomba', 'valvula_paso', 'valvula_solenoide', 'filtro', 'manometro'])->index();
            $table->string('nombre_identificador', 100); // Ej: "Bomba Diésel 10HP" o "Válvula Compuerta 4-Pulgadas"
            $table->string('diametro_pulgadas', 20)->nullable(); // Ej: "4", "2.5", "1/2"
            $table->decimal('presion_trabajo_psi', 6, 2)->nullable(); // Presión óptima de operación
            $table->decimal('caudal_estimado_litros_minuto', 8, 2)->nullable(); // Capacidad nominal teórica
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });
        Schema::create('eventos_riego', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sistema_riego_id')->constrained('sistemas_riego');
            $table->foreignId('responsable_id')->constrained('users'); // Quién abrió la válvula
            $table->dateTime('fecha_hora_inicio')->index();
            $table->dateTime('fecha_hora_fin')->nullable();
            $table->integer('duracion_total_minutos')->nullable(); // (Fin - Inicio)
            // Parámetros físicos medidos en el evento
            $table->decimal('presion_promedio_psi', 6, 2)->nullable();
            $table->decimal('caudal_estimado_litros_minuto', 8, 2); // Sumatoria nominal de componentes activos
            // MÉTRICA CLAVE MÓVIL: Calculada por el software inmediatamente al cerrar el riego
            // Fórmula : (duracion_total_minutos * caudal_estimado_litros_minuto)
            $table->decimal('volumen_estimado_litros', 12, 2)->default(0); 
            $table->enum('estado', ['en_progreso', 'finalizado', 'cancelado'])->default('en_progreso')->index();
            $table->timestamps();
        });
        Schema::create('evento_riego_componentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_riego_id')->constrained('eventos_riego')->onDelete('cascade');
            $table->foreignId('componente_id')->constrained('componentes_riego');
            $table->boolean('estaba_activo')->default(true); // Controla si la válvula se abrió o no en ese turno
            $table->timestamps();
        });
        Schema::create('validaciones_riego', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_riego_id')->constrained('eventos_riego')->onDelete('cascade');
            $table->foreignId('tanque_id')->constrained('tanques');
            
            // Mediciones físicas reales del tanque
            $table->decimal('nivel_tanque_antes_litros', 12, 2);
            $table->decimal('nivel_tanque_despues_litros', 12, 2);
            
            // Volumen Real Descido = (Antes - Después)
            $table->decimal('volumen_real_consumido_litros', 12, 2); 
            
            // Auditoría Matemática de Eficiencia (Espejo de control)
            $table->decimal('volumen_estimado_litros', 12, 2); // Lo que dijo la tabla eventos_riego
            $table->decimal('diferencia_litros', 12, 2); // (Volumen Real - Volumen Estimado)
            $table->decimal('porcentaje_error', 5, 2); // ((Diferencia / Volumen Real) * 100)
            
            // Diagnóstico agronómico de campo
            $table->text('observaciones_auditoria')->nullable(); // Ej: "Porcentaje alto por fuga detectada en manguera de 3 pulgadas"  
            $table->timestamps();
        });
        Schema::create('lecturas_sensores_tanque', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->foreignId('tanque_id')->constrained('tanques')->onDelete('cascade');
            $table->decimal('lectura_distancia_cm', 6, 2)->nullable(); // Medición cruda del sensor ultrasonido
            $table->decimal('porcentaje_volumen', 5, 2); // Ej: 85.50% lleno
            $table->decimal('calculo_litros_actuales', 12, 2); // Volumen neto convertido por la geometría del tanque
            
            $table->timestamp('fecha_hora_lectura')->index();
            $table->string('dispositivo_mac', 50)->nullable(); // Identificador del hardware IoT
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('tanques');
    }
};
