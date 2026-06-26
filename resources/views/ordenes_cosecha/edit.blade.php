<x-app-layout>
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-edit text-yellow-600 mr-2"></i>Editar Orden #{{ $ordenesCosecha->id }}
        </h2>
        <a href="{{ route('ordenes_cosecha.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-1"></i>Volver
        </a>
    </div>

    <form action="{{ route('ordenes_cosecha.update', $ordenesCosecha) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Cliente -->
            <div>
                <label for="cliente_id" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-user text-blue-600 mr-1"></i>Cliente <span class="text-red-600">*</span>
                </label>
                <select name="cliente_id" id="cliente_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('cliente_id') border-red-500 @enderror" required>
                    <option value="">Seleccionar cliente</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}" {{ old('cliente_id', $ordenesCosecha->cliente_id) == $cliente->id ? 'selected' : '' }}>{{ $cliente->name }}</option>
                    @endforeach
                </select>
                @error('cliente_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Responsable -->
            <div>
                <label for="responsable_id" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-user-tie text-green-600 mr-1"></i>Responsable
                </label>
                <select name="responsable_id" id="responsable_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('responsable_id') border-red-500 @enderror">
                    <option value="">Seleccionar responsable</option>
                    @foreach($responsables as $responsable)
                        <option value="{{ $responsable->id }}" {{ old('responsable_id', $ordenesCosecha->responsable_id) == $responsable->id ? 'selected' : '' }}>{{ $responsable->name }}</option>
                    @endforeach
                </select>
                @error('responsable_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ciclo Productivo -->
            <div>
                <label for="ciclo_productivo_id" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-seedling text-green-600 mr-1"></i>Ciclo Productivo <span class="text-red-600">*</span>
                </label>
                <select name="ciclo_productivo_id" id="ciclo_productivo_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('ciclo_productivo_id') border-red-500 @enderror" required>
                    <option value="">Seleccionar ciclo</option>
                    @foreach($ciclos as $ciclo)
                        <option value="{{ $ciclo->id }}" {{ old('ciclo_productivo_id', $ordenesCosecha->ciclo_productivo_id) == $ciclo->id ? 'selected' : '' }}>{{ $ciclo->nombre }}</option>
                    @endforeach
                </select>
                @error('ciclo_productivo_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lote Cultivo -->
            <div>
                <label for="lote_cultivo_id" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-tractor text-yellow-600 mr-1"></i>Lote Cultivo
                </label>
                <select name="lote_cultivo_id" id="lote_cultivo_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('lote_cultivo_id') border-red-500 @enderror">
                    <option value="">Seleccionar lote</option>
                    @foreach($lotes as $lote)
                        <option value="{{ $lote->id }}" {{ old('lote_cultivo_id', $ordenesCosecha->lote_cultivo_id) == $lote->id ? 'selected' : '' }}>{{ $lote->nombre }}</option>
                    @endforeach
                </select>
                @error('lote_cultivo_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lote Zona -->
            <div>
                <label for="lote_zona_id" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-map-marker-alt text-red-600 mr-1"></i>Zona de Manejo
                </label>
                <select name="lote_zona_id" id="lote_zona_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('lote_zona_id') border-red-500 @enderror">
                    <option value="">Seleccionar zona</option>
                    @foreach($zonas as $zona)
                        <option value="{{ $zona->id }}" {{ old('lote_zona_id', $ordenesCosecha->lote_zona_id) == $zona->id ? 'selected' : '' }}>{{ $zona->nombre }}</option>
                    @endforeach
                </select>
                @error('lote_zona_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fechas -->
            <div>
                <label for="fecha_programada" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-calendar-alt text-blue-600 mr-1"></i>Fecha Programada <span class="text-red-600">*</span>
                </label>
                <input type="date" name="fecha_programada" id="fecha_programada" value="{{ old('fecha_programada', $ordenesCosecha->fecha_programada ? $ordenesCosecha->fecha_programada->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('fecha_programada') border-red-500 @enderror" required>
                @error('fecha_programada')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="fecha_entrega" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-calendar-check text-green-600 mr-1"></i>Fecha Entrega <span class="text-red-600">*</span>
                </label>
                <input type="date" name="fecha_entrega" id="fecha_entrega" value="{{ old('fecha_entrega', $ordenesCosecha->fecha_entrega ? $ordenesCosecha->fecha_entrega->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('fecha_entrega') border-red-500 @enderror" required>
                @error('fecha_entrega')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Cantidades -->
            <div>
                <label for="cantidad_solicitada_kg" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-weight-hanging text-purple-600 mr-1"></i>Cantidad Solicitada (kg)
                </label>
                <input type="number" step="0.01" name="cantidad_solicitada_kg" id="cantidad_solicitada_kg" value="{{ old('cantidad_solicitada_kg', $ordenesCosecha->cantidad_solicitada_kg) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('cantidad_solicitada_kg') border-red-500 @enderror" min="0">
                @error('cantidad_solicitada_kg')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="variedad_requerida" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-seedling text-green-600 mr-1"></i>Variedad Requerida
                </label>
                <input type="text" name="variedad_requerida" id="variedad_requerida" value="{{ old('variedad_requerida', $ordenesCosecha->variedad_requerida) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('variedad_requerida') border-red-500 @enderror" maxlength="255">
                @error('variedad_requerida')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="cantidad_planificada_kg" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-clipboard-list text-indigo-600 mr-1"></i>Cantidad Planificada (kg)
                </label>
                <input type="number" step="0.01" name="cantidad_planificada_kg" id="cantidad_planificada_kg" value="{{ old('cantidad_planificada_kg', $ordenesCosecha->cantidad_planificada_kg) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('cantidad_planificada_kg') border-red-500 @enderror" min="0">
                @error('cantidad_planificada_kg')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="cantidad_recolectada_kg" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-boxes text-orange-600 mr-1"></i>Cantidad Recolectada (kg)
                </label>
                <input type="number" step="0.01" name="cantidad_recolectada_kg" id="cantidad_recolectada_kg" value="{{ old('cantidad_recolectada_kg', $ordenesCosecha->cantidad_recolectada_kg) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('cantidad_recolectada_kg') border-red-500 @enderror" min="0">
                @error('cantidad_recolectada_kg')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fechas Inicio/Fin -->
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-play-circle text-blue-600 mr-1"></i>Fecha Inicio
                </label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio', $ordenesCosecha->fecha_inicio ? $ordenesCosecha->fecha_inicio->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('fecha_inicio') border-red-500 @enderror">
                @error('fecha_inicio')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-stop-circle text-red-600 mr-1"></i>Fecha Fin
                </label>
                <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin', $ordenesCosecha->fecha_fin ? $ordenesCosecha->fecha_fin->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('fecha_fin') border-red-500 @enderror">
                @error('fecha_fin')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Estado -->
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-tag text-yellow-600 mr-1"></i>Estado <span class="text-red-600">*</span>
                </label>
                <select name="estado" id="estado" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('estado') border-red-500 @enderror" required>
                    <option value="borrador" {{ old('estado', $ordenesCosecha->estado) == 'borrador' ? 'selected' : '' }}>Borrador</option>
                    <option value="confirmada" {{ old('estado', $ordenesCosecha->estado) == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                    <option value="en_proceso" {{ old('estado', $ordenesCosecha->estado) == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                    <option value="completada" {{ old('estado', $ordenesCosecha->estado) == 'completada' ? 'selected' : '' }}>Completada</option>
                    <option value="cancelada" {{ old('estado', $ordenesCosecha->estado) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
                @error('estado')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notas (campo completo) -->
            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-sticky-note text-gray-600 mr-1"></i>Notas
                </label>
                <textarea name="notes" id="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes', $ordenesCosecha->notes) }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
            <a href="{{ route('ordenes_cosecha.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200">
                <i class="fas fa-save mr-2"></i>Actualizar Orden
            </button>
        </div>
    </form>
</div>
</x-app-layout>