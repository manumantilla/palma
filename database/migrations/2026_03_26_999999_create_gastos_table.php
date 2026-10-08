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
     Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->enum('origen', [
                'manual',
                'aplicacion_insumo',
                'mano_obra',
                'uso_maquinaria',
                'mantenimiento_maquinaria',
                'flete_despacho',
                'comision_venta',
                'compra_servicio',
                'amortizacion',
            ])->default('manual');
            $table->nullableMorphs('gastable'); // documento origen: EventoInsumo, EventoManoObra, CartaPorte...
            $table->foreignId('categoria_gasto_id')->constrained('categorias_gasto')->restrictOnDelete();
            $table->enum('clase_costo', [
                'costo_produccion', 'opex', 'capex', 'gasto_financiero', 'impuesto', 'otro',
            ])->default('opex');
            $table->enum('imputacion', ['directo', 'indirecto'])->default('indirecto');
            $table->foreignId('lote_id')->nullable()->constrained('lotes')->restrictOnDelete();
            $table->foreignId('ciclo_productivo_id')->nullable()->constrained('ciclos_productivos')->restrictOnDelete();
            $table->unsignedBigInteger('ciclo_etapa_id')->nullable();

            // ---------- Capitalización ----------
            $table->boolean('capitalizable')->default(false);
            $table->foreignId('activo_amortizable_id')->nullable()->constrained('activos_amortizables')->restrictOnDelete();

            // ---------- Tercero ----------
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->restrictOnDelete();
            $table->foreignId('trabajador_id')->nullable()->constrained('trabajadores')->restrictOnDelete();
            $table->string('tercero_nombre')->nullable();        // DIAN, municipio, conductor externo...
            $table->string('tercero_documento', 30)->nullable();

            // ---------- Soporte (DIAN) ----------
            $table->enum('tipo_soporte', [
                'factura_electronica',
                'documento_soporte',   // compras a no obligados a facturar (campesinos, jornaleros)
                'cuenta_cobro',
                'recibo_caja',
                'nomina_electronica',
                'comprobante_interno',
            ])->default('comprobante_interno');
            $table->string('numero_soporte', 60)->nullable();
            $table->string('cufe', 120)->nullable();
            $table->string('comprobante_archivo')->nullable();
            $table->string('cuenta_puc', 10)->nullable(); // sobrescribe la de la categoría

            // ---------- Descripción y fechas ----------
            $table->string('concepto');
            $table->text('descripcion')->nullable();
            $table->date('fecha');                       // fecha de causación
            $table->date('fecha_vencimiento')->nullable();

            // ---------- Valores ----------
            $table->char('moneda', 3)->default('COP');
            $table->decimal('tasa_cambio', 12, 4)->default(1);
            $table->decimal('base_gravable', 14, 2);
            $table->decimal('porcentaje_iva', 5, 2)->default(0);
            $table->decimal('valor_iva', 14, 2)->default(0);
            $table->boolean('iva_descontable')->default(false);
            $table->decimal('retefuente', 14, 2)->default(0);
            $table->decimal('reteica', 14, 2)->default(0);
            $table->decimal('reteiva', 14, 2)->default(0);

            $table->decimal('total', 14, 2)->storedAs('base_gravable + valor_iva');
            $table->decimal('neto_a_pagar', 14, 2)->storedAs('base_gravable + valor_iva - retefuente - reteica - reteiva');
            $table->decimal('costo_cop', 16, 2)->storedAs(
                'ROUND((base_gravable + CASE WHEN iva_descontable THEN 0 ELSE valor_iva END) * tasa_cambio, 2)'
            );

            $table->boolean('es_cuenta_por_pagar')->default(true);
            $table->decimal('valor_pagado', 14, 2)->default(0);

            // ---------- Estado y auditoría ----------
            $table->enum('estado', ['borrador', 'devengado', 'pagado_parcial', 'pagado', 'anulado'])->default('devengado');
            $table->string('motivo_anulacion')->nullable();
            $table->foreignId('anulado_por')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('anulado_at')->nullable();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->foreign(['ciclo_etapa_id', 'ciclo_productivo_id'], 'gastos_etapa_pertenece_al_ciclo_fk')
                ->references(['id', 'ciclo_productivo_id'])
                ->on('ciclo_etapas_historial')
                ->restrictOnDelete();

            $table->index(['ciclo_productivo_id', 'categoria_gasto_id']);
            $table->index(['fecha', 'estado']);
            $table->index(['proveedor_id', 'estado']);
            $table->index('activo_amortizable_id');
            $table->index('fecha_vencimiento');
        });

        Schema::create('gastos_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gasto_id')->constrained('gastos')->restrictOnDelete();
            $table->decimal('monto', 14, 2);
            $table->date('fecha_pago');
            $table->enum('metodo', ['efectivo', 'transferencia', 'cheque', 'tarjeta'])->default('transferencia');
            $table->string('referencia_transaccion')->nullable();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('fecha_pago');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos_pagos');
        Schema::dropIfExists('gastos');
    }
};
