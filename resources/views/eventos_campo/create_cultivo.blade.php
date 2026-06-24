<x-app-layout>
<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10">
    <div class="border-b pb-4 mb-6">
        <span class="text-sm font-bold uppercase tracking-wider text-green-600">Fase 1: Programación Agronómica</span>
        <h2 class="text-2xl font-bold text-gray-800 mt-1">Nuevo Evento para: {{ $ciclo->nombre_campana }}</h2>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('eventos_campo.store_cultivo', $ciclo->id) }}" method="POST">
        @csrf

        <div class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200 grid grid-cols-2 gap-2">
            <div>
                <span class="text-xs text-gray-500 font-semibold uppercase">Ciclo Productivo Vinculado</span>
                <p class="font-bold text-gray-700">{{ $ciclo->nombre_campana }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-semibold uppercase">Tipo de Destino</span>
                <p class="font-bold text-gray-700 text-sm">Georreferenciado (Lote/Zonas/Árboles)</p>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Labor / Tipo de Evento *</label>
            <select name="tipo_evento_id" class="w-full border p-2 rounded" required>
                <option value="">-- Seleccione la labor --</option>
                @foreach($tiposEvento as $tipo)
                    <option value="{{ $tipo->id }}" {{ old('tipo_evento_id') == $tipo->id ? 'selected' : '' }}>
                        {{ $tipo->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Lote Destino *</label>
            <select id="lote_id" name="lote_id" class="w-full border p-2 rounded" required onchange="filtrarZonas()">
                <option value="">-- Seleccione un lote --</option>
                @foreach($lotes as $lote)
                    <option value="{{ $lote->id }}" {{ old('lote_id') == $lote->id ? 'selected' : '' }}>
                        {{ $lote->nombre_lote }} ({{ $lote->codigo_lote }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Zona de Manejo (Opcional)</label>
            <select id="zona_id" name="zona_id" class="w-full border p-2 rounded">
                <option value="">-- Todo el lote completo --</option>
                @foreach($zonas as $zona)
                    <option value="{{ $zona->id }}" data-lote="{{ $zona->lote_id }}" {{ old('zona_id') == $zona->id ? 'selected' : '' }}>
                        {{ $zona->nombre_zona }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400 mt-1">Si no seleccionas una zona, la labor se entenderá para todo el lote.</p>
        </div>

        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <label class="block text-green-900 font-bold mb-2">¿Cuál es el alcance u objetivo de esta labor?</label>
            <div class="space-y-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="alcance" value="lote_zona" checked class="text-green-600 focus:ring-green-500">
                    <span class="text-sm text-gray-700 font-medium">Aplicar de forma global al lote / zona (Predeterminado)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="alcance" value="arbol" class="text-green-600 focus:ring-green-500">
                    <span class="text-sm text-gray-700 font-medium text-orange-700 font-semibold">
                        Asignación Masiva por Árbol (Afecta la tabla evento_arbol) 🌲
                    </span>
                </label>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Fecha Programada de Ejecución *</label>
            <input type="date" name="fecha_programada" value="{{ old('fecha_programada', date('Y-m-d')) }}" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Instrucciones técnicas para los operarios</label>
            <textarea name="observaciones" rows="3" placeholder="Ej: Aplicar dosis recomendada solo bajo sombra..." class="w-full border p-2 rounded">{{ old('observaciones') }}</textarea>
        </div>

        <input type="hidden" name="estado" value="Pendiente">

        <div class="flex justify-end gap-4">
            <a href="{{ route('eventos_campo.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancelar</a>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-semibold">Agendar al Cultivo</button>
        </div>
    </form>
</div>

<script>
function filtrarZonas() {
    const loteSelect = document.getElementById('lote_id');
    const zonaSelect = document.getElementById('zona_id');
    const loteIdSeleccionado = loteSelect.value;
    
    // Reseteamos el select de zonas a su opción por defecto
    zonaSelect.value = "";
    
    // Recorremos las opciones del select de zonas buscando el atributo 'data-lote'
    const opciones = zonaSelect.options;
    for (let i = 1; i < opciones.length; i++) {
        const opcion = opciones[i];
        const lotePadreId = opcion.getAttribute('data-lote');
        
        if (loteIdSeleccionado === "" || lotePadreId === loteIdSeleccionado) {
            opcion.style.display = "block"; // Mostrar zona si pertenece al lote
        } else {
            opcion.style.display = "none";  // Ocultar zona si es de otro lote
        }
    }
}

// Ejecutar al cargar la página por si hay valores cargados de la sesión anterior (old)
document.addEventListener("DOMContentLoaded", function() {
    filtrarZonas();
});
</script>
</x-app-layout>