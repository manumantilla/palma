<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-800 leading-tight">
            {{ __('Detalle de Etapa Fenológica') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-200">
                <div class="p-6 bg-green-50/30">
                    <div class="mb-6 flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-bold text-green-900">{{ $etapa->nombre }}</h3>
                            <p class="text-sm text-green-700">Orden: {{ $etapa->orden }}</p>
                        </div>
                        <div class="space-x-2">
                            <a href="{{ route('fenologia-etapa.edit', $etapa) }}" 
                               class="inline-flex items-center px-3 py-1 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Editar
                            </a>
                            <a href="{{ route('fenologia-etapa.index') }}" 
                               class="inline-flex items-center px-3 py-1 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Volver
                            </a>
                        </div>
                    </div>

                    <!-- Datos principales -->
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Cultivo</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $etapa->cultivo->nombre ?? 'Sin asignar' }}</dd>
                        </div>
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Duración estimada</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $etapa->duracion_dias_estimada }} días</dd>
                        </div>
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100">
                            <dt class="text-sm font-medium text-green-600">Duración desde inicio</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $etapa->duracion_dias_desde_inicio ?? 'No definida' }}</dd>
                        </div>
                        <div class="bg-white/60 p-4 rounded-lg border border-green-100 col-span-2">
                            <dt class="text-sm font-medium text-green-600">Descripción</dt>
                            <dd class="mt-1 text-gray-700">{{ $etapa->descripcion ?? 'Sin descripción' }}</dd>
                        </div>
                    </dl>

                    <!-- Relaciones: Recomendaciones -->
                    <div class="mt-8">
                        <h4 class="text-lg font-semibold text-green-800 mb-4">Recomendaciones asociadas</h4>
                        @if($etapa->recomendaciones->count() > 0)
                            <ul class="divide-y divide-green-100 bg-white/60 rounded-lg border border-green-100">
                                @foreach($etapa->recomendaciones as $recomendacion)
                                    <li class="p-4 hover:bg-green-50/50 transition">
                                        <p class="text-sm text-gray-800">{{ $recomendacion->descripcion ?? 'Recomendación' }}</p>
                                        <!-- Puedes agregar más campos según tu modelo FenologiaRecomendacion -->
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500 italic">No hay recomendaciones registradas para esta etapa.</p>
                        @endif
                    </div>

                    <!-- Relaciones: Historiales -->
                    <div class="mt-8">
                        <h4 class="text-lg font-semibold text-green-800 mb-4">Historial de ciclos</h4>
                        @if($etapa->historiales->count() > 0)
                            <ul class="divide-y divide-green-100 bg-white/60 rounded-lg border border-green-100">
                                @foreach($etapa->historiales as $historial)
                                    <li class="p-4 hover:bg-green-50/50 transition">
                                        <p class="text-sm text-gray-800">
                                            Ciclo ID: {{ $historial->ciclo_id ?? 'N/A' }} - 
                                            Fecha inicio: {{ $historial->fecha_inicio ?? 'N/A' }}
                                            <!-- Ajusta según los campos de CicloEtapaHistorial -->
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500 italic">No hay registros históricos para esta etapa.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>