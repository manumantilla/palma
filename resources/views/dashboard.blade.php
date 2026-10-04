<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-2xl text-emerald-800 leading-tight">
                {{ __('🌱 Resumen Agrícola') }}
            </h2>
            <div class="mt-2 sm:mt-0 flex flex-wrap gap-2">
                <a href="{{ route('cultivos.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Cultivos
                </a>
                <a href="{{ route('lotes-gis.index') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg shadow-sm transition duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    Lotes GIS
                </a>
                <a href="{{ route('ordenes_cosecha.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Órdenes
                </a>
                <a href="{{ route('eventos_campo.index') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg shadow-sm transition duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Eventos
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-stone-50 to-emerald-50/30 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- TARJETAS DE KPIs (Métricas Clave) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                <!-- Tarjeta 1: Árboles -->
                <a href="{{ route('arboles.index') }}" class="group block bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-emerald-100 hover:border-emerald-300 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-emerald-100 text-emerald-600 mr-4 group-hover:bg-emerald-200 transition">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-stone-500">Total Árboles</p>
                            <p class="text-2xl font-bold text-stone-800">{{ $kpis['total_arboles'] ?? '2,450' }}</p>
                        </div>
                    </div>
                    <div class="mt-3 text-xs font-medium text-emerald-600 opacity-0 group-hover:opacity-100 transition-opacity">
                        Gestionar árboles →
                    </div>
                </a>

                <!-- Tarjeta 2: Lotes -->
                <a href="{{ route('lotes-gis.index') }}" class="group block bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-amber-100 hover:border-amber-300 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-amber-100 text-amber-600 mr-4 group-hover:bg-amber-200 transition">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-stone-500">Lotes Activos</p>
                            <p class="text-2xl font-bold text-stone-800">{{ $kpis['lotes_activos'] ?? '12' }}</p>
                        </div>
                    </div>
                    <div class="mt-3 text-xs font-medium text-amber-600 opacity-0 group-hover:opacity-100 transition-opacity">
                        Ver lotes →
                    </div>
                </a>

                <!-- Tarjeta 3: Órdenes de Cosecha -->
                <a href="{{ route('ordenes_cosecha.index') }}" class="group block bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-blue-100 hover:border-blue-300 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4 group-hover:bg-blue-200 transition">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-stone-500">Cosechas Pendientes</p>
                            <p class="text-2xl font-bold text-stone-800">{{ $kpis['ordenes_pendientes'] ?? '3' }}</p>
                        </div>
                    </div>
                    <div class="mt-3 text-xs font-medium text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">
                        Ver órdenes →
                    </div>
                </a>

                <!-- Tarjeta 4: Gastos -->
                <a href="{{ route('gastos.index') }}" class="group block bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-red-100 hover:border-red-300 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4 group-hover:bg-red-200 transition">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-stone-500">Gastos del Mes</p>
                            <p class="text-2xl font-bold text-stone-800">${{ $kpis['gastos_mes'] ?? '4,500' }}</p>
                        </div>
                    </div>
                    <div class="mt-3 text-xs font-medium text-red-600 opacity-0 group-hover:opacity-100 transition-opacity">
                        Ver detalle →
                    </div>
                </a>
            </div>

            <!-- FILA DE ACCESO RÁPIDO ADICIONAL -->
            <div class="flex flex-wrap gap-3 mb-8">
                <a href="{{ route('insumos.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-stone-200 rounded-xl shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 text-sm font-medium text-stone-700">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Insumos
                </a>
                <a href="{{ route('trabajadores.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-stone-200 rounded-xl shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 text-sm font-medium text-stone-700">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Trabajadores
                </a>
                <a href="{{ route('clientes.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-stone-200 rounded-xl shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 text-sm font-medium text-stone-700">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Clientes
                </a>
                <a href="{{ route('grafo.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-stone-200 rounded-xl shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 text-sm font-medium text-stone-700">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Grafo de Árboles
                </a>
                <a href="{{ route('fenologia-etapa.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-stone-200 rounded-xl shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 text-sm font-medium text-stone-700">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Fenologia y Etapas
                </a>
            </div>

            <!-- ZONA DE GRÁFICOS Y EVENTOS -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Gráfico Principal (2 columnas) -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-md border border-emerald-100 p-6 hover:shadow-lg transition">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-emerald-800">📈 Rendimiento de Cosecha (Estimado)</h3>
                        <span class="text-xs text-stone-400 bg-stone-100 px-3 py-1 rounded-full">Últimos 30 días</span>
                    </div>
                    <div class="w-full h-72 bg-gradient-to-br from-stone-50 to-emerald-50/50 rounded-xl border border-dashed border-stone-300 flex items-center justify-center relative">
                        <!-- Espacio para Chart.js o ApexCharts -->
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <p class="text-stone-400 font-medium mt-2">Gráfico de líneas (próximamente)</p>
                        </div>
                    </div>
                </div>

                <!-- Lista de Eventos (1 columna) -->
                <div class="bg-white rounded-2xl shadow-md border border-emerald-100 p-6 hover:shadow-lg transition">
                    <h3 class="text-lg font-semibold text-emerald-800 mb-4">📅 Próximos Eventos</h3>

                    <ul class="divide-y divide-stone-100">
                        <li class="py-3 flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-stone-800">Fumigación Lote Norte</p>
                                <p class="text-xs text-stone-500">Mañana, 06:00 AM</p>
                            </div>
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-xs rounded-full font-medium">Pendiente</span>
                        </li>
                        <li class="py-3 flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-stone-800">Poda de Mantenimiento</p>
                                <p class="text-xs text-stone-500">Jueves, 08:00 AM</p>
                            </div>
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full font-medium">Programado</span>
                        </li>
                        <li class="py-3 flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-stone-800">Muestreo de Suelo</p>
                                <p class="text-xs text-stone-500">Viernes, 10:00 AM</p>
                            </div>
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full font-medium">Programado</span>
                        </li>
                        <li class="py-3 flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-stone-800">Revisión de Riego</p>
                                <p class="text-xs text-stone-500">Sábado, 07:30 AM</p>
                            </div>
                            <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">Nuevo</span>
                        </li>
                    </ul>

                    <a href="{{ route('eventos_campo.index') }}" class="mt-4 w-full inline-flex justify-center items-center gap-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold py-2.5 px-4 rounded-xl transition border border-emerald-200">
                        <span>Ver Calendario Completo</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- PIE DE PÁGINA (opcional) -->
            <div class="mt-8 text-center text-xs text-stone-400">
                <p>🌾 Panel de control agrícola — Datos actualizados al día de hoy</p>
            </div>

        </div>
    </div>
</x-app-layout>