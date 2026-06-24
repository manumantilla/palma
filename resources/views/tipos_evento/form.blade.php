@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-xl">
    <div class="bg-white shadow-md rounded-lg p-6">
        
        <h2 class="text-xl font-bold text-gray-800 mb-6">
            {{ isset($tipo) ? 'Editar Tipo de Evento' : 'Nuevo Tipo de Evento' }}
        </h2>

        <form action="{{ isset($tipo) ? route('tipos-evento.update', $tipo) : route('tipos-evento.store') }}" method="POST">
            @csrf
            @if(isset($tipo))
                @method('PUT')
            @endif

            <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Actividad *</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ej: Fumigación contra roya, Cosecha manual"
                       value="{{ old('nombre', $tipo->nombre ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required>
            </div>

            <div class="mb-6">
                <label for="categoria" class="block text-sm font-medium text-gray-700 mb-1">Categoría Operativa *</label>
                <select name="categoria" id="categoria" onchange="checkFitosanitario()" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required>
                    <option value="">-- Elige una categoría --</option>
                    @foreach(['Mantenimiento', 'Fitosanitario', 'Cosecha', 'Fertilización', 'Logística'] as $cat)
                        <option value="{{ $cat }}" {{ old('categoria', $tipo->categoria ?? '') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div id="seccion_fitosanitaria" class="bg-amber-50 border border-amber-200 p-4 rounded-lg mb-6 hidden">
                <h3 class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-3">Parámetros de Seguridad Fitosanitaria</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="periodo_reingreso_horas" class="block text-xs font-medium text-amber-900 mb-1">Periodo Reingreso (Horas)</label>
                        <input type="number" name="periodo_reingreso_horas" id="periodo_reingreso_horas" min="0" placeholder="Ej: 24"
                               value="{{ old('periodo_reingreso_horas', $tipo->periodo_reingreso_horas ?? '') }}" class="w-full border-amber-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    <div>
                        <label for="periodo_carencia_dias" class="block text-xs font-medium text-amber-900 mb-1">Periodo Carencia (Días)</label>
                        <input type="number" name="periodo_carencia_dias" id="periodo_carencia_dias" min="0" placeholder="Ej: 7"
                               value="{{ old('periodo_carencia_dias', $tipo->periodo_carencia_dias ?? '') }}" class="w-full border-amber-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 border border-gray-200 p-4 rounded-lg mb-6">
                <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Comportamiento e Impacto</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="flex items-center text-sm text-gray-700">
                        <input type="checkbox" name="consume_insumos" value="1" {{ old('consume_insumos', $tipo->consume_insumos ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500 mr-2">
                        Consume Insumos
                    </label>
                    <label class="flex items-center text-sm text-gray-700">
                        <input type="checkbox" name="consume_mano_obra" value="1" {{ old('consume_mano_obra', $tipo->consume_mano_obra ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500 mr-2">
                        Consume Mano de Obra
                    </label>
                    <label class="flex items-center text-sm text-gray-700">
                        <input type="checkbox" name="genera_ingreso" value="1" {{ old('genera_ingreso', $tipo->genera_ingreso ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500 mr-2">
                        Genera Ingreso (Venta)
                    </label>
                    <label class="flex items-center text-sm text-gray-700">
                        <input type="checkbox" name="genera_movimiento_stock" value="1" {{ old('genera_movimiento_stock', $tipo->genera_movimiento_stock ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500 mr-2">
                        Mueve Inventario/Stock
                    </label>
                </div>

                <hr class="my-3 border-gray-200">
                <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Unidad de Medida / Aplicación</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="flex items-center text-sm text-gray-700">
                        <input type="checkbox" name="requiere_area_ha" value="1" {{ old('requiere_area_ha', $tipo->requiere_area_ha ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500 mr-2">
                        Aplica por Hectáreas (Ha)
                    </label>
                    <label class="flex items-center text-sm text-gray-700">
                        <input type="checkbox" name="aplica_a_arbol" value="1" {{ old('aplica_a_arbol', $tipo->aplica_a_arbol ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500 mr-2">
                        Aplica a Árbol Individual
                    </label>
                    <label class="flex items-center text-sm text-gray-700">
                        <input type="checkbox" name="aplica_a_ciclo" value="1" {{ old('aplica_a_ciclo', $tipo->aplica_a_ciclo ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500 mr-2">
                        Aplica a todo el Ciclo
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('tipos-evento.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium">Cancelar</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow">Guardar Tipo</button>
            </div>
        </form>
    </div>
</div>

<script>
    function checkFitosanitario() {
        const categoria = document.getElementById('categoria').value;
        const divFito = document.getElementById('seccion_fitosanitaria');
        
        if (categoria === 'Fitosanitario') {
            divFito.classList.remove('hidden');
        } else {
            divFito.classList.add('hidden');
            // Limpiar valores por seguridad si se oculta
            document.getElementById('periodo_reingreso_horas').value = '';
            document.getElementById('periodo_carencia_dias').value = '';
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        checkFitosanitario();
    });
</script>
@endsection 