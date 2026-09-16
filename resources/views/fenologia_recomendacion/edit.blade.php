<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-800 leading-tight">
            {{ __('Editar Recomendación Fenológica') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-200">
                <div class="p-6 bg-green-50/30">
                    <form method="POST" action="{{ route('fenologia-recomendacion.update', $recomendacion) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Etapa -->
                        <div>
                            <label for="fenologia_etapa_id" class="block text-sm font-medium text-green-800">Etapa fenológica</label>
                            <select name="fenologia_etapa_id" id="fenologia_etapa_id" 
                                    class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80">
                                <option value="">Seleccione una etapa</option>
                                @foreach($etapas as $etapa)
                                    <option value="{{ $etapa->id }}" {{ old('fenologia_etapa_id', $recomendacion->fenologia_etapa_id) == $etapa->id ? 'selected' : '' }}>
                                        {{ $etapa->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fenologia_etapa_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipo evento (opcional) -->
                        <div>
                            <label for="tipo_evento_id" class="block text-sm font-medium text-green-800">Tipo de evento <span class="text-gray-500 text-xs">(opcional)</span></label>
                            <select name="tipo_evento_id" id="tipo_evento_id" 
                                    class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80">
                                <option value="">Seleccione un tipo de evento</option>
                                @foreach($tiposEvento as $tipo)
                                    <option value="{{ $tipo->id }}" {{ old('tipo_evento_id', $recomendacion->tipo_evento_id) == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_evento_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Título -->
                        <div>
                            <label for="titulo" class="block text-sm font-medium text-green-800">Título</label>
                            <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $recomendacion->titulo) }}"
                                   class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80"
                                   placeholder="Ej. Aplicación de fertilizante">
                            @error('titulo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-green-800">Descripción</label>
                            <textarea name="descripcion" id="descripcion" rows="3"
                                      class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80"
                                      placeholder="Detalles de la recomendación...">{{ old('descripcion', $recomendacion->descripcion) }}</textarea>
                            @error('descripcion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Prioridad -->
                        <div>
                            <label for="prioridad" class="block text-sm font-medium text-green-800">Prioridad</label>
                            <select name="prioridad" id="prioridad" 
                                    class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80">
                                <option value="Baja" {{ old('prioridad', $recomendacion->prioridad) == 'Baja' ? 'selected' : '' }}>Baja</option>
                                <option value="Media" {{ old('prioridad', $recomendacion->prioridad) == 'Media' ? 'selected' : '' }}>Media</option>
                                <option value="Alta" {{ old('prioridad', $recomendacion->prioridad) == 'Alta' ? 'selected' : '' }}>Alta</option>
                                <option value="Crítica" {{ old('prioridad', $recomendacion->prioridad) == 'Crítica' ? 'selected' : '' }}>Crítica</option>
                            </select>
                            @error('prioridad')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Días offset -->
                        <div>
                            <label for="dias_offset" class="block text-sm font-medium text-green-800">Días offset (desde inicio de etapa)</label>
                            <input type="number" name="dias_offset" id="dias_offset" value="{{ old('dias_offset', $recomendacion->dias_offset) }}"
                                   class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80"
                                   placeholder="Ej. 5">
                            @error('dias_offset')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ventana de ejecución (opcional) -->
                        <div>
                            <label for="ventana_ejecucion_dias" class="block text-sm font-medium text-green-800">Ventana de ejecución (días) <span class="text-gray-500 text-xs">(opcional)</span></label>
                            <input type="number" name="ventana_ejecucion_dias" id="ventana_ejecucion_dias" value="{{ old('ventana_ejecucion_dias', $recomendacion->ventana_ejecucion_dias) }}"
                                   class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80"
                                   placeholder="Ej. 3">
                            @error('ventana_ejecucion_dias')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Checkboxes -->
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="checkbox" name="genera_evento_automatico" id="genera_evento_automatico" value="1"
                                       {{ old('genera_evento_automatico', $recomendacion->genera_evento_automatico) ? 'checked' : '' }}
                                       class="rounded border-green-300 text-green-600 shadow-sm focus:ring-green-500">
                                <label for="genera_evento_automatico" class="ml-2 block text-sm text-green-800">
                                    Generar evento automático
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="requiere_verificacion_campo" id="requiere_verificacion_campo" value="1"
                                       {{ old('requiere_verificacion_campo', $recomendacion->requiere_verificacion_campo) ? 'checked' : '' }}
                                       class="rounded border-green-300 text-green-600 shadow-sm focus:ring-green-500">
                                <label for="requiere_verificacion_campo" class="ml-2 block text-sm text-green-800">
                                    Requiere verificación en campo
                                </label>
                            </div>
                        </div>

                        <!-- Instrucciones técnicas (opcional) -->
                        <div>
                            <label for="instrucciones_tecnicas" class="block text-sm font-medium text-green-800">Instrucciones técnicas <span class="text-gray-500 text-xs">(opcional)</span></label>
                            <textarea name="instrucciones_tecnicas" id="instrucciones_tecnicas" rows="3"
                                      class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80"
                                      placeholder="Detalles técnicos para el personal...">{{ old('instrucciones_tecnicas', $recomendacion->instrucciones_tecnicas) }}</textarea>
                            @error('instrucciones_tecnicas')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('fenologia-recomendacion.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-800 focus:bg-green-800 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Actualizar Recomendación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>