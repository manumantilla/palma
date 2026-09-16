<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-800 leading-tight">
            {{ __('Recomendaciones Fenológicas') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-200">
                <div class="p-6 bg-green-50/30">
                    <!-- Botón para crear nueva recomendación -->
                    <div class="mb-6 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-green-900">Listado de recomendaciones</h3>
                        <a href="{{ route('fenologia-recomendacion.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-800 focus:bg-green-800 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Nueva Recomendación
                        </a>
                    </div>

                    <!-- Tabla -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-green-200">
                            <thead class="bg-green-100">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Título</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Etapa</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Prioridad</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Días offset</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Evento auto.</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-green-100">
                                @forelse($recomendaciones as $recomendacion)
                                    <tr class="hover:bg-green-50/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $recomendacion->titulo }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $recomendacion->etapa->nombre ?? 'Sin etapa' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $prioridadColores = [
                                                    'Baja' => 'bg-gray-100 text-gray-800',
                                                    'Media' => 'bg-yellow-100 text-yellow-800',
                                                    'Alta' => 'bg-orange-100 text-orange-800',
                                                    'Crítica' => 'bg-red-100 text-red-800',
                                                ];
                                                $color = $prioridadColores[$recomendacion->prioridad] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                                {{ $recomendacion->prioridad }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $recomendacion->dias_offset }} días
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            @if($recomendacion->genera_evento_automatico)
                                                <span class="text-green-600">✔ Sí</span>
                                            @else
                                                <span class="text-gray-400">✖ No</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a href="{{ route('fenologia-recomendacion.show', $recomendacion) }}" 
                                               class="text-green-600 hover:text-green-900 inline-flex items-center">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('fenologia-recomendacion.edit', $recomendacion) }}" 
                                               class="text-amber-600 hover:text-amber-900 inline-flex items-center">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('fenologia-recomendacion.destroy', $recomendacion) }}" method="POST" 
                                                  class="inline-block" 
                                                  onsubmit="return confirm('¿Estás seguro de eliminar esta recomendación?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 inline-flex items-center">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="mt-2 text-sm">No hay recomendaciones registradas.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>