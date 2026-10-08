<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_gasto', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 40)->unique();         
            $table->string('nombre');
            $table->string('cuenta_puc', 10)->nullable();   
            $table->enum('clase_costo_default', [
                'costo_produccion', 'opex', 'capex', 'gasto_financiero', 'impuesto', 'otro',
            ])->default('opex');
            $table->enum('imputacion_default', ['directo', 'indirecto'])->default('indirecto');
            $table->string('origen_automatico', 40)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('categorias_gasto')->insert(array_map(fn ($r) => $r + ['created_at' => $now, 'updated_at' => $now], [
          
            ['codigo' => 'insumos_agricolas',     'nombre' => 'Insumos agrícolas (fertilizantes, agroquímicos, bioinsumos)', 'cuenta_puc' => '7105', 'clase_costo_default' => 'costo_produccion', 'imputacion_default' => 'directo',   'origen_automatico' => 'aplicacion_insumo'],
            ['codigo' => 'material_vegetal',      'nombre' => 'Semillas, plántulas y material vegetal',                      'cuenta_puc' => '7105', 'clase_costo_default' => 'costo_produccion', 'imputacion_default' => 'directo',   'origen_automatico' => null],
            ['codigo' => 'mano_obra_directa',     'nombre' => 'Mano de obra directa (jornales y destajo)',                   'cuenta_puc' => '7205', 'clase_costo_default' => 'costo_produccion', 'imputacion_default' => 'directo',   'origen_automatico' => 'mano_obra'],
            ['codigo' => 'maquinaria_operacion',  'nombre' => 'Uso de maquinaria (combustible y horas máquina)',             'cuenta_puc' => '7305', 'clase_costo_default' => 'costo_produccion', 'imputacion_default' => 'directo',   'origen_automatico' => 'uso_maquinaria'],
            ['codigo' => 'maquinaria_mantenim',   'nombre' => 'Mantenimiento de maquinaria',                                 'cuenta_puc' => '7310', 'clase_costo_default' => 'costo_produccion', 'imputacion_default' => 'indirecto', 'origen_automatico' => 'mantenimiento_maquinaria'],
            ['codigo' => 'riego_energia',         'nombre' => 'Agua, energía y riego',                                       'cuenta_puc' => '7315', 'clase_costo_default' => 'costo_produccion', 'imputacion_default' => 'indirecto', 'origen_automatico' => null],
            ['codigo' => 'servicios_contratados', 'nombre' => 'Contratos de servicios agrícolas (fumigación, cosecha, etc.)', 'cuenta_puc' => '7405', 'clase_costo_default' => 'costo_produccion', 'imputacion_default' => 'directo',   'origen_automatico' => 'compra_servicio'],
            ['codigo' => 'amortizacion_cultivo',  'nombre' => 'Amortización de cultivos y depreciación de activos productivos', 'cuenta_puc' => '7360', 'clase_costo_default' => 'costo_produccion', 'imputacion_default' => 'indirecto', 'origen_automatico' => 'amortizacion'],

            ['codigo' => 'personal_admin',        'nombre' => 'Gastos de personal administrativo',                          'cuenta_puc' => '5105', 'clase_costo_default' => 'opex', 'imputacion_default' => 'indirecto', 'origen_automatico' => null],
            ['codigo' => 'honorarios',            'nombre' => 'Honorarios (contador, agrónomo asesor, abogado)',             'cuenta_puc' => '5110', 'clase_costo_default' => 'opex', 'imputacion_default' => 'indirecto', 'origen_automatico' => null],
            ['codigo' => 'impuestos',             'nombre' => 'Impuestos (predial, ICA, vehículos)',                         'cuenta_puc' => '5115', 'clase_costo_default' => 'impuesto', 'imputacion_default' => 'indirecto', 'origen_automatico' => null],
            ['codigo' => 'arrendamientos',        'nombre' => 'Arrendamiento de tierras y equipos',                         'cuenta_puc' => '5120', 'clase_costo_default' => 'opex', 'imputacion_default' => 'indirecto', 'origen_automatico' => null],
            ['codigo' => 'seguros',               'nombre' => 'Seguros (agropecuario, maquinaria)',                         'cuenta_puc' => '5130', 'clase_costo_default' => 'opex', 'imputacion_default' => 'indirecto', 'origen_automatico' => null],
            ['codigo' => 'servicios_publicos',    'nombre' => 'Servicios públicos e internet',                               'cuenta_puc' => '5135', 'clase_costo_default' => 'opex', 'imputacion_default' => 'indirecto', 'origen_automatico' => null],
            ['codigo' => 'gastos_legales',        'nombre' => 'Gastos legales y certificaciones (ICA, GlobalG.A.P.)',       'cuenta_puc' => '5140', 'clase_costo_default' => 'opex', 'imputacion_default' => 'indirecto', 'origen_automatico' => null],

            ['codigo' => 'fletes_venta',          'nombre' => 'Fletes y acarreos de despacho',                              'cuenta_puc' => '5235', 'clase_costo_default' => 'opex', 'imputacion_default' => 'directo', 'origen_automatico' => 'flete_despacho'],
            ['codigo' => 'comisiones_venta',      'nombre' => 'Comisiones de comercialización',                             'cuenta_puc' => '5205', 'clase_costo_default' => 'opex', 'imputacion_default' => 'directo', 'origen_automatico' => 'comision_venta'],

            ['codigo' => 'financieros',           'nombre' => 'Intereses, 4x1000 y comisiones bancarias',                   'cuenta_puc' => '5305', 'clase_costo_default' => 'gasto_financiero', 'imputacion_default' => 'indirecto', 'origen_automatico' => null],

            ['codigo' => 'otros',                 'nombre' => 'Otros gastos',                                               'cuenta_puc' => '5295', 'clase_costo_default' => 'otro', 'imputacion_default' => 'indirecto', 'origen_automatico' => null],
        ]));
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_gasto');
    }
};