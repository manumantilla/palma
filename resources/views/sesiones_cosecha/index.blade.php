<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">
                Sesiones de Cosecha
            </h2>
            {{-- El botón create va desde la Orden, no desde aquí directamente --}}
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- FILTROS --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <form method="GET" action="{{ route('sesiones-cosecha.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Responsable</label>
                        <select name="responsable_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Todos</option>
                            @foreach ($responsables as $r)
                                <option value="{{ $r->id }}" @selected(request('responsable_id') == $r->id)>
                                    {{ $r->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
                        <select name="estado" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Todos</option>
                            <option value="abierta"  @selected(request('estado') === 'abierta')>Abierta</option>
                            <option value="cerrada"  @selected(request('estado') === 'cerrada')>Cerrada</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Fecha desde</label>
                        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                               class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Fecha hasta</label>
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                               class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div class="sm:col-span-2 lg:col-span-4 flex items-center gap-3">
                        <button type="submit"
                                class="px-4 py-2 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-lg transition-colors">
                            Filtrar
                        </button>
                        <a href="{{ route('sesiones-cosecha.index') }}"
                           class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>

            {{-- TABLA --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Orden</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Evento de campo</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Responsable</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Meta kg/día</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Recolectores</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($sesiones as $sesion)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 whitespace-nowrap font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($sesion->fecha)->format('d/m/Y') }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-gray-600">
                                    {{ $sesion->ordenCosecha?->codigo ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">
                                    {{ $sesion->eventoCampo?->nombre ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-gray-600">
                                    {{ $sesion->responsable?->name ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-right text-gray-700">
                                    {{ $sesion->meta_kg_dia ? number_format($sesion->meta_kg_dia, 1) : '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-right text-gray-700">
                                    {{ $sesion->numero_recolectores ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if ($sesion->estado === 'abierta')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Abierta
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                            Cerrada
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('sesiones_cosecha.show', $sesion->id) }}"
                                           class="text-gray-400 hover:text-green-700 transition-colors" title="Ver detalle">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('sesiones_cosecha.edit', $sesion->id) }}"
                                           class="text-gray-400 hover:text-blue-600 transition-colors" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('sesiones_cosecha.destroy', $sesion->id) }}"
                                              onsubmit="return confirm('¿Eliminar esta sesión?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors" title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2 text-gray-400">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <p class="text-sm font-medium text-gray-500">No hay sesiones registradas</p>
                                        <p class="text-xs text-gray-400">Crea una sesión desde una orden de cosecha.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($sesiones->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $sesiones->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>