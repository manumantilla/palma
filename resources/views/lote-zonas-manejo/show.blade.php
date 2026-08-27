<x-app-layout>
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Detalle de Zona de Manejo</h1>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600 text-sm">Código</p>
                <p class="text-lg font-semibold">{{ $loteZonaManejo->codigo_zona }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Nombre</p>
                <p class="text-lg font-semibold">{{ $loteZonaManejo->nombre_zona }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Lote</p>
                <p class="text-lg font-semibold">{{ $loteZonaManejo->lote->nombre_lote ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Área (Ha)</p>
                <p class="text-lg font-semibold">{{ number_format($loteZonaManejo->area_hectareas, 4) }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-gray-600 text-sm">Geometría (WKT)</p>
                <p class="text-sm bg-gray-100 p-2 rounded overflow-x-auto">{{ $loteZonaManejo->geometria_zona ?? 'No definida' }}</p>
            </div>
        </div>
        <div class="mt-6 flex space-x-4">
            <a href="{{ route('lote-zonas-manejo.edit', $loteZonaManejo) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">Editar</a>
            <a href="{{ route('lote-zonas-manejo.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Volver</a>
        </div>
    </div>
</div>
</x-app-layout>