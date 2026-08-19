<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Botón Volver -->
        <div class="mb-6">
            <a href="{{ route('ordenes_cosecha.show', $ordenCosecha) }}" class="inline-flex items-center gap-2 text-green-600 hover:text-green-800 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Volver a orden #{{ $ordenCosecha->id }}</span>
            </a>
        </div>

        <!-- Encabezado -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
            <div class="flex items-center gap-3">
                <div class="bg-amber-600 p-2.5 rounded-full shadow-md">
                    <i class="fas fa-tractor text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-green-800 tracking-wide" style="font-family: 'Segoe UI', 'Georgia', serif;">
                        Nueva Sesión de Cosecha
                    </h1>
                    <p class="text-sm text-amber-700 flex items-center gap-2">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Orden #{{ $ordenCosecha->id }} - {{ $ordenCosecha->cliente->name ?? 'N/A' }}
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    </p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <span class="text-sm text-green-600 bg-green-50 px-4 py-1.5 rounded-full border border-green-200">
                    <i class="fas fa-seedling mr-1.5"></i> Sesión #{{ \App\Models\SesionCosecha::count() + 1 }}
                </span>
            </div>
        </div>

        <!-- Tarjetas de información de la orden -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-lg border border-green-100 p-4">
                <div class="flex items-center gap-3">
                    <div class="bg-green-100 p-2 rounded-lg">
                        <i class="fas fa-weight-hanging text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Restante por recolectar</p>
                        @php
                            $restante = ($ordenCosecha->cantidad_solicitada_kg ?? 0) - ($ordenCosecha->cantidad_recolectada_kg ?? 0);
                        @endphp
                        <p class="text-lg font-bold text-green-700">{{ number_format(max($restante, 0), 2) }} kg</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg border border-green-100 p-4">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <i class="fas fa-calendar-alt text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Fecha programada</p>
                        <p class="text-sm font-bold text-blue-700">{{ \Carbon\Carbon::parse($ordenCosecha->fecha_programada)->locale('es')->isoFormat('D MMM [de] Y') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg border border-green-100 p-4">
                <div class="flex items-center gap-3">
                    <div class="bg-amber-100 p-2 rounded-lg">
                        <i class="fas fa-chart-line text-amber-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Progreso actual</p>
                        @php
                            $progreso = $ordenCosecha->cantidad_solicitada_kg > 0 ? 
                                min(($ordenCosecha->cantidad_recolectada_kg / $ordenCosecha->cantidad_solicitada_kg) * 100, 100) : 0;
                        @endphp
                        <p class="text-sm font-bold text-amber-700">{{ number_format($progreso, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-green-100/80">
            <form action="{{ route('sesiones_cosecha.store', $ordenCosecha->id) }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="orden_cosecha_id" value="{{ $ordenCosecha->id }}">

                <div class="p-6 border-b border-green-100">
                    <h3 class="text-lg font-semibold text-green-800 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-amber-600"></i>
                        Datos de la Sesión
                    </h3>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fecha -->
                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-day text-green-600 mr-1"></i> Fecha de la sesión
                        </label>
                        <input type="date" 
                               name="fecha" 
                               id="fecha" 
                               value="{{ old('fecha', date('Y-m-d')) }}"
                               class="w-full px-4 py-2 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 @error('fecha') border-red-500 @enderror"
                               required>
                        @error('fecha')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Evento de Campo -->
                    <div>
                        <label for="evento_campo_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marked-alt text-green-600 mr-1"></i> Evento de Campo
                        </label>
                        <select name="evento_campo_id" 
                                id="evento_campo_id" 
                                class="w-full px-4 py-2 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 @error('evento_campo_id') border-red-500 @enderror"
                                required>
                            <option value="">Seleccione un evento</option>
                            @foreach($eventosCampo as $evento)
                                <option value="{{ $evento->id }}" {{ old('evento_campo_id') == $evento->id ? 'selected' : '' }}>
                                    #{{ $evento->id }} - {{ $evento->nombre ?? 'Evento ' . $evento->id }}
                                </option>
                            @endforeach
                        </select>
                        @error('evento_campo_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Responsable -->
                    <div>
                        <label for="responsable_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-tie text-green-600 mr-1"></i> Responsable
                        </label>
                        <select name="responsable_id" 
                                id="responsable_id" 
                                class="w-full px-4 py-2 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 @error('responsable_id') border-red-500 @enderror"
                                required>
                            <option value="">Seleccione un responsable</option>
                            @foreach($responsables as $responsable)
                                <option value="{{ $responsable->id }}" {{ old('responsable_id') == $responsable->id ? 'selected' : '' }}>
                                    {{ $responsable->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('responsable_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estado (oculto por defecto) -->
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-circle text-green-600 mr-1"></i> Estado
                        </label>
                        <select name="estado" 
                                id="estado" 
                                class="w-full px-4 py-2 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200">
                            <option value="abierta" {{ old('estado', 'abierta') == 'abierta' ? 'selected' : '' }}>Abierta</option>
                            <option value="cerrada" {{ old('estado') == 'cerrada' ? 'selected' : '' }}>Cerrada</option>
                        </select>
                        @error('estado')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Meta KG por Día -->
                    <div>
                        <label for="meta_kg_dia" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-bullseye text-green-600 mr-1"></i> Meta KG por Día
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   name="meta_kg_dia" 
                                   id="meta_kg_dia" 
                                   value="{{ old('meta_kg_dia') }}"
                                   step="0.01"
                                   min="0"
                                   placeholder="0.00"
                                   class="w-full px-4 py-2 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 @error('meta_kg_dia') border-red-500 @enderror">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">kg</span>
                        </div>
                        @error('meta_kg_dia')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Número de Recolectores -->
                    <div>
                        <label for="numero_recolectores" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-users text-green-600 mr-1"></i> Número de Recolectores
                        </label>
                        <input type="number" 
                               name="numero_recolectores" 
                               id="numero_recolectores" 
                               value="{{ old('numero_recolectores') }}"
                               min="1"
                               placeholder="0"
                               class="w-full px-4 py-2 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 @error('numero_recolectores') border-red-500 @enderror">
                        @error('numero_recolectores')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hora de Inicio -->
                    <div>
                        <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-play text-green-600 mr-1"></i> Hora de Inicio
                        </label>
                        <input type="time" 
                               name="hora_inicio" 
                               id="hora_inicio" 
                               value="{{ old('hora_inicio', date('H:i')) }}"
                               class="w-full px-4 py-2 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 @error('hora_inicio') border-red-500 @enderror">
                        @error('hora_inicio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hora de Fin -->
                    <div>
                        <label for="hora_fin" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-stop text-green-600 mr-1"></i> Hora de Fin
                        </label>
                        <input type="time" 
                               name="hora_fin" 
                               id="hora_fin" 
                               value="{{ old('hora_fin') }}"
                               class="w-full px-4 py-2 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 @error('hora_fin') border-red-500 @enderror">
                        @error('hora_fin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Recolectado KG -->
                    <div>
                        <label for="total_recolectado_kg" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-weight text-green-600 mr-1"></i> Total Recolectado KG
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   name="total_recolectado_kg" 
                                   id="total_recolectado_kg" 
                                   value="{{ old('total_recolectado_kg', 0) }}"
                                   step="0.01"
                                   min="0"
                                   placeholder="0.00"
                                   class="w-full px-4 py-2 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 @error('total_recolectado_kg') border-red-500 @enderror">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">kg</span>
                        </div>
                        @error('total_recolectado_kg')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Metadatos de sincronización (ocultos) -->
                    <input type="hidden" name="client_updated_at" value="{{ now() }}">
                    <input type="hidden" name="synced_at" value="{{ now() }}">
                </div>

                <!-- Botones de acción -->
                <div class="px-6 py-4 bg-green-50/50 border-t border-green-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <i class="fas fa-info-circle text-green-600"></i>
                        <span>Los campos marcados con <span class="text-red-500">*</span> son obligatorios</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('ordenes_cosecha.show', $ordenCosecha) }}" 
                           class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full transition duration-200 flex items-center gap-2">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-full shadow-md transition duration-200 flex items-center gap-2">
                            <i class="fas fa-save"></i> Crear Sesión
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Puntos decorativos agrícolas -->
        <div class="mt-6 flex justify-center gap-2 opacity-40">
            <span class="inline-block w-2 h-2 rounded-full bg-green-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-amber-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-green-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-amber-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-green-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-amber-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-green-300"></span>
        </div>

    </div>
</x-app-layout>