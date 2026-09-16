<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-800 leading-tight">
            {{ __('Nueva Etapa Fenológica') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-200">
                <div class="p-6 bg-green-50/30">
                    <form method="POST" action="{{ route('fenologia-etapa.store') }}" class="space-y-6">
                        @csrf

                        <!-- Cultivo -->
                        <div>
                            <label for="cultivo_id" class="block text-sm font-medium text-green-800">Cultivo</label>
                            <select name="cultivo_id" id="cultivo_id" 
                                    class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80">
                                <option value="">Seleccione un cultivo</option>
                                @foreach($cultivos as $cultivo)
                                    <option value="{{ $cultivo->id }}" {{ old('cultivo_id') == $cultivo->id ? 'selected' : '' }}>
                                        {{ $cultivo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cultivo_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nombre -->
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-green-800">Nombre de la etapa</label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                                   class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80"
                                   placeholder="Ej. Floración">
                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Orden -->
                        <div>
                            <label for="orden" class="block text-sm font-medium text-green-800">Orden</label>
                            <input type="number" name="orden" id="orden" value="{{ old('orden') }}"
                                   class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80"
                                   placeholder="Número de orden">
                            @error('orden')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duración desde inicio (opcional) -->
                        <div>
                            <label for="duracion_dias_desde_inicio" class="block text-sm font-medium text-green-800">Duración desde inicio (días) <span class="text-gray-500 text-xs">(opcional)</span></label>
                            <input type="number" name="duracion_dias_desde_inicio" id="duracion_dias_desde_inicio" value="{{ old('duracion_dias_desde_inicio') }}"
                                   class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80"
                                   placeholder="Ej. 30">
                            @error('duracion_dias_desde_inicio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duración estimada -->
                        <div>
                            <label for="duracion_dias_estimada" class="block text-sm font-medium text-green-800">Duración estimada (días)</label>
                            <input type="number" name="duracion_dias_estimada" id="duracion_dias_estimada" value="{{ old('duracion_dias_estimada') }}"
                                   class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80"
                                   placeholder="Ej. 15">
                            @error('duracion_dias_estimada')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-green-800">Descripción <span class="text-gray-500 text-xs">(opcional)</span></label>
                            <textarea name="descripcion" id="descripcion" rows="4"
                                      class="mt-1 block w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white/80"
                                      placeholder="Detalles de la etapa...">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('fenologia-etapa.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-800 focus:bg-green-800 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Guardar Etapa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>