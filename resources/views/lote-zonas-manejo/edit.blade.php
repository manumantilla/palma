<x-app-layout>
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Editar Zona de Manejo</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('lote-zonas-manejo.update', $loteZonaManejo) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="lote_id" class="block text-gray-700 text-sm font-bold mb-2">Lote *</label>
            <select name="lote_id" id="lote_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @foreach ($lotes as $lote)
                    <option value="{{ $lote->id }}" {{ old('lote_id', $loteZonaManejo->lote_id) == $lote->id ? 'selected' : '' }}>
                        {{ $lote->codigo_lote }} - {{ $lote->nombre_lote }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="nombre_zona" class="block text-gray-700 text-sm font-bold mb-2">Nombre de la Zona *</label>
            <input type="text" name="nombre_zona" id="nombre_zona" value="{{ old('nombre_zona', $loteZonaManejo->nombre_zona) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>

        <div class="mb-4">
            <label for="codigo_zona" class="block text-gray-700 text-sm font-bold mb-2">Código de Zona * (único)</label>
            <input type="text" name="codigo_zona" id="codigo_zona" value="{{ old('codigo_zona', $loteZonaManejo->codigo_zona) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>

        <div class="mb-4">
            <label for="area_hectareas" class="block text-gray-700 text-sm font-bold mb-2">Área (hectáreas) *</label>
            <input type="number" step="0.0001" name="area_hectareas" id="area_hectareas" value="{{ old('area_hectareas', $loteZonaManejo->area_hectareas) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>

        <div class="mb-4">
            <label for="geometria_zona" class="block text-gray-700 text-sm font-bold mb-2">Geometría (WKT - Polígono)</label>
            <textarea name="geometria_zona" id="geometria_zona" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('geometria_zona', $loteZonaManejo->geometria_zona) }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Opcional. Ejemplo: POLYGON((x1 y1, x2 y2, ...))</p>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Actualizar
            </button>
            <a href="{{ route('lote-zonas-manejo.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                Cancelar
            </a>
        </div>
    </form>
</div>
<x-app-layout>