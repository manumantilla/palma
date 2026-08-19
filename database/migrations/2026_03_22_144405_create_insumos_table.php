<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('unidades_medida', function (Blueprint $table){
            $table->id();
            $table->string('nombre', 50);
            $table->string('abreviatura', 10);
            $table->enum('tipo', ['masa', 'volumen', 'unidad'])->default('unidad');
            $table->decimal('factor_conversion', 8, 4)->default(1); // Factor de conversión a la unidad base
            $table->timestamps();
        });
        DB::table('unidades_medida')->insert([
            ['nombre' => 'Kilogramo', 'abreviatura' => 'kg', 'tipo' => 'masa', 'factor_conversion' => 1],
            ['nombre' => 'Gramo', 'abreviatura' => 'g', 'tipo' => 'masa', 'factor_conversion' => 0.001],
            ['nombre' => 'Litro', 'abreviatura' => 'L', 'tipo' => 'volumen', 'factor_conversion' => 1],
            ['nombre' => 'Mililitro', 'abreviatura' => 'mL', 'tipo' => 'volumen', 'factor_conversion' => 0.001],
            ['nombre' => 'Unidad', 'abreviatura' => 'u', 'tipo' => 'unidad', 'factor_conversion' => 1],
        ]);

        Schema::create('insumos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias_insumo')->onDelete('cascade');
            $table->string('nombre');
            $table->string('registro_ica', 50)->nullable();
            $table->string('ingrediente_principal')->nullable();
            $table->foreignId('unidad_base_id')->constrained('unidades_medida')->after('nombre');
            $table->foreignId('unidad_uso_id')->nullable()->constrained('unidades_medida')->after('unidad_base_id');
            $table->enum('nivel_toxicidad', ['bajo', 'medio', 'alto'])->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->integer('rei_horas')->nullable();
            $table->integer('phi_dias')->nullable();
            $table->string('clasificacion_toxicologica', 20)->nullable(); // 'Ia', 'Ib', 'II', 'III', 'IV'
            $table->string('equipo_proteccion', 255)->nullable();  // texto o JSON
            $table->string('franja_color', 20)->nullable(); // rojo, amarillo, azul, verde
            $table->string('almacenamiento_temp_min', 10)->nullable();
            $table->string('almacenamiento_temp_max', 10)->nullable();
            $table->string('almacenamiento_humedad', 20)->nullable();
            $table->boolean('requiere_refrigeracion')->default(false);
            $table->boolean('sensible_luz')->default(false);
            $table->decimal('stock_minimo', 12,2)->nullable();
            $table->integer('dias_aviso_vencimiento')->default(30);
            $table->jsonb('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('insumo_componentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insumo_id')->constrained('insumos')->onDelete('cascade');
            $table->enum('tipo_componente', ['nutriente', 'activo', 'coadyuvante', 'carga', 'otros'])->default('nutriente');
            $table->string('componente');          // 'Nitrógeno', 'Potasio', 'Trichoderma', etc.
            $table->string('unidad');              // '%', 'UFC/g', 'mg/kg'
            $table->decimal('concentracion', 8, 4); // Ej: 46.00 para Urea (46% N)
            $table->timestamps();
        });

        DB::table('insumos')->insert([
            [
                'categoria_id'               => 1, // Fertilizantes
                'nombre'                     => 'Urea 46%',
                'registro_ica'               => 'ICA-1234',
                'ingrediente_principal'      => 'Nitrógeno',
                'unidad_base_id'             => 1, // Kilogramo
                'unidad_uso_id'              => 1,
                'nivel_toxicidad'            => null,
                'estado'                     => 'activo',
                'rei_horas'                  => 0,
                'phi_dias'                   => 0,
                'clasificacion_toxicologica' => null,
                'equipo_proteccion'          => null,
                'franja_color'               => null,
                'almacenamiento_temp_min'    => '0',
                'almacenamiento_temp_max'    => '40',
                'almacenamiento_humedad'     => '<70%',
                'requiere_refrigeracion'     => false,
                'sensible_luz'               => false,
                'stock_minimo'               => 100.00,
                'dias_aviso_vencimiento'     => 30,
                'metadata'                   => null,
                'created_at'                 => Carbon::now(),
                'updated_at'                 => Carbon::now(),
                'deleted_at'                 => null,
            ],
            [
                'categoria_id'               => 2, // Plaguicidas
                'nombre'                     => 'Glifosato 48%',
                'registro_ica'               => 'ICA-5678',
                'ingrediente_principal'      => 'Glifosato',
                'unidad_base_id'             => 2, // Litro
                'unidad_uso_id'              => 2,
                'nivel_toxicidad'            => 'medio',
                'estado'                     => 'activo',
                'rei_horas'                  => 12,
                'phi_dias'                   => 7,
                'clasificacion_toxicologica' => 'II',
                'equipo_proteccion'          => 'Guantes, mascarilla, overol',
                'franja_color'               => 'amarillo',
                'almacenamiento_temp_min'    => '5',
                'almacenamiento_temp_max'    => '35',
                'almacenamiento_humedad'     => '<60%',
                'requiere_refrigeracion'     => false,
                'sensible_luz'               => true,
                'stock_minimo'               => 50.00,
                'dias_aviso_vencimiento'     => 30,
                'metadata'                   => null,
                'created_at'                 => Carbon::now(),
                'updated_at'                 => Carbon::now(),
                'deleted_at'                 => null,
            ],
            [
                'categoria_id'               => 3, // Biológicos
                'nombre'                     => 'Trichoderma harzianum',
                'registro_ica'               => 'ICA-9012',
                'ingrediente_principal'      => 'Trichoderma',
                'unidad_base_id'             => 3, // Gramo
                'unidad_uso_id'              => 4, // Sobre (unidad de uso)
                'nivel_toxicidad'            => 'bajo',
                'estado'                     => 'activo',
                'rei_horas'                  => null,
                'phi_dias'                   => null,
                'clasificacion_toxicologica' => null,
                'equipo_proteccion'          => 'Guantes',
                'franja_color'               => 'verde',
                'almacenamiento_temp_min'    => '4',
                'almacenamiento_temp_max'    => '25',
                'almacenamiento_humedad'     => '<50%',
                'requiere_refrigeracion'     => true,
                'sensible_luz'               => true,
                'stock_minimo'               => 10.00,
                'dias_aviso_vencimiento'     => 60,
                'metadata'                   => json_encode(['cepa' => 'T-22', 'formulacion' => 'polvo mojable']),
                'created_at'                 => Carbon::now(),
                'updated_at'                 => Carbon::now(),
                'deleted_at'                 => null,
            ],
        ]);

    // Insertar componentes (asumiendo que los IDs de insumos son 1, 2 y 3)
        DB::table('insumo_componentes')->insert([
            [
                'insumo_id'      => 1, // Urea
                'tipo_componente' => 'nutriente',
                'componente'     => 'Nitrógeno',
                'unidad'         => '%',
                'concentracion'  => 46.0000,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
            [
                'insumo_id'      => 1, // Urea (opcional, otro componente)
                'tipo_componente' => 'otros',
                'componente'     => 'Bióxido de carbono',
                'unidad'         => '%',
                'concentracion'  => 0.5000,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
            [
                'insumo_id'      => 2, // Glifosato
                'tipo_componente' => 'activo',
                'componente'     => 'Glifosato',
                'unidad'         => '%',
                'concentracion'  => 48.0000,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
            [
                'insumo_id'      => 2, // Glifosato, coadyuvante
                'tipo_componente' => 'coadyuvante',
                'componente'     => 'Surfactante',
                'unidad'         => '%',
                'concentracion'  => 5.0000,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
            [
                'insumo_id'      => 3, // Trichoderma
                'tipo_componente' => 'activo',
                'componente'     => 'Trichoderma harzianum',
                'unidad'         => 'UFC/g',
                'concentracion'  => 1000.0000, // Ajusta según rango decimal (8,4)
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('insumos');
        Schema::dropIfExists('insumo_componentes');       
    }
};
