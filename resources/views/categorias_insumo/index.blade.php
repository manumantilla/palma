<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">📦 Categorías de Insumos</h2>
                        <a href="{{ route('categorias-insumo.create') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                            <i class="fas fa-plus mr-2"></i> Nueva Categoría
                        </a>
                    </div>
                    
                    <!-- Filtros -->
                    <form method="GET" action="{{ route('categorias-insumo.index') }}" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <input type="text" 
                                       name="search" 
                                       placeholder="Buscar categoría..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       value="{{ request('search') }}">
                            </div>
                            
                            <div>
                                <select name="maneja_vencimiento" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Maneja vencimiento: Todos</option>
                                    <option value="1" {{ request('maneja_vencimiento') == '1' ? 'selected' : '' }}>Sí</option>
                                    <option value="0" {{ request('maneja_vencimiento') == '0' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            
                            <div>
                                <select name="maneja_toxicidad" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Maneja toxicidad: Todos</option>
                                    <option value="1" {{ request('maneja_toxicidad') == '1' ? 'selected' : '' }}>Sí</option>
                                    <option value="0" {{ request('maneja_toxicidad') == '0' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            
                            <div class="flex space-x-2">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                                <a href="{{ route('categorias-insumo.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                    <i class="fas fa-undo"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Tabla -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-600 uppercase">
                                        <a href="{{ route('categorias-insumo.index', array_merge(request()->all(), ['sort' => 'id', 'direction' => request('sort') == 'id' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                                            ID
                                            @if(request('sort') == 'id')
                                                <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-600 uppercase">
                                        <a href="{{ route('categorias-insumo.index', array_merge(request()->all(), ['sort' => 'nombre', 'direction' => request('sort') == 'nombre' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                                            Nombre
                                            @if(request('sort') == 'nombre')
                                                <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-600 uppercase">Maneja Vencimiento</th>
                                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-600 uppercase">Maneja Toxicidad</th>
                                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-600 uppercase">Fecha Creación</th>
                                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-600 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categorias as $categoria)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 border-b text-sm">{{ $categoria->id }}</td>
                                        <td class="px-6 py-4 border-b text-sm font-medium text-gray-900">{{ $categoria->nombre }}</td>
                                        <td class="px-6 py-4 border-b text-sm">
                                            @if($categoria->maneja_vencimiento)
                                                <span class="inline-block px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">
                                                    <i class="fas fa-check-circle"></i> Sí
                                                </span>
                                            @else
                                                <span class="inline-block px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded">
                                                    <i class="fas fa-times-circle"></i> No
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 border-b text-sm">
                                            @if($categoria->maneja_toxicidad)
                                                <span class="inline-block px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded">
                                                    <i class="fas fa-exclamation-triangle"></i> Sí
                                                </span>
                                            @else
                                                <span class="inline-block px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded">
                                                    No
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 border-b text-sm">{{ $categoria->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 border-b text-sm">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('categorias-insumo.show', $categoria) }}" 
                                                   class="text-blue-500 hover:text-blue-700" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('categorias-insumo.edit', $categoria) }}" 
                                                   class="text-yellow-500 hover:text-yellow-700" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('categorias-insumo.destroy', $categoria) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('¿Está seguro de eliminar esta categoría?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No hay categorías registradas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="mt-6">
                        {{ $categorias->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>