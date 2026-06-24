<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">📦 Gestión de Insumos</h2>
                        <a href="{{ route('insumos.create') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                            <i class="fas fa-plus mr-2"></i> Nuevo Insumo
                        </a>
                    </div>
                    
                    <!-- Filtros -->
                    <form method="GET" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                            <div>
                                <input type="text" 
                                       name="nombre" 
                                       placeholder="Buscar por nombre..."
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       value="{{ request('nombre') }}">
                            </div>
                            
                            <div>
                                <input type="text" 
                                       name="ingrediente_principal" 
                                       placeholder="Ingrediente principal..."
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       value="{{ request('ingrediente_principal') }}">
                            </div>
                            
                            <div>
                                <select name="categoria_id" class="w-full px-3 py-2 border rounded-lg">
                                    <option value="">Todas las categorías</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <select name="estado" class="w-full px-3 py-2 border rounded-lg">
                                    <option value="">Todos los estados</option>
                                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                            </div>
                            
                            <div>
                                <select name="nivel_toxicidad" class="w-full px-3 py-2 border rounded-lg">
                                    <option value="">Todos los niveles</option>
                                    <option value="bajo" {{ request('nivel_toxicidad') == 'bajo' ? 'selected' : '' }}>Bajo</option>
                                    <option value="medio" {{ request('nivel_toxicidad') == 'medio' ? 'selected' : '' }}>Medio</option>
                                    <option value="alto" {{ request('nivel_toxicidad') == 'alto' ? 'selected' : '' }}>Alto</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex justify-between mt-4">
                            <div class="flex space-x-2">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="requiere_refrigeracion" value="1" {{ request('requiere_refrigeracion') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600">
                                    <span class="text-sm text-gray-700">Requiere refrigeración</span>
                                </label>
                            </div>
                            <div class="flex space-x-2">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                                    <i class="fas fa-filter mr-2"></i> Filtrar
                                </button>
                                <a href="{{ route('insumos.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg">
                                    <i class="fas fa-undo mr-2"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Tabla -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Categoría</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Unidad Base</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Toxicidad</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Stock Mínimo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($insumos as $insumo)
                                    <tr class="hover:bg-gray-50 border-b">
                                        <td class="px-6 py-4 text-sm">{{ $insumo->id }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900">{{ $insumo->nombre }}</div>
                                            @if($insumo->ingrediente_principal)
                                                <div class="text-xs text-gray-500">Ing: {{ $insumo->ingrediente_principal }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm">{{ $insumo->categoria->nombre ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $insumo->unidad_base }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            @if($insumo->nivel_toxicidad)
                                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded
                                                    @if($insumo->nivel_toxicidad == 'bajo') bg-green-100 text-green-800
                                                    @elseif($insumo->nivel_toxicidad == 'medio') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ ucfirst($insumo->nivel_toxicidad) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm">{{ number_format($insumo->stock_minimo, 2) }} {{ $insumo->unidad_base }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded
                                                @if($insumo->estado == 'activo') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($insumo->estado) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('insumos.show', $insumo) }}" class="text-blue-500 hover:text-blue-700">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('insumos.edit', $insumo) }}" class="text-yellow-500 hover:text-yellow-700">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" onclick="toggleEstado({{ $insumo->id }})" 
                                                        class="text-{{ $insumo->estado == 'activo' ? 'gray' : 'green' }}-500 hover:text-{{ $insumo->estado == 'activo' ? 'gray' : 'green' }}-700">
                                                    <i class="fas fa-{{ $insumo->estado == 'activo' ? 'ban' : 'check-circle' }}"></i>
                                                </button>
                                                <form action="{{ route('insumos.destroy', $insumo) }}" method="POST" 
                                                      onsubmit="return confirm('¿Eliminar este insumo?')" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                            No hay insumos registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        {{ $insumos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <form id="toggleEstadoForm" method="POST" style="display: none;">
        @csrf
        @method('PUT')
    </form>
    
    @push('scripts')
    <script>
        function toggleEstado(id) {
            const form = document.getElementById('toggleEstadoForm');
            form.action = `/insumos/${id}/cambiar-estado`;
            if (confirm('¿Cambiar el estado de este insumo?')) {
                form.submit();
            }
        }
    </script>
    @endpush
</x-app-layout>