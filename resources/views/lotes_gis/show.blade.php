<x-app-layout>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #geometry-map-show { height: 400px; border-radius: 0.75rem; z-index: 0; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    window.initShowMap = function() {
        // Capturamos el GeoJSON generado por la base de datos
        const geojsonRaw = @json($loteGis->geometria_geojson);
        
        // Capas base
        const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri'
        });

        const streets = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OSM & CartoDB'
        });

        // Inicializar mapa centrado en Colombia por defecto
        const map = L.map('geometry-map-show', {
            center: [4.5709, -74.2973],
            zoom: 6,
            layers: [satellite]
        });

        const baseMaps = {
            "Vista Satelital": satellite,
            "Mapa de Calles": streets
        };
        L.control.layers(baseMaps).addTo(map);

        // Procesar y renderizar el GeoJSON
        if (geojsonRaw) {
            try {
                const geojsonData = JSON.parse(geojsonRaw);

                // Leaflet dibuja automáticamente Polígonos o MultiPolígonos desde GeoJSON
                const geojsonLayer = L.geoJSON(geojsonData, {
                    style: {
                        color: '#10b981',      // Verde esmeralda
                        fillColor: '#10b981',
                        fillOpacity: 0.25,
                        weight: 3
                    }
                }).addTo(map);

                // Auto-ajustar el zoom a los límites de la geometría
                map.fitBounds(geojsonLayer.getBounds());
            } catch (error) {
                console.error("Error al interpretar el GeoJSON del lote:", error);
            }
        }
    };

    document.addEventListener('DOMContentLoaded', initShowMap);
</script>
@endpush

