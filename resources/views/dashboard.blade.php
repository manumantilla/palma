<x-app-layout>
    <!-- ESTILOS Y ANIMACIONES AGRÍCOLAS -->
    <style>
        @keyframes float-leaf {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(6deg); }
        }
        @keyframes wind-sway {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(-4deg); }
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.1); }
        }
        @keyframes bar-grow {
            from { transform: scaleY(0); }
            to { transform: scaleY(1); }
        }
        @keyframes wave-flow {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .animate-float-leaf { animation: float-leaf 4s ease-in-out infinite; }
        .animate-wind { animation: wind-sway 5s ease-in-out infinite; transform-origin: bottom center; }
        .animate-pulse-glow { animation: pulse-glow 3.5s ease-in-out infinite; }
        .animate-bar { transform-origin: bottom; animation: bar-grow 1.2s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
        }
    </style>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 relative z-10">
            <!-- Título Principal con animación de brote -->
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl animate-float-leaf shadow-sm">
                    <svg class="w-8 h-8 text-emerald-600 animate-wind" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-emerald-950 tracking-tight flex items-center gap-2">
                        {{ __('🌱 Resumen Agrícola') }}
                    </h2>
                    <p class="text-xs font-medium text-emerald-700/70">Monitoreo inteligente del cultivo en tiempo real</p>
                </div>
            </div>

            <!-- Botones de Acción Rápida Encabezado -->
            <div class="flex flex-wrap gap-2.5">
                <a href="{{ route('cultivos.index') }}" class="group relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-sm font-semibold rounded-xl shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/30 transition-all duration-300 hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Cultivos
                </a>
                <a href="{{ route('lotes-gis.index') }}" class="group relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-sm font-semibold rounded-xl shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/30 transition-all duration-300 hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    Lotes GIS
                </a>
                <a href="{{ route('ordenes_cosecha.index') }}" class="group inline-flex items-center px-4 py-2 bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-500 hover:to-cyan-500 text-white text-sm font-semibold rounded-xl shadow-md shadow-teal-600/20 hover:shadow-lg hover:shadow-teal-600/30 transition-all duration-300 hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Órdenes
                </a>
                <a href="{{ route('eventos_campo.index') }}" class="group inline-flex items-center px-4 py-2 bg-gradient-to-r from-lime-600 to-emerald-600 hover:from-lime-500 hover:to-emerald-500 text-white text-sm font-semibold rounded-xl shadow-md shadow-lime-600/20 hover:shadow-lg hover:shadow-lime-600/30 transition-all duration-300 hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Eventos
                </a>
                <a href="{{ route('eventos_campo.index') }}" class="group inline-flex items-center px-4 py-2 bg-gradient-to-r from-lime-600 to-emerald-600 hover:from-lime-500 hover:to-emerald-500 text-white text-sm font-semibold rounded-xl shadow-md shadow-lime-600/20 hover:shadow-lg hover:shadow-lime-600/30 transition-all duration-300 hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Invernaderos
                </a>
                
            </div>
        </div>
    </x-slot>

    <!-- CONTENEDOR PRINCIPAL FONDO CLARO CON DEGRADADO ORGÁNICO -->
    <div class="py-10 bg-gradient-to-b from-emerald-50/60 via-stone-50 to-emerald-100/30 min-h-screen relative overflow-hidden">
        
        <!-- Elementos Decorativos Agrícolas de Fondo (Hojas Flotantes) -->
        <div class="absolute top-10 left-5 text-emerald-200/40 pointer-events-none animate-float-leaf" style="animation-duration: 7s;">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M17 8C8 10 59 16.17 3.83 12 20c0-4.42 3.58-8 8-8s8 3.58 8 8z"/></svg>
        </div>
        <div class="absolute bottom-20 right-10 text-emerald-300/30 pointer-events-none animate-float-leaf" style="animation-duration: 9s;">
            <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

            <!-- TARJETAS DE METRICAS CLAVE (KPIs) CON ANIMACIÓN Y LUMINOSIDAD -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                <!-- Tarjeta 1: Total Árboles -->
                <a href="{{ route('arboles.index') }}" class="group glass-card rounded-3xl p-6 border border-emerald-200/70 shadow-sm hover:shadow-xl hover:shadow-emerald-500/10 hover:border-emerald-400 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-400/10 rounded-full animate-pulse-glow"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-emerald-800/70">Total Árboles</p>
                            <h3 class="text-3xl font-extrabold text-emerald-950 mt-1">{{ $kpis['total_arboles'] ?? '2,450' }}</h3>
                            <span class="inline-flex items-center text-xs font-semibold text-emerald-600 bg-emerald-100/80 px-2.5 py-0.5 rounded-full mt-2">
                                🌿 Saludable
                            </span>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl text-white shadow-md shadow-emerald-500/20 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-xs font-bold text-emerald-700 group-hover:text-emerald-800">
                        <span>Gestionar árboles</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </div>
                </a>

                <!-- Tarjeta 2: Lotes Activos -->
                <a href="{{ route('lotes-gis.index') }}" class="group glass-card rounded-3xl p-6 border border-amber-200/70 shadow-sm hover:shadow-xl hover:shadow-amber-500/10 hover:border-amber-400 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-amber-400/10 rounded-full animate-pulse-glow"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-amber-800/70">Lotes Activos</p>
                            <h3 class="text-3xl font-extrabold text-stone-900 mt-1">{{ $kpis['lotes_activos'] ?? '12' }}</h3>
                            <span class="inline-flex items-center text-xs font-semibold text-amber-700 bg-amber-100/80 px-2.5 py-0.5 rounded-full mt-2">
                                📍 Mapeados GIS
                            </span>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl text-white shadow-md shadow-amber-500/20 group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-xs font-bold text-amber-700 group-hover:text-amber-800">
                        <span>Ver todos los lotes</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </div>
                </a>

                <!-- Tarjeta 3: Órdenes de Cosecha -->
                <a href="{{ route('ordenes_cosecha.index') }}" class="group glass-card rounded-3xl p-6 border border-teal-200/70 shadow-sm hover:shadow-xl hover:shadow-teal-500/10 hover:border-teal-400 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-teal-400/10 rounded-full animate-pulse-glow"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-teal-800/70">Cosechas Pendientes</p>
                            <h3 class="text-3xl font-extrabold text-stone-900 mt-1">{{ $kpis['ordenes_pendientes'] ?? '3' }}</h3>
                            <span class="inline-flex items-center text-xs font-semibold text-teal-700 bg-teal-100/80 px-2.5 py-0.5 rounded-full mt-2 animate-pulse">
                                🌾 En Producción
                            </span>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-2xl text-white shadow-md shadow-teal-500/20 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-xs font-bold text-teal-700 group-hover:text-teal-800">
                        <span>Ver lista de órdenes</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </div>
                </a>

                <!-- Tarjeta 4: Gastos del Mes -->
                <a href="{{ route('gastos.index') }}" class="group glass-card rounded-3xl p-6 border border-emerald-200/80 shadow-sm hover:shadow-xl hover:shadow-emerald-500/10 hover:border-emerald-400 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-300/10 rounded-full animate-pulse-glow"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-emerald-800/70">Gastos del Mes</p>
                            <h3 class="text-3xl font-extrabold text-stone-900 mt-1">${{ $kpis['gastos_mes'] ?? '4,500' }}</h3>
                            <span class="inline-flex items-center text-xs font-semibold text-emerald-800 bg-emerald-100/90 px-2.5 py-0.5 rounded-full mt-2">
                                📊 Presupuesto Ok
                            </span>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-emerald-600 to-lime-600 rounded-2xl text-white shadow-md shadow-emerald-600/20 group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-xs font-bold text-emerald-700 group-hover:text-emerald-800">
                        <span>Ver balance financiero</span>
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </div>
                </a>

            </div>

            <!-- FILA DE ACCESOS RÁPIDOS MÚLTIPLES -->
            <div class="mb-8">
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-800/60 mb-3 ml-1 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Módulos de Gestión
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    <a href="{{ route('insumos.index') }}" class="group glass-card p-3.5 rounded-2xl border border-emerald-100/80 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-emerald-100 text-emerald-700 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <span class="text-sm font-semibold text-stone-700 group-hover:text-emerald-950">Insumos</span>
                    </a>

                    <a href="{{ route('trabajadores.index') }}" class="group glass-card p-3.5 rounded-2xl border border-emerald-100/80 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-teal-100 text-teal-700 group-hover:bg-teal-500 group-hover:text-white transition-colors duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <span class="text-sm font-semibold text-stone-700 group-hover:text-emerald-950">Personal</span>
                    </a>

                    <a href="{{ route('clientes.index') }}" class="group glass-card p-3.5 rounded-2xl border border-emerald-100/80 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-amber-100 text-amber-700 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="text-sm font-semibold text-stone-700 group-hover:text-emerald-950">Clientes</span>
                    </a>

                    <a href="{{ route('grafo.index') }}" class="group glass-card p-3.5 rounded-2xl border border-emerald-100/80 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-lime-100 text-lime-700 group-hover:bg-lime-500 group-hover:text-white transition-colors duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18"/></svg>
                        </div>
                        <span class="text-sm font-semibold text-stone-700 group-hover:text-emerald-950">Grafo Árboles</span>
                    </a>

                    <a href="{{ route('fenologia-etapa.index') }}" class="group glass-card p-3.5 rounded-2xl border border-emerald-100/80 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-emerald-100 text-emerald-700 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/></svg>
                        </div>
                        <span class="text-sm font-semibold text-stone-700 group-hover:text-emerald-950">Fenología</span>
                    </a>

                    <a href="{{ route('compras.index') }}" class="group glass-card p-3.5 rounded-2xl border border-emerald-100/80 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-cyan-100 text-cyan-700 group-hover:bg-cyan-500 group-hover:text-white transition-colors duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="text-sm font-semibold text-stone-700 group-hover:text-emerald-950">Compras</span>
                    </a>
                </div>
            </div>

            <!-- SECCIÓN DE GRÁFICO Y EVENTOS PROGRAMADOS -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Gráfico de Rendimiento Animado -->
                <div class="lg:col-span-2 glass-card rounded-3xl p-6 border border-emerald-200/80 shadow-sm hover:shadow-lg transition duration-300 relative overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 bg-emerald-500 rounded-full animate-ping"></span>
                                <h3 class="text-lg font-bold text-emerald-950">📈 Estimado de Rendimiento de Cosecha</h3>
                            </div>
                            <span class="text-xs font-semibold text-emerald-700 bg-emerald-100/80 px-3 py-1 rounded-full border border-emerald-200">Últimos 30 días</span>
                        </div>
                        <p class="text-xs text-stone-500 mb-6">Proyección semanal de rendimiento en kg por hectárea según fenología actual.</p>
                    </div>

                    <!-- Representación Visual Animada de Gráfico Agrícola -->
                    <div class="w-full h-64 bg-gradient-to-t from-emerald-100/50 via-emerald-50/20 to-transparent rounded-2xl border border-emerald-100 p-4 relative flex items-end justify-between gap-3 overflow-hidden">
                        
                        <!-- Malla de Fondo -->
                        <div class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:2rem_2rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_50%,#000_70%,transparent_100%)] opacity-30"></div>

                        <!-- Columnas de Rendimiento Animadas -->
                        <div class="w-1/6 flex flex-col items-center gap-2 z-10">
                            <span class="text-[10px] font-bold text-emerald-700">320 kg</span>
                            <div class="w-full bg-gradient-to-t from-emerald-600 to-emerald-400 rounded-t-xl h-24 animate-bar shadow-md"></div>
                            <span class="text-xs font-medium text-stone-500">Sem 1</span>
                        </div>
                        <div class="w-1/6 flex flex-col items-center gap-2 z-10">
                            <span class="text-[10px] font-bold text-emerald-700">450 kg</span>
                            <div class="w-full bg-gradient-to-t from-emerald-600 to-emerald-400 rounded-t-xl h-32 animate-bar shadow-md" style="animation-delay: 0.1s;"></div>
                            <span class="text-xs font-medium text-stone-500">Sem 2</span>
                        </div>
                        <div class="w-1/6 flex flex-col items-center gap-2 z-10">
                            <span class="text-[10px] font-bold text-emerald-700">580 kg</span>
                            <div class="w-full bg-gradient-to-t from-emerald-600 to-teal-400 rounded-t-xl h-44 animate-bar shadow-md" style="animation-delay: 0.2s;"></div>
                            <span class="text-xs font-medium text-stone-500">Sem 3</span>
                        </div>
                        <div class="w-1/6 flex flex-col items-center gap-2 z-10">
                            <span class="text-[10px] font-bold text-emerald-700">710 kg</span>
                            <div class="w-full bg-gradient-to-t from-teal-600 to-emerald-400 rounded-t-xl h-52 animate-bar shadow-md" style="animation-delay: 0.3s;"></div>
                            <span class="text-xs font-medium text-stone-500">Sem 4</span>
                        </div>
                        <div class="w-1/6 flex flex-col items-center gap-2 z-10">
                            <span class="text-[10px] font-bold text-emerald-800">890 kg</span>
                            <div class="w-full bg-gradient-to-t from-emerald-500 to-lime-400 rounded-t-xl h-56 animate-bar shadow-md shadow-lime-500/20" style="animation-delay: 0.4s;"></div>
                            <span class="text-xs font-medium text-stone-500">Sem 5</span>
                        </div>
                    </div>
                </div>

                <!-- Lista de Próximos Eventos de Campo -->
                <div class="glass-card rounded-3xl p-6 border border-emerald-200/80 shadow-sm hover:shadow-lg transition duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-emerald-950 flex items-center gap-2">
                                📅 Próximos Eventos
                            </h3>
                            <span class="p-1.5 bg-emerald-100 rounded-lg text-emerald-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>

                        <ul class="space-y-3">
                            <!-- Evento 1 -->
                            <li class="p-3.5 rounded-2xl bg-white/80 border border-amber-100 hover:border-amber-300 shadow-sm hover:shadow transition-all duration-200 flex justify-between items-center group">
                                <div class="flex items-center gap-3">
                                    <div class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></div>
                                    <div>
                                        <p class="text-sm font-semibold text-stone-800 group-hover:text-amber-900 transition-colors">Fumigación Lote Norte</p>
                                        <p class="text-xs text-stone-500">Mañana, 06:00 AM</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 bg-amber-100/80 text-amber-800 text-[11px] font-bold rounded-lg">Pendiente</span>
                            </li>

                            <!-- Evento 2 -->
                            <li class="p-3.5 rounded-2xl bg-white/80 border border-emerald-100 hover:border-emerald-300 shadow-sm hover:shadow transition-all duration-200 flex justify-between items-center group">
                                <div class="flex items-center gap-3">
                                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                                    <div>
                                        <p class="text-sm font-semibold text-stone-800 group-hover:text-emerald-950 transition-colors">Poda de Mantenimiento</p>
                                        <p class="text-xs text-stone-500">Jueves, 08:00 AM</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 bg-emerald-100/80 text-emerald-800 text-[11px] font-bold rounded-lg">Programado</span>
                            </li>

                            <!-- Evento 3 -->
                            <li class="p-3.5 rounded-2xl bg-white/80 border border-emerald-100 hover:border-emerald-300 shadow-sm hover:shadow transition-all duration-200 flex justify-between items-center group">
                                <div class="flex items-center gap-3">
                                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                                    <div>
                                        <p class="text-sm font-semibold text-stone-800 group-hover:text-emerald-950 transition-colors">Muestreo de Suelo</p>
                                        <p class="text-xs text-stone-500">Viernes, 10:00 AM</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 bg-emerald-100/80 text-emerald-800 text-[11px] font-bold rounded-lg">Programado</span>
                            </li>

                            <!-- Evento 4 -->
                            <li class="p-3.5 rounded-2xl bg-white/80 border border-teal-100 hover:border-teal-300 shadow-sm hover:shadow transition-all duration-200 flex justify-between items-center group">
                                <div class="flex items-center gap-3">
                                    <div class="w-2.5 h-2.5 rounded-full bg-teal-500 animate-ping"></div>
                                    <div>
                                        <p class="text-sm font-semibold text-stone-800 group-hover:text-teal-950 transition-colors">Revisión del Sistema de Riego</p>
                                        <p class="text-xs text-stone-500">Sábado, 07:30 AM</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 bg-teal-100/80 text-teal-800 text-[11px] font-bold rounded-lg">Nuevo</span>
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('eventos_campo.index') }}" class="mt-4 w-full inline-flex justify-center items-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold py-3 px-4 rounded-2xl transition duration-300 shadow-md shadow-emerald-500/20 hover:shadow-lg">
                        <span>Ver Calendario Completo</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

            </div>

            <!-- PIE DE PÁGINA AMBIENTAL -->
            <div class="mt-12 text-center text-xs font-semibold text-emerald-800/60 flex items-center justify-center gap-2">
                <span class="inline-block animate-float-leaf">🌾</span>
                <p>Panel de control agrícola — Sincronización continua de datos del campo</p>
            </div>

        </div>
    </div>
</x-app-layout>