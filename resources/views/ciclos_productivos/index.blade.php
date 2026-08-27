<x-app-layout>
<div class="container mx-auto px-4 py-8 max-w-7xl">
    
    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🌾 Ciclos Productivos y Campañas</h1>
            <p class="text-sm text-gray-500">Gestión, trazabilidad y estados fenológicos de los cultivos en lote.</p>
        </div>
        <a href="{{ route('ciclos-productivos.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-5 rounded-lg shadow transition flex items-center gap-2">
            <span>➕ Nuevo Ciclo</span>
        </a>
    </div>

    {{-- Bloque de Alertas de Éxito --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Formulario de Búsqueda y Filtros Avanzados --}}
    <form method="GET" action="{{ route('ciclos-productivos.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Buscador Principal --}}
            <div class="md:col-span-2 relative">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Buscar por Campaña o Códigos</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ej: Campaña Maíz 2026, Lote Norte..." class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
            </div>

            {{-- Filtro Estado --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Estado del Ciclo</label>
                <select name="estado" class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-green-500">
                    <option value="">Todos los estados</option>
                    @foreach(['preparacion_suelo' => 'Preparación de Suelo', 'siembra_establecimiento' => 'Siembra/Establecimiento', 'desarrollo_vegetativo' => 'Desarrollo Vegetativo', 'floracion_llenado' => 'Floración/Llenado', 'cosecha_activa' => 'Cosecha Activa', 'receso_invernal_poda' => 'Receso/Poda', 'concluido' => 'Concluido', 'siniestrado_perdida' => 'Siniestrado/Pérdida'] as $key => $value)
                        <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Filtros Secundarios Desplegables / Grilla --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-2 border-t border-gray-100">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Lote Especial específico</label>
                <select name="lote_id" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">Todos los lotes</option>
                    @foreach($lotes as $lote)
                        <option value="{{ $lote->id }}" {{ request('lote_id') == $lote->id ? 'selected' : '' }}>{{ $lote->nombre_lote }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Variedad/Cultivo</label>
                <select name="cultivo_id" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">Todos los cultivos</option>
                    @foreach($cultivos as $cultivo)
                        <option value="{{ $cultivo->id }}" {{ request('cultivo_id') == $cultivo->id ? 'selected' : '' }}>{{ $cultivo->nombre_cultivo}}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Desde (Fecha Inicio)</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Hasta (Fecha Inicio)</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
        </div>

        {{-- Botones de Control del Filtro --}}
        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('ciclos-productivos.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg text-sm transition">
                Limpiar Filtros
            </a>
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-5 rounded-lg text-sm shadow-sm transition">
                🔍 Aplicar Filtros
            </button>
        </div>
    </form>

    {{-- Tabla de Datos --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">Campaña / Destino</th>
                        <th class="px-6 py-4">Cultivo</th>
                        <th class="px-6 py-4">Estado Fenológico</th>
                        <th class="px-6 py-4">Tipo</th>
                        <th class="px-6 py-4">Fecha Inicio</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($ciclos as $ciclo)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <span class="block text-gray-900 font-semibold text-base">{{ $ciclo->nombre_campana }}</span>
                                <span class="text-xs text-gray-400">Lote: <strong>{{ $ciclo->lote->nombre_lote }}</strong> ({{ $ciclo->lote->codigo_lote }})</span>
                            </td>
                            <td class="px-6 py-4 text-gray-900 font-medium">
                                {{ $ciclo->cultivo->nombre }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'preparacion_suelo' => 'bg-amber-50 text-amber-800 border-amber-200',
                                        'siembra_establecimiento' => 'bg-lime-50 text-lime-800 border-lime-200',
                                        'desarrollo_vegetativo' => 'bg-green-50 text-green-800 border-green-200',
                                        'floracion_llenado' => 'bg-fuchsia-50 text-fuchsia-800 border-fuchsia-200',
                                        'cosecha_activa' => 'bg-emerald-600 text-white border-transparent',
                                        'receso_invernal_poda' => 'bg-blue-50 text-blue-800 border-blue-200',
                                        'concluido' => 'bg-gray-100 text-gray-700 border-gray-300',
                                        'siniestrado_perdida' => 'bg-red-50 text-red-800 border-red-200',
                                    ];
                                    $color = $statusColors[$ciclo->estado] ?? 'bg-gray-50 text-gray-600';
                                @endphp
                                <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full border {{ $color }} capitalize">
                                    {{ str_replace('_', ' ', $ciclo->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 capitalize text-xs font-medium">
                                <span class="{{ $ciclo->tipo === 'perenne' ? 'text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded' : 'text-orange-600 bg-orange-50 px-2 py-0.5 rounded' }}">
                                    {{ $ciclo->tipo }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-sm">
                                {{ \Carbon\Carbon::parse($ciclo->fecha_inicio)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('ciclos-productivos.show', $ciclo->id) }}" class="text-gray-600 hover:text-gray-900 font-medium text-xs bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded transition">Ver</a>
                            </td>
                            
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                           
</td>   
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                                No se encontraron ciclos productivos activos que coincidan con los criterios estructurados de búsqueda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Paginación Turob --}}
        @if($ciclos->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $ciclos->links() }}
            </div>
        @endif
    </div>
</div>
</x-app-layout>