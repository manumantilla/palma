<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensajes de éxito -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">📋 Gestión de Trabajadores</h2>
                        <a href="{{ route('trabajadores.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                            <i class="fas fa-plus mr-2"></i> Nuevo Trabajador
                        </a>
                    </div>
                    
                    <!-- Formulario de Filtros -->
                    <form method="GET" action="{{ route('trabajadores.index') }}" id="filterForm" class="mb-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <!-- Búsqueda general -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">🔍 Buscar</label>
                                <input type="text" 
                                       name="search" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Documento, nombres, apellidos o cargo..."
                                       value="{{ request('search') }}">
                            </div>
                            
                            <!-- Tipo documento -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tipo Documento</label>
                                <select name="tipo_documento" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Todos</option>
                                    @foreach($tiposDocumento as $tipo)
                                        <option value="{{ $tipo }}" {{ request('tipo_documento') == $tipo ? 'selected' : '' }}>
                                            {{ $tipo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Cargo -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Cargo</label>
                                <input type="text" 
                                       name="cargo" 
                                       list="cargosList"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Ej: Desarrollador"
                                       value="{{ request('cargo') }}">
                                <datalist id="cargosList">
                                    @foreach($cargosUnicos as $cargo)
                                        <option value="{{ $cargo }}">
                                    @endforeach
                                </datalist>
                            </div>
                            
                            <!-- Tipo contrato -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tipo Contrato</label>
                                <select name="tipo_contrato" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Todos</option>
                                    @foreach($tiposContrato as $tipo)
                                        <option value="{{ $tipo }}" {{ request('tipo_contrato') == $tipo ? 'selected' : '' }}>
                                            {{ ucfirst($tipo) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                            <!-- Estado -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Estado</label>
                                <select name="activo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Todos</option>
                                    <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                            </div>
                            
                            <!-- Fecha desde -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Fecha Ingreso (desde)</label>
                                <input type="date" 
                                       name="fecha_ingreso_desde" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       value="{{ request('fecha_ingreso_desde') }}">
                            </div>
                            
                            <!-- Fecha hasta -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Fecha Ingreso (hasta)</label>
                                <input type="date" 
                                       name="fecha_ingreso_hasta" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       value="{{ request('fecha_ingreso_hasta') }}">
                            </div>
                            
                            <!-- Salario mínimo -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Salario Mínimo</label>
                                <input type="number" 
                                       step="100000" 
                                       name="salario_min" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="$0"
                                       value="{{ request('salario_min') }}">
                            </div>
                            
                            <!-- Salario máximo -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Salario Máximo</label>
                                <input type="number" 
                                       step="100000" 
                                       name="salario_max" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="$∞"
                                       value="{{ request('salario_max') }}">
                            </div>
                        </div>
                        
                        <!-- Botones -->
                        <div class="flex justify-end space-x-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                <i class="fas fa-filter mr-2"></i> Filtrar
                            </button>
                            <a href="{{ route('trabajadores.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                <i class="fas fa-undo mr-2"></i> Limpiar
                            </a>
                        </div>
                    </form>
                    
                    <!-- Tabla de Trabajadores -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        <a href="{{ route('trabajadores.index', array_merge(request()->all(), ['sort' => 'id', 'direction' => request('sort') == 'id' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-gray-900">
                                            ID
                                            @if(request('sort') == 'id')
                                                <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Documento</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Nombres</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Apellidos</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        <a href="{{ route('trabajadores.index', array_merge(request()->all(), ['sort' => 'cargo', 'direction' => request('sort') == 'cargo' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-gray-900">
                                            Cargo
                                            @if(request('sort') == 'cargo')
                                                <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Fecha Ingreso</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Salario</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trabajadores as $trabajador)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 border-b border-gray-200 text-sm">{{ $trabajador->id }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200 text-sm">
                                            <span class="inline-block px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded">{{ $trabajador->tipo_documento }}</span>
                                            {{ $trabajador->numero_documento }}
                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-200 text-sm">{{ $trabajador->nombres }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200 text-sm">{{ $trabajador->apellidos }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200 text-sm">{{ $trabajador->cargo }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200 text-sm">{{ $trabajador->fecha_ingreso ? \Carbon\Carbon::parse($trabajador->fecha_ingreso)->format('d/m/Y') : 'N/A' }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200 text-sm">$ {{ number_format($trabajador->salario_base, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200 text-sm">
                                            @if($trabajador->activo)
                                                <span class="inline-block px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">Activo</span>
                                            @else
                                                <span class="inline-block px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded">Inactivo</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-200 text-sm">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('trabajadores.show', $trabajador) }}" class="text-blue-500 hover:text-blue-700" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('trabajadores.edit', $trabajador) }}" class="text-yellow-500 hover:text-yellow-700" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('trabajadores.destroy', $trabajador) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Está seguro de eliminar este trabajador?')">
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
                                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                            No se encontraron trabajadores registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="mt-6">
                        {{ $trabajadores->appends(request()->query())->links() }}
                    </div>
                    
                    <!-- Contador de resultados -->
                    <div class="mt-4 text-sm text-gray-600">
                        Mostrando {{ $trabajadores->firstItem() ?? 0 }} a {{ $trabajadores->lastItem() ?? 0 }} 
                        de {{ $trabajadores->total() }} resultados
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        // Auto-submit al cambiar selects
        document.querySelectorAll('select[name="tipo_documento"], select[name="tipo_contrato"], select[name="activo"]').forEach(select => {
            select.addEventListener('change', () => {
                document.getElementById('filterForm').submit();
            });
        });
        
        // Debounce para búsqueda automática
        let searchTimeout;
        const searchInput = document.querySelector('input[name="search"]');
        if(searchInput) {
            searchInput.addEventListener('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 500);
            });
        }
    </script>
    @endpush
</x-app-layout>