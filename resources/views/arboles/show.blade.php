<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-2 md:space-y-0">
            <div>
                <nav class="text-sm font-medium text-gray-500 mb-1">
                    <a href="{{ route('lotes-gis.index') }}" class="hover:text-gray-700">Lotes</a> &middot; 
                    <a href="{{ route('lotes-gis.show', $arbol->lote_id) }}" class="hover:text-gray-700">{{ $arbol->lote->nombre_lote }}</a> &middot;
                    <span class="text-gray-900">Árbol {{ $arbol->codigo_unico }}</span>
                </nav>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                    Ficha Técnica: Individuo {{ $arbol->codigo_unico }}
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold bg-gray-100 text-gray-800">
                        {{ $arbol->etapa_biologica }}
                    </span>
                </h2>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('arboles.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Volver al índice
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'metricas' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <x-alert-notification />

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5 border-t-4 border-emerald-500">
                    <span class="text-xs text-gray-400 font-bold uppercase">Estado Vital</span>
                    <p class="text-lg font-bold text-gray-900 mt-1">{{ $arbol->estado_vital_badge }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5 border-t-4 border-indigo-500">
                    <span class="text-xs text-gray-400 font-bold uppercase">Edad Fisiológica</span>
                    <p class="text-lg font-bold text-gray-900 mt-1">{{ $arbol->edad_meses }} Meses</p>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5 border-t-4 border-amber-500">
                    <span class="text-xs text-gray-400 font-bold uppercase">Ubicación Campo</span>
                    <p class="text-lg font-bold text-gray-900 mt-1">Fila {{ $arbol->fila_indice }} &middot; Pos {{ $arbol->posicion_indice }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5 border-t-4 border-teal-500">
                    <span class="text-xs text-gray-400 font-bold uppercase">Rendimiento Histórico</span>
                    <p class="text-lg font-bold text-gray-900 mt-1">{{ number_format($arbol->produccion_acumulada_kg, 2) }} kg</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 lg:col-span-2">
                    <h3 class="text-md font-bold text-gray-900 border-b pb-2 mb-4">Información de Establecimiento y Variedad</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 block">Variedad botánica:</span>
                            <span class="font-semibold text-gray-800">{{ $arbol->variedad ?? 'No especificada' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Campaña / Ciclo Inicial:</span>
                            <span class="font-semibold text-gray-800">{{ $arbol->cicloProductivo->nombre_campana ?? 'Sin ciclo' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Fecha de Siembra:</span>
                            <span class="font-semibold text-gray-800">{{ $arbol->fecha_siembra ? $arbol->fecha_siembra->format('d/m/Y') : 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Primera Cosecha:</span>
                            <span class="font-semibold text-gray-800">{{ $arbol->fecha_primera_cosecha ? $arbol->fecha_primera_cosecha->format('d/m/Y') : 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Zona de Manejo Asociada:</span>
                            <span class="font-semibold text-gray-800 text-indigo-600">{{ $arbol->zonaManejo->nombre_zona ?? 'Ninguna segmentación' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Condición de Reemplazo:</span>
                            <span class="font-semibold text-gray-800">
                                {!! $arbol->es_reemplazo ? '⚠️ Sí (Reemplazado el ' . ($arbol->fecha_reemplazo ? $arbol->fecha_reemplazo->format('d/m/Y') : '') . ')' : 'Original' !!}
                            </span>
                        </div>
                    </div>

                    @if($arbol->observaciones)
                        <div class="mt-6 bg-gray-50 p-3 rounded border text-sm text-gray-600">
                            <span class="font-bold block text-gray-700 mb-1">Observaciones técnicas:</span>
                            {{ $arbol->observaciones }}
                        </div>
                    @endif
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-md font-bold text-gray-900 border-b pb-2 mb-4">Telemetría y Coordenadas</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Altitud Relativa:</span>
                                <span class="font-semibold text-gray-800">{{ $arbol->altitud ?? 'N/A' }} m</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Altitud Ortométrica (MSNM):</span>
                                <span class="font-semibold text-gray-800">{{ $arbol->altitud_ortometrica_msnm ?? 'N/A' }} msnm</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span class="text-gray-500">Longitud (X):</span>
                                <span class="font-mono text-xs font-semibold text-gray-800">{{ $arbol->coordenadas_gps[0] ?? 'Sin señal GPS' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Latitud (Y):</span>
                                <span class="font-mono text-xs font-semibold text-gray-800">{{ $arbol->coordenadas_gps[1] ?? 'Sin señal GPS' }}</span>
                            </div>
                        </div>
                    </div>
@if($arbol->coordenadas_gps)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Extraer las coordenadas procesadas por PostGIS desde el Accessor de Laravel
        // $arbol->coordenadas_gps retorna [longitud, latitud]
        const lng = {{ $arbol->coordenadas_gps[0] }};
        const lat = {{ $arbol->coordenadas_gps[1] }};
        const codigoUnico = "{{ $arbol->codigo_unico }}";
        const estadoVital = "{{ $arbol->estado_vital_badge }}";

        // 2. Inicializar el mapa centrado exactamente en el árbol con un zoom alto (ideal para agricultura)
        const map = L.map('map-arbol').setView([lat, lng], 19);

        // 3. Capa de Mapa Estándar (OpenStreetMap) por si la quieres como alternativa
        const osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 21,
            attribution: '© OpenStreetMap'
        });

        // 4. CAPA SATELITAL DE PRECISIÓN (Esri World Imagery)
        const satelital = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 21, // Permite un nivel de acercamiento brutal para ver copas de árboles
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        });

        // Activar la capa satelital por defecto
        satelital.addTo(map);

        // 5. Crear un controlador de capas en la esquina superior derecha por si quieren cambiar de vista
        const baseMaps = {
            "Vista Satelital": satelital,
            "Mapa Político": osm
        };
        L.control.layers(baseMaps).addTo(map);

        // 6. Dibujar el marcador del Árbol en el mapa
        const marker = L.marker([lat, lng]).addTo(map);

        // 7. Añadir un Popup informativo estilizado al hacer clic en el marcador
        marker.bindPopup(`
            <div style="font-family: sans-serif; sm:text-xs">
                <strong style="color: #4f46e5; font-size: 14px;">${codigoUnico}</strong><br>
                <span style="font-size: 12px; display: block; margin-top: 4px;">Status: ${estadoVital}</span>
                <span style="font-size: 11px; color: #6b7280; display: block;">Fila-Pos: F{{ $arbol->fila_indice }}-P{{ $arbol->posicion_indice }}</span>
            </div>
        `).openPopup(); // Abre el globo informativo por defecto al cargar el mapa
    });
</script>
@endif
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-md font-bold text-gray-900 border-b pb-2 mb-4">Ubicación Espacial de Precisión (PostGIS)</h3>
                        
                        @if($arbol->coordenadas_gps)
                            <div id="map-arbol" class="w-full h-80 rounded-lg shadow-inner border border-gray-200 z-10"></div>
                            
                            <div class="mt-2 flex justify-between text-xs text-gray-500 font-mono">
                                <span>Lat: {{ $arbol->coordenadas_gps[1] }}</span>
                                <span>Lng: {{ $arbol->coordenadas_gps[0] }}</span>
                                <span class="text-emerald-600 font-bold">EPSG:4326 (WGS84)</span>
                            </div>
                        @else
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-center text-sm text-amber-700">
                                ⚠️ Este individuo no cuenta con datos geométricos o de posicionamiento GPS registrados.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="border-b border-gray-200 bg-gray-50 px-4">
                    <nav class="-mb-px flex space-x-6">
                        <button 
                            @click="activeTab = 'metricas'"
                            :class="activeTab === 'metricas' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none"
                        >
                            📊 Métricas Alométricas e IoT ({{ $arbol->metricasHistoricas->count() }})
                        </button>
                        <button 
                            @click="activeTab = 'fitosanitario'"
                            :class="activeTab === 'fitosanitario' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none"
                        >
                            🛡️ Sanidad e Historial Fitosanitario ({{ $arbol->historialFitosanitario->count() }})
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <div x-show="activeTab === 'metricas'" space-y-6>
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Historial de Crecimiento y Teledetección</h4>
                            </div>

                        @if($arbol->metricasHistoricas->isEmpty())
                            <p class="text-sm text-gray-500 italic text-center py-6">No se han registrado telemetrías ni mediciones con drones para este árbol.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Dimensiones (H / Ø Tronco / Ø Copa)</th>
                                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">NDVI</th>
                                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">NDRE</th>
                                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Canopia (°C)</th>
                                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Escala BBCH</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Origen</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($arbol->metricasHistoricas as $metrica)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($metrica->fecha_medicion)->format('d/m/Y H:i') }}
                                                </td>
                                                <td class="px-4 py-2 text-gray-600">
                                                    {{ $metrica->altura_metros ?? '-' }}m / {{ $metrica->diametro_tronco_cm ?? '-' }}cm / {{ $metrica->diametro_copa_proyeccion_m ?? '-' }}m
                                                </td>
                                                <td class="px-4 py-2 text-center font-semibold {{ $metrica->indice_ndvi_medido > 0.6 ? 'text-emerald-600' : 'text-amber-600' }}">
                                                    {{ $metrica->indice_ndvi_medido ?? '-' }}
                                                </td>
                                                <td class="px-4 py-2 text-center text-gray-600">{{ $metrica->indice_ndre_medido ?? '-' }}</td>
                                                <td class="px-4 py-2 text-center text-gray-600">{{ $metrica->temperatura_canopia_celsius ? $metrica->temperatura_canopia_celsius.'°C' : '-' }}</td>
                                                <td class="px-4 py-2 text-center font-mono text-xs text-gray-700">{{ $metrica->codigo_escala_bbch ?? '-' }}</td>
                                                <td class="px-4 py-2">
                                                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                                        {{ $metrica->origen_datos }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div x-show="activeTab === 'fitosanitario'" space-y-6>
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Reportes e Incidencias de Sanidad Vegetal</h4>
                        </div>

                        @if($arbol->historialFitosanitario->isEmpty())
                            <p class="text-sm text-gray-500 italic text-center py-6">Excelente: No se registran afectaciones de plagas o enfermedades en la bitácora.</p>
                        @else
                            <div class="space-y-4">
                                @foreach($arbol->historialFitosanitario as $incidencia)
                                    <div class="border rounded-lg p-4 bg-gray-50 hover:bg-white transition shadow-sm">
                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b pb-2 mb-2 gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-gray-900 capitalize">{{ $incidencia->tipo_incidencia }}</span>
                                                <span class="text-sm text-gray-700 font-semibold bg-white px-2 py-0.5 rounded border">
                                                    🦠 {{ $incidencia->agente_patogeno_nombre }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs">
                                                <span class="text-gray-400 font-mono">{{ \Carbon\Carbon::parse($incidencia->fecha_hallazgo)->format('d/m/Y') }}</span>
                                                <span class="px-2 py-0.5 rounded font-bold uppercase 
                                                    {{ $incidencia->severidad_afectacion === 'critica_cuarentena' ? 'bg-red-100 text-red-800' : ($incidencia->severidad_afectacion === 'moderada' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800') }}">
                                                    {{ $incidencia->severidad_afectacion }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm mt-3">
                                            <div class="md:col-span-2">
                                                <span class="text-xs font-bold text-gray-400 uppercase block">Sintomatología</span>
                                                <p class="text-gray-700 mt-0.5">{{ $incidencia->descripcion_sintomas }}</p>
                                                
                                                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-3 text-xs text-gray-500">
                                                    <span>🛠️ Químico requerido: <strong class="text-gray-700">{{ $incidencia->requiere_intervencion_quimica ? 'Sí' : 'No' }}</strong></span>
                                                    <span>🎯 Estatus actual: <strong class="{{ $incidencia->caso_controlado ? 'text-green-600' : 'text-rose-600' }}">{{ $incidencia->caso_controlado ? 'Caso Solucionado' : 'Alerta Activa' }}</strong></span>
                                                </div>
                                            </div>
                                            
                                            <div class="flex flex-col justify-center items-center bg-white p-2 rounded border">
                                                @if($incidencia->evidencia_fotografica_url)
                                                    <a href="{{ $incidencia->evidencia_fotografica_url }}" target="_blank" class="group relative block overflow-hidden rounded">
                                                        <img src="{{ $incidencia->evidencia_fotografica_url }}" alt="Evidencia de campo" class="h-20 w-auto object-cover group-hover:scale-105 transition duration-200">
                                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-200">
                                                            <span class="text-[10px] text-white font-bold uppercase">Ampliar</span>
                                                        </div>
                                                    </a>
                                                @else
                                                    <div class="text-center text-gray-400 py-3 text-xs">
                                                        <svg class="mx-auto h-5 w-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375 0 1 1-.75 0 .375 0 0 1 .75 0Z" /></svg>
                                                        Sin evidencia
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout> 