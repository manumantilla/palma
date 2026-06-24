{{-- resources/views/evento_insumos/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalle del Evento de Insumo') }} #{{ $eventoInsumo->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $eventoInsumo->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Evento de Campo</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                #{{ $eventoInsumo->eventoCampo->id ?? 'N/A' }}
                                @if($eventoInsumo->eventoCampo)
                                    <span class="text-gray-500 text-xs">
                                        ({{ $eventoInsumo->eventoCampo->tipoEvento->nombre ?? 'Sin tipo' }})
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Insumo</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $eventoInsumo->insumo->nombre ?? 'N/A' }}
                                @if($eventoInsumo->insumo)
                                    <span class="text-gray-500 text-xs">
                                        ({{ $eventoInsumo->insumo->tipo ?? 'Sin tipo' }})
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cantidad</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($eventoInsumo->cantidad, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Unidad de Medida</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($eventoInsumo->unidad_medida) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Método de Aplicación</dt>
                            <dd class="mt-1">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($eventoInsumo->metodo_aplicacion) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Área Aplicada</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $eventoInsumo->area_aplicada ? number_format($eventoInsumo->area_aplicada, 2) . ' HA' : 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Costo Total</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $eventoInsumo->costo_total ? '$ ' . number_format($eventoInsumo->costo_total, 2) : 'N/A' }}
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Observaciones</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $eventoInsumo->observaciones ?? 'Sin observaciones' }}
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Fecha de Creación</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $eventoInsumo->created_at ? $eventoInsumo->created_at->format('d/m/Y H:i') : 'N/A' }}
                            </dd>
                        </div>
                    </dl>

                    {{-- Lotes asociados --}}
                    @if($eventoInsumo->eventoInsumoLotes->count())
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900">Lotes Asociados</h3>
                            <div class="mt-4 overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Lote</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Asignación</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($eventoInsumo->eventoInsumoLotes as $item)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    #{{ $item->loteInsumo->id ?? 'N/A' }}
                                                    @if($item->loteInsumo)
                                                        <span class="text-gray-500 text-xs">
                                                            ({{ $item->loteInsumo->lote->nombre ?? 'Sin lote' }})
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ number_format($item->cantidad, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $eventoInsumo->unidad_medida }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $item->created_at ? $item->created_at->format('d/m/Y') : 'N/A' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="4" class="px-6 py-3 text-sm font-medium text-gray-900">
                                                Total de lotes: {{ $eventoInsumo->eventoInsumoLotes->count() }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="mt-8 p-4 bg-yellow-50 border-l-4 border-yellow-400">
                            <p class="text-sm text-yellow-700">Este evento de insumo no tiene lotes asociados.</p>
                        </div>
                    @endif

                    <div class="mt-8 flex items-center gap-4">
                        <a href="{{ route('evento_insumos.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            ← Volver al listado
                        </a>
                        <a href="{{ route('evento_insumos.edit', $eventoInsumo->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Editar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>