{{-- resources/views/eventos_campo/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Eventos de Campo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    
                    <form method="GET" action="{{ route('eventos_campo.index') }}" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            
                            <div>
                                <label for="tipo_evento_id" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Tipo de Evento</label>
                                <select name="tipo_evento_id" id="tipo_evento_id" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Todos --</option>
                                    @foreach($tiposEvento as $tipo)
                                        <option value="{{ $tipo->id }}" {{ request('tipo_evento_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="lote_id" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Lote</label>
                                <select name="lote_id" id="lote_id" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Todos --</option>
                                    @foreach($lotes as $lote)
                                        <option value="{{ $lote->id }}" {{ request('lote_id') == $lote->id ? 'selected' : '' }}>{{ $lote->nombre_lote }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="zona_id" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Zona</label>
                                <select name="zona_id" id="zona_id" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Todas --</option>
                                    @foreach($zonas as $zona)
                                        <option value="{{ $zona->id }}" {{ request('zona_id') == $zona->id ? 'selected' : '' }}>{{ $zona->nombre_zona }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="estado" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Estado</label>
                                <select name="estado" id="estado" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Todos --</option>
                                    @foreach(['Pendiente', 'En Proceso', 'Completado', 'Cancelado'] as $est)
                                        <option value="{{ $est }}" {{ request('estado') == $est ? 'selected' : '' }}>{{ $est }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="ciclo_productivo_id" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Ciclo Productivo</label>
                                <select name="ciclo_productivo_id" id="ciclo_productivo_id" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Todos --</option>
                                    @foreach($ciclos as $ciclo)
                                        <option value="{{ $ciclo->id }}" {{ request('ciclo_productivo_id') == $ciclo->id ? 'selected' : '' }}>{{ $ciclo->nombre_campana }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="fecha_inicio" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Desde (Fecha)</label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio') }}" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="fecha_fin" class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-1">Hasta (Fecha)</label>
                                <input type="date" name="fecha_fin" id="fecha_fin" value="{{ request('fecha_fin') }}" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div class="flex items-end space-x-2">
                                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out text-center">
                                    Filtrar
                                </button>
                                <a href="{{ route('eventos_campo.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium text-sm py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out text-center">
                                    Limpiar
                                </a>
                                <a href="{{ route('eventos_campo.create_general') }}" class="flex-1 bg-emerald-300 hover:bg-emerald-400 text-white font-medium text-sm py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out text-center">
                                    Crear
                                </a>
                            </div>

                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciclo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lote</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zona</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Programada</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($eventos as $evento)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $evento->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $evento->tipoEvento->nombre ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $evento->cicloProductivo->nombre_campana ?? 'no tien' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $evento->lote->nombre_lote ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $evento->zona->nombre_zona ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($evento->fecha_programada)->format('d/m/Y') }}</td>
                                        <td> <a href="{{route('eventos_campo.show', $evento->id)}}">Ver Detalles</a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No hay eventos registrados con los criterios seleccionados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $eventos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>