<x-app-layout>  
<div class="container mx-auto px-4 py-6 max-w-7xl">
    
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Sesiones de Cosecha</h1>
            <p class="text-sm text-gray-500">Planificación, control de pesajes y recolección offline en campo</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('sesiones-cosecha.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow inline-flex items-center text-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nueva Sesión
            </a>
        </div>
    </div>

    <!-- Panel de Filtros Dinámicos -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <form method="GET" action="{{ route('sesiones-cosecha.index') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            
            <!-- Estado -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Estado</label>
                <select name="estado" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Todos</option>
                    <option value="abierta" {{ request('estado') == 'abierta' ? 'selected' : '' }}>Abierta</option>
                    <option value="cerrada" {{ request('estado') == 'cerrada' ? 'selected' : '' }}>Cerrada</option>
                </select>
            </div>

            <!-- Orden de Cosecha -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Orden Cosecha</label>
                <select name="orden_cosecha_id" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Todas</option>
                    @foreach($ordenes as $orden)
                        <option value="{{ $orden->id }}" {{ request('orden_cosecha_id') == $orden->id ? 'selected' : '' }}>
                            #{{ $orden->id }} - {{ $orden->variedad_requerida }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Responsable -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Responsable</label>
                <select name="responsable_id" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Todos</option>
                    @foreach($responsables as $resp)
                        <option value="{{ $resp->id }}" {{ request('responsable_id') == $resp->id ? 'selected' : '' }}>
                            {{ $resp->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Fecha Desde -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
            </div>

            <!-- Fecha Hasta -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-3 rounded-lg text-sm transition">
                    Filtrar
                </button>
                <a href="{{ route('sesiones-cosecha.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium py-2 px-3 rounded-lg text-sm transition" title="Limpiar Filtros">
                    🔄
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Sesiones de Cosecha -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Sesión / Fecha</th>
                        <th class="py-3 px-4">Orden / Cultivo</th>
                        <th class="py-3 px-4">Responsable</th>
                        <th class="py-3 px-4">Avance Metas</th>
                        <th class="py-3 px-4">Recolectores</th>
                        <th class="py-3 px-4 text-center">Estado</th>
                        <th class="py-3 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($sesiones as $sesion)
                        <tr class="hover:bg-gray-50/50 transition">
                            <!-- ID y Fecha -->
                            <td class="py-3 px-4 font-medium text-gray-900">
                                #{{ $sesion->id }}
                                <span class="text-xs text-gray-400 block font-normal">
                                    📅 {{ $sesion->fecha ? $sesion->fecha->format('d/m/Y') : 'Sin fecha' }}
                                </span>
                            </td>

                            <!-- Orden y Lote -->
                            <td class="py-3 px-4">
                                @if($sesion->ordenCosecha)
                                    <span class="font-medium text-gray-800">Orden #{{ $sesion->ordenCosecha->id }}</span>
                                    <span class="text-xs text-gray-500 block">
                                        {{ $sesion->ordenCosecha->variedad_requerida ?? 'Variedad N/A' }} 
                                        ({{ $sesion->ordenCosecha->loteCultivo->nombre ?? 'Sin Lote' }})
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">Sin orden asignada</span>
                                @endif
                            </td>

                            <!-- Responsable -->
                            <td class="py-3 px-4">
                                {{ $sesion->responsable->name ?? 'Sin asignar' }}
                            </td>

                            <!-- Avance (Meta vs Real) -->
                            <td class="py-3 px-4">
                                @php 
                                    $meta = $sesion->meta_kg_dia ?? 0;
                                    $recolectado = $sesion->total_recolectado_kg ?? 0;
                                    $porcentaje = $meta > 0 ? min(100, round(($recolectado / $meta) * 100)) : 0;
                                @endphp
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-semibold text-gray-800">{{ number_format($recolectado, 1) }} kg</span>
                                    <span class="text-gray-400">/ {{ number_format($meta, 1) }} kg</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $porcentaje }}%"></div>
                                </div>
                            </td>

                            <!-- Recolectores / Pesajes -->
                            <td class="py-3 px-4 text-xs">
                                <span class="font-medium text-gray-700">👥 {{ $sesion->numero_recolectores ?? 0 }} personas</span>
                                <span class="text-gray-400 block">📦 {{ $sesion->recepciones_campo_count ?? 0 }} pesajes</span>
                            </td>

                            <!-- Estado -->
                            <td class="py-3 px-4 text-center">
                                @php
                                    $badge = $sesion->estado === 'abierta' 
                                        ? 'bg-green-50 text-green-700 border-green-200' 
                                        : 'bg-gray-100 text-gray-600 border-gray-200';
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium border {{ $badge }}">
                                    {{ ucfirst($sesion->estado) }}
                                </span>
                            </td>

                            <!-- Acciones / Botón Modo Campo -->
                            <td class="py-3 px-4 text-right space-x-2">
                                <a href="{{ route('cosecha.preparar_offline', $sesion->id) }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1.5 px-3 rounded text-xs inline-flex items-center shadow-sm transition">
                                    📲 Ir a Campo
                                </a>
                                <a href="{{ route('sesiones-cosecha.show', $sesion->id) }}" class="text-gray-500 hover:text-gray-700 text-xs font-medium">
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400 text-sm">
                                No se encontraron sesiones de cosecha con los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($sesiones->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $sesiones->links() }}
            </div>
        @endif
    </div>
</div>
</x-app-layout>