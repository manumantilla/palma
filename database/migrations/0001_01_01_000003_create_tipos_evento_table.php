<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tipos_evento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->enum('categoria', ['Mantenimiento', 'Fitosanitario', 'Cosecha', 'Fertilización', 'Logística']);
            $table->boolean('consume_insumos')->default(false);
            $table->boolean('consume_mano_obra')->default(false);
            $table->boolean('genera_ingreso')->default(false);
            $table->boolean('genera_movimiento_stock')->default(false);

            $table->boolean('requiere_area_ha')->default(false);   // fumigación, riego, encalado
            $table->boolean('aplica_a_arbol')->default(false);     // poda, polinización (perennes)
            $table->boolean('aplica_a_ciclo')->default(true);

            // Para eventos fitosanitarios: período de reingreso al lote post-aplicación
            $table->unsignedSmallInteger('periodo_reingreso_horas')->nullable();
            $table->unsignedSmallInteger('periodo_carencia_dias')->nullable(); // Días antes de cosecha
            $table->timestamps();
        });

        DB::table('tipos_evento')->insert([
        [
            'nombre' => 'Riego',
            'categoria' => 'Mantenimiento',
            'consume_insumos' => false,
            'consume_mano_obra' => true,
            'genera_ingreso' => false,
            'genera_movimiento_stock' => false,
            'requiere_area_ha' => true,
            'aplica_a_arbol' => false,
            'aplica_a_ciclo' => true,
            
            'periodo_reingreso_horas' => 24,
            'periodo_carencia_dias' => 14,

        ],
        [
            'nombre' => 'Deshierbe',
            'categoria' => 'Mantenimiento',
            'consume_insumos' => false,
            'consume_mano_obra' => true,
            'genera_ingreso' => false,
            'genera_movimiento_stock' => false,
            'requiere_area_ha' => true,
            'aplica_a_arbol' => false,
            'aplica_a_ciclo' => true,
            
            'periodo_reingreso_horas' => 24,
            'periodo_carencia_dias' => 14,
        ],
        [
            'nombre' => 'Poda',
            'categoria' => 'Mantenimiento',
            'consume_insumos' => false,
            'consume_mano_obra' => true,
            'genera_ingreso' => false,
            'genera_movimiento_stock' => false,
            'requiere_area_ha' => false,
            'aplica_a_arbol' => true,
            'aplica_a_ciclo' => true,
            
            'periodo_reingreso_horas' => 24,
            'periodo_carencia_dias' => 14,
        ],
        [
            'nombre' => 'Tutorado',
            'categoria' => 'Mantenimiento',
            'consume_insumos' => true,
            'consume_mano_obra' => true,
            'genera_ingreso' => false,
            'genera_movimiento_stock' => true,
            'requiere_area_ha' => false,
            'aplica_a_arbol' => false,
            'aplica_a_ciclo' => true,
            
            'periodo_reingreso_horas' => 24,
            'periodo_carencia_dias' => 14,
        ],

        // ==========================
        // FERTILIZACIÓN
        // ==========================
        [
            'nombre' => 'Fertilización química',
            'categoria' => 'Fertilización',
            'consume_insumos' => true,
            'consume_mano_obra' => true,
            'genera_ingreso' => false,
            'genera_movimiento_stock' => true,
            'requiere_area_ha' => true,
            'aplica_a_arbol' => false,
            'aplica_a_ciclo' => true,
            
            'periodo_reingreso_horas' => 24,
            'periodo_carencia_dias' => 14,
        ],
        [
            'nombre' => 'Fertilización orgánica',
            'categoria' => 'Fertilización',
            'consume_insumos' => true,
            'consume_mano_obra' => true,
            'genera_ingreso' => false,
            'genera_movimiento_stock' => true,
            'requiere_area_ha' => true,
            'aplica_a_arbol' => false,
            'aplica_a_ciclo' => true,
            
            'periodo_reingreso_horas' => 24,
            'periodo_carencia_dias' => 14,
        ],
        [
            'nombre' => 'Encalado',
            'categoria' => 'Fertilización',
            'consume_insumos' => true,
            'consume_mano_obra' => true,
            'genera_ingreso' => false,
            'genera_movimiento_stock' => true,
            'requiere_area_ha' => true,
            'aplica_a_arbol' => false,
            'aplica_a_ciclo' => true,
            
            'periodo_reingreso_horas' => 24,
            'periodo_carencia_dias' => 14,
        ],

        // ==========================
        // FITOSANITARIO
        // ==========================
        [
            'nombre' => 'Aplicación de fungicida',
            'categoria' => 'Fitosanitario',
            'consume_insumos' => true,
            'consume_mano_obra' => true,
            'genera_ingreso' => false,
            'genera_movimiento_stock' => true,
            'requiere_area_ha' => true,
            'aplica_a_arbol' => false,
            'aplica_a_ciclo' => true,
            'periodo_reingreso_horas' => 24,
            'periodo_carencia_dias' => 14,
        ],
        [
            'nombre' => 'Aplicación de insecticida',
            'categoria' => 'Fitosanitario',
            'consume_insumos' => true,
            'consume_mano_obra' => true,
            'genera_ingreso' => false,
            'genera_movimiento_stock' => true,
            'requiere_area_ha' => true,
            'aplica_a_arbol' => false,
            'aplica_a_ciclo' => true,
            'periodo_reingreso_horas' => 24,
            'periodo_carencia_dias' => 14,
        ],
        [
            'nombre' => 'Aplicación de herbicida',
            'categoria' => 'Fitosanitario',
            'consume_insumos' => true,
            'consume_mano_obra' => true,
            'genera_ingreso' => false,
            'genera_movimiento_stock' => true,
            'requiere_area_ha' => true,
            'aplica_a_arbol' => false,
            'aplica_a_ciclo' => true,
            'periodo_reingreso_horas' => 24,
            'periodo_carencia_dias' => 21,
        ],
        [
            'nombre' => 'Aplicación de bioinsumos',
            'categoria' => 'Fitosanitario',
            'consume_insumos' => true,
            'consume_mano_obra' => true,
            'genera_ingreso' => false,
            'genera_movimiento_stock' => true,
            'requiere_area_ha' => true,
            'aplica_a_arbol' => false,
            'aplica_a_ciclo' => true,
            
            'periodo_reingreso_horas' => 24,
            'periodo_carencia_dias' => 14,
        ],

        // ==========================
        // COSECHA
        // ==========================
        [
            'nombre' => 'Cosecha',
            'categoria' => 'Cosecha',
            'consume_insumos' => false,
            'consume_mano_obra' => true,
            'genera_ingreso' => true,
            'genera_movimiento_stock' => true,
            'requiere_area_ha' => false,
            'aplica_a_arbol' => false,
            'aplica_a_ciclo' => true,
            
            'periodo_reingreso_horas' => 24,
            'periodo_carencia_dias' => 14,
        ],

        // ==========================
        // LOGÍSTICA
        // ==========================
        [
            'nombre' => 'Recepción de cosecha',
            'categoria' => 'Logística',
            'consume_insumos' => false,
            'consume_mano_obra' => true,
            'genera_ingreso' => false,
            'genera_movimiento_stock' => true,
            'requiere_area_ha' => false,
            'aplica_a_arbol' => false,
            'aplica_a_ciclo' => true,
            
            'periodo_reingreso_horas' => 24,
            'periodo_carencia_dias' => 14,
        ],
        [
            'nombre' => 'Transporte',
            'categoria' => 'Logística',
            'consume_insumos' => false,
            'consume_mano_obra' => true,
            'genera_ingreso' => false,
            'genera_movimiento_stock' => false,
            'requiere_area_ha' => false,
            'aplica_a_arbol' => false,
            'aplica_a_ciclo' => true,
            
            'periodo_reingreso_horas' => 24,
            'periodo_carencia_dias' => 14,
        ],
        [
            'nombre' => 'Clasificación',
            'categoria' => 'Logística',
            'consume_insumos' => false,
            'consume_mano_obra' => true,
            'genera_ingreso' => false,
            'genera_movimiento_stock' => true,
            'requiere_area_ha' => false,
            'aplica_a_arbol' => false,
            'aplica_a_ciclo' => true,
            
            'periodo_reingreso_horas' => 24,
            'periodo_carencia_dias' => 14,
        ],
    ]);

        // Carencia (PHI) y reingreso (REI) solo tienen sentido cuando se aplica un producto;
        // el valor real lo da el insumo (insumos.phi_dias / rei_horas).
        DB::table('tipos_evento')
            ->whereIn('nombre', ['Riego', 'Deshierbe', 'Poda', 'Tutorado', 'Cosecha', 'Recepción de cosecha', 'Transporte', 'Clasificación'])
            ->update(['periodo_reingreso_horas' => null, 'periodo_carencia_dias' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_evento');
    }
};
