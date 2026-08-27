<x-app-layout>
    <x-slot name="header">
        <style>
            @keyframes fade-in-up {
                0% { opacity: 0; transform: translateY(20px); }
                100% { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in-up {
                animation: fade-in-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                opacity: 0;
            }
            .delay-100 { animation-delay: 100ms; }
            .delay-200 { animation-delay: 200ms; }
            .delay-300 { animation-delay: 300ms; }
            .delay-400 { animation-delay: 400ms; }
            .delay-500 { animation-delay: 500ms; }
            
            /* Mejoras responsive */
            @media (max-width: 1024px) {
                .lg\:sticky {
                    position: relative !important;
                }
                .lg\:h-\[calc\(100vh-6rem\)\] {
                    height: auto !important;
                }
            }
            
            @media (max-width: 640px) {
                .grid-cols-2 {
                    grid-template-columns: 1fr 1fr !important;
                }
                .gap-4 {
                    gap: 0.75rem !important;
                }
                .p-8 {
                    padding: 1rem !important;
                }
                .lg\:p-12 {
                    padding: 1rem !important;
                }
                .px-6 {
                    padding-left: 0.75rem !important;
                    padding-right: 0.75rem !important;
                }
                .py-4 {
                    padding-top: 0.5rem !important;
                    padding-bottom: 0.5rem !important;
                }
                .text-2xl {
                    font-size: 1.25rem !important;
                }
                .text-3xl {
                    font-size: 1.5rem !important;
                }
                .rounded-\[2rem\] {
                    border-radius: 1rem !important;
                }
                .w-16 {
                    width: 3rem !important;
                    height: 3rem !important;
                }
                .h-16 {
                    height: 3rem !important;
                }
            }
            
            @media (max-width: 480px) {
                .grid-cols-2 {
                    grid-template-columns: 1fr !important;
                }
                .flex-wrap {
                    flex-direction: column !important;
                    align-items: stretch !important;
                }
                .flex-wrap .gap-4 {
                    gap: 0.5rem !important;
                }
                .text-sm {
                    font-size: 0.75rem !important;
                }
                .text-xs {
                    font-size: 0.6rem !important;
                }
            }
        </style>
    </x-slot>

    <div class="py-6 sm:py-8 md:py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-2xl sm:rounded-[2rem] shadow-2xl overflow-hidden flex flex-col lg:flex-row relative">
                
                <!-- Barra lateral izquierda - Resumen del árbol (Responsive) -->
                <div class="lg:w-2/5 w-full bg-gradient-to-br from-emerald-600 to-green-900 p-6 sm:p-8 lg:p-12 text-white relative overflow-hidden lg:sticky lg:top-0 lg:h-[calc(100vh-6rem)]">
                    <div class="absolute -top-24 -left-24 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-emerald-400 opacity-20 rounded-full blur-3xl"></div>

                    <div class="relative z-10 animate-fade-in-up">
                        <!-- Cabecera responsive -->
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div class="flex items-center sm:block gap-4">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-2 sm:mb-6 border border-white/30 shadow-lg flex-shrink-0">
                                    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-1 sm:mb-2 break-all">{{ $arbol->codigo_unico ?? 'Sin Código' }}</h2>
                                    <p class="text-emerald-100 text-xs sm:text-sm font-light">Ficha detallada del árbol</p>
                                </div>
                            </div>
                            <!-- Badge de estado vital -->
                            <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg border border-white/20 {{ $arbol->estado_vital_badge_class ?? 'bg-white/20 text-white' }} self-start sm:self-auto">
                                {{ $arbol->estado_vital_badge }}
                            </span>
                        </div>

                        <!-- Información responsive -->
                        <div class="mt-6 sm:mt-8 space-y-3 sm:space-y-4 text-sm">
                            <div class="flex items-center gap-3 border-b border-white/10 pb-3">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <div class="min-w-0">
                                    <p class="text-emerald-200 text-xs uppercase tracking-wider">Lote GIS</p>
                                    <p class="font-semibold truncate">{{ $arbol->lote->nombre_lote ?? 'No asignado' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 border-b border-white/10 pb-3">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <div class="min-w-0">
                                    <p class="text-emerald-200 text-xs uppercase tracking-wider">Zona de Manejo</p>
                                    <p class="font-semibold truncate">{{ $arbol->zonaManejo->nombre_zona ?? 'No asignada' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 border-b border-white/10 pb-3">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                                <div>
                                    <p class="text-emerald-200 text-xs uppercase tracking-wider">Topología</p>
                                    <p class="font-semibold text-xs sm:text-sm">Fila {{ $arbol->fila_indice ?? '-' }} / Pos. {{ $arbol->posicion_indice ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div class="min-w-0">
                                    <p class="text-emerald-200 text-xs uppercase tracking-wider">Ciclo Productivo</p>
                                    @if($arbol->ciclo_productivo_id)
                                        <a href="{{ route('ciclos-productivos.show', $arbol->ciclo_productivo_id) }}" class="font-semibold text-white hover:underline flex items-center gap-1 text-xs sm:text-sm">
                                            <span class="truncate">{{ $arbol->cicloProductivo->nombre_campana ?? 'Ver Ciclo' }}</span>
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @else
                                        <span class="text-emerald-200 italic">Ninguno</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Info extra responsive -->
                        <div class="mt-6 grid grid-cols-2 gap-2 sm:gap-3 p-3 sm:p-4 bg-black/10 backdrop-blur-sm rounded-xl border border-white/10">
                            <div>
                                <p class="text-emerald-200 text-[10px] sm:text-xs uppercase tracking-wider">Variedad</p>
                                <p class="font-semibold text-xs sm:text-sm truncate">{{ $arbol->variedad ?? 'Desconocida' }}</p>
                            </div>
                            <div>
                                <p class="text-emerald-200 text-[10px] sm:text-xs uppercase tracking-wider">Edad</p>
                                <p class="font-semibold text-xs sm:text-sm">{{ $arbol->edad_meses ?? 0 }} <span class="text-[10px] sm:text-xs font-normal">meses</span></p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-emerald-200 text-[10px] sm:text-xs uppercase tracking-wider">Etapa Biológica</p>
                                <p class="font-semibold text-xs sm:text-sm capitalize truncate">{{ $arbol->etapa_biologica ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Producción histórica responsive -->
                    <div class="relative z-10 mt-6 sm:mt-8 hidden md:block animate-fade-in-up delay-200">
                        <div class="p-3 sm:p-4 bg-black/10 backdrop-blur-md rounded-2xl border border-white/10">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-emerald-500 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-semibold">Producción Histórica</p>
                                    <p class="text-base sm:text-lg font-bold truncate">{{ number_format($arbol->produccion_acumulada_kg ?? 0, 2) }} Kg</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contenido principal - lado derecho (Responsive) -->
                <div class="lg:w-3/5 w-full p-4 sm:p-6 md:p-8 lg:p-12 bg-white relative">
                    
                    <!-- Cabecera con botón volver (Responsive) -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 mb-6 sm:mb-8 animate-fade-in-up">
                        <h3 class="text-xl sm:text-2xl font-bold text-gray-800 flex items-center gap-2">
                            <span class="bg-emerald-100 text-emerald-700 p-1.5 sm:p-2 rounded-xl">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </span>
                            <span class="text-lg sm:text-2xl">Detalles del Árbol</span>
                        </h3>
                        <a href="{{ route('arboles.index') }}" class="inline-flex items-center gap-2 px-3 sm:px-5 py-2 sm:py-2.5 bg-white border border-emerald-200 rounded-lg text-emerald-700 hover:bg-emerald-50 hover:border-emerald-300 transition-all duration-200 font-medium text-xs sm:text-sm shadow-sm hover:shadow w-full sm:w-auto justify-center sm:justify-start">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span class="whitespace-nowrap">Volver al Inventario</span>
                        </a>
                    </div>

                    <!-- Métricas rápidas (resumen) Responsive -->
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-2 sm:gap-3 md:gap-4 mb-6 sm:mb-8 animate-fade-in-up delay-100">
                        <div class="bg-emerald-50/50 p-2 sm:p-3 md:p-4 rounded-xl border border-emerald-100 flex items-center gap-2 sm:gap-3">
                            <div class="p-1.5 sm:p-2 bg-emerald-100 rounded-lg text-emerald-700 flex-shrink-0">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] sm:text-xs text-stone-500 font-medium uppercase">Edad</p>
                                <p class="text-base sm:text-lg font-bold text-stone-800 truncate">{{ $arbol->edad_meses ?? 0 }} <span class="text-[10px] sm:text-sm font-normal text-stone-500">meses</span></p>
                            </div>
                        </div>
                        <div class="bg-amber-50/50 p-2 sm:p-3 md:p-4 rounded-xl border border-amber-100 flex items-center gap-2 sm:gap-3">
                            <div class="p-1.5 sm:p-2 bg-amber-100 rounded-lg text-amber-700 flex-shrink-0">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] sm:text-xs text-stone-500 font-medium uppercase">Producción</p>
                                <p class="text-base sm:text-lg font-bold text-stone-800 truncate">{{ number_format($arbol->produccion_acumulada_kg ?? 0, 2) }} <span class="text-[10px] sm:text-sm font-normal text-stone-500">kg</span></p>
                            </div>
                        </div>
                        <div class="bg-sky-50/50 p-2 sm:p-3 md:p-4 rounded-xl border border-sky-100 flex items-center gap-2 sm:gap-3">
                            <div class="p-1.5 sm:p-2 bg-sky-100 rounded-lg text-sky-700 flex-shrink-0">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] sm:text-xs text-stone-500 font-medium uppercase">Estado Vital</p>
                                <p class="text-base sm:text-lg font-bold text-stone-800 truncate">{{ $arbol->estado_vital_badge ?? 'N/D' }}</p>
                            </div>
                        </div>
                        <div class="bg-purple-50/50 p-2 sm:p-3 md:p-4 rounded-xl border border-purple-100 flex items-center gap-2 sm:gap-3">
                            <div class="p-1.5 sm:p-2 bg-purple-100 rounded-lg text-purple-700 flex-shrink-0">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] sm:text-xs text-stone-500 font-medium uppercase">Ciclo</p>
                                <p class="text-base sm:text-lg font-bold text-stone-800 truncate max-w-[60px] sm:max-w-[80px] md:max-w-[100px]">{{ $arbol->cicloProductivo->nombre_campana ?? 'Sin ciclo' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Historial de Métricas (Responsive) -->
                    <div class="mb-6 sm:mb-8 animate-fade-in-up delay-200">
                        <div class="bg-white rounded-xl sm:rounded-2xl shadow-md border border-emerald-100/80 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="bg-gradient-to-r from-emerald-50 to-emerald-100/50 px-4 sm:px-6 py-3 sm:py-4 border-b border-emerald-200/50 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    <h3 class="font-bold text-emerald-800 text-sm sm:text-base">Métricas Históricas</h3>
                                </div>
                                <span class="text-[10px] sm:text-xs bg-emerald-200 text-emerald-800 px-2 sm:px-3 py-0.5 sm:py-1 rounded-full font-medium">{{ $arbol->metricasHistoricas->count() }}</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-stone-200">
                                    <thead class="bg-stone-50/80">
                                        <tr>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-[10px] sm:text-xs font-medium text-stone-500 uppercase tracking-wider">Fecha</th>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-[10px] sm:text-xs font-medium text-stone-500 uppercase tracking-wider">Métrica</th>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-[10px] sm:text-xs font-medium text-stone-500 uppercase tracking-wider">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-stone-200 bg-white">
                                        @forelse($arbol->metricasHistoricas as $metrica)
                                            <tr class="hover:bg-emerald-50/50 transition-colors duration-150 even:bg-stone-50/30">
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm text-stone-800">{{ $metrica->fecha_medicion->format('d/m/Y') }}</td>
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm text-stone-600">{{ $metrica->tipo_metrica }}</td>
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm font-medium text-emerald-700">{{ $metrica->valor }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-3 sm:px-6 py-6 sm:py-10 text-center">
                                                    <div class="flex flex-col items-center text-stone-400">
                                                        <svg class="w-6 h-6 sm:w-8 sm:h-8 mb-1 sm:mb-2 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <span class="text-xs sm:text-sm">No hay métricas registradas.</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Historial Fitosanitario (Responsive) -->
                    <div class="animate-fade-in-up delay-300">
                        <div class="bg-white rounded-xl sm:rounded-2xl shadow-md border border-amber-100/80 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="bg-gradient-to-r from-amber-50 to-amber-100/50 px-4 sm:px-6 py-3 sm:py-4 border-b border-amber-200/50 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-amber-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <h3 class="font-bold text-amber-800 text-sm sm:text-base">Historial Fitosanitario</h3>
                                </div>
                                <span class="text-[10px] sm:text-xs bg-amber-200 text-amber-800 px-2 sm:px-3 py-0.5 sm:py-1 rounded-full font-medium">{{ $arbol->historialFitosanitario->count() }}</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-stone-200">
                                    <thead class="bg-stone-50/80">
                                        <tr>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-[10px] sm:text-xs font-medium text-stone-500 uppercase tracking-wider">Fecha</th>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-[10px] sm:text-xs font-medium text-stone-500 uppercase tracking-wider">Agente</th>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-[10px] sm:text-xs font-medium text-stone-500 uppercase tracking-wider">Severidad</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-stone-200 bg-white">
                                        @forelse($arbol->historialFitosanitario as $registro)
                                            <tr class="hover:bg-amber-50/50 transition-colors duration-150 even:bg-stone-50/30">
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm text-stone-800">{{ $registro->fecha_hallazgo->format('d/m/Y') }}</td>
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm text-stone-600">{{ $registro->agente_causal }}</td>
                                                <td class="px-3 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm">
                                                    @php
                                                        $severityColors = [
                                                            'alta' => 'bg-red-100 text-red-800 border-red-200',
                                                            'media' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                            'baja' => 'bg-green-100 text-green-800 border-green-200',
                                                        ];
                                                        $class = $severityColors[$registro->severidad] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                                    @endphp
                                                    <span class="inline-flex items-center px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold uppercase border {{ $class }}">
                                                        {{ $registro->severidad }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-3 sm:px-6 py-6 sm:py-10 text-center">
                                                    <div class="flex flex-col items-center text-stone-400">
                                                        <svg class="w-6 h-6 sm:w-8 sm:h-8 mb-1 sm:mb-2 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                        <span class="text-xs sm:text-sm">Árbol sano. Sin reportes fitosanitarios.</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>