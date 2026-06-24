<x-app-layout>

<div class="min-h-screen bg-gray-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Consola Central de Precisión</h1>
                <p class="mt-1 text-sm text-gray-500">Monitoreo bioespacial, fitosanitario y financiero del cultivo de Palma.</p>
            </div>
            <div class="mt-4 md:mt-0 flex flex-wrap gap-3">
                <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all">
                    🖨️ Exportar Reporte ICA
                </button>
                <button class="inline-flex items-center px-4 py-2 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-green-200 shadow-md transition-all">
                    ➕ Sincronizar App Móvil
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-green-50 text-green-600">
                        🌴
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Censo de Individuos</p>
                        <h3 class="text-2xl font-bold text-gray-900">14,250 <span class="text-xs font-normal text-gray-500">árboles</span></h3>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500 flex items-center gap-1">
                    <span class="text-green-600 font-bold">↑ 2.3%</span> resiembras este ciclo.
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-red-50 text-red-600">
                        🚨
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Casos Cuarentena</p>
                        <h3 class="text-2xl font-bold text-red-600">18 <span class="text-xs font-normal text-gray-500">críticos</span></h3>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500 flex items-center gap-1">
                    <span class="text-red-500 font-bold">⚠️ Alerta:</span> Pudrición del Cogollo detectada.
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-indigo-50 text-indigo-600">
                        🛰️
                    </div>
                    <a href="{{route('trabajadores.index')}}"class="ml-4">
                        <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Trabajadores</p>
                        <h3 class="text-2xl font-bold text-indigo-600">0.742 <span class="text-xs font-normal text-gray-500">Vigor</span></h3>
</a>
                </div>
                <div class="mt-4 text-xs text-gray-500 flex items-center gap-1">
                    <span class="text-green-600 font-bold">🟢 Óptimo:</span> 84% de la canopia estable.
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-amber-50 text-amber-600">
                        💰
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Inversión Insumos</p>
                        <h3 class="text-2xl font-bold text-amber-600">$48,250 <span class="text-xs font-normal text-gray-500">USD</span></h3>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500 flex items-center gap-1">
                    <span class="text-amber-600 font-bold">📋 5 Facturas</span> pendientes por pagar.
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-gray-900">Estado de Macro-Lotes (PostGIS GIS)</h2>
                        <span class="bg-green-100 text-green-800 text-xs px-2.5 py-0.5 rounded-full font-semibold">Sistemas Activos</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50 text-gray-500 font-medium">
                                <tr>
                                    <th class="px-6 py-3 text-left">Código / Nombre</th>
                                    <th class="px-6 py-3 text-left">Área GIS</th>
                                    <th class="px-6 py-3 text-left">PH Suelo</th>
                                    <th class="px-6 py-3 text-left">Riego</th>
                                    <th class="px-6 py-3 text-left">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                <tr class="hover:bg-gray-50 transition-all">
                                    <td class="px-6 py-4 font-semibold text-gray-900">L-01 (Lote Palma Norte)</td>
                                    <td class="px-6 py-4">45.5200 Has</td>
                                    <td class="px-6 py-4"><span class="bg-amber-50 text-amber-700 px-2 py-1 rounded-lg text-xs font-bold">5.8 pH</span></td>
                                    <td class="px-6 py-4">💧 Goteo (Instalado)</td>
                                    <td class="px-6 py-4">
                                        <button class="text-blue-600 hover:text-blue-800 font-medium text-xs">Ver Capa Geo</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-all">
                                    <td class="px-6 py-4 font-semibold text-gray-900">L-02 (Lote Central Viejo)</td>
                                    <td class="px-6 py-4">12.1150 Has</td>
                                    <td class="px-6 py-4"><span class="bg-green-50 text-green-700 px-2 py-1 rounded-lg text-xs font-bold">6.2 pH</span></td>
                                    <td class="px-6 py-4">❌ No Posee</td>
                                    <td class="px-6 py-4">
                                        <button class="text-blue-600 hover:text-blue-800 font-medium text-xs">Ver Capa Geo</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Auditoría Fitosanitaria de Campo (GlobalG.A.P.)</h2>
                    <div class="flow-root">
                        <ul class="divide-y divide-gray-100">
                            <li class="py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0 bg-red-100 text-red-600 p-2 rounded-xl text-lg">🪲</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate">Árbol P-12-F03: *Phytophthora cinnamomi*</p>
                                        <p class="text-xs text-gray-500">Detectado por Ing. Evaluador • Hace 2 horas</p>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Química Crítica</span>
                                    </div>
                                </div>
                            </li>
                            <li class="py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0 bg-yellow-100 text-yellow-600 p-2 rounded-xl text-lg">🍂</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate">Árbol P-02-F01: Deficiencia de Potasio</p>
                                        <p class="text-xs text-gray-500">Muestreo foliar manual • Ayer</p>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Monitoreo</span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-gradient-to-br from-gray-900 to-slate-800 shadow-xl rounded-2xl p-6 text-white">
                    <h2 class="text-lg font-bold mb-2">Módulo Financiero Suministros</h2>
                    <p class="text-xs text-slate-400 mb-6">Últimas transacciones asignadas a ciclos productivos.</p>
                    
                    <div class="space-y-4">
                        <div class="bg-white/5 p-4 rounded-xl border border-white/10 flex justify-between items-center">
                            <div>
                                <p class="text-xs font-medium text-slate-400">Factura #ICA-9823</p>
                                <p class="text-sm font-semibold">Fertilizantes Nitrofoska</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-green-400">$12,400.00</p>
                                <span class="text-[10px] bg-green-500/20 text-green-300 px-2 py-0.5 rounded-md">Pagado</span>
                            </div>
                        </div>

                        <div class="bg-white/5 p-4 rounded-xl border border-white/10 flex justify-between items-center">
                            <div>
                                <p class="text-xs font-medium text-slate-400">Factura #MAQ-0219</p>
                                <p class="text-sm font-semibold">Repuesto Cuchillas Tractor</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-amber-400">$3,150.00</p>
                                <span class="text-[10px] bg-amber-500/20 text-amber-300 px-2 py-0.5 rounded-md">Pendiente</span>
                            </div>
                        </div>
                    </div>

                    <button class="mt-6 w-full py-3 bg-white text-slate-900 rounded-xl font-bold text-sm hover:bg-slate-100 shadow-lg transition-all">
                        ➕ Registrar Nueva Compra
                    </button>
                </div>

                <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Telemetría IoT Estación</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-blue-50/50 p-4 rounded-xl text-center">
                            <span class="text-2xl">🌡️</span>
                            <p class="text-xs text-gray-400 mt-1 uppercase font-semibold">Canopia</p>
                            <p class="text-xl font-bold text-blue-900">28.4 °C</p>
                        </div>
                        <div class="bg-orange-50/50 p-4 rounded-xl text-center">
                            <span class="text-2xl">💧</span>
                            <p class="text-xs text-gray-400 mt-1 uppercase font-semibold">Eficiencia Riego</p>
                            <p class="text-xl font-bold text-orange-900">92.5 %</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
</x-app-layout>