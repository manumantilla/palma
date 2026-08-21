<x-app-layout>
<div class="container mx-auto px-4 py-6 max-w-7xl">
    
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Órdenes de Cosecha</h1>
            <p class="text-sm text-gray-500">Planificación y seguimiento de recolecciones en campo</p>
        </div>
        <div class="mt-4 md:mt-0">
          
        </div>
    </div>

    <!-- Panel de Filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <form method="GET" action="{{ route('ordenes_cosecha.index') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            
            <!-- Búsqueda General -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Buscar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Variedad, notas..." class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Estado</label>
                <select name="estado" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Todos</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                    <option value="completada" {{ request('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                    <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>

            <!-- Lote -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Lote</label>
                <select name="lote_cultivo_id" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Todos</option>
                    @foreach($lotes as $lote)
                        <option value="{{ $lote->id }}" {{ request('lote_cultivo_id') == $lote->id ? 'selected' : '' }}>{{ $lote->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Fecha Desde -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-3 rounded-lg text-sm transition">
                    Filtrar
                </button>
                <a href="{{ route('ordenes_cosecha.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium py-2 px-3 rounded-lg text-sm transition" title="Limpiar Filtros">
                    🔄
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Resultados -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-4">ID / Variedad</th>
                        <th class="py-3 px-4">Lote / Zona</th>
                        <th class="py-3 px-4">Prog. / Cosechado</th>
                        <th class="py-3 px-4">Fecha Prog.</th>
                        <th class="py-3 px-4">Responsable</th>
                        <th class="py-3 px-4 text-center">Estado</th>
                        <th class="py-3 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($ordenes as $orden)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-3 px-4 font-medium text-gray-900">
                                #{{ $orden->id }} - {{ $orden->variedad_requerida ?? 'N/A' }}
                                <div class="text-xs text-gray-400 font-normal">{{ $orden->cicloProductivo->nombre_campana ?? '' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                {{ $orden->loteCultivo->nombre ?? 'N/A' }}
                                @if($orden->loteZona)
                                    <span class="text-xs text-gray-400 block">({{ $orden->loteZona->nombre }})</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-semibold text-gray-800">{{ number_format($orden->cantidad_planificada_kg ?? 0, 1) }} kg</span>
                                <span class="text-xs text-gray-400 block">Rec: {{ number_format($orden->cantidad_recolectada_kg ?? 0, 1) }} kg</span>
                            </td>
                            <td class="py-3 px-4">
                                {{ $orden->fecha_programada ? $orden->fecha_programada->format('d/m/Y') : 'Sin fecha' }}
                            </td>
                            <td class="py-3 px-4">
                                {{ $orden->responsable->name ?? 'Unassigned' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @php
                                    $badgeClasses = [
                                        'pendiente' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                        'en_proceso' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'completada' => 'bg-green-50 text-green-700 border-green-200',
                                        'cancelada' => 'bg-red-50 text-red-700 border-red-200',
                                    ][$orden->estado] ?? 'bg-gray-50 text-gray-600 border-gray-200';
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium border {{ $badgeClasses }}">
                                    {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <a href="{{ route('ordenes_cosecha.show', $orden) }}" class="text-blue-600 hover:text-blue-800 font-medium text-xs">Ver</a>
                                <a href="{{ route('ordenes_cosecha.edit', $orden) }}" class="text-gray-600 hover:text-gray-800 font-medium text-xs">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400 text-sm">
                                No se encontraron órdenes de cosecha que coincidan con los filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($ordenes->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $ordenes->links() }}
            </div>
        @endif
    </div>
</div>
</x-app-layout>