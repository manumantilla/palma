<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-green-800 flex items-center">
            <svg class="w-8 h-8 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            Contenedores Agrícolas
        </h1>

    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border-l-4 border-green-600">
        <form method="GET" action="{{ route('contenedores.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" name="nombre" value="{{ request('nombre') }}" 
                       class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Todos</option>
                    <option value="abierta" {{ request('estado') == 'abierta' ? 'selected' : '' }}>Abierta</option>
                    <option value="cerrada" {{ request('estado') == 'cerrada' ? 'selected' : '' }}>Cerrada</option>
                    <option value="despachada" {{ request('estado') == 'despachada' ? 'selected' : '' }}>Despachada</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Calidad</label>
                <select name="calidad" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Todas</option>
                    <option value="extra" {{ request('calidad') == 'extra' ? 'selected' : '' }}>Extra</option>
                    <option value="primera" {{ request('calidad') == 'primera' ? 'selected' : '' }}>Primera</option>
                    <option value="segunda" {{ request('calidad') == 'segunda' ? 'selected' : '' }}>Segunda</option>
                    <option value="industria" {{ request('calidad') == 'industria' ? 'selected' : '' }}>Industria</option>
                    <option value="descarte" {{ request('calidad') == 'descarte' ? 'selected' : '' }}>Descarte</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de Contenedores -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-green-700 to-green-800">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Sesión</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Calidad</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Destino</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Peso Total</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($contenedores as $contenedor)
                    <tr class="hover:bg-green-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $contenedor->nombre }}</div>
                            <div class="text-sm text-gray-500">Talla: {{ ucfirst($contenedor->calibre_talla ?? 'N/A') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $contenedor->sesionCosecha->nombre ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $contenedor->estado == 'abierta' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $contenedor->estado == 'cerrada' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $contenedor->estado == 'despachada' ? 'bg-blue-100 text-blue-800' : '' }}">
                                {{ ucfirst($contenedor->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ ucfirst($contenedor->calidad ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ucfirst(str_replace('_', ' ', $contenedor->tipo_destino)) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ number_format($contenedor->peso_total, 2) }} kg
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('contenedores.show', $contenedor) }}" 
                               class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 px-3 py-1 rounded-lg transition-colors inline-block">
                                Ver
                            </a>
                            <a href="{{ route('contenedores.edit', $contenedor) }}" 
                               class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded-lg transition-colors inline-block">
                                Editar
                            </a>
                            <form action="{{ route('contenedores.destroy', $contenedor) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded-lg transition-colors"
                                        onclick="return confirm('¿Está seguro de eliminar este contenedor?')">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p class="text-lg font-medium">No hay contenedores registrados</p>
                            <p class="text-sm">Comienza creando tu primer contenedor agrícola</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($contenedores->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $contenedores->links() }}
        </div>
        @endif
    </div>
</div>
</x-app-layout>