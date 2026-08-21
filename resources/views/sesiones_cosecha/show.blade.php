<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-green-800 leading-tight">
                🌾 Detalle de Sesión de Cosecha #{{ $sesion->id }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('sesiones-cosecha.edit', $sesion) }}" 
                   class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                    ✏️ Editar
                </a>
                <a href="{{ route('sesiones-cosecha.index') }}" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg transition duration-200">
                    ← Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg border border-green-100 overflow-hidden">
                <!-- Estado -->
                <div class="bg-green-50 px-6 py-4 border-b border-green-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">🌾</span>
                            <span class="font-bold text-green-800">Sesión de Cosecha #{{ $sesion->id }}</span>
                        </div>
                        <div>
                            @if($sesion->estado == 'abierta')
                                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    🟢 Abierta
                                </span>
                            @else
                                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    🔴 Cerrada
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Información -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="bg-green-50 rounded-lg p-4">
                                <h3 class="text-sm font-medium text-green-600 mb-2">📅 Información General</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Fecha:</span>
                                        <span class="font-medium">{{ \Carbon\Carbon::parse($sesion->fecha)->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Responsable:</span>
                                        <span class="font-medium">{{ $sesion->responsable->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Recolectores:</span>
                                        <span class="font-medium">{{ $sesion->numero_recolectores ?? 'No especificado' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50 rounded-lg p-4">
                                <h3 class="text-sm font-medium text-blue-600 mb-2">⏰ Horario</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Hora Inicio:</span>
                                        <span class="font-medium">{{ $sesion->hora_inicio ? date('h:i A', strtotime($sesion->hora_inicio)) : 'No definida' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Hora Fin:</span>
                                        <span class="font-medium">{{ $sesion->hora_fin ? date('h:i A', strtotime($sesion->hora_fin)) : 'No definida' }}</span>
                                    </div>
                                    @if($sesion->hora_inicio && $sesion->hora_fin)
                                        <div class="flex justify-between text-green-700">
                                            <span class="font-medium">Duración:</span>
                                            <span class="font-medium">
                                                {{ \Carbon\Carbon::parse($sesion->hora_inicio)->diffInHours($sesion->hora_fin) }} horas
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="bg-yellow-50 rounded-lg p-4">
                                <h3 class="text-sm font-medium text-yellow-600 mb-2">📊 Producción</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Meta KG/Día:</span>
                                        <span class="font-medium">{{ number_format($sesion->meta_kg_dia ?? 0, 2) }} kg</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Recolectado:</span>
                                        <span class="font-medium text-green-700">{{ number_format($sesion->total_recolectado_kg ?? 0, 2) }} kg</span>
                                    </div>
                                    @if($sesion->meta_kg_dia > 0)
                                        <div class="flex justify-between text-green-700">
                                            <span class="font-medium">Cumplimiento:</span>
                                            <span class="font-medium">
                                                {{ number_format(($sesion->total_recolectado_kg / $sesion->meta_kg_dia) * 100, 1) }}%
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-green-600 h-2.5 rounded-full" 
                                                 style="width: {{ min(100, ($sesion->total_recolectado_kg / $sesion->meta_kg_dia) * 100) }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="bg-purple-50 rounded-lg p-4">
                                <h3 class="text-sm font-medium text-purple-600 mb-2">📌 Referencias</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Orden de Cosecha:</span>
                                        <span class="font-medium">{{ $sesion->ordenCosecha->nombre ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Evento de Campo:</span>
                                        <span class="font-medium">{{ $sesion->eventoCampo->nombre ?? 'N/A' }}</span>
                                    </div>
                                    @if($sesion->eventoCampo && $sesion->eventoCampo->ubicacion)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Ubicación:</span>
                                            <span class="font-medium">{{ $sesion->eventoCampo->ubicacion }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fechas de creación y actualización -->
                    <div class="mt-6 pt-4 border-t border-green-200 text-xs text-gray-500">
                        <div class="flex justify-between">
                            <span>Creado: {{ $sesion->created_at->format('d/m/Y H:i:s') }}</span>
                            <span>Actualizado: {{ $sesion->updated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
