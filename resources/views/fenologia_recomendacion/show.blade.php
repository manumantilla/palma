<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-800 leading-tight">
            {{ __('Detalle de Recomendación Fenológica') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-200">
                <div class="p-6 bg-green-50/30">
                    <div class="mb-6 flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-bold text-green-900">{{ $recomendacion->titulo }}</h3>
                            <p class="text-sm text-green-700">
                                Etapa: {{ $recomendacion->etapa->nombre ?? 'Sin etapa' }}
                            </p>
                        </div>
                        <div class="space-x-2">
                            <a href="{{ route('fenologia-recomendacion.edit', $recomendacion) }}" 
                               class="inline-flex items-center px-3 py-1 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Editar
                            </a>
                            <a href="{{ route('fenologia-recomendacion.index') }}" 
                               class="inline-flex items-center px-3 py-1 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Volver
                            </a>
                        </div>
                    </div>

                    <!-- Datos principales -->
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Etapa fenológica</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $recomendacion->etapa->nombre ?? 'Sin asignar' }}</dd>
                        </div>
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Tipo de evento</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $recomendacion->tipoEvento->nombre ?? 'No definido' }}</dd>
                        </div>
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Prioridad</dt>
                            <dd class="mt-1">
                                @php
                                    $prioridadColores = [
                                        'Baja' => 'bg-gray-100 text-gray-800',
                                        'Media' => 'bg-yellow-100 text-yellow-800',
                                        'Alta' => 'bg-orange-100 text-orange-800',
                                        'Crítica' => 'bg-red-100 text-red-800',
                                    ];
                                    $color = $prioridadColores[$recomendacion->prioridad] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ $recomendacion->prioridad }}
                                </span>
                            </dd>
                        </div>
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Días offset</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $recomendacion->dias_offset }} días después del inicio de etapa</dd>
                        </div>
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Ventana de ejecución</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $recomendacion->ventana_ejecucion_dias ? $recomendacion->ventana_ejecucion_dias.' días' : 'No definida' }}</dd>
                        </div>
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Genera evento automático</dt>
                            <dd class="mt-1 text-lg text-gray-900">
                                @if($recomendacion->genera_evento_automatico)
                                    <span class="text-green-600">✔ Sí</span>
                                @else
                                    <span class="text-gray-400">✖ No</span>
                                @endif
                            </dd>
                        </div>
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Requiere verificación en campo</dt>
                            <dd class="mt-1 text-lg text-gray-900">
                                @if($recomendacion->requiere_verificacion_campo)
                                    <span class="text-green-600">✔ Sí</span>
                                @else
                                    <span class="text-gray-400">✖ No</span>
                                @endif
                            </dd>
                        </div>
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100 col-span-2">
                            <dt class="text-sm font-medium text-green-600">Descripción</dt>
                            <dd class="mt-1 text-gray-700">{{ $recomendacion->descripcion }}</dd>
                        </div>
                        @if($recomendacion->instrucciones_tecnicas)
                            <div class="bg-white/60 p-4 rounded-lg border border-green-100 col-span-2">
                                <dt class="text-sm font-medium text-green-600">Instrucciones técnicas</dt>
                                <dd class="mt-1 text-gray-700">{{ $recomendacion->instrucciones_tecnicas }}</dd>
                            </div>
                        @endif
                    </dl>

                    <!-- Nota adicional -->
                    <div class="mt-8 p-4 bg-green-100/50 rounded-lg border border-green-200">
                        <p class="text-sm text-green-800">
                            <span class="font-semibold">Nota:</span> Esta recomendación se aplica durante la etapa 
                            <strong>{{ $recomendacion->etapa->nombre ?? '' }}</strong>, 
                            con un desfase de <strong>{{ $recomendacion->dias_offset }} días</strong> desde el inicio de la misma.
                            @if($recomendacion->genera_evento_automatico)
                                Se generará un evento de campo automáticamente.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>