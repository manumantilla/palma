
<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center">
            <a href="{{ route('contenedores.index') }}" class="text-green-700 hover:text-green-900 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-green-800 flex items-center">
                <svg class="w-8 h-8 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                {{ $contenedor->nombre }}
            </h1>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('contenedores.edit', $contenedor) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-lg transition-all transform hover:scale-105">
                Editar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Tarjeta de Información -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-600">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Información del Contenedor</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Estado</p>
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            {{ $contenedor->estado == 'abierta' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $contenedor->estado == 'cerrada' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $contenedor->estado == 'despachada' ? 'bg-blue-100 text-blue-800' : '' }}">
                            {{ ucfirst($contenedor->estado) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Calidad</p>
                        <p class="font-semibold">{{ ucfirst($contenedor->calidad ?? 'No definida') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Variedad</p>
                        <p class="font-semibold">{{ $contenedor->variedad ?? 'No especificada' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Calibre / Talla</p>
                        <p class="font-semibold">{{ ucfirst($contenedor->calibre_talla ?? 'No especificada') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tipo de Destino</p>
                        <p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $contenedor->tipo_destino)) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Sesión de Cosecha</p>
                        <p class="font-semibold">{{ $contenedor->sesionCosecha->nombre ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Pesos -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-600">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Registro de Pesos</h2>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <p class="text-sm text-gray-500">Peso Tara</p>
                        <p class="text-2xl font-bold text-green-700">{{ number_format($contenedor->peso_tara, 2) }} kg</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <p class="text-sm text-gray-500">Kilos Acumulados</p>
                        <p class="text-2xl font-bold text-blue-700">{{ number_format($contenedor->kilos_acumulados, 2) }} kg</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4 text-center">
                        <p class="text-sm text-gray-500">Peso Total</p>
                        <p class="text-2xl font-bold text-purple-700">{{ number_format($contenedor->peso_total, 2) }} kg</p>
                    </div>
                </div>
            </div>

            <!-- Movimientos de Clasificación -->
            @if($contenedor->movimientosClasificacion->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-600">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Movimientos de Clasificación</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Operario</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Calidad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($contenedor->movimientosClasificacion as $movimiento)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-2 text-sm">{{ $movimiento->operario->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-sm font-medium">{{ number_format($movimiento->cantidad, 2) }} kg</td>
                                <td class="px-4 py-2 text-sm">{{ ucfirst($movimiento->calidad) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Cliente y Orden -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-600">
                <h3 class="font-semibold text-gray-700 mb-3">Asignaciones</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Cliente</p>
                        <p class="font-medium">{{ $contenedor->cliente->name ?? 'Sin asignar' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Orden de Pedido</p>
                        <p class="font-medium">{{ $contenedor->ordenPedido->codigo ?? 'Sin orden' }}</p>
                        @if($contenedor->ordenPedido)
                            <p class="text-xs text-gray-500">{{ $contenedor->ordenPedido->producto }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mermas -->
            @if($contenedor->mermas->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-600">
                <h3 class="font-semibold text-gray-700 mb-3">Registro de Mermas</h3>
                <div class="space-y-2">
                    @foreach($contenedor->mermas as $merma)
                    <div class="bg-red-50 rounded-lg p-3">
                        <p class="font-medium text-red-700">{{ number_format($merma->cantidad, 2) }} kg</p>
                        <p class="text-xs text-gray-500">{{ $merma->created_at->format('d/m/Y H:i') }}</p>
                        @if($merma->motivo)
                            <p class="text-xs text-gray-600">{{ $merma->motivo }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Metadata -->
            <div class="bg-gray-50 rounded-xl p-4 text-xs text-gray-500">
                <p>Creado: {{ $contenedor->created_at->format('d/m/Y H:i') }}</p>
                <p>Actualizado: {{ $contenedor->updated_at->format('d/m/Y H:i') }}</p>
                <p>UUID: {{ $contenedor->uuid }}</p>
            </div>
        </div>
    </div>
</div>

</x-app-layout>