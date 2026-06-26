<x-app-layout>
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-list text-blue-600 mr-2"></i>Ordenes de Cosecha
        </h2>
    </div>

    <!-- Tabla de órdenes -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciclo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Programada</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($ordenes as $orden)
                <tr class="hover:bg-gray-50 transition duration-200">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $orden->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $orden->cliente->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $orden->cicloProductivo->nombre ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ \Carbon\Carbon::parse($orden->fecha_programada)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $estados = [
                                'borrador' => 'bg-gray-200 text-gray-800',
                                'confirmada' => 'bg-blue-200 text-blue-800',
                                'en_proceso' => 'bg-yellow-200 text-yellow-800',
                                'completada' => 'bg-green-200 text-green-800',
                                'cancelada' => 'bg-red-200 text-red-800'
                            ];
                        @endphp
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $estados[$orden->estado] ?? 'bg-gray-200 text-gray-800' }}">
                            {{ ucfirst($orden->estado) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('ordenes_cosecha.show', $orden) }}" class="text-blue-600 hover:text-blue-900" title="Ver">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('ordenes_cosecha.edit', $orden) }}" class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('ordenes_cosecha.destroy', $orden) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta orden?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay órdenes de cosecha registradas</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $ordenes->links() }}
    </div>
</div>
</x-app-layout>