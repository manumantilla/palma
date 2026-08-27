<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arbol;
use App\Models\Lote;
use App\Models\OrdenCosecha;
use App\Models\Gasto;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Estadísticas rápidas (Métricas clave o KPIs)
        // Sustituye los 0 por consultas reales como: Arbol::count()
        $kpis = [
            'total_arboles' => 0, 
            'lotes_activos' => 0,
            'ordenes_pendientes' => 0,
            'gastos_mes' => 0, 
        ];

        // 2. Aquí prepararemos los datos para los gráficos en el futuro
        $graficoProduccion = [
            'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            'datos' => [120, 190, 150, 220, 300, 250] // Kilos cosechados, por ejemplo
        ];

        return view('dashboard', compact('kpis', 'graficoProduccion'));
    }
}