<div class="container mx-auto px-4 py-8 max-w-6xl">
    
    {{-- Encabezado de Control --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('lotes-gis.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition">
                ← Volver a la lista
            </a>
            <div class="h-4 w-px bg-gray-300"></div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <span>Lote: {{ $loteGis->nombre_lote }}</span>
                <span class="text-sm font-mono bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md border border-gray-200">
                    {{ $loteGis->codigo_lote }}
                </span>
            </h1>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">

        </div>
    </div>

    {{-- Grid Principal del Dashboard --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Bloque Izquierdo: Mapa e Información Espacial --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Tarjeta de Mapa --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                    🌍 Delimitación Geográfica e Información SIG
                </h2>
                <div id="geometry-map-show"></div>
                <div class="grid grid-cols-2 gap-4 mt-4 bg-gray-50 p-3 rounded-lg border border-gray-100 text-sm">
                    <div>
                        <span class="text-gray-500 block">Área Declarada:</span>
                        <strong class="text-gray-900 text-base">{{ number_format($loteGis->area_hectareas_declaradas, 2) }} Ha</strong>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Área Calculada por GIS:</span>
                        <strong class="text-gray-900 text-base">
                            {{ $loteGis->area_hectareas_gis ? number_format($loteGis->area_hectareas_gis, 4) . ' Ha' : 'No calculada' }}
                        </strong>
                    </div>
                </div>
            </div>

            {{-- Fincas y Zonas de Manejo Relacionadas --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-4 flex items-center gap-2">
                    🗺️ Zonas de Manejo de este Lote
                </h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3">Nombre Zona</th>
                                <th class="px-4 py-3">Tipo de Manejo</th>
                                <th class="px-4 py-3 text-right">Área</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($loteGis->zonasManejo as $zona)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 font-semibold text-gray-900">{{ $zona->nombre_zona ?? $zona->nombre }}</td>
                                    <td class="px-4 py-3"><span class="bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full font-medium">{{ $zona->tipo_manejo ?? 'General' }}</span></td>
                                    <td class="px-4 py-3 text-right text-gray-900 font-medium">{{ number_format($zona->area_hectareas ?? 0, 2) }} Ha</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-400 italic">
                                        No hay micro-zonas de manejo agronómico delimitadas internamente en este lote.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Historial de Analíticas de Suelo --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-4 flex items-center gap-2">
                    🧪 Historial de Laboratorio (Muestras de Suelo)
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3">Código/Fecha</th>
                                <th class="px-4 py-3">pH</th>
                                <th class="px-4 py-3">Materia Orgánica</th>
                                <th class="px-4 py-3">Textura Lab</th>
                                <th class="px-4 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($loteGis->analiticasSuelo as $analitica)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3">
                                        <span class="block text-gray-900 font-semibold">{{ $analitica->fecha_muestreo ?? 'S/F' }}</span>
                                        <span class="text-xs text-gray-400 font-mono">{{ $analitica->codigo_muestra ?? '#' . $analitica->id }}</span>
                                    </td>
                                    <td class="px-4 py-3 font-mono font-bold text-gray-900">{{ number_format($analitica->ph ?? 0, 2) }}</td>
                                    <td class="px-4 py-3">{{ $analitica->materia_organica ?? 'N/A' }}%</td>
                                    <td class="px-4 py-3 capitalize">{{ $analitica->textura_verificada ?? 'No registrada' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="text-xs text-green-600 font-medium hover:underline cursor-pointer">Ver Completo</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 italic">
                                        No se registran datos fisicoquímicos históricos de laboratorio para este lote.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Bloque Derecho: Ficha Técnica Estructural --}}
        <div class="space-y-6">
            
            {{-- Tarjeta Finca Raíz / Pertenece a --}}
            <div class="bg-gradient-to-br from-green-800 to-emerald-900 rounded-xl shadow-md p-6 text-white">
                <span class="text-xs uppercase tracking-wider text-green-200 font-medium">Unidad Productiva Principal</span>
                <h3 class="text-xl font-bold mt-1">{{ $loteGis->finca->nombre ?? 'Finca No Asignada' }}</h3>
                <hr class="my-3 border-green-700/50">
                <div class="text-sm space-y-2 text-green-100">
                    <p class="flex justify-between"><span>Estatus Operativo:</span> <span class="bg-green-700 px-2 py-0.5 rounded text-xs text-white font-bold">Activo en GIS</span></p>
                    <p class="flex justify-between"><span>Registro ICA:</span> <strong class="text-white">{{ $loteGis->registro_ica ?? 'Sin Registro' }}</strong></p>
                    <p class="flex justify-between"><span>Régimen Tenencia:</span> <strong class="text-white capitalize">{{ $loteGis->tenencia }}</strong></p>
                </div>
            </div>

            {{-- Factores Topográficos y Edafoclimáticos --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-100 pb-2">
                    ⛰️ Variables Edafoclimáticas
                </h2>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center py-1 border-b border-gray-50">
                        <span class="text-gray-500">Altitud Promedio</span>
                        <span class="font-semibold text-gray-900">{{ number_format($loteGis->altitud_mediana_msnm, 0) }} msnm</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-gray-50">
                        <span class="text-gray-500">Pendiente Numérica</span>
                        <span class="font-semibold text-gray-900">{{ number_format($loteGis->pendiente_promedio_porcentaje, 1) }}%</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-gray-50">
                        <span class="text-gray-500">Relieve del Terreno</span>
                        <span class="font-semibold text-gray-900 capitalize bg-amber-50 text-amber-800 px-2.5 py-0.5 rounded-md border border-amber-200/50">
                            {{ str_replace('_', ' ', $loteGis->pendiente_terreno) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-gray-50">
                        <span class="text-gray-500">Clase de Suelo (Textura)</span>
                        <span class="font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $loteGis->tipo_suelo) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-gray-500">pH Ficha Técnica</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-gray-900">{{ number_format($loteGis->ph_suelo, 2) }}</span>
                            @if($loteGis->ph_suelo < 5.5)
                                <span class="text-[10px] bg-red-100 text-red-700 px-1.5 py-0.5 rounded font-bold">Ácido</span>
                            @elseif($loteGis->ph_suelo > 7.5)
                                <span class="text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-bold">Alcalino</span>
                            @else
                                <span class="text-[10px] bg-green-100 text-green-700 px-1.5 py-0.5 rounded font-bold">Neutro</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Infraestructura Hidráulica / Riego --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-100 pb-2">
                    💧 Ecosistema Hidráulico y Riego
                </h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">¿Posee Red de Riego?</span>
                        @if($loteGis->tiene_riego_instalado)
                            <span class="bg-teal-100 text-teal-800 text-xs font-bold px-3 py-1 rounded-full">Sí Instalado</span>
                        @else
                            <span class="bg-gray-100 text-gray-500 text-xs font-bold px-3 py-1 rounded-full">No Posee</span>
                        @endif
                    </div>

                    @if($loteGis->tiene_riego_instalado)
                        <div class="flex justify-between items-center py-1 border-t border-gray-50 pt-2">
                            <span class="text-gray-500">Fuente Suministro</span>
                            <span class="font-semibold text-gray-900 capitalize bg-cyan-50 text-cyan-800 px-2 py-0.5 rounded border border-cyan-100">
                                {{ $loteGis->fuente_agua }}
                            </span>
                        </div>
                        
                        <div class="pt-2">
                            <span class="text-xs font-medium text-gray-400 block mb-2">Equipos / Infraestructuras asociadas:</span>
                            <ul class="space-y-1.5 text-xs text-gray-600">
                                @forelse($loteGis->sistemasRiego as $riego)
                                    <li class="flex items-center gap-1.5 bg-gray-50 p-2 rounded border border-gray-100">
                                        <span>⚙️</span>
                                        <div>
                                            <strong class="text-gray-900 block">{{ $riego->tipo_sistema ?? 'Aspersión/Goteo' }}</strong>
                                            <span class="text-[11px] text-gray-400">Caudal: {{ $riego->caudal_disponible ?? 'No def' }} - Eficiencia: {{ $riego->eficiencia_porcentaje ?? '100' }}%</span>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-gray-400 italic text-center py-1">No se han detallado las marcas/tipos de emisores hidráulicos.</li>
                                @endforelse
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
</x-app-layout>