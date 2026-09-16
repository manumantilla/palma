<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-800 leading-tight">
            {{ __('Editar Historial de Etapa') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-200">
                <div class="p-6 bg-green-50/30">
                    <form method="POST" action="{{ route('ciclo-etapa-historial.update', $historial) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Ciclo Productivo -->
                        <div>
                            <label for="ciclo_productivo_id" class="block text-sm font-medium text-green-800">Ciclo Productivo</label>
                            <select name="ciclo_productivo_id" id="ciclo_productivo_id" 
                                    class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80">
                                <option value="">Seleccione un ciclo</option>
                                @foreach($ciclosProductivos as $ciclo)
                                    <option value="{{ $ciclo->id }}" {{ old('ciclo_productivo_id', $historial->ciclo_productivo_id) == $ciclo->id ? 'selected' : '' }}>
                                        {{ $ciclo->nombre ?? 'Ciclo #'.$ciclo->id }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ciclo_productivo_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Etapa Fenológica -->
                        <div>
                            <label for="fenologia_etapa_id" class="block text-sm font-medium text-green-800">Etapa Fenológica</label>
                            <select name="fenologia_etapa_id" id="fenologia_etapa_id" 
                                    class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80">
                                <option value="">Seleccione una etapa</option>
                                @foreach($etapas as $etapa)
                                    <option value="{{ $etapa->id }}" {{ old('fenologia_etapa_id', $historial->fenologia_etapa_id) == $etapa->id ? 'selected' : '' }}>
                                        {{ $etapa->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fenologia_etapa_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha Inicio Estimada -->
                        <div>
                            <label for="fecha_inicio_estimada" class="block text-sm font-medium text-green-800">Fecha inicio estimada</label>
                            <input type="date" name="fecha_inicio_estimada" id="fecha_inicio_estimada" 
                                   value="{{ old('fecha_inicio_estimada', $historial->fecha_inicio_estimada ? $historial->fecha_inicio_estimada->format('Y-m-d') : '') }}"
                                   class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80">
                            @error('fecha_inicio_estimada')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha Fin Estimada -->
                        <div>
                            <label for="fecha_fin_estimada" class="block text-sm font-medium text-green-800">Fecha fin estimada</label>
                            <input type="date" name="fecha_fin_estimada" id="fecha_fin_estimada" 
                                   value="{{ old('fecha_fin_estimada', $historial->fecha_fin_estimada ? $historial->fecha_fin_estimada->format('Y-m-d') : '') }}"
                                   class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80">
                            @error('fecha_fin_estimada')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha Inicio Real (opcional) -->
                        <div>
                            <label for="fecha_inicio_real" class="block text-sm font-medium text-green-800">Fecha inicio real <span class="text-gray-500 text-xs">(opcional)</span></label>
                            <input type="date" name="fecha_inicio_real" id="fecha_inicio_real" 
                                   value="{{ old('fecha_inicio_real', $historial->fecha_inicio_real ? $historial->fecha_inicio_real->format('Y-m-d') : '') }}"
                                   class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80">
                            @error('fecha_inicio_real')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha Fin Real (opcional) -->
                        <div>
                            <label for="fecha_fin_real" class="block text-sm font-medium text-green-800">Fecha fin real <span class="text-gray-500 text-xs">(opcional)</span></label>
                            <input type="date" name="fecha_fin_real" id="fecha_fin_real" 
                                   value="{{ old('fecha_fin_real', $historial->fecha_fin_real ? $historial->fecha_fin_real->format('Y-m-d') : '') }}"
                                   class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80">
                            @error('fecha_fin_real')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Estado -->
                        <div>
                            <label for="estado" class="block text-sm font-medium text-green-800">Estado</label>
                            <select name="estado" id="estado" 
                                    class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80">
                                <option value="pendiente" {{ old('estado', $historial->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_progreso" {{ old('estado', $historial->estado) == 'en_progreso' ? 'selected' : '' }}>En progreso</option>
                                <option value="completada" {{ old('estado', $historial->estado) == 'completada' ? 'selected' : '' }}>Completada</option>
                                <option value="omitida" {{ old('estado', $historial->estado) == 'omitida' ? 'selected' : '' }}>Omitida</option>
                            </select>
                            @error('estado')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('ciclo-etapa-historial.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-800 focus:bg-green-800 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Actualizar Historial
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>