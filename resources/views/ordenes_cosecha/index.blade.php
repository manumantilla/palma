<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Encabezado estilo campo -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div class="flex items-center gap-3">
                <div class="bg-green-700 p-2.5 rounded-full shadow-md">
                    <i class="fas fa-seedling text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-green-800 tracking-wide" style="font-family: 'Segoe UI', 'Georgia', serif;">
                        Órdenes de Cosecha
                    </h1>
                    <p class="text-sm text-amber-700 flex items-center gap-2">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Campo activo
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-green-600 bg-green-50 px-4 py-1.5 rounded-full border border-green-200">
                    <i class="fas fa-tractor mr-1.5"></i> Temporada actual
                </span>
                <button class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2 rounded-full shadow-md transition-all duration-200 flex items-center gap-2 text-sm font-medium">
                    <i class="fas fa-plus-circle"></i> Nueva orden
                </button>
            </div>
        </div>

        <!-- Tabla con estilo rústico -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-green-100/80">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-green-100">
                    <thead class="bg-green-50/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">Ciclo</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">Fecha Programada</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-green-50">
                        @forelse($ordenes as $orden)
                        <tr class="hover:bg-green-50/40 transition duration-150 ease-in-out group">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-900">
                                <span class="bg-green-100 text-green-800 px-2.5 py-0.5 rounded-full text-xs font-mono">#{{ $orden->id }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user-tie text-amber-600 text-xs"></i>
                                    {{ $orden->cliente->name ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-leaf text-green-500 text-xs"></i>
                                    {{ $orden->cicloProductivo->nombre_campana ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <div class="flex items-center gap-2">
                                    <i class="far fa-calendar-alt text-amber-500 text-xs"></i>
                                    {{ \Carbon\Carbon::parse($orden->fecha_programada)->locale('es')->isoFormat('D MMMM [de] Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $estados = [
                                        'borrador' => ['bg-gray-100 text-gray-700', '📄'],
                                        'confirmada' => ['bg-blue-100 text-blue-700', '✅'],
                                        'en_proceso' => ['bg-amber-100 text-amber-700', '⏳'],
                                        'completada' => ['bg-green-100 text-green-700', '🌾'],
                                        'cancelada' => ['bg-red-100 text-red-700', '❌']
                                    ];
                                    $estado = $estados[$orden->estado] ?? ['bg-gray-100 text-gray-700', '📄'];
                                @endphp
                                <span class="px-3 py-1 inline-flex items-center gap-1.5 text-xs leading-4 font-semibold rounded-full {{ $estado[0] }}">
                                    <span>{{ $estado[1] }}</span>
                                    {{ ucfirst($orden->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('ordenes_cosecha.show', $orden) }}" class="text-green-600 hover:text-green-800 transition-colors" title="Ver">
                                        <i class="fas fa-eye">Ver</i>
                                    </a>
                                    <a href="{{ route('ordenes_cosecha.edit', $orden) }}" class="text-amber-600 hover:text-amber-800 transition-colors" title="Editar">
                                        <i class="fas fa-pen">Editar</i>
                                    </a>
                                    <form action="{{ route('ordenes_cosecha.destroy', $orden) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta orden?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition-colors" title="Eliminar">
                                            <i class="fas fa-trash-alt">Eliminar</i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-seedling text-3xl text-green-200 block mb-3"></i>
                                <span class="text-lg font-medium text-gray-400">No hay órdenes de cosecha registradas</span>
                                <p class="text-sm text-gray-400 mt-1">Comienza creando una nueva orden</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pie con paginación y decoración -->
            <div class="bg-green-50/30 px-6 py-4 border-t border-green-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-3 text-sm text-green-700">
                    <span class="flex items-center gap-1.5">
                        <span class="inline-block w-2 h-2 rounded-full bg-green-400"></span>
                        <span class="font-medium">Total:</span> {{ $ordenes->total() }} órdenes
                    </span>
                    <span class="inline-block w-px h-4 bg-green-200"></span>
                    <span class="flex items-center gap-1.5">
                        <span class="inline-block w-2 h-2 rounded-full bg-amber-400"></span>
                        <span class="font-medium">Activas:</span> {{ $ordenes->whereIn('estado', ['confirmada', 'en_proceso'])->count() }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    {{ $ordenes->links() }}
                </div>
            </div>
        </div>

        <!-- Pequeños puntos decorativos (estilo agrícola) -->
        <div class="mt-6 flex justify-center gap-2 opacity-40">
            <span class="inline-block w-2 h-2 rounded-full bg-green-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-amber-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-green-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-amber-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-green-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-amber-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-green-300"></span>
        </div>

    </div>
</x-app-layout>