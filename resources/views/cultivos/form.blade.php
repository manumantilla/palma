@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-lg">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">
            {{ isset($cultivo) ? 'Editar Cultivo' : 'Crear Nuevo Cultivo' }}
        </h2>

        <form action="{{ isset($cultivo) ? route('cultivos.update', $cultivo) : route('cultivos.store') }}" method="POST">
            @csrf
            @if(isset($cultivo))
                @method('PUT')
            @endif

            <div class="mb-4">
                <label for="nombre_cultivo" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Cultivo *</label>
                <input type="text" name="nombre_cultivo" id="nombre_cultivo" 
                       value="{{ old('nombre_cultivo', $cultivo->nombre_cultivo ?? '') }}" 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 @error('nombre_cultivo') border-red-500 @enderror" required>
                @error('nombre_cultivo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Cultivo *</label>
                <select name="tipo" id="tipo" class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    <option value="transitorio" {{ old('tipo', $cultivo->tipo ?? '') == 'transitorio' ? 'selected' : '' }}>Transitorio (Ciclo corto)</option>
                    <option value="perenne" {{ old('tipo', $cultivo->tipo ?? '') == 'perenne' ? 'selected' : '' }}>Perenne (Ciclo largo)</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="3" 
                          class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('descripcion', $cultivo->descripcion ?? '') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('cultivos.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium">
                    Cancelar
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow">
                    {{ isset($cultivo) ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection