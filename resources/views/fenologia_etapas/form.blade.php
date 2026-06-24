@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-lg">
    <div class="bg-white shadow-md rounded-lg p-6">
        
        <div class="mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-green-600">Cultivo: {{ $cultivo->nombre_cultivo }}</span>
            <h2 class="text-xl font-bold text-gray-800">
                {{ isset($etapa) ? 'Editar Etapa Fenológica' : 'Nueva Etapa Fenológica' }}
            </h2>
        </div>

        <form action="{{ isset($etapa) ? route('fenologia-etapas.update', $etapa) : route('fenologia-etapas.store') }}" method="POST">
            @csrf
            @if(isset($etapa))
                @method('PUT')
            @endif

            <input type="hidden" name="cultivo_id" value="{{ $cultivo->id }}">

            <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Etapa *</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ej: Floración, Maduración, Cuajado"
                       value="{{ old('nombre', $etapa->nombre ?? '') }}" 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500" required>
                @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="orden" class="block text-sm font-medium text-gray-700 mb-1">Orden de Aparición *</label>
                    <input type="number" name="orden" id="orden" min="1" placeholder="Ej: 1"
                           value="{{ old('orden', $etapa->orden ?? '') }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    @error('orden') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="duracion_dias_estimada" class="block text-sm font-medium text-gray-700 mb-1">Duración (Días) *</label>
                    <input type="number" name="duracion_dias_estimada" id="duracion_dias_estimada" min="1" placeholder="¿Cuánto dura?"
                           value="{{ old('duracion_dias_estimada', $etapa->duracion_dias_estimada ?? '') }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    @error('duracion_dias_estimada') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="duracion_dias_desde_inicio" class="block text-sm font-medium text-gray-700 mb-1">
                    Días transcurridos desde el inicio del ciclo
                </label>
                <input type="number" name="duracion_dias_desde_inicio" id="duracion_dias_desde_inicio" min="0" placeholder="Opcional. Ej: Al día 45"
                       value="{{ old('duracion_dias_desde_inicio', $etapa->duracion_dias_desde_inicio ?? '') }}" 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500">
                <p class="text-gray-400 text-xs mt-1">
                    Días desde la siembra ({{ $cultivo->tipo }}) o desde la brotación inicial.
                </p>
                @error('duracion_dias_desde_inicio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción / Características</label>
                <textarea name="descripcion" id="descripcion" rows="3" placeholder="Detalles visuales o condiciones de la etapa..."
                          class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('descripcion', $etapa->descripcion ?? '') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('cultivos.show', $cultivo->id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium">
                    Cancelar
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow">
                    {{ isset($etapa) ? 'Actualizar' : 'Guardar Etapa' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection