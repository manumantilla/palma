<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-800 leading-tight">
            {{ __('Detalle del Historial de Etapa') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-200">
                <div class="p-6 bg-green-50/30">
                    <div class="mb-6 flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-bold text-green-900">
                                {{ $historial->etapa->nombre ?? 'Etapa sin nombre' }}
                            </h3>
                            <p class="text-sm text-green-700">
                                Ciclo: {{ $historial->cicloProductivo->nombre ?? 'Sin ciclo' }}
                            </p>
                        </div>
                        <div class="space-x-2">
                            <a href="{{ route('ciclo-etapa-historial.edit', $historial) }}" 
                               class="inline-flex items-center px-3 py-1 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Editar
                            </a>
                            <a href="{{ route('ciclo-etapa-historial.index') }}" 
                               class="inline-flex items-center px-3 py-1 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Volver
                            </a>
                        </div>
                    </div>

                    <!-- Datos principales -->
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Ciclo Productivo</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $historial->cicloProductivo->nombre ?? 'Sin asignar' }}</dd>
                        </div>
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Etapa Fenológica</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $historial->etapa->nombre ?? 'Sin asignar' }}</dd>
                        </div>
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Fecha inicio estimada</dt>
                            <dd class="mt-1 text-gray-900">{{ $historial->fecha_inicio_estimada ? $historial->fecha_inicio_estimada->format('d/m/Y') : '-' }}</dd>
                        </div>
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Fecha fin estimada</dt>
                            <dd class="mt-1 text-gray-900">{{ $historial->fecha_fin_estimada ? $historial->fecha_fin_estimada->format('d/m/Y') : '-' }}</dd>
                        </div>
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Fecha inicio real</dt>
                            <dd class="mt-1 text-gray-900">{{ $historial->fecha_inicio_real ? $historial->fecha_inicio_real->format('d/m/Y') : 'No registrada' }}</dd>
                        </div>
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Fecha fin real</dt>
                            <dd class="mt-1 text-gray-900">{{ $historial->fecha_fin_real ? $historial->fecha_fin_real->format('d/m/Y') : 'No registrada' }}</dd>
                        </div>
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Estado</dt>
                            <dd class="mt-1">
                                @php
                                    $estadoColores = [
                                        'pendiente' => 'bg-yellow-100 text-yellow-800',
                                        'en_progreso' => 'bg-blue-100 text-blue-800',
                                        'completada' => 'bg-green-100 text-green-800',
                                        'omitida' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $color = $estadoColores[$historial->estado] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ ucfirst($historial->estado) }}
                                </span>
                            </dd>
                        </div>
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Días de desfase (inicio real vs estimado)</dt>
                            <dd class="mt-1 text-gray-900">
                                @if($historial->desfase_dias !== null)
                                    {{ $historial->desfase_dias }} días
                                    @if($historial->desfase_dias > 0)
                                        <span class="text-red-600 text-sm">(retraso)</span>
                                    @elseif($historial->desfase_dias < 0)
                                        <span class="text-green-600 text-sm">(adelanto)</span>
                                    @else
                                        <span class="text-gray-500 text-sm">(exacto)</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">Sin fecha real</span>
                                @endif
                            </dd>
                        </div>
                    </dl>

                    <!-- Nota sobre eventos generados automáticamente (si se desea mostrar) -->
                    <div class="mt-8 p-4 bg-green-100/50 rounded-lg border border-green-200">
                        <p class="text-sm text-green-800">
                            <span class="font-semibold">Nota:</span> Al iniciar una etapa (fecha inicio real), el sistema genera automáticamente eventos de campo según las recomendaciones configuradas.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>