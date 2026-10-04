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
        Schema::create('eventos_campo', function (Blueprint $table) {
            $table->id(); 
            //Puede ser nullable
            $table->foreignId('ciclo_productivo_id')->nullable()->constrained('ciclos_productivos')->onDelete('set null');
            $table->foreignId('lote_id')->nullable()->constrained('lotes');
            //zona de lotex
            $table->foreignId('zona_id')->nullable()->constrained('lotes_zonas_manejo')->onDelete('set null');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            //Importante
            $table->foreignId('tipo_evento_id')->constrained('tipos_evento');
            $table->dateTime('fecha_programada');
            $table->dateTime('fecha_ejecucion')->nullable();
            $table->geometry('coordenada_gps', 'GEOMETRY', 4326)->nullable();
            $table->enum('estado', ['Pendiente', 'En Proceso', 'Completado', 'Cancelado'])->default('Pendiente');
            $table->text('observaciones')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('evento_arbol', function(Blueprint $table){
            $table->id(); // ID incremental normal para la velocidad de indexación interna de Postgre
            $table->foreignId('evento_campo_id')->constrained('eventos_campo')->onDelete('cascade');

            // Llave foránea al árbol
            $table->foreignId('arbol_id')->constrained('arboles')->onDelete('cascade');
            
            // Opcionales de trazabilidad agronómica individual por si un árbol específico tuvo novedad
            $table->enum('novedad_arbol', ['ninguna', 'enfermo', 'muerto', 'no_aplicó', 'reemplazo'])->default('ninguna');
            $table->text('nota_individual')->nullable(); 
            
            $table->timestamps();
            
            // Súper importante para que las consultas por árbol vuelen en Postgres
            $table->index(['evento_campo_id', 'arbol_id']);
        });

        $table->foreignId('ciclo_etapa_id')
            ->nullable()
            ->constrained('ciclo_etapas_historial')
            ->onDelete('set null');

        Schema::create('evento_insumos', function(Blueprint $table){
            $table->id();
            $table->foreignId('evento_campo_id')->constrained('eventos_campo')->onDelete('cascade');
            $table->foreignId('insumo_id')->constrained('insumos')->onDelete('cascade');
            $table->decimal('cantidad',12,2);
            $table->decimal('area_aplicada',10,2)->nullable(); // siempre en HA, si la aplicacion es por abrbol tiene que quedar null
            $table->enum('metodo_aplicacion', ['terrestre', 'foliar', 'dron', 'fertirriego', 'drench'])->default('terrestre');  
            $table->enum('unidad_medida',['kg', 'litros', 'unidades']);
            $table->decimal('costo_total', 12,2)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('evento_insumo_lotes', function(Blueprint $table){
            $table->id();
            $table->foreignId('evento_insumo_id')->constrained('evento_insumos')->onDelete('cascade');
            $table->foreignId('lote_insumo_id')->constrained('lotes_insumos')->onDelete('restrict');
            $table->decimal('cantidad', 12,2); // cantidad del insumo aplicada a ese lote específico
            $table->decimal('precio',14,2);
            $table->decimal('area_aplicada',10,2)->nullable();
            
            $table->timestamps();
        });

      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos_campo');
        Schema::dropIfExists('evento_insumos');
        Schema::dropIfExists('evento_insumo_lotes');
        
        
    }
};
