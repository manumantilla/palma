{{-- resources/views/ordenes_cosecha/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100 shadow-sm">
            <h2 class="text-center font-semibold text-xl text-emerald-800 leading-tight">
                {{ __('Registrar Nueva Orden de Cosecha') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-b border-gray-200">
                
                <form method="POST" action="{{ route('ordenes_cosecha.store') }}">
                    @csrf

                    <input type="hidden" name="ciclo_productivo_id" value="{{ $ciclo->id }}">
                    <input type="hidden" name="lote_cultivo_id" value="{{ is_object($lote) ? $lote->id : $lote }}">

                    <h3 class="text-sm font-bold text-emerald-700 uppercase tracking-wider mb-4 border-b border-emerald-100 pb-1">1. Contexto de la Orden</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Ciclo Productivo Asignado</label>
                            <input type="text" disabled value="{{ $ciclo->nombre_campana }}" class="mt-1 block w-full rounded-md border-gray-200 bg-gray-50 text-gray-600 shadow-sm text-sm font-semibold">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Lote de Cultivo</label>
                            <input type="text" disabled value="{{ is_object($lote) ? $lote->nombre : 'Lote ID: '.$lote }}" class="mt-1 block w-full rounded-md border-gray-200 bg-gray-50 text-gray-600 shadow-sm text-sm font-semibold">
                        </div>
                    </div>

                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4 border-b pb-1">2. Asignación y Ubicación</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        
                        <div>
                            <label for="cliente_id" class="block text-sm font-medium text-gray-700">Cliente <span class="text-red-500">*</span></label>
                            <select name="cliente_id" id="cliente_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="">-- Seleccione Cliente --</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->name }}</option>
                                @endforeach
                            </select>
                            @error('cliente_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="responsable_id" class="block text-sm font-medium text-gray-700">Responsable Cosecha</label>
                            <select name="responsable_id" id="responsable_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="">-- Sin Asignar (Opcional) --</option>
                                @foreach($responsables as $resp)
                                    <option value="{{ $resp->id }}" {{ old('responsable_id') == $resp->id ? 'selected' : '' }}>{{ $resp->name }}</option>
                                @endforeach
                            </select>
                            @error('responsable_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="lote_zona_id" class="block text-sm font-medium text-gray-700">Zona de Manejo</label>
                            <select name="lote_zona_id" id="lote_zona_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="">-- Todo el Lote --</option>
                                @foreach($zonas as $zona)
                                    <option value="{{ $zona->id }}" {{ old('lote_zona_id') == $zona->id ? 'selected' : '' }}>{{ $zona->nombre }}</option>
                                @endforeach
                            </select>
                            @error('lote_zona_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4 border-b pb-1">3. Fechas y Estado</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                        
                        <div>
                            <label for="fecha_programada" class="block text-sm font-medium text-gray-700">F. Programada <span class="text-red-500">*</span></label>
                            <input type="date" name="fecha_programada" id="fecha_programada" required value="{{ old('fecha_programada', date('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @error('fecha_programada') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="fecha_entrega" class="block text-sm font-medium text-gray-700">F. Entrega Limite <span class="text-red-500">*</span></label>
                            <input type="date" name="fecha_entrega" id="fecha_entrega" required value="{{ old('fecha_entrega') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @error('fecha_entrega') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">F. Inicio Real</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @error('fecha_inicio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700">F. Fin Real</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @error('fecha_fin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">Estado <span class="text-red-500">*</span></label>
                            <select name="estado" id="estado" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                @foreach(['borrador', 'confirmada', 'en_proceso', 'completada', 'cancelada'] as $est)
                                    <option value="{{ $est }}" {{ old('estado', 'borrador') == $est ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $est)) }}</option>
                                @endforeach
                            </select>
                            @error('estado') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4 border-b pb-1">4. Especificaciones del Producto</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        
                        <div class="md:col-span-1">
                            <label for="variedad_requerida" class="block text-sm font-medium text-gray-700">Variedad Requerida</label>
                            <input type="text" name="variedad_requerida" id="variedad_requerida" placeholder="Ej: Kent, Tommy" value="{{ old('variedad_requerida') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @error('variedad_requerida') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="cantidad_solicitada_kg" class="block text-sm font-medium text-gray-700">Cant. Solicitada (Kg)</label>
                            <input type="number" step="0.01" min="0" name="cantidad_solicitada_kg" id="cantidad_solicitada_kg" placeholder="0.00" value="{{ old('cantidad_solicitada_kg') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @error('cantidad_solicitada_kg') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="cantidad_planificada_kg" class="block text-sm font-medium text-gray-700">Cant. Planificada (Kg)</label>
                            <input type="number" step="0.01" min="0" name="cantidad_planificada_kg" id="cantidad_planificada_kg" placeholder="0.00" value="{{ old('cantidad_planificada_kg') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @error('cantidad_planificada_kg') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="cantidad_recolectada_kg" class="block text-sm font-medium text-gray-700">Cant. Recolectada (Kg)</label>
                            <input type="number" step="0.01" min="0" name="cantidad_recolectada_kg" id="cantidad_recolectada_kg" value="{{ old('cantidad_recolectada_kg', '0.00') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @error('cantidad_recolectada_kg') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notas u Observaciones de Cosecha</label>
                        <textarea name="notes" id="notes" rows="3" placeholder="Añade especificaciones de empaque, calidad o transporte..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('notes') }}</textarea>
                        @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end space-x-3 border-t border-gray-100 pt-4">
                        <a href="{{ route('ordenes_cosecha.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out">
                            Crear Orden
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>