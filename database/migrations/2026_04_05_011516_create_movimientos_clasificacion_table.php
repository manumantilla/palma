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
            Schema::create('movimientos_clasificacion', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignId('operario_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignUuid('recepcion_campo_id')->constrained('recepciones_campo')->onDelete('cascade');
                $table->foreignUuid('contenedor_id')->constrained('contenedores')->onDelete('cascade');
                $table->date('fecha_movimiento');
                $table->decimal('kilos_asignados', 10, 2);
                $table->text('observaciones')->nullable();
                $table->timestamp('client_updated_at')->nullable();
                $table->timestamp('synced_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_clasificacion');
    }
};
