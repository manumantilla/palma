<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-gray-50 min-h-screen" x-data="{ alcance: '{{ old('alcance', '') }}' }">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                <div class="bg-green-100 p-2.5 rounded-lg text-green-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">🌱 Registrar Evento Agrícola</h1>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 text-sm">
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <span class="text-gray-500 block text-xs uppercase tracking-wider font-semibold mb-1">Campaña</span>
                    <span class="font-bold text-gray-900 text-base">{{ $ciclo->nombre_campana ?? 'N/D' }}</span>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <span class="text-gray-500 block text-xs uppercase tracking-wider font-semibold mb-1">Lote</span>
                    <span class="font-bold text-gray-900 text-base">{{ $ciclo->lote->nombre_lote ?? 'N/D' }}</span>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <span class="text-gray-500 block text-xs uppercase tracking-wider font-semibold mb-1">Área Sembrada</span>
                    <span class="font-bold text-gray-900 text-base">{{ $ciclo->lote->area_hectareas_declaradas ?? 'N/D' }} ha</span>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <span class="text-gray-500 block text-xs uppercase tracking-wider font-semibold mb-1">Estado del Ciclo</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-green-100 text-green-800">
                        {{ $ciclo->estado ?? 'Activo' }}
                    </span>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <span class="text-gray-500 block text-xs uppercase tracking-wider font-semibold mb-1">Fecha de Siembra</span>
                    <span class="font-bold text-gray-900 text-base">{{ $ciclo->fecha_inicio ? \Carbon\Carbon::parse($ciclo->fecha_inicio)->format('d/m/Y') : 'N/D' }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('eventos_campo.store_cultivo', $ciclo->id) }}" method="POST">
            @csrf
            
            <input type="hidden" name="lote_id" value="{{ $ciclo->lote_id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                    <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Información General
                        </h2>
                    </div>
                    <div class="p-6 space-y-5 flex-1">
                        
                        <div>
                            <label for="tipo_evento_id" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Evento <span class="text-red-500">*</span></label>
                            <select id="tipo_evento_id" name="tipo_evento_id" required
                                class="mt-1 block w-full rounded-lg shadow-sm sm:text-sm transition-colors duration-200 
                                @error('tipo_evento_id') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-green-500 focus:ring-green-500 @enderror">
                                <option value="">Seleccione un tipo...</option>
                                @foreach($tiposEvento as $tipo)
                                    <option value="{{ $tipo->id }}" {{ old('tipo_evento_id') == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_evento_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado <span class="text-red-500">*</span></label>
                            <select id="estado" name="estado" required
                                class="mt-1 block w-full rounded-lg shadow-sm sm:text-sm transition-colors duration-200
                                @error('estado') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-green-500 focus:ring-green-500 @enderror">
                                <option value="Pendiente" {{ old('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="En Proceso" {{ old('estado') == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                                <option value="Completado" {{ old('estado') == 'Completado' ? 'selected' : '' }}>Completado</option>
                                <option value="Cancelado" {{ old('estado') == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                            @error('estado')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="alcance" class="block text-sm font-medium text-gray-700 mb-1">Alcance</label>
                            <select id="alcance" name="alcance" x-model="alcance"
                                class="mt-1 block w-full rounded-lg shadow-sm sm:text-sm transition-colors duration-200
                                @error('alcance') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-green-500 focus:ring-green-500 @enderror">
                                <option value="">Seleccione el alcance...</option>
                                <option value="global">Global (Todo el lote)</option>
                                <option value="lote_zona">Lote / Zona Específica</option>
                                <option value="arbol">A nivel de Árbol</option>
                            </select>
                            @error('alcance')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                    <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Ubicación
                        </h2>
                    </div>
                    <div class="p-6 space-y-5 flex-1">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lote</label>
                            <input type="text" readonly value="{{ $ciclo->lote->nombre ?? 'Lote ID: '.$ciclo->lote_id }}"
                                class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-100 text-gray-500 cursor-not-allowed shadow-sm sm:text-sm">
                        </div>

                        <div x-show="alcance === 'lote_zona' || alcance === 'arbol'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
                            <label for="zona_id" class="block text-sm font-medium text-gray-700 mb-1">Zona de Manejo</label>
                            <select id="zona_id" name="zona_id"
                                class="mt-1 block w-full rounded-lg shadow-sm sm:text-sm transition-colors duration-200
                                @error('zona_id') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-green-500 focus:ring-green-500 @enderror">
                                <option value="">Todas las zonas (Lote completo)</option>
                                @foreach($zonas as $zona)
                                    <option value="{{ $zona->id }}" {{ old('zona_id') == $zona->id ? 'selected' : '' }}>
                                        {{ $zona->nombre_zona }}
                                    </option>
                                @endforeach
                            </select>
                            @error('zona_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="alcance === 'arbol'" x-transition style="display: none;">
                            <div class="rounded-lg bg-blue-50 p-4 border border-blue-100">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">
                                            Este evento será asignado automáticamente a todos los árboles pertenecientes a la zona seleccionada. Si no selecciona una zona se aplicará a todos los árboles del lote.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="latitud" class="block text-sm font-medium text-gray-700 mb-1">Latitud</label>
                                <input type="number" step="any" id="latitud" name="latitud" value="{{ old('latitud') }}"
                                    class="mt-1 block w-full rounded-lg shadow-sm sm:text-sm transition-colors duration-200
                                    @error('latitud') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-green-500 focus:ring-green-500 @enderror">
                                @error('latitud')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="longitud" class="block text-sm font-medium text-gray-700 mb-1">Longitud</label>
                                <input type="number" step="any" id="longitud" name="longitud" value="{{ old('longitud') }}"
                                    class="mt-1 block w-full rounded-lg shadow-sm sm:text-sm transition-colors duration-200
                                    @error('longitud') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-green-500 focus:ring-green-500 @enderror">
                                @error('longitud')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                    <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Programación
                        </h2>
                    </div>
                    <div class="p-6 space-y-5 flex-1">
                        
                        <div>
                            <label for="fecha_programada" class="block text-sm font-medium text-gray-700 mb-1">Fecha Programada <span class="text-red-500">*</span></label>
                            <input type="date" id="fecha_programada" name="fecha_programada" value="{{ old('fecha_programada') }}" required
                                class="mt-1 block w-full rounded-lg shadow-sm sm:text-sm transition-colors duration-200
                                @error('fecha_programada') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-green-500 focus:ring-green-500 @enderror">
                            @error('fecha_programada')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-1">Hora Inicio</label>
                                <input type="time" id="hora_inicio" name="hora_inicio" value="{{ old('hora_inicio') }}"
                                    class="mt-1 block w-full rounded-lg shadow-sm sm:text-sm transition-colors duration-200
                                    @error('hora_inicio') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-green-500 focus:ring-green-500 @enderror">
                                @error('hora_inicio')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="hora_fin" class="block text-sm font-medium text-gray-700 mb-1">Hora Fin</label>
                                <input type="time" id="hora_fin" name="hora_fin" value="{{ old('hora_fin') }}"
                                    class="mt-1 block w-full rounded-lg shadow-sm sm:text-sm transition-colors duration-200
                                    @error('hora_fin') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-green-500 focus:ring-green-500 @enderror">
                                @error('hora_fin')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="fecha_ejecucion" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Ejecución Real</label>
                            <input type="date" id="fecha_ejecucion" name="fecha_ejecucion" value="{{ old('fecha_ejecucion') }}"
                                class="mt-1 block w-full rounded-lg shadow-sm sm:text-sm transition-colors duration-200
                                @error('fecha_ejecucion') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-green-500 focus:ring-green-500 @enderror">
                            @error('fecha_ejecucion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="md:col-span-2 lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gray-50/50 border-b border-gray-100 px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Observaciones
                        </h2>
                    </div>
                    <div class="p-6">
                        <label for="observaciones" class="sr-only">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" rows="4" placeholder="Ingrese detalles adicionales, condiciones climáticas, maquinaria utilizada, etc..."
                            class="block w-full rounded-lg shadow-sm sm:text-sm transition-colors duration-200
                            @error('observaciones') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-green-500 focus:ring-green-500 @enderror">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            <div class="mt-8 flex items-center justify-end gap-4 border-t border-gray-200 pt-6">
                <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-gray-600 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                    Volver
                </a>
                
                <a href="{{ route('eventos_campo.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                    Cancelar
                </a>
                
                <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Guardar Evento
                </button>
            </div>
            
        </form>
    </div>
</x-app-layout>
