<x-app-layout>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-stone-900 tracking-tight flex items-center gap-2">
                    🌾 Órdenes de Cosecha
                </h1>
                <p class="mt-2 text-sm text-stone-500">Planificación, pesaje y despacho de fruta recolectada por lotes.</p>
            </div>
            <div>
                <!-- <a href="{{ route('ordenes-cosecha.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Programar Cosecha
                </a> -->
            </div>
        </div>

        <div class="bg-stone-50 rounded-xl p-4 mb-6 border border-stone-200 shadow-sm">
            <form method="GET" action="{{ route('ordenes-cosecha.index') }}" class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider">Estado</label>
                    <select name="estado" class="mt-1 block w-full rounded-lg border-stone-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Todos los estados</option>
                        <option value="borrador">Borrador</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="completada">Completada</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-600 uppercase tracking-wider">Registros por Pág.</label>
                    <select name="per_page" class="mt-1 block w-full rounded-lg border-stone-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="15">15 Órdenes</option>
                        <option value="30">30 Órdenes</option>
                        <option value="50">50 Órdenes</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full bg-stone-200 hover:bg-stone-300 text-stone-700 text-sm font-medium py-2 px-4 rounded-lg transition-colors border border-stone-300">
                        Filtrar Tablero
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-stone-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 text-left">
                    <thead class="bg-stone-100 text-xs font-semibold text-stone-600 uppercase text-center">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left">Lote / Zona</th>
                            <th scope="col" class="px-6 py-4 text-left">Comprador / Cliente</th>
                            <th scope="col" class="px-4 py-4">Fecha Programada</th>
                            <th scope="col" class="px-4 py-4">Variedad</th>
                            <th scope="col" class="px-4 py-4">Progreso Kilos (Meta)</th>
                            <th scope="col" class="px-4 py-4">Estado</th>
                            <th scope="col" class="px-6 py-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white text-sm">
                        @forelse($ordenes as $orden)
                            <tr class="hover:bg-stone-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-stone-900">{{ $orden->loteCultivo->nombre ?? 'Lote N/A' }}</div>
                                    <div class="text-xs text-stone-500">{{ $orden->zonaManejo->nombre ?? 'Zona General' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-stone-700">
                                    {{ $orden->cliente->name ?? 'Venta Directa / Libre' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-stone-600 font-medium">
                                    {{ \Carbon\Carbon::parse($orden->fecha_programada)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <span class="inline-block px-2 py-0.5 bg-amber-50 border border-amber-200 text-amber-800 rounded text-xs font-medium">
                                        {{ $orden->variedad_requerida ?? 'Estándar' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <div class="text-xs font-bold text-stone-800">
                                        {{ number_format($orden->cantidad_recolectada_kg ?? 0) }} / {{ number_format($orden->cantidad_planificada_kg ?? $orden->kilos_solicitados ?? 0) }} Kg
                                    </div>
                                    @php
                                        $meta = $orden->cantidad_planificada_kg ?? $orden->kilos_solicitados ?? 1;
                                        $porcentaje = min(($orden->cantidad_recolectada_kg ?? 0) / $meta * 100, 100);
                                    @endphp
                                    <div class="w-full bg-stone-100 rounded-full h-1.5 mt-1 border border-stone-200">
                                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $porcentaje }}%"></div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wide
                                        @if($orden->estado === 'completada') bg-emerald-100 text-emerald-800
                                        @elseif($orden->estado === 'en_proceso') bg-amber-100 text-amber-800
                                        @elseif($orden->estado === 'cancelada') bg-stone-200 text-stone-600
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ str_replace('_', ' ', $orden->estado) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('ordenes-cosecha.show', $orden->id) }}" class="text-emerald-600 hover:text-emerald-900 font-bold">Revisar Pesajes →</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-stone-400 italic">
                                    No hay órdenes de cosecha registradas para los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($ordenes->hasPages())
                <div class="px-6 py-4 bg-stone-50 border-t border-stone-200">
                    {{ $ordenes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>