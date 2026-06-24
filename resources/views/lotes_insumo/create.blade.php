<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ingresar Nuevo Lote a Bodega') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <strong class="font-bold">¡Vaya! Algo salió mal.</strong>
                        <span class="block sm:inline">Por favor revisa los campos abajo.</span>
                    </div>
                @endif

                <form action="{{ route('lotes-insumo.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div>
                            <label for="insumo_id" class="block text-sm font-medium text-gray-700">Insumo / Producto *</label>
                            <select id="insumo_id" name="insumo_id" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Seleccione un Insumo --</option>
                                @foreach($insumos as $insumo)
                                    <option value="{{ $insumo->id }}" {{ old('insumo_id') == $insumo->id ? 'selected' : '' }}>
                                        {{ $insumo->nombre }} (Base: {{ $insumo->unidad_base }})
                                    </option>
                                @endforeach
                            </select>
                            @error('insumo_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="proveedor_id" class="block text-sm font-medium text-gray-700">Proveedor</label>
                            <select id="proveedor_id" name="proveedor_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Seleccione un Proveedor (Opcional) --</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                        {{ $proveedor->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('proveedor_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="codigo_lote" class="block text-sm font-medium text-gray-700">Código del Lote *</label>
                            <input type="text" name="codigo_lote" id="codigo_lote" value="{{ old('codigo_lote') }}" required placeholder="Ej: LOT-2026-XYZ" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @error('codigo_lote') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="ubicacion_bodega" class="block text-sm font-medium text-gray-700">Ubicación en Bodega</label>
                            <input type="text" name="ubicacion_bodega" id="ubicacion_bodega" value="{{ old('ubicacion_bodega') }}" placeholder="Ej: Estante B, Fila 3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @error('ubicacion_bodega') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="fecha_ingreso" class="block text-sm font-medium text-gray-700">Fecha de Ingreso *</label>
                            <input type="date" name="fecha_ingreso" id="fecha_ingreso" value="{{ old('fecha_ingreso', date('Y-m-d')) }}" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @error('fecha_ingreso') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700">Fecha de Vencimiento *</label>
                            <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @error('fecha_vencimiento') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="cantidad_inicial" class="block text-sm font-medium text-gray-700">Cantidad Inicial *</label>
                            <input type="number" name="cantidad_inicial" id="cantidad_inicial" step="0.01" value="{{ old('cantidad_inicial') }}" required placeholder="0.00" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @error('cantidad_inicial') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="unidad" class="block text-sm font-medium text-gray-700">Unidad de Medida del Lote *</label>
                            <input type="text" name="unidad" id="unidad" value="{{ old('unidad') }}" required placeholder="Ej: kg, l, unidades, bultos" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @error('unidad') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="costo_unitario" class="block text-sm font-medium text-gray-700">Costo Unitario *</label>
                            <input type="number" name="costo_unitario" id="costo_unitario" step="0.0001" value="{{ old('costo_unitario') }}" required placeholder="0.0000" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @error('costo_unitario') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">Estado del Lote *</label>
                            <select id="estado" name="estado" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>Activo (Disponible)</option>
                                <option value="cuarentena" {{ old('estado') == 'cuarentena' ? 'selected' : '' }}>Cuarentena (Retenido)</option>
                            </select>
                            @error('estado') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="observacion_kardex" class="block text-sm font-medium text-gray-700">Observación para el Kardex (Bitácora)</label>
                        <textarea name="observacion_kardex" id="observacion_kardex" rows="3" placeholder="Ej: Ingreso por factura de compra N° 4550..." class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('observacion_kardex') }}</textarea>
                        @error('observacion_kardex') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-8 flex justify-end space-x-3 border-t pt-4">
                        <a href="{{ route('lotes-insumo.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded shadow-sm text-sm transition">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow-sm text-sm transition">
                            Guardar Lote y Asentar
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>