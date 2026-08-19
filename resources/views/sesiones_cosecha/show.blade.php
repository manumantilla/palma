
<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center">
            <a href="{{ route('sesiones-cosecha.index') }}" class="text-green-700 hover:text-green-900 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-green-800 flex items-center">
                <svg class="w-8 h-8 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Sesión de Cosecha
            </h1>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('sesiones-cosecha.edit', $sesionCosecha) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-lg transition-all transform hover:scale-105">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
            <a href="{{ route('contenedores.create', ['sesion_cosecha_id' => $sesionCosecha->id]) }}" 
               class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow-lg transition-all transform hover:scale-105">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Contenedor
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Tarjeta de Información General -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-green-600">
                <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-green-200">
                    <h2 class="text-xl font-bold text-green-800 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Información General
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Columna Izquierda -->
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Fecha de Cosecha</p>
                                <p class="text-lg font-semibold text-gray-800">
                                    {{ $sesionCosecha->fecha->format('d/m/Y') }}
                                    <span class="text-sm font-normal text-gray-500 ml-2">
                                        ({{ $sesionCosecha->fecha->diffForHumans() }})
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Estado</p>
                                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full 
                                    {{ $sesionCosecha->estado == 'abierta' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $sesionCosecha->estado == 'cerrada' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $sesionCosecha->estado == 'cancelada' ? 'bg-red-100 text-red-800' : '' }}">
                                    <span class="w-2 h-2 rounded-full mr-2 inline-block 
                                        {{ $sesionCosecha->estado == 'abierta' ? 'bg-green-500' : '' }}
                                        {{ $sesionCosecha->estado == 'cerrada' ? 'bg-yellow-500' : '' }}
                                        {{ $sesionCosecha->estado == 'cancelada' ? 'bg-red-500' : '' }}">
                                    </span>
                                    {{ ucfirst($sesionCosecha->estado) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Responsable</p>
                                <p class="text-lg font-semibold text-gray-800 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $sesionCosecha->responsable->name ?? 'No asignado' }}
                                </p>
                            </div>
                        </div>

                        <!-- Columna Derecha -->
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Horario</p>
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-sm font-medium">{{ $sesionCosecha->hora_inicio ? \Carbon\Carbon::parse($sesionCosecha->hora_inicio)->format('H:i') : '--:--' }}</span>
                                    </div>
                                    <span class="text-gray-400">→</span>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-sm font-medium">{{ $sesionCosecha->hora_fin ? \Carbon\Carbon::parse($sesionCosecha->hora_fin)->format('H:i') : '--:--' }}</span>
                                    </div>
                                    @if($sesionCosecha->hora_inicio && $sesionCosecha->hora_fin)
                                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                            {{ \Carbon\Carbon::parse($sesionCosecha->hora_inicio)->diffInHours(\Carbon\Carbon::parse($sesionCosecha->hora_fin)) }}h
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Orden de Cosecha</p>
                                <p class="text-lg font-semibold text-gray-800">
                                    @if($sesionCosecha->ordenCosecha)
                                        <span class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            {{ $sesionCosecha->ordenCosecha->codigo }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">No asignada</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Evento de Campo</p>
                                <p class="text-lg font-semibold text-gray-800">
                                    @if($sesionCosecha->eventoCampo)
                                        <span class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $sesionCosecha->eventoCampo->nombre }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">No asignado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Métricas de Producción -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tarjeta de Recolectores -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-600">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Recolectores</h3>
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="text-4xl font-bold text-blue-700">{{ $sesionCosecha->numero_recolectores ?? 0 }}</p>
                        <p class="text-sm text-gray-500 mt-1">Recolectores en campo</p>
                    </div>
                </div>

                <!-- Tarjeta de Meta Diaria -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-amber-600">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Meta Diaria</h3>
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="text-4xl font-bold text-amber-700">{{ number_format($sesionCosecha->meta_kg_dia ?? 0, 2) }}</p>
                        <p class="text-sm text-gray-500 mt-1">Kilogramos meta</p>
                    </div>
                </div>
            </div>

            <!-- Total Recolectado con Barra de Progreso -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-600">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Total Recolectado</h3>
                        <p class="text-sm text-gray-500">Progreso de la cosecha</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-green-700">{{ number_format($sesionCosecha->total_recolectado_kg ?? 0, 2) }} kg</p>
                        @php
                            $meta = $sesionCosecha->meta_kg_dia ?? 1;
                            $recolectado = $sesionCosecha->total_recolectado_kg ?? 0;
                            $porcentaje = min(($recolectado / $meta) * 100, 100);
                        @endphp
                        <p class="text-sm text-gray-500">{{ number_format($porcentaje, 1) }}% de la meta</p>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-1000 ease-out bg-gradient-to-r from-green-500 to-green-700" 
                         style="width: {{ $porcentaje }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>0 kg</span>
                    <span>{{ number_format($sesionCosecha->meta_kg_dia ?? 0, 2) }} kg</span>
                </div>
            </div>

            <!-- Contenedores Asociados -->
            @if(isset($sesionCosecha->contenedores) && $sesionCosecha->contenedores->count() > 0)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-purple-600">
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-purple-200">
                    <h3 class="text-lg font-bold text-purple-800 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Contenedores Asociados ({{ $sesionCosecha->contenedores->count() }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Calidad</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peso Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($sesionCosecha->contenedores as $contenedor)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $contenedor->nombre }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $contenedor->estado == 'abierta' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $contenedor->estado == 'cerrada' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $contenedor->estado == 'despachada' ? 'bg-blue-100 text-blue-800' : '' }}">
                                        {{ ucfirst($contenedor->estado) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ ucfirst($contenedor->calidad ?? 'N/A') }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ number_format($contenedor->peso_total, 2) }} kg</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('contenedores.show', $contenedor) }}" 
                                       class="text-green-600 hover:text-green-900 font-medium text-sm">
                                        Ver
                                    </a>
                                </td>
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
            <!-- Resumen Rápido -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-600">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Resumen
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                        <span class="text-sm text-gray-500">Peso Promedio</span>
                        <span class="font-semibold">
                            @php
                                $contenedores = $sesionCosecha->contenedores ?? collect();
                                $promedio = $contenedores->count() > 0 ? $contenedores->avg('peso_total') : 0;
                            @endphp
                            {{ number_format($promedio, 2) }} kg
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Total Recolectado</span>
                        <span class="font-bold text-green-700 text-lg">{{ number_format($sesionCosecha->total_recolectado_kg ?? 0, 2) }} kg</span>
                    </div>
                </div>
            </div>

            <!-- Ciclo Productivo -->
            @if($sesionCosecha->ordenCosecha && $sesionCosecha->ordenCosecha->cicloProductivo)
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-amber-600">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Ciclo Productivo
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Cultivo</span>
                        <span class="font-medium">{{ $sesionCosecha->ordenCosecha->cicloProductivo->nombre_campana   ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Lote</span>
                        <span class="font-medium">{{ $sesionCosecha->ordenCosecha->cicloProductivo->lote->nombre_lote ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Fecha Inicio</span>
                        <span class="font-medium">{{ $sesionCosecha->ordenCosecha->cicloProductivo->fecha_inicio ? \Carbon\Carbon::parse($sesionCosecha->ordenCosecha->cicloProductivo->fecha_inicio)->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Metadata -->
            <div class="bg-gray-50 rounded-xl p-4 text-xs text-gray-500 space-y-1">
                <p><span class="font-medium">UUID:</span> {{ $sesionCosecha->id }}</p>
                <p><span class="font-medium">Creado:</span> {{ $sesionCosecha->created_at->format('d/m/Y H:i') }}</p>
                <p><span class="font-medium">Actualizado:</span> {{ $sesionCosecha->updated_at->format('d/m/Y H:i') }}</p>
            </div>

            <!-- Acciones Rápidas -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-gray-400">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Acciones Rápidas
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('contenedores.create', ['sesion_cosecha_id' => $sesionCosecha->id]) }}" 
                       class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar Contenedor
                    </a>
                    <a href="{{ route('sesiones-cosecha.edit', $sesionCosecha) }}" 
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar Sesión
                    </a>
                    @if($sesionCosecha->estado == 'abierta')
       
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>