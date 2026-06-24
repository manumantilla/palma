<x-app-layout>
<x-slot name="header">
    <h2 class="text-center font-semibold text-xl text-emerald-800 leading-tight bg-emerald-100 py-2 px-4 rounded-md inline-block mx-auto table">
        {{ __('Registrar Evento de Campo General') }}
    </h2>
</x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 bg-white border-b border-gray-200">
                
                <form method="POST" action="{{ route('eventos_campo.store_general') }}">
                    @csrf

                    <h3 class="text-md font-bold text-gray-700 uppercase tracking-wider mb-4 border-b pb-1">1. Información Principal</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        
                        <div class="md:col-span-1">
                            <label for="tipo_evento_id" class="block text-sm font-medium text-gray-700">Tipo de Evento <span class="text-red-500">*</span></label>
                            <select name="tipo_evento_id" id="tipo_evento_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">-- Seleccione --</option>
                                @foreach($tiposEvento as $tipo)
                                    <option value="{{ $tipo->id }}" {{ old('tipo_evento_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                            @error('tipo_evento_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="fecha_programada" class="block text-sm font-medium text-gray-700">Fecha Programada <span class="text-red-500">*</span></label>
                            <input type="date" name="fecha_programada" id="fecha_programada" required value="{{ old('fecha_programada') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('fecha_programada') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">Estado <span class="text-red-500">*</span></label>
                            <select name="estado" id="estado" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @foreach(['Pendiente', 'En Proceso', 'Completado', 'Cancelado'] as $est)
                                    <option value="{{ $est }}" {{ old('estado', 'Pendiente') == $est ? 'selected' : '' }}>{{ $est }}</option>
                                @endforeach
                            </select>
                            @error('estado') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <h3 class="text-md font-bold text-gray-700 uppercase tracking-wider mb-4 border-b pb-1">2. Tiempos y Ubicación</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                        
                        <div>
                            <label for="hora_inicio" class="block text-sm font-medium text-gray-700">Hora Inicio</label>
                            <input type="time" name="hora_inicio" id="hora_inicio" value="{{ old('hora_inicio') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('hora_inicio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="hora_fin" class="block text-sm font-medium text-gray-700">Hora Fin</label>
                            <input type="time" name="hora_fin" id="hora_fin" value="{{ old('hora_fin') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('hora_fin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="latitud" class="block text-sm font-medium text-gray-700">Latitud</label>
                            <input type="number" step="any" name="latitud" id="latitud" placeholder="Ej: -12.3456" value="{{ old('latitud') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('latitud') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="longitud" class="block text-sm font-medium text-gray-700">Longitud</label>
                            <input type="number" step="any" name="longitud" id="longitud" placeholder="Ej: -75.1234" value="{{ old('longitud') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('longitud') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2 lg:col-span-4">
                            <label for="fecha_ejecucion" class="block text-sm font-medium text-gray-700">Fecha de Ejecución Real</label>
                            <input type="date" name="fecha_ejecucion" id="fecha_ejecucion" value="{{ old('fecha_ejecucion') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('fecha_ejecucion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <h3 class="text-md font-bold text-gray-700 uppercase tracking-wider mb-4 border-b pb-1">3. Información Adicional</h3>
                    <div class="mb-6">
                        <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" rows="4" placeholder="Detalles o notas sobre el evento..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('observaciones') }}</textarea>
                        @error('observaciones') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end space-x-3 border-t pt-4">
                        <a href="{{ route('eventos_campo.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out">
                            Guardar Evento
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>