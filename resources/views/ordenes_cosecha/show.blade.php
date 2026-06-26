<x-app-layout>
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-file-alt text-blue-600 mr-2"></i>Orden #{{ $ordenesCosecha->id }}
        </h2>
        <div class="flex space-x-3">
            <a href="{{ route('ordenes_cosecha.edit', $ordenesCosecha) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-edit mr-2"></i>Editar
            </a>
            <a href="{{ route('ordenes_cosecha.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-1"></i>Volver
            </a>
        </div>
    </div>

    <!-- Estado -->
    <div class="mb-6">
        @php
            $estados = [
                'borrador' => 'bg-gray-200 text-gray-800',
                'confirmada' => 'bg-blue-200 text-blue-800',
                'en_proceso' => 'bg-yellow-200 text-yellow-800',
                'completada' => 'bg-green-200 text-green-800',
                'cancelada' => 'bg-red-200 text-red-800'
            ];
        @endphp
        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full {{ $estados[$ordenesCosecha->estado] ?? 'bg-gray-200 text-gray-800' }}">
            <i class="fas fa-tag mr-2"></i>{{ ucfirst($ordenesCosecha->estado) }}
        </span>
    </div>

    <!-- Información Principal -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Información del Cliente</h3>
                <p class="text-lg font-semibold text-gray-800">{{ $ordenesCosecha->cliente->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600">ID: {{ $ordenesCosecha->cliente->id ?? 'N/A' }}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Ciclo Productivo</h3>
                <p class="text-lg font-semibold text-gray-800">{{ $ordenesCosecha->cicloProductivo->nombre ?? 'N/A' }}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Responsable</h3>
                <p class="text-lg font-semibold text-gray-800">{{ $ordenesCosecha->responsable->name ?? 'No asignado' }}</p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Fechas</h3>
                <div class="space-y-1">
                    <p><span class="font-medium">Programada:</span> {{ \Carbon\Carbon::parse($ordenesCosecha->fecha_programada)->format('d/m/Y') }}</p>
                    <p><span class="font-medium">Entrega:</span> {{ \Carbon\Carbon::parse($ordenesCosecha->fecha_entrega)->format('d/m/Y') }}</p>
                    @if($ordenesCosecha->fecha_inicio)
                        <p><span class="font-medium">Inicio:</span> {{ \Carbon\Carbon::parse($ordenesCosecha->fecha_inicio)->format('d/m/Y') }}</p>
                    @endif
                    @if($ordenesCosecha->fecha_fin)
                        <p><span class="font-medium">Fin:</span> {{ \Carbon\Carbon::parse($ordenesCosecha->fecha_fin)->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Cantidades -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <h4 class="text-sm font-medium text-blue-600 mb-1">Cantidad Solicitada</h4>
            <p class="text-2xl font-bold text-blue-800">{{ number_format($ordenesCosecha->cantidad_solicitada_kg ?? 0, 2) }} kg</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
            <h4 class="text-sm font-medium text-purple-600 mb-1">Cantidad Planificada</h4>
            <p class="text-2xl font-bold text-purple-800">{{ number_format($ordenesCosecha->cantidad_planificada_kg ?? 0, 2) }} kg</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <h4 class="text-sm font-medium text-green-600 mb-1">Cantidad Recolectada</h4>
            <p class="text-2xl font-bold text-green-800">{{ number_format($ordenesCosecha->cantidad_recolectada_kg ?? 0, 2) }} kg</p>
        </div>
    </div>

    <!-- Información Adicional -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        @if($ordenesCosecha->loteCultivo)
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Lote Cultivo</h3>
            <p class="text-lg font-semibold text-gray-800">{{ $ordenesCosecha->loteCultivo->nombre ?? 'N/A' }}</p>
        </div>
        @endif

        @if($ordenesCosecha->loteZona)
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Zona de Manejo</h3>
            <p class="text-lg font-semibold text-gray-800">{{ $ordenesCosecha->loteZona->nombre ?? 'N/A' }}</p>
        </div>
        @endif

        @if($ordenesCosecha->variedad_requerida)
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Variedad Requerida</h3>
            <p class="text-lg font-semibold text-gray-800">{{ $ordenesCosecha->variedad_requerida }}</p>
        </div>
        @endif

        @if($ordenesCosecha->notes)
        <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Notas</h3>
            <p class="text-gray-700">{{ $ordenesCosecha->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Información de Auditoría -->
    <div class="mt-6 pt-4 border-t border-gray-200">
        <div class="flex justify-between text-sm text-gray-500">
            <span>Creado: {{ $ordenesCosecha->created_at->format('d/m/Y H:i') }}</span>
            <span>Actualizado: {{ $ordenesCosecha->updated_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>
</div>
</x-app-layout>