<x-app-layout>

<div class="container mx-auto px-4 py-6">
    {{-- Encabezado --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <svg class="w-8 h-8 mr-2 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Lotes
            </h1>
            <p class="text-sm text-gray-500">Gestión de lotes y parcelas agrícolas</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('lotes-gis.create') }}" class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo Lote
            </a>
        </div>
    </div>

    {{-- Barra de filtros (colapsable) --}}
    <div x-data="{ open: true }" class="mb-6">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
            <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between cursor-pointer" @click="open = !open">
                <h3 class="text-sm font-medium text-gray-700 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filtros de búsqueda
                    <span class="ml-2 text-xs text-gray-400">
               
                    </span>
                </h3>
                <div class="flex items-center space-x-2">
                    @if(request()->anyFilled(['finca_id', 'nombre_lote', 'codigo_lote', 'area_min', 'area_max', 'altitud_min', 'altitud_max', 'pendiente_min', 'pendiente_max', 'tipo_suelo', 'ph_min', 'ph_max', 'tiene_riego_instalado', 'fuente_agua', 'tenencia', 'activo']))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Filtros activos
                        </span>
                    @endif
                    <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            <div x-show="open" x-transition.duration.300ms>
                <form method="GET" action="{{ route('lotes-gis.index') }}" class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    {{-- Finca --}}
                    <div>
                        <label for="finca_id" class="block text-sm font-medium text-gray-700">Finca</label>
                        <select name="finca_id" id="finca_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                            <option value="">Todas las fincas</option>
                            @foreach($fincas as $id => $nombre)
                                <option value="{{ $id }}" {{ request('finca_id') == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Nombre --}}
                    <div>
                        <label for="nombre_lote" class="block text-sm font-medium text-gray-700">Nombre del lote</label>
                        <input type="text" name="nombre_lote" id="nombre_lote" value="{{ request('nombre_lote') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="Ej. Lote 5">
                    </div>

                    {{-- Código --}}
                    <div>
                        <label for="codigo_lote" class="block text-sm font-medium text-gray-700">Código</label>
                        <input type="text" name="codigo_lote" id="codigo_lote" value="{{ request('codigo_lote') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="Ej. L-001">
                    </div>

                    {{-- Área (rango) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Área (ha)</label>
                        <div class="flex space-x-2">
                            <input type="number" step="0.01" name="area_min" value="{{ request('area_min') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="Mín">
                            <span class="self-center text-gray-500">-</span>
                            <input type="number" step="0.01" name="area_max" value="{{ request('area_max') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="Máx">
                        </div>
                    </div>

                    {{-- Altitud (rango) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Altitud (msnm)</label>
                        <div class="flex space-x-2">
                            <input type="number" step="1" name="altitud_min" value="{{ request('altitud_min') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="Mín">
                            <span class="self-center text-gray-500">-</span>
                            <input type="number" step="1" name="altitud_max" value="{{ request('altitud_max') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="Máx">
                        </div>
                    </div>

                    {{-- Pendiente (rango) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pendiente (%)</label>
                        <div class="flex space-x-2">
                            <input type="number" step="0.1" name="pendiente_min" value="{{ request('pendiente_min') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="Mín">
                            <span class="self-center text-gray-500">-</span>
                            <input type="number" step="0.1" name="pendiente_max" value="{{ request('pendiente_max') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="Máx">
                        </div>
                    </div>

                    {{-- Tipo de suelo --}}
                    <div>
                        <label for="tipo_suelo" class="block text-sm font-medium text-gray-700">Tipo de suelo</label>
                        <select name="tipo_suelo" id="tipo_suelo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                            <option value="">Todos</option>
                            @foreach($tiposSuelo as $tipo)
                                <option value="{{ $tipo }}" {{ request('tipo_suelo') == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- pH (rango) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">pH del suelo</label>
                        <div class="flex space-x-2">
                            <input type="number" step="0.1" name="ph_min" value="{{ request('ph_min') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="Mín">
                            <span class="self-center text-gray-500">-</span>
                            <input type="number" step="0.1" name="ph_max" value="{{ request('ph_max') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="Máx">
                        </div>
                    </div>

                    {{-- Tiene riego instalado --}}
                    <div>
                        <label for="tiene_riego_instalado" class="block text-sm font-medium text-gray-700">Riego instalado</label>
                        <select name="tiene_riego_instalado" id="tiene_riego_instalado" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                            <option value="">Todos</option>
                            <option value="1" {{ request('tiene_riego_instalado') == '1' ? 'selected' : '' }}>Sí</option>
                            <option value="0" {{ request('tiene_riego_instalado') == '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                    {{-- Fuente de agua --}}
                    <div>
                        <label for="fuente_agua" class="block text-sm font-medium text-gray-700">Fuente de agua</label>
                        <input type="text" name="fuente_agua" id="fuente_agua" value="{{ request('fuente_agua') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="Ej. Río, pozo">
                    </div>

                    {{-- Tenencia --}}
                    <div>
                        <label for="tenencia" class="block text-sm font-medium text-gray-700">Tenencia</label>
                        <select name="tenencia" id="tenencia" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                            <option value="">Todas</option>
                            @foreach($tenencias as $tenencia)
                                <option value="{{ $tenencia }}" {{ request('tenencia') == $tenencia ? 'selected' : '' }}>{{ $tenencia }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Activo --}}
                    <div>
                        <label for="activo" class="block text-sm font-medium text-gray-700">Estado</label>
                        <select name="activo" id="activo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                            <option value="">Todos</option>
                            <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-end space-x-2 col-span-1 md:col-span-2 lg:col-span-3 xl:col-span-4">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Filtrar
                        </button>
                        <a href="{{ route('lotes-gis.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabla de resultados --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lote</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Finca</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área (ha)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Altitud (msnm)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suelo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Riego</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lotes as $lote)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $lote->nombre_lote }}</div>
                                <div class="text-xs text-gray-500">ID: {{ $lote->id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $lote->codigo_lote ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $lote->finca->nombre ?? 'Sin finca' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($lote->area_hectareas_declaradas, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($lote->altitud_mediana_msnm, 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $lote->tipo_suelo ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($lote->tiene_riego_instalado)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Sí</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">No</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($lote->activo)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('lotes-gis.show', $lote) }}" class="text-green-600 hover:text-green-900 mr-3" title="Ver">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                         
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-10 text-center text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="mt-2 text-sm">No se encontraron lotes con los filtros aplicados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $lotes->appends(request()->query())->links() }}
        </div>
    </div>
</div>
</x-app-layout>