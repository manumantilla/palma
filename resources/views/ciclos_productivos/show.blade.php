<x-app-layout>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 bg-stone-50/50 min-h-screen">

        <div class="bg-gradient-to-r from-emerald-800 to-green-700 rounded-3xl shadow-xl overflow-hidden mb-8 relative border-b-4 border-amber-500">
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
            
            <div class="p-6 sm:p-8 relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold text-amber-300 uppercase tracking-widest">
                            Campañaa: {{ $cicloProductivo->nombre_campana ?? 'Sin Nombre' }}
                        </span>
                        <span class="text-xs text-stone-200 font-medium">| ID #{{ $cicloProductivo->id }}</span>
                    </div>
                    <h1 class="text-3xl font-black text-white tracking-tight mt-2 flex items-center gap-2">
                        🌱 {{ $cicloProductivo->cultivo->nombre ?? 'Cultivo' }} 
                        <span class="text-emerald-200 font-light text-xl">en {{ $cicloProductivo->lote->nombre_lote ?? 'Lote N/A' }}</span>
                    </h1>
                    <p class="text-stone-100 text-sm mt-1 max-w-xl">
                        Manejo técnico agronómico de precisión, densidades y costeo operativo en tiempo real.
                    </p>
                </div>
                
                <div class="flex flex-col items-end gap-2">
                    <span class="inline-flex items-center px-4 py-2 rounded-2xl text-sm font-black uppercase tracking-wider bg-white text-emerald-900 shadow-md border border-emerald-100">
                        {{ $cicloProductivo->estado_badge_texto }}
                    </span>
                    <div class="text-xs text-emerald-100 bg-emerald-900/30 px-3 py-1 rounded-full border border-white/10">
                        🗓️ {{ $cicloProductivo->dias_en_campo }} Días en Campo
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <div class="lg:col-span-2 space-y-8">
                
                <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 overflow-hidden relative">
                    <div class="absolute top-0 right-0 p-4 opacity-5 text-stone-900 font-black text-7xl select-none pointer-events-none">📐</div>
                    <h3 class="text-sm font-bold text-stone-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        📐 Topografía y Marco de Siembra
                    </h3>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                        <div class="bg-stone-50 p-4 rounded-xl border border-stone-100">
                            <span class="text-xs font-semibold text-stone-500 block">Modalidad</span>
                            <span class="text-base font-bold text-stone-800 capitalize mt-1 block">
                                {{ str_replace('_', ' ', $cicloProductivo->modalidad_siembra ?? 'No definida') }}
                            </span>
                        </div>
                        <div class="bg-stone-50 p-4 rounded-xl border border-stone-100">
                            <span class="text-xs font-semibold text-stone-500 block">Entre Hileras</span>
                            <span class="text-lg font-black text-emerald-700 mt-1 block">
                                {{ $cicloProductivo->distancia_entre_hileras_metros }} <span class="text-xs font-normal text-stone-500">mts</span>
                            </span>
                        </div>
                        <div class="bg-stone-50 p-4 rounded-xl border border-stone-100">
                            <span class="text-xs font-semibold text-stone-500 block">Entre Plantas</span>
                            <span class="text-lg font-black text-emerald-700 mt-1 block">
                                {{ $cicloProductivo->distancia_entre_plantas_metros }} <span class="text-xs font-normal text-stone-500">mts</span>
                            </span>
                        </div>
                        
                        <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100 ring-2 ring-emerald-600/10">
                            <span class="text-xs font-bold text-emerald-800 block">Densidad Real</span>
                            <span class="text-xl font-black text-emerald-900 mt-1 block">
                                {{ number_format($cicloProductivo->plantas_por_hectarea_real) }}
                                <span class="text-xs font-medium text-emerald-700 block">Plantas / Ha</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 relative">
                    <h3 class="text-sm font-bold text-stone-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                        ⏱️ Cronograma Fases de Desarrollo (Fenología)
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
                        <div class="hidden md:block absolute top-1/2 left-4 right-4 h-0.5 bg-stone-100 -translate-y-4 z-0"></div>
                        
                        <div class="relative z-10 bg-stone-50/60 p-4 rounded-xl border border-stone-200">
                            <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm mb-3 shadow-sm shadow-emerald-600/20">1</div>
                            <span class="text-xs font-bold text-stone-500 block uppercase tracking-wide">Inicio del Ciclo</span>
                            <span class="text-sm font-black text-stone-800 mt-1 block">
                                {{ $cicloProductivo->fecha_inicio ? $cicloProductivo->fecha_inicio->format('d/m/Y') : 'Sin registrar' }}
                            </span>
                        </div>

                        <div class="relative z-10 bg-amber-50/40 p-4 rounded-xl border border-amber-200/60">
                            <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold text-sm mb-3 shadow-sm">2</div>
                            <span class="text-xs font-bold text-amber-800 block uppercase tracking-wide">Ventana Est. Cosecha</span>
                            <span class="text-sm font-bold text-stone-800 mt-1 block">
                                {{ $cicloProductivo->fecha_estimada_cosecha ? $cicloProductivo->fecha_estimada_cosecha->format('d/m/Y') : 'N/A' }} 
                                <span class="text-stone-400 font-normal">al</span> 
                                {{ $cicloProductivo->fecha_estimada_fin_cosecha ? $cicloProductivo->fecha_estimada_fin_cosecha->format('d/m/Y') : 'N/A' }}
                            </span>
                        </div>

                        <div class="relative z-10 bg-stone-50/60 p-4 rounded-xl border border-stone-200">
                            <div class="w-8 h-8 rounded-full bg-stone-700 text-white flex items-center justify-center font-bold text-sm mb-3 shadow-sm">3</div>
                            <span class="text-xs font-bold text-stone-500 block uppercase tracking-wide">Corte Real & Cierre</span>
                            <span class="text-sm font-semibold text-stone-800 mt-1 block">
                                @if($cicloProductivo->fecha_real_inicio_cosecha)
                                    Corte: <strong class="text-stone-900">{{ $cicloProductivo->fecha_real_inicio_cosecha->format('d/m/Y') }}</strong>
                                @else
                                    <span class="text-stone-400 italic text-xs block">Cosecha no iniciada</span>
                                @endif
                                @if($cicloProductivo->fecha_finalizacion_ciclo)
                                    <span class="text-xs text-emerald-700 block mt-1 font-medium">Finalizado: {{ $cicloProductivo->fecha_finalizacion_ciclo->format('d/m/Y') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6">
                    <h3 class="text-sm font-bold text-stone-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        📜 Pasaporte Sanitario y Trazabilidad de Vivero
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="border-b border-stone-100 sm:border-b-0 sm:border-r border-stone-200 pb-3 sm:pb-0 sm:pr-4">
                            <p class="text-xs text-stone-400 font-semibold uppercase">Proveedor de Material Vegetal</p>
                            <p class="font-bold text-stone-800 mt-0.5">{{ $cicloProductivo->proveedorMaterial->nombre ?? 'Material Propio / Semillero Local' }}</p>
                            
                            <p class="text-xs text-stone-400 font-semibold uppercase mt-3">Código de Lote Origen</p>
                            <p class="font-mono text-xs text-stone-700 bg-stone-50 p-1.5 rounded border border-stone-200 mt-0.5 inline-block">
                                {{ $cicloProductivo->codigo_lote_vivero_origen ?? 'SIN_LOTE_VIVERO' }}
                            </p>
                        </div>
                        <div class="sm:pl-4">
                            <p class="text-xs text-stone-400 font-semibold uppercase">Permiso / Registro Institucional (ICA)</p>
                            <p class="font-medium text-stone-800 mt-0.5">{{ $cicloProductivo->registro_autorizacion_institucional ?? 'En trámite de inspección' }}</p>
                            
                            <div class="mt-4 flex items-center gap-2">
                                @if($cicloProductivo->es_organico_certified)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-300">
                                        🍃 Orgánico Certificado (GlobalGAP)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-stone-100 text-stone-500 border border-stone-200">
                                        🚜 Manejo Convencional / Técnico
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="space-y-8">
                
                <div class="bg-gradient-to-b from-stone-900 to-stone-800 rounded-2xl shadow-lg border border-stone-950 p-6 text-white relative overflow-hidden">
                    <div class="absolute -bottom-6 -right-6 text-white/5 text-8xl font-bold select-none pointer-events-none">💰</div>
                    
                    <h3 class="text-xs font-bold text-stone-400 uppercase tracking-widest border-b border-stone-700/60 pb-3 mb-4 flex items-center gap-2">
                        📊 Costo Acumulado Invertido (OPEX)
                    </h3>
                    
                    <div class="mb-6">
                        <span class="text-xs text-stone-400 block">Inversión Consolidada del Ciclo</span>
                        <span class="text-3xl font-black tracking-tight text-amber-400">
                            ${{ number_format($cicloProductivo->costo_total_invertido, 2) }} <span class="text-xs text-stone-300">COP</span>
                        </span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between bg-stone-800/60 p-2.5 rounded-lg border border-stone-700/50">
                            <span class="text-stone-300">Costos Directos (Insumos/Jornal):</span>
                            <span class="font-bold text-stone-100">${{ number_format($cicloProductivo->costo_acumulado_directo, 2) }}</span>
                        </div>
                        <div class="flex justify-between bg-stone-800/60 p-2.5 rounded-lg border border-stone-700/50">
                            <span class="text-stone-300">Costos Indirectos (Riego/Admin):</span>
                            <span class="font-bold text-stone-100">${{ number_format($cicloProductivo->costo_acumulado_indirecto, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-stone-700/60 text-center">
                        <p class="text-[10px] text-stone-400 italic">Los costos incrementales se liquidan automáticamente al cerrar las Órdenes de Campo vinculadas.</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-xl shadow-inner">
                        👨‍🌾
                    </div>
                    <div>
                        <span class="text-xs font-bold text-stone-400 uppercase tracking-wider block">Agrónomo Responsable</span>
                        <span class="font-black text-stone-800 text-base block">
                            {{ $cicloProductivo->agronomo->name ?? 'Ingeniero No Asignado' }}
                        </span>
                        <span class="text-xs text-emerald-600 font-semibold mt-0.5 inline-block bg-emerald-50 px-2 py-0.5 rounded">
                            Matrícula Profesional Verificada
                        </span>
                    </div>
                </div>

                <div class="bg-stone-100/80 border border-stone-200 rounded-2xl p-4 space-y-3">
                                  <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('ciclos-productivos.index') }}" class="px-3 py-2 border border-stone-300 bg-white text-stone-700 hover:bg-stone-50 rounded-xl text-center font-semibold text-xs transition-colors shadow-sm">
                            ← Volver
                        </a>
                    <a href="{{ route('ordenes_cosecha.create', ['ciclo' => $cicloProductivo->id ]) }}" class="px-3 py-2 border border-stone-300 bg-white text-stone-700 hover:bg-stone-50 rounded-xl text-center font-semibold text-xs transition-colors shadow-sm">
                        Crear Nueva Orden de Cosecha
                    </a>
                        <button type="button" class="px-3 py-2 border border-stone-300 bg-white text-amber-700 hover:bg-amber-50 rounded-xl text-center font-bold text-xs transition-colors shadow-sm">
                            📝 Editar Ciclo
                        </button>
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-app-layout>