@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-xl">
    <div class="bg-white shadow-md rounded-lg p-6">
        
        <div class="mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-green-600">Cultivo: {{ $cultivo->nombre_cultivo }}</span>
            <h2 class="text-xl font-bold text-gray-800">
                {{ isset($labor) ? 'Editar Labor de Plantilla' : 'Nueva Labor para Plantilla' }}
            </h2>
        </div>

        <form action="{{ isset($labor) ? route('labores-plantilla.update', $labor) : route('labores-plantilla.store') }}" method="POST">
            @csrf
            @if(isset($labor))
                @method('PUT')
            @endif

            <input type="hidden" name="cultivo_id" value="{{ $cultivo->id }}">

            <div class="mb-4">
                <label for="nombre_labor" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Labor *</label>
                <input type="text" name="nombre_labor" id="nombre_labor" placeholder="Ej: Fertilización nitrogenada, Poda de formación"
                       value="{{ old('nombre_labor', $labor->nombre_labor ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required>
            </div>

            <div class="mb-4">
                <label for="tipo_evento_id" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Evento/Categoría *</label>
                <select name="tipo_evento_id" id="tipo_evento_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required>
                    <option value="">-- Selecciona un tipo --</option>
                    @foreach($tiposEvento as $tipo)
                        <option value="{{ $tipo->id }}" {{ old('tipo_evento_id', $labor->tipo_evento_id ?? '') == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nombre ?? 'Evento #'.$tipo->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">Programación del Momento</h3>

                <div class="mb-4">
                    <label for="momento_tipo" class="block text-sm font-medium text-gray-700 mb-1">¿Cómo se agendará esta labor? *</label>
                    <select name="momento_tipo" id="momento_tipo" onchange="toggleMomentoCampos()" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required>
                        <option value="dias_desde_siembra" {{ old('momento_tipo', $labor->momento_tipo ?? '') == 'dias_desde_siembra' ? 'selected' : '' }}>Días desde la siembra/inicio</option>
                        <option value="etapa_fenologica" {{ old('momento_tipo', $labor->momento_tipo ?? '') == 'etapa_fenologica' ? 'selected' : '' }}>En una Etapa Fenológica específica</option>
                        <option value="fecha_fija_anual" {{ old('momento_tipo', $labor->momento_tipo ?? '') == 'fecha_fija_anual' ? 'selected' : '' }}>Fecha fija anual</option>
                    </select>
                </div>

                <div id="wrapper_dias_desde_siembra" class="mb-2 hidden">
                    <label for="dias_desde_siembra" class="block text-sm font-medium text-gray-700 mb-1">Número de días desde el inicio *</label>
                    <input type="number" name="dias_desde_siembra" id="dias_desde_siembra" min="0" placeholder="Ej: 15"
                           value="{{ old('dias_desde_siembra', $labor->dias_desde_siembra ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>

                <div id="wrapper_etapa_fenologica" class="mb-2 hidden">
                    <label for="fenologia_etapa_id" class="block text-sm font-medium text-gray-700 mb-1">Selecciona la Etapa Fenológica *</label>
                    <select name="fenologia_etapa_id" id="fenologia_etapa_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">-- Selecciona una etapa de este cultivo --</option>
                        @foreach($cultivo->etapasFenologicas->sortBy('orden') as $etapa)
                            <option value="{{ $etapa->id }}" {{ old('fenologia_etapa_id', $labor->fenologia_etapa_id ?? '') == $etapa->id ? 'selected' : '' }}>
                                #{{ $etapa->orden }} - {{ $etapa->nombre }} (Dura aprox. {{ $etapa->duracion_dias_estimada }} días)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="periodicidad_dias" class="block text-sm font-medium text-gray-700 mb-1">Periodicidad (Cada cuántos días)</label>
                    <input type="number" name="periodicidad_dias" id="periodicidad_dias" min="1" placeholder="Vacio si es única"
                           value="{{ old('periodicidad_dias', $labor->periodicidad_dias ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="duracion_estimada_horas" class="block text-sm font-medium text-gray-700 mb-1">Duración por Ha (Horas)</label>
                    <input type="number" name="duracion_estimada_horas" id="duracion_estimada_horas" min="1" placeholder="Ej: 4"
                           value="{{ old('duracion_estimada_horas', $labor->duracion_estimada_horas ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <div class="flex space-x-6 mb-4 p-2 bg-gray-50 rounded border border-gray-200">
                <label class="inline-flex items-center text-sm font-medium text-gray-700">
                    <input type="checkbox" name="requiere_insumos" value="1" {{ old('requiere_insumos', $labor->requiere_insumos ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500 mr-2">
                    ¿Requiere Insumos?
                </label>
                <label class="inline-flex items-center text-sm font-medium text-gray-700">
                    <input type="checkbox" name="requiere_mano_obra" value="1" {{ old('requiere_mano_obra', $labor->requiere_mano_obra ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500 mr-2">
                    ¿Requiere Mano de Obra?
                </label>
            </div>

            <div class="mb-6">
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Instrucciones o Detalles</label>
                <textarea name="descripcion" id="descripcion" rows="3" placeholder="Describe los detalles operativos de la labor..." class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('descripcion', $labor->descripcion ?? '') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('cultivos.show', $cultivo->id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium">Cancelar</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow">Guardar Labor</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleMomentoCampos() {
        const tipo = document.getElementById('momento_tipo').value;
        const wrapperDias = document.getElementById('wrapper_dias_desde_siembra');
        const wrapperEtapa = document.getElementById('wrapper_etapa_fenologica');

        // Resetear visibilidad
        wrapperDias.classList.add('hidden');
        wrapperEtapa.classList.add('hidden');

        if (tipo === 'dias_desde_siembra') {
            wrapperDias.classList.remove('hidden');
        } else if (tipo === 'etapa_fenologica') {
            wrapperEtapa.classList.remove('hidden');
        }
    }

    // Ejecutar al cargar la vista por si es una edición o hay errores de validación
    document.addEventListener("DOMContentLoaded", function() {
        toggleMomentoCampos();
    });
</script>
@endsection