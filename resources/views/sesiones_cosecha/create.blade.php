{{-- resources/views/sesiones-cosecha/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-green-800 leading-tight">
                🌾 Nueva Sesión de Cosecha
            </h2>
            <a href="{{ route('sesiones-cosecha.index') }}" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg transition duration-200">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg border border-green-100 p-6">
                @if($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">⚠️</span>
                            <div>
                                <p class="font-bold">Por favor corrija los siguientes errores:</p>
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('sesiones-cosecha.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Orden de Cosecha -->
                        <div>
                            <label for="orden_cosecha_id" class="block text-sm font-medium text-green-700 mb-1">
                                Orden de Cosecha *
                            </label>
                            <select name="orden_cosecha_id" id="orden_cosecha_id" 
                                    class="w-full rounded-lg border-green-300 focus:border-green-500 focus:ring-green-500 @error('orden_cosecha_id') border-red-500 @enderror" required>
                                <option value="">Seleccione una orden</option>
                                @foreach($ordenes as $orden)
                                    <option value="{{ $orden->id }}" {{ old('orden_cosecha_id') == $orden->id ? 'selected' : '' }}>
                                        {{$orden->id}}{{ $orden->cliente->nombre_comercial }} - {{ $orden->mombre_cultivo ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('orden_cosecha_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Evento de Campo -->
                        <div>
                            <label for="evento_campo_id" class="block text-sm font-medium text-green-700 mb-1">
                                Evento de Campo *
                            </label>
                            <select name="evento_campo_id" id="evento_campo_id" 
                                    class="w-full rounded-lg border-green-300 focus:border-green-500 focus:ring-green-500 @error('evento_campo_id') border-red-500 @enderror" required>
                                <option value="">Seleccione un evento</option>
                                @foreach($eventos as $evento)
                                    <option value="{{ $evento->id }}" {{ old('evento_campo_id') == $evento->id ? 'selected' : '' }}>
                                        {{ $evento->id }} - {{ $evento->tipo_evento_id ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('evento_campo_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Responsable -->
                        <div>
                            <label for="responsable_id" class="block text-sm font-medium text-green-700 mb-1">
                                Responsable *
                            </label>
                            <select name="responsable_id" id="responsable_id" 
                                    class="w-full rounded-lg border-green-300 focus:border-green-500 focus:ring-green-500 @error('responsable_id') border-red-500 @enderror" required>
                                <option value="">Seleccione un responsable</option>
                                @foreach($responsables as $responsable)
                                    <option value="{{ $responsable->id }}" {{ old('responsable_id') == $responsable->id ? 'selected' : '' }}>
                                        {{ $responsable->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('responsable_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha -->
                        <div>
                            <label for="fecha" class="block text-sm font-medium text-green-700 mb-1">
                                Fecha *
                            </label>
                            <input type="date" name="fecha" id="fecha" 
                                   value="{{ old('fecha', date('Y-m-d')) }}"
                                   class="w-full rounded-lg border-green-300 focus:border-green-500 focus:ring-green-500 @error('fecha') border-red-500 @enderror" required>
                            @error('fecha')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Estado -->
                        <div>
                            <label for="estado" class="block text-sm font-medium text-green-700 mb-1">
                                Estado *
                            </label>
                            <select name="estado" id="estado" 
                                    class="w-full rounded-lg border-green-300 focus:border-green-500 focus:ring-green-500 @error('estado') border-red-500 @enderror" required>
                                <option value="abierta" {{ old('estado') == 'abierta' ? 'selected' : '' }}>🟢 Abierta</option>
                                <option value="cerrada" {{ old('estado') == 'cerrada' ? 'selected' : '' }}>🔴 Cerrada</option>
                            </select>
                            @error('estado')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meta KG Día -->
                        <div>
                            <label for="meta_kg_dia" class="block text-sm font-medium text-green-700 mb-1">
                                Meta KG/Día
                            </label>
                            <input type="number" step="0.01" name="meta_kg_dia" id="meta_kg_dia" 
                                   value="{{ old('meta_kg_dia') }}"
                                   placeholder="Ej: 500.00"
                                   class="w-full rounded-lg border-green-300 focus:border-green-500 focus:ring-green-500 @error('meta_kg_dia') border-red-500 @enderror">
                            @error('meta_kg_dia')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Número de Recolectores -->
                        <div>
                            <label for="numero_recolectores" class="block text-sm font-medium text-green-700 mb-1">
                                Número de Recolectores
                            </label>
                            <input type="number" name="numero_recolectores" id="numero_recolectores" 
                                   value="{{ old('numero_recolectores') }}"
                                   placeholder="Ej: 10"
                                   class="w-full rounded-lg border-green-300 focus:border-green-500 focus:ring-green-500 @error('numero_recolectores') border-red-500 @enderror">
                            @error('numero_recolectores')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hora Inicio -->
                        <div>
                            <label for="hora_inicio" class="block text-sm font-medium text-green-700 mb-1">
                                Hora de Inicio
                            </label>
                            <input type="time" name="hora_inicio" id="hora_inicio" 
                                   value="{{ old('hora_inicio') }}"
                                   class="w-full rounded-lg border-green-300 focus:border-green-500 focus:ring-green-500 @error('hora_inicio') border-red-500 @enderror">
                            @error('hora_inicio')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hora Fin -->
                        <div>
                            <label for="hora_fin" class="block text-sm font-medium text-green-700 mb-1">
                                Hora de Fin
                            </label>
                            <input type="time" name="hora_fin" id="hora_fin" 
                                   value="{{ old('hora_fin') }}"
                                   class="w-full rounded-lg border-green-300 focus:border-green-500 focus:ring-green-500 @error('hora_fin') border-red-500 @enderror">
                            @error('hora_fin')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Total Recolectado KG -->
                        <div class="md:col-span-2">
                            <label for="total_recolectado_kg" class="block text-sm font-medium text-green-700 mb-1">
                                Total Recolectado (KG)
                            </label>
                            <input type="number" step="0.01" name="total_recolectado_kg" id="total_recolectado_kg" 
                                   value="{{ old('total_recolectado_kg') }}"
                                   placeholder="Ej: 450.50"
                                   class="w-full rounded-lg border-green-300 focus:border-green-500 focus:ring-green-500 @error('total_recolectado_kg') border-red-500 @enderror">
                            @error('total_recolectado_kg')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-green-200">
                        <a href="{{ route('sesiones-cosecha.index') }}" 
                           class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="bg-green-700 hover:bg-green-800 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                            <span>🌾</span>
                            Guardar Sesión
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>