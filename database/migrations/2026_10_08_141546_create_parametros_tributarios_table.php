<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametros_tributarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('anio')->unique();
            $table->decimal('uvt', 12, 2);
            $table->decimal('smmlv', 14, 2);
            $table->decimal('auxilio_transporte', 14, 2);

            $table->decimal('pct_cesantias', 5, 2)->default(8.33);
            $table->decimal('pct_intereses_cesantias', 5, 2)->default(1.00);
            $table->decimal('pct_prima', 5, 2)->default(8.33);
            $table->decimal('pct_vacaciones', 5, 2)->default(4.17);
            $table->decimal('pct_salud_empleador', 5, 2)->default(8.50);   
            $table->decimal('pct_pension_empleador', 5, 2)->default(12.00);
            $table->decimal('pct_arl', 6, 3)->default(0.522);             
            $table->decimal('pct_caja_compensacion', 5, 2)->default(4.00);
            $table->decimal('pct_icbf', 5, 2)->default(3.00);            
            $table->decimal('pct_sena', 5, 2)->default(2.00);        
            $table->decimal('pct_cuota_fomento', 5, 2)->default(0);
            $table->decimal('pct_gmf', 5, 3)->default(0.400);                // 4x1000
            $table->timestamps();
        });

        Schema::create('tarifas_retencion', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('anio');
            $table->enum('tipo', ['retefuente', 'reteica', 'reteiva']);
            $table->string('concepto', 60);                 
            $table->string('descripcion')->nullable();
            $table->decimal('base_minima_uvt', 8, 2)->default(0);
            $table->decimal('tarifa_pct', 6, 3);    // 0.966 por mil = 0.0966
            $table->string('municipio')->nullable();        // solo reteica
            $table->boolean('aplica_declarante')->nullable(); // null = aplica igual
            $table->timestamps();

            $table->unique(['anio', 'tipo', 'concepto', 'municipio'], 'tarifas_retencion_unica');
        });

        $now = now();
        DB::table('parametros_tributarios')->insert([
            'anio' => 2025, 'uvt' => 49799, 'smmlv' => 1423500, 'auxilio_transporte' => 200000,
            'pct_cuota_fomento' => 0, //depende del cultivo y la asociacion
            'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('tarifas_retencion')->insert(array_map(fn ($r) => $r + ['anio' => 2025, 'tipo' => 'retefuente', 'created_at' => $now, 'updated_at' => $now], [
            ['concepto' => 'compras_generales',              'descripcion' => 'Compras generales (declarantes)',               'base_minima_uvt' => 27, 'tarifa_pct' => 2.5, 'aplica_declarante' => true,  'municipio' => null],
            ['concepto' => 'compras_agricolas_sin_procesar', 'descripcion' => 'Productos agrícolas o pecuarios sin procesamiento industrial', 'base_minima_uvt' => 92, 'tarifa_pct' => 1.5, 'aplica_declarante' => null, 'municipio' => null],
            ['concepto' => 'servicios_generales',            'descripcion' => 'Servicios generales (declarantes)',             'base_minima_uvt' => 4,  'tarifa_pct' => 4.0, 'aplica_declarante' => true,  'municipio' => null],
            ['concepto' => 'transporte_carga',               'descripcion' => 'Transporte nacional de carga',                  'base_minima_uvt' => 4,  'tarifa_pct' => 1.0, 'aplica_declarante' => null, 'municipio' => null],
            ['concepto' => 'arrendamiento_inmuebles',        'descripcion' => 'Arrendamiento de bienes raíces',                'base_minima_uvt' => 27, 'tarifa_pct' => 3.5, 'aplica_declarante' => null, 'municipio' => null],
            ['concepto' => 'honorarios_pn',                  'descripcion' => 'Honorarios persona natural',                    'base_minima_uvt' => 0,  'tarifa_pct' => 10.0, 'aplica_declarante' => null, 'municipio' => null],
        ]));
    }

    public function down(): void
    {
        Schema::dropIfExists('tarifas_retencion');
        Schema::dropIfExists('parametros_tributarios');
    }
};
