@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    
    <div class="mb-6">
        <a href="{{ route('cultivos.index') }}" class="text-sm text-green-600 hover:text-green-800 font-medium">
            ← Volver al listado
        </a>
        <div class="flex justify-between items-center mt-2">
            <h1 class="text-3xl font-bold text-gray-900">{{ $cultivo->nombre_cultivo }}</h1>
            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $cultivo->tipo === 'perenne' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                Tipo: {{ ucfirst($cultivo->tipo) }}
            </span>
        </div>
        <p class="text-gray-600 mt-2 italic">{{ $cultivo->descripcion ?? 'Sin descripción disponible.' }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="border-b border-gray-200 mb-6">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button onclick="switchTab('etapas')" id="tab-btn-etapas" class="border-b-2 border-green-500 py-4 px-1 text-sm font-medium text-green-600 whitespace-nowrap UI-tab-btn">
                Etapas Fenológicas ({{ $cultivo->etapasFenologicas->count() }})
            </button>
            <button onclick="switchTab('labores')" id="tab-btn-labores" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap UI-tab-btn">
                Plantilla de Labores ({{ $cultivo->laboresPlantilla->count() }})
            </button>
        </nav>
    </div>

    <div id="tab-content-etapas" class="UI-tab-content">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Ciclo Fenológico</h2>
            <a href="{{ route('fenologia-etapas.create', ['cultivo_id' => $cultivo->id]) }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-3 rounded shadow">
                + Agregar Etapa
            </a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orden</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Etapa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inicio (Días)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duración Estimada</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($cultivo->etapasFenologicas->sortBy('orden') as $etapa)
                        <tr>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">#{{ $etapa->orden }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $etapa->nombre }}</div>
                                <div class="text-xs text-gray-500">{{ $etapa->descripcion }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $etapa->duracion_dias_desde_inicio ?? 'N/A' }} días
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $etapa->duracion_dias_estimada }} días
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-2">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No hay etapas fenológicas configuradas para este cultivo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="tab-content-labores" class="UI-tab-content hidden">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Labores Programadas por Defecto</h2>
            <a href="{{ route('labores-plantilla.create', ['cultivo_id' => $cultivo->id]) }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-3 rounded shadow">
                + Agregar Labor
            </a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Labor / Tarea</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Momento de Ejecución</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Frecuencia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recursos</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($cultivo->laboresPlantilla as $labor)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $labor->nombre_labor }}</div>
                                <div class="text-xs text-gray-400">Duración: {{ $labor->duracion_estimada_horas ?? 'N/A' }}h/ha</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($labor->momento_tipo === 'dias_desde_siembra')
                                    <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded">
                                        Día {{ $labor->dias_desde_siembra }} desde siembra
                                    </span>
                                @elseif($labor->momento_tipo === 'etapa_fenologica')
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">
                                        Etapa: {{ $labor->etapaFenologica->nombre ?? 'Desconocida' }}
                                    </span>
                                @else
                                    <span class="bg-amber-100 text-amber-800 text-xs font-medium px-2 py-1 rounded">
                                        Fecha fija anual
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $labor->periodicidad_dias ? "Cada {$labor->periodicidad_dias} días" : 'Única vez' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex flex-col space-y-1">
                                    <span class="text-xs {{ $labor->requiere_insumos ? 'text-green-600 font-semibold' : 'text-gray-400' }}">
                                        {{ $labor->requiere_insumos ? '✓ Requiere Insumos' : '✕ Sin Insumos' }}
                                    </span>
                                    <span class="text-xs {{ $labor->requiere_mano_obra ? 'text-green-600 font-semibold' : 'text-gray-400' }}">
                                        {{ $labor->requiere_mano_obra ? '✓ Mano de Obra' : '✕ Sin Mano de Obra' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No hay labores preestablecidas para este cultivo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function switchTab(tabId) {
        // Ocultar todo el contenido de pestañas
        document.querySelectorAll('.UI-tab-content').forEach(el => el.classList.add('hidden'));
        // Mostrar el contenido seleccionado
        document.getElementById('tab-content-' + tabId).classList.remove('hidden');

        // Resetear estilos de los botones
        document.querySelectorAll('.UI-tab-btn').forEach(btn => {
            btn.classList.remove('border-green-500', 'text-green-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });

        // Activar el botón seleccionado
        const activeBtn = document.getElementById('tab-btn-' + tabId);
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
        activeBtn.classList.add('border-green-500', 'text-green-600');
    }
</script>
@endsection