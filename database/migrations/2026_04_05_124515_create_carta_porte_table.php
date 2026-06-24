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
       Schema::create('carta_porte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('despacho_id')->constrained('despachos')->onDelete('cascade');

            $table->string('numero_carta_porte')->nullable();   // número del documento físico

            // Transportador
            $table->enum('tipo_transportador', ['propio', 'tercero', 'fletero']);
            $table->foreignId('transportador_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nombre_conductor');
            $table->string('cedula_conductor');
            $table->string('telefono_conductor')->nullable();

            // Vehículo
            $table->string('placa_vehiculo');
            $table->string('tipo_vehiculo')->nullable();        // camión, camioneta, tractomula
            $table->string('placa_trailer')->nullable();        // si lleva remolque

            // Ruta
            $table->string('municipio_origen');
            $table->string('departamento_origen');
            $table->string('municipio_destino');
            $table->string('departamento_destino');
            $table->string('ruta_descripcion')->nullable();     // "por la vía Panamericana"

            // Flete
            $table->decimal('valor_flete', 12, 2)->nullable();
            $table->enum('quien_paga_flete', ['productor', 'comprador', 'comisionista'])->nullable();
            $table->enum('forma_pago_flete', ['contado', 'credito', 'descuento_liquidacion'])->nullable();

            // Pesos para la carta porte
            $table->decimal('peso_declarado_kg', 12, 2);       // peso oficial declarado en el documento
            $table->string('descripcion_carga');               // "100 bultos cebolla cabezona"

            // Sellos/Control
            $table->string('numero_sello')->nullable();         // sello del camión si aplica
            $table->dateTime('fecha_salida');
            $table->dateTime('fecha_llegada_real')->nullable();

            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carta_porte');
    }
};
