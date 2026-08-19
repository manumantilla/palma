
<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="{{ route('contenedores.index') }}" class="text-green-700 hover:text-green-900 mr-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-3xl font-bold text-green-800 flex items-center">
            <svg class="w-8 h-8 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar: {{ $contenedor->nombre }}
        </h1>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-xl shadow-lg p-8 max-w-4xl mx-auto border-l-4 border-yellow-600">
        <form action="{{ route('contenedores.update', $contenedor) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Sesión y Cliente -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="sesion_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Sesión de Cosecha *
                    </label>
                    <select name="sesion_id" id="sesion_id" 
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('sesion_id') border-red-500 @enderror">
                        <option value="">Seleccionar sesión</option>
                        @foreach($sesiones as $sesion)
                            <option value="{{ $sesion->id }}" {{ old('sesion_id', $contenedor->sesion_id) == $sesion->id ? 'selected' : '' }}>
                                {{ $sesion->nombre }} - {{ $sesion->fecha_inicio }}
                            </option>
                        @endforeach
                    </select>
                    @error('sesion_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="cliente_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Cliente
                    </label>
                    <select name="cliente_id" id="cliente_id" 
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('cliente_id') border-red-500 @enderror">
                        <option value="">Sin cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ old('cliente_id', $contenedor->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('cliente_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Orden Pedido y Nombre -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="orden_pedido_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Orden de Pedido
                    </label>
                    <select name="orden_pedido_id" id="orden_pedido_id" 
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('orden_pedido_id') border-red-500 @enderror">
                        <option value="">Sin orden</option>
                        @foreach($ordenes as $orden)
                            <option value="{{ $orden->id }}" {{ old('orden_pedido_id', $contenedor->orden_pedido_id) == $orden->id ? 'selected' : '' }}>
                                {{ $orden->codigo }} - {{ $orden->producto }}
                            </option>
                        @endforeach
                    </select>
                    @error('orden_pedido_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre del Contenedor *
                    </label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $contenedor->nombre) }}"
                           class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('nombre') border-red-500 @enderror"
                           placeholder="Ej: Contenedor A-01">
                    @error('nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Estado y Variedad -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                        Estado *
                    </label>
                    <select name="estado" id="estado" 
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('estado') border-red-500 @enderror">
                        <option value="abierta" {{ old('estado', $contenedor->estado) == 'abierta' ? 'selected' : '' }}>Abierta</option>
                        <option value="cerrada" {{ old('estado', $contenedor->estado) == 'cerrada' ? 'selected' : '' }}>Cerrada</option>
                        <option value="despachada" {{ old('estado', $contenedor->estado) == 'despachada' ? 'selected' : '' }}>Despachada</option>
                    </select>
                    @error('estado')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="variedad" class="block text-sm font-medium text-gray-700 mb-1">
                        Variedad
                    </label>
                    <input type="text" name="variedad" id="variedad" value="{{ old('variedad', $contenedor->variedad) }}"
                           class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('variedad') border-red-500 @enderror"
                           placeholder="Ej: Hass, Fuerte, Bacon">
                    @error('variedad')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Calidad y Destino -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="calidad" class="block text-sm font-medium text-gray-700 mb-1">
                        Calidad
                    </label>
                    <select name="calidad" id="calidad" 
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('calidad') border-red-500 @enderror">
                        <option value="">Seleccionar calidad</option>
                        <option value="extra" {{ old('calidad', $contenedor->calidad) == 'extra' ? 'selected' : '' }}>Extra</option>
                        <option value="primera" {{ old('calidad', $contenedor->calidad) == 'primera' ? 'selected' : '' }}>Primera</option>
                        <option value="segunda" {{ old('calidad', $contenedor->calidad) == 'segunda' ? 'selected' : '' }}>Segunda</option>
                        <option value="industria" {{ old('calidad', $contenedor->calidad) == 'industria' ? 'selected' : '' }}>Industria</option>
                        <option value="descarte" {{ old('calidad', $contenedor->calidad) == 'descarte' ? 'selected' : '' }}>Descarte</option>
                    </select>
                    @error('calidad')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tipo_destino" class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de Destino *
                    </label>
                    <select name="tipo_destino" id="tipo_destino" 
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('tipo_destino') border-red-500 @enderror">
                        <option value="exportacion" {{ old('tipo_destino', $contenedor->tipo_destino) == 'exportacion' ? 'selected' : '' }}>Exportación</option>
                        <option value="mercado_local" {{ old('tipo_destino', $contenedor->tipo_destino) == 'mercado_local' ? 'selected' : '' }}>Mercado Local</option>
                        <option value="industria" {{ old('tipo_destino', $contenedor->tipo_destino) == 'industria' ? 'selected' : '' }}>Industria</option>
                        <option value="consumo_interno" {{ old('tipo_destino', $contenedor->tipo_destino) == 'consumo_interno' ? 'selected' : '' }}>Consumo Interno</option>
                        <option value="descarte" {{ old('tipo_destino', $contenedor->tipo_destino) == 'descarte' ? 'selected' : '' }}>Descarte</option>
                    </select>
                    @error('tipo_destino')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Calibre y Pesos -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="calibre_talla" class="block text-sm font-medium text-gray-700 mb-1">
                        Calibre / Talla
                    </label>
                    <select name="calibre_talla" id="calibre_talla" 
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('calibre_talla') border-red-500 @enderror">
                        <option value="">Seleccionar talla</option>
                        <option value="pequeño" {{ old('calibre_talla', $contenedor->calibre_talla) == 'pequeño' ? 'selected' : '' }}>Pequeño</option>
                        <option value="mediano" {{ old('calibre_talla', $contenedor->calibre_talla) == 'mediano' ? 'selected' : '' }}>Mediano</option>
                        <option value="grande" {{ old('calibre_talla', $contenedor->calibre_talla) == 'grande' ? 'selected' : '' }}>Grande</option>
                        <option value="jumbo" {{ old('calibre_talla', $contenedor->calibre_talla) == 'jumbo' ? 'selected' : '' }}>Jumbo</option>
                    </select>
                    @error('calibre_talla')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="peso_tara" class="block text-sm font-medium text-gray-700 mb-1">
                        Peso Tara (kg) *
                    </label>
                    <input type="number" step="0.01" name="peso_tara" id="peso_tara" 
                           value="{{ old('peso_tara', $contenedor->peso_tara) }}"
                           class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('peso_tara') border-red-500 @enderror"
                           placeholder="0.00">
                    @error('peso_tara')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="kilos_acumulados" class="block text-sm font-medium text-gray-700 mb-1">
                        Kilos Acumulados *
                    </label>
                    <input type="number" step="0.01" name="kilos_acumulados" id="kilos_acumulados" 
                           value="{{ old('kilos_acumulados', $contenedor->kilos_acumulados) }}"
                           class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('kilos_acumulados') border-red-500 @enderror"
                           placeholder="0.00">
                    @error('kilos_acumulados')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Kilos Merma Acumulada -->
            <div>
                <label for="kilos_merma_acumulada" class="block text-sm font-medium text-gray-700 mb-1">
                    Kilos Merma Acumulada *
                </label>
                <input type="number" step="0.01" name="kilos_merma_acumulada" id="kilos_merma_acumulada" 
                       value="{{ old('kilos_merma_acumulada', $contenedor->kilos_merma_acumulada ?? 0) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('kilos_merma_acumulada') border-red-500 @enderror"
                       placeholder="0.00">
                @error('kilos_merma_acumulada')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('contenedores.index') }}" 
                   class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-8 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-bold rounded-lg shadow-lg transition-all transform hover:scale-105">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v16h16V4H4zm2 2h12v12H6V6zm2 2v8h8V8H8z"/>
                    </svg>
                    Actualizar Contenedor
                </button>
            </div>
        </form>
    </div>
</div>

</x-app-layout>