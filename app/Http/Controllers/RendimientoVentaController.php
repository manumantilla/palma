<?php

namespace App\Http\Controllers;

use App\Models\CicloProductivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RendimientoVentaController extends Controller
{
    public function dashboard(Request $request, $ciclo_id)
    {
        $ciclo = CicloProductivo::with(['lote', 'cultivo'])->findOrFail($ciclo_id);

        // 1. RENDIMIENTO FÍSICO (Campo a Contenedor)
        // Sumar todo el peso neto recibido de los trabajadores en este ciclo
        $kilosCosechados = DB::table('recepciones_campo')
            ->join('sesiones_cosecha', 'recepciones_campo.sesion_id', '=', 'sesiones_cosecha.id')
            ->where('sesiones_cosecha.ciclo_productivo_id', $ciclo_id)
            ->sum('recepciones_campo.peso_neto');

        // Sumar mermas registradas
        $kilosMerma = DB::table('mermas')
            ->join('recepciones_campo', 'mermas.recepcion_campo_id', '=', 'recepciones_campo.id')
            ->join('sesiones_cosecha', 'recepciones_campo.sesion_id', '=', 'sesiones_cosecha.id')
            ->where('sesiones_cosecha.ciclo_productivo_id', $ciclo_id)
            ->sum('mermas.kilos_merma');

        // 2. LOGÍSTICA DE SALIDA
        // Kilos despachados (asumiendo que los items apuntan al ciclo)
        $kilosDespachados = DB::table('despacho_items')
            ->where('ciclo_productivo_id', $ciclo_id)
            ->sum('peso_neto_total');

        // 3. RENDIMIENTO FINANCIERO (Ventas y Liquidaciones)
        // Valor Neto liquidado (después de fletes y comisiones del intermediario)
        $ventasLiquidadas = DB::table('liquidaciones_despacho')
            ->join('despacho_items', 'liquidaciones_despacho.despacho_item_id', '=', 'despacho_items.id')
            ->where('despacho_items.ciclo_productivo_id', $ciclo_id)
            ->sum('liquidaciones_despacho.valor_neto_item');

        // Flujo de caja real: Plata que ya pagaron
        $flujoCajaReal = DB::table('pagos_liquidacion')
            ->join('despachos', 'pagos_liquidacion.despacho_id', '=', 'despachos.id')
            ->join('despacho_items', 'despacho_items.despacho_id', '=', 'despachos.id')
            ->where('despacho_items.ciclo_productivo_id', $ciclo_id)
            ->distinct('pagos_liquidacion.id') // Evitar duplicar pagos si hay múltiples items
            ->sum('pagos_liquidacion.valor_pagado');

        // 4. INDICADORES CLAVE (KPIs)
        $hectareas = $ciclo->lote->area_hectareas ?? 1; // Prevenir división por cero
        $rendimientoHa = $hectareas > 0 ? ($kilosCosechados / $hectareas) : 0;
        $porcentajeMerma = $kilosCosechados > 0 ? ($kilosMerma / $kilosCosechados) * 100 : 0;
        $carteraPendiente = $ventasLiquidadas - $flujoCajaReal;
        
        // Retorno de Inversión (ROI) - Asumiendo que el ciclo tiene el costo guardado
        $utilidadBruta = $ventasLiquidadas - $ciclo->costo_total_invertido;

        return view('rendimientos.dashboard', compact(
            'ciclo', 'kilosCosechados', 'kilosMerma', 'kilosDespachados', 
            'ventasLiquidadas', 'flujoCajaReal', 'rendimientoHa', 
            'porcentajeMerma', 'carteraPendiente', 'utilidadBruta'
        ));
    }
}