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
        </style>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl w-full mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-[2rem] shadow-2xl overflow-hidden flex flex-col lg:flex-row relative items-start">
                
                <div class="lg:w-2/5 w-full bg-gradient-to-br from-emerald-600 to-green-900 p-12 text-white relative overflow-hidden lg:sticky lg:top-0 h-auto lg:h-[calc(100vh-6rem)] flex flex-col justify-between">
                    <div class="absolute -top-24 -left-24 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-emerald-400 opacity-20 rounded-full blur-3xl"></div>

                    <div class="relative z-10 animate-fade-in-up">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-6 border border-white/30 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-4xl font-extrabold tracking-tight mb-4">Bio-Registro Activo 🌴</h2>
                        <p class="text-emerald-100 text-lg leading-relaxed font-light">
                            Digitaliza la vida de un nuevo árbol. Al registrar sus datos agronómicos y coordenadas GPS, fortaleces el gemelo digital de tu lote.
                        </p>
                    </div>

                    <div class="relative z-10 mt-12 hidden lg:block animate-fade-in-up delay-200">
                        <div class="p-6 bg-black/10 backdrop-blur-md rounded-2xl border border-white/10">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold">PostGIS Habilitado</p>
                                    <p class="text-xs text-emerald-200">Geometría espacial SRID 4326</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:w-3/5 w-full p-8 lg:p-12 bg-white relative">
                    
                    @if($errors->has('error_database'))
                        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl flex items-start animate-fade-in-up">
                            <svg class="w-5 h-5 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div>
                                <p class="text-sm font-bold">Error del Sistema</p>
                                <p class="text-xs">{{ $errors->first('error_database') }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-8 animate-fade-in-up">
                        <h3 class="text-2xl font-bold text-gray-800">Datos del Arbol 🌱</h3>
                        <a href="{{ route('arboles.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-500 transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Cancelar
                        </a>
                    </div>

                    <form action="{{ route('arboles.store') }}" method="POST" class="space-y-8">
                        @csrf

                        <div class="animate-fade-in-up delay-100">
                            <h4 class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-4 border-b pb-2">1. Identificación y Ubicación Organizacional</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                
                                <div class="relative group">
                                    <input type="text" name="codigo_unico" id="codigo_unico" required
                                        class="peer w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all placeholder-transparent"
                                        placeholder="Código Único" value="{{ old('codigo_unico') }}">
                                    <label class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm transition-all pointer-events-none peer-focus:-top-2 peer-focus:left-3 peer-focus:text-xs peer-focus:text-emerald-600 peer-focus:bg-white peer-focus:px-1 peer-valid:-top-2 peer-valid:left-3 peer-valid:text-xs peer-valid:bg-white peer-valid:px-1 {{ old('codigo_unico') ? '-top-2 left-3 text-xs bg-white px-1' : '' }}">
                                        Código Único *
                                    </label>
                                    @error('codigo_unico') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="relative">
                                    <select name="lote_id" id="lote_id" required
                                        class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all appearance-none cursor-pointer">
                                        <option value="" disabled {{ old('lote_id') ? '' : 'selected' }}>Lote Asignado *</option>
                                        @foreach($lotes as $lote)
                                            <option value="{{ $lote->id }}" {{ old('lote_id') == $lote->id ? 'selected' : '' }}>{{ $lote->nombre_lote }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                                    @error('lote_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="relative md:col-span-2">
                                    <select name="lote_zona_manejo_id" id="lote_zona_manejo_id"
                                        class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all appearance-none cursor-pointer">
                                        <option value="" {{ old('lote_zona_manejo_id') ? '' : 'selected' }}>Zona de Manejo (Opcional)</option>
                                        @foreach($zonas as $zona)
                                            <option value="{{ $zona->id }}" {{ old('lote_zona_manejo_id') == $zona->id ? 'selected' : '' }}>{{ $zona->nombre_zona }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                                </div>
                            </div>
                        </div>

                        <div class="animate-fade-in-up delay-200">
                            <h4 class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-4 border-b pb-2">2. Desarrollo y Estado Agronómico</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                
                                <div class="relative">
                                    <select name="ciclo_productivo_id" id="ciclo_productivo_id" required
                                        class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all appearance-none cursor-pointer">
                                        <option value="" disabled {{ old('ciclo_productivo_id') ? '' : 'selected' }}>Ciclo Productivo *</option>
                                        @foreach($ciclos as $ciclo)
                                            <option value="{{ $ciclo->id }}" {{ old('ciclo_productivo_id') == $ciclo->id ? 'selected' : '' }}>{{ $ciclo->nombre_ciclo ?? 'Ciclo #'.$ciclo->id }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                                </div>

                                <div class="relative group">
                                    <input type="text" name="variedad" id="variedad" maxlength="100"
                                        class="peer w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all placeholder-transparent"
                                        placeholder="Variedad" value="{{ old('variedad') }}">
                                    <label class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm transition-all pointer-events-none peer-focus:-top-2 peer-focus:left-3 peer-focus:text-xs peer-focus:text-emerald-600 peer-focus:bg-white peer-focus:px-1 peer-valid:-top-2 peer-valid:left-3 peer-valid:text-xs peer-valid:bg-white peer-valid:px-1 {{ old('variedad') ? '-top-2 left-3 text-xs bg-white px-1' : '' }}">
                                        Variedad (Opcional)
                                    </label>
                                </div>

                                <div class="relative">
                                    <select name="estado_vital" id="estado_vital" required
                                        class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all appearance-none cursor-pointer">
                                        <option value="" disabled {{ old('estado_vital') ? '' : 'selected' }}>Estado Vital *</option>
                                        <option value="excelente" {{ old('estado_vital') == 'excelente' ? 'selected' : '' }}>Excelente</option>
                                        <option value="con_estres" {{ old('estado_vital') == 'con_estres' ? 'selected' : '' }}>Con Estrés</option>
                                        <option value="enfermo_critico" {{ old('estado_vital') == 'enfermo_critico' ? 'selected' : '' }}>Enfermo Crítico</option>
                                        <option value="muerto" {{ old('estado_vital') == 'muerto' ? 'selected' : '' }}>Muerto</option>
                                        <option value="erradicado" {{ old('estado_vital') == 'erradicado' ? 'selected' : '' }}>Erradicado</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                                </div>

                                <div class="relative">
                                    <select name="etapa_biologica" id="etapa_biologica" required
                                        class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all appearance-none cursor-pointer">
                                        <option value="" disabled {{ old('etapa_biologica') ? '' : 'selected' }}>Etapa Biológica *</option>
                                        <option value="vivero" {{ old('etapa_biologica') == 'vivero' ? 'selected' : '' }}>Vivero</option>
                                        <option value="establecimiento" {{ old('etapa_biologica') == 'establecimiento' ? 'selected' : '' }}>Establecimiento</option>
                                        <option value="desarrollo_inmaduro" {{ old('etapa_biologica') == 'desarrollo_inmaduro' ? 'selected' : '' }}>Desarrollo Inmaduro</option>
                                        <option value="produccion_madura" {{ old('etapa_biologica') == 'produccion_madura' ? 'selected' : '' }}>Producción Madura</option>
                                        <option value="senescencia" {{ old('etapa_biologica') == 'senescencia' ? 'selected' : '' }}>Senescencia</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                                </div>

                                <div class="relative group md:col-span-2">
                                    <input type="date" name="fecha_siembra" id="fecha_siembra"
                                        class="peer w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all"
                                        value="{{ old('fecha_siembra') }}">
                                    <label class="absolute -top-2 left-3 text-xs text-emerald-600 bg-white px-1">Fecha de Siembra (Opcional)</label>
                                </div>
                            </div>
                        </div>

                        <div class="animate-fade-in-up delay-300">
                            <div class="flex items-center justify-between mb-4 border-b pb-2">
                                <h4 class="text-xs font-bold text-emerald-600 uppercase tracking-wider">3. Geometría y Topología</h4>
                                <span class="text-[10px] bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">GPS WGS84</span>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="relative group col-span-1 md:col-span-2">
                                    <input type="number" name="fila_indice" id="fila_indice" min="0" required
                                        class="peer w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all placeholder-transparent"
                                        placeholder="Fila" value="{{ old('fila_indice') }}">
                                    <label class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm transition-all pointer-events-none peer-focus:-top-2 peer-focus:left-3 peer-focus:text-xs peer-focus:text-emerald-600 peer-focus:bg-white peer-focus:px-1 peer-valid:-top-2 peer-valid:left-3 peer-valid:text-xs peer-valid:bg-white peer-valid:px-1 {{ old('fila_indice') !== null ? '-top-2 left-3 text-xs bg-white px-1' : '' }}">
                                        Nº Fila *
                                    </label>
                                </div>

                                <div class="relative group col-span-1 md:col-span-2">
                                    <input type="number" name="posicion_indice" id="posicion_indice" min="0" required
                                        class="peer w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all placeholder-transparent"
                                        placeholder="Posición" value="{{ old('posicion_indice') }}">
                                    <label class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm transition-all pointer-events-none peer-focus:-top-2 peer-focus:left-3 peer-focus:text-xs peer-focus:text-emerald-600 peer-focus:bg-white peer-focus:px-1 peer-valid:-top-2 peer-valid:left-3 peer-valid:text-xs peer-valid:bg-white peer-valid:px-1 {{ old('posicion_indice') !== null ? '-top-2 left-3 text-xs bg-white px-1' : '' }}">
                                        Posición en Fila *
                                    </label>
                                </div>

                                <div class="relative group col-span-2">
                                    <input type="number" step="any" name="latitude" id="latitude" min="-90" max="90"
                                        class="peer w-full h-12 px-4 pl-10 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all placeholder-transparent"
                                        placeholder="Latitud" value="{{ old('latitude') }}">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg></div>
                                    <label class="absolute left-10 top-1/2 -translate-y-1/2 text-gray-400 text-sm transition-all pointer-events-none peer-focus:-top-2 peer-focus:left-3 peer-focus:text-xs peer-focus:text-emerald-600 peer-focus:bg-white peer-focus:px-1 peer-valid:-top-2 peer-valid:left-3 peer-valid:text-xs peer-valid:bg-white peer-valid:px-1 {{ old('latitude') ? '-top-2 left-3 text-xs bg-white px-1' : '' }}">
                                        Latitud
                                    </label>
                                </div>

                                <div class="relative group col-span-2">
                                    <input type="number" step="any" name="longitude" id="longitude" min="-180" max="180"
                                        class="peer w-full h-12 px-4 pl-10 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all placeholder-transparent"
                                        placeholder="Longitud" value="{{ old('longitude') }}">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                                    <label class="absolute left-10 top-1/2 -translate-y-1/2 text-gray-400 text-sm transition-all pointer-events-none peer-focus:-top-2 peer-focus:left-3 peer-focus:text-xs peer-focus:text-emerald-600 peer-focus:bg-white peer-focus:px-1 peer-valid:-top-2 peer-valid:left-3 peer-valid:text-xs peer-valid:bg-white peer-valid:px-1 {{ old('longitude') ? '-top-2 left-3 text-xs bg-white px-1' : '' }}">
                                        Longitud
                                    </label>
                                </div>

                                <div class="relative group col-span-2">
                                    <input type="number" step="any" name="altitud" id="altitud"
                                        class="peer w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all placeholder-transparent"
                                        placeholder="Altitud" value="{{ old('altitud') }}">
                                    <label class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm transition-all pointer-events-none peer-focus:-top-2 peer-focus:left-3 peer-focus:text-xs peer-focus:text-emerald-600 peer-focus:bg-white peer-focus:px-1 peer-valid:-top-2 peer-valid:left-3 peer-valid:text-xs peer-valid:bg-white peer-valid:px-1 {{ old('altitud') ? '-top-2 left-3 text-xs bg-white px-1' : '' }}">
                                        Altitud (m)
                                    </label>
                                </div>

                                <div class="relative group col-span-2">
                                    <input type="number" step="any" name="altitud_ortometrica_msnm" id="altitud_ortometrica_msnm"
                                        class="peer w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all placeholder-transparent"
                                        placeholder="Altitud Ort." value="{{ old('altitud_ortometrica_msnm') }}">
                                    <label class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm transition-all pointer-events-none peer-focus:-top-2 peer-focus:left-3 peer-focus:text-xs peer-focus:text-emerald-600 peer-focus:bg-white peer-focus:px-1 peer-valid:-top-2 peer-valid:left-3 peer-valid:text-xs peer-valid:bg-white peer-valid:px-1 {{ old('altitud_ortometrica_msnm') ? '-top-2 left-3 text-xs bg-white px-1' : '' }}">
                                        Alt. Ortometríca (msnm)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="animate-fade-in-up delay-400">
                            <h4 class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-4 border-b pb-2">4. Notas</h4>
                            <div class="relative group">
                                <textarea name="observaciones" id="observaciones" rows="3"
                                    class="peer w-full p-4 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all placeholder-transparent resize-none"
                                    placeholder="Observaciones">{{ old('observaciones') }}</textarea>
                                <label class="absolute left-4 top-4 text-gray-400 text-sm transition-all pointer-events-none peer-focus:-top-2 peer-focus:left-3 peer-focus:text-xs peer-focus:text-emerald-600 peer-focus:bg-white peer-focus:px-1 peer-valid:-top-2 peer-valid:left-3 peer-valid:text-xs peer-valid:bg-white peer-valid:px-1 {{ old('observaciones') ? '-top-2 left-3 text-xs bg-white px-1' : '' }}">
                                    Observaciones adicionales...
                                </label>
                            </div>
                        </div>

                        <div class="mt-10 pt-6 border-t border-gray-100 flex items-center justify-end space-x-4 animate-fade-in-up delay-500">
                            <button type="submit" class="w-full sm:w-auto px-8 py-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-green-600 shadow-[0_10px_20px_rgba(16,185,129,0.3)] hover:shadow-[0_15px_30px_rgba(16,185,129,0.4)] hover:-translate-y-1 transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                Guardar Geometría del Árbol
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>