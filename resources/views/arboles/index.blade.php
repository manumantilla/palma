<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Árboles') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="GET" action="{{ route('arboles.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700">Buscar por Código</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Ej. ARB-001" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="lote_id" class="block text-sm font-medium text-gray-700">Lote</label>
                                <select name="lote_id" id="lote_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Todos los lotes</option>
                                    @foreach($lotes as $lote)
                                        <option value="{{ $lote->id }}" {{ request('lote_id') == $lote->id ? 'selected' : '' }}>
                                            {{ $lote->nombre_lote }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="lote_zona_manejo_id" class="block text-sm font-medium text-gray-700">Zona de Manejo</label>
                                <select name="lote_zona_manejo_id" id="lote_zona_manejo_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Todas las zonas</option>
                                    @foreach($zonas as $zona)
                                        <option value="{{ $zona->id }}" {{ request('lote_zona_manejo_id') == $zona->id ? 'selected' : '' }}>
                                            {{ $zona->nombre_zona }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="estado_vital" class="block text-sm font-medium text-gray-700">Estado Vital</label>
                                <select name="estado_vital" id="estado_vital" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Todos los estados</option>
                                    <option value="Vivo" {{ request('estado_vital') == 'Vivo' ? 'selected' : '' }}>Vivo</option>
                                    <option value="Muerto" {{ request('estado_vital') == 'Muerto' ? 'selected' : '' }}>Muerto</option>
                                    <option value="Enfermo" {{ request('estado_vital') == 'Enfermo' ? 'selected' : '' }}>Enfermo</option>
                                </select>
                            </div>

                            <div>
                                <label for="etapa_biologica" class="block text-sm font-medium text-gray-700">Etapa Biológica</label>
                                <select name="etapa_biologica" id="etapa_biologica" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Todas las etapas</option>
                                    <option value="Crecimiento" {{ request('etapa_biologica') == 'Crecimiento' ? 'selected' : '' }}>Crecimiento</option>
                                    <option value="Produccion" {{ request('etapa_biologica') == 'Produccion' ? 'selected' : '' }}>Producción</option>
                                    <option value="Renovacion" {{ request('etapa_biologica') == 'Renovacion' ? 'selected' : '' }}>Renovación</option>
                                </select>
                            </div>

                            <div>
                                <label for="variedad" class="block text-sm font-medium text-gray-700">Variedad</label>
                                <input type="text" name="variedad" id="variedad" value="{{ request('variedad') }}" placeholder="Ej. Hass, Fuerte..." 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-4">
                            <a href="{{ route('arboles.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Limpiar Filtros
                            </a>

                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Buscar / Filtrar
                            </button>
                            <a href="{{ route('arboles.create') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-white bg-emerald-800 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Crear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código Único</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación (Lote/Zona)</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posición (Fila/Pos)</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variedad</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado / Etapa</th>
                                
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
               
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($arboles as $arbol)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $arbol->codigo_unico }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $arbol->lote?->nombre_lote ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $arbol->loteZonaManejo?->nombre_zona ?? 'Sin zona' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Fila: {{ $arbol->fila_indice }}</div>
                                        <div class="text-sm text-gray-500">Pos: {{ $arbol->posicion_indice }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $arbol->variedad }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $arbol->estado_vital == 'Vivo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $arbol->estado_vital }}
                                        </span>
                                        <div class="text-sm text-gray-500 mt-1">{{ $arbol->etapa_biologica }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{route('arboles.show', $arbol->id)}}" class="text-indigo-600 hover:text-indigo-900 mr-3">Ver</a>
                                        <a href="#" class="text-yellow-600 hover:text-yellow-900">Editar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        No se encontraron árboles que coincidan con los filtros.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($arboles->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $arboles->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>