<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        {{-- Encabezado con estilo agrícola --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-green-800 via-green-700 to-green-900 p-6 text-white shadow-xl">
            <div class="absolute right-0 top-0 -mt-12 -mr-12 h-48 w-48 rounded-full bg-green-500 opacity-20"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 h-32 w-32 rounded-full bg-yellow-500 opacity-10"></div>
            
            <div class="relative flex flex-col md:flex-row items-start md:items-center justify-between">
                <div class="flex items-center space-x-4">
                    {{-- Icono de árbol --}}
                    <svg class="h-12 w-12 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">
                            Árbol <span class="text-green-200">{{ $arbol->codigo_unico }}</span>
                        </h1>
                        <p class="text-green-100 text-sm flex items-center gap-2">
                            <span class="inline-block w-2 h-2 rounded-full {{ $arbol->estado_vital === 'activo' ? 'bg-green-400' : ($arbol->estado_vital === 'enfermo' ? 'bg-yellow-400' : 'bg-red-400') }}"></span>
                            {{ ucfirst($arbol->estado_vital ?? 'Sin estado') }}
                            · Edad: <strong>{{ $arbol->edad_meses }}</strong> meses
                            · Variedad: {{ $arbol->variedad ?? 'N/A' }}
                        </p>
                    </div>
                </div>
                <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
                    <span class="inline-flex items-center rounded-full bg-green-600 px-4 py-1 text-sm font-medium text-white">
                        {{ $arbol->etapa_biologica ?? 'Sin etapa' }}
                    </span>
                    @if($arbol->es_reemplazo)
                        <span class="inline-flex items-center rounded-full bg-yellow-500 px-4 py-1 text-sm font-medium text-white">
                            Reemplazo
                        </span>
                    @endif
                </div>
            </div>

            {{-- Mini estadísticas --}}
            <div class="relative mt-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div class="bg-white/10 rounded-lg p-3 backdrop-blur-sm">
                    <p class="text-green-200">Producción acumulada</p>
                    <p class="text-2xl font-semibold">{{ number_format($arbol->produccion_acumulada_kg ?? 0, 2) }} kg</p>
                </div>
                <div class="bg-white/10 rounded-lg p-3 backdrop-blur-sm">
                    <p class="text-green-200">Ciclos productivos</p>
                    <p class="text-2xl font-semibold">{{ $arbol->ciclos_productivos_count ?? 0 }}</p>
                </div>
                <div class="bg-white/10 rounded-lg p-3 backdrop-blur-sm">
                    <p class="text-green-200">Altitud</p>
                    <p class="text-2xl font-semibold">{{ number_format($arbol->altitud ?? 0, 2) }} m</p>
                </div>
                <div class="bg-white/10 rounded-lg p-3 backdrop-blur-sm">
                    <p class="text-green-200">Coordenadas</p>
                    <p class="text-xs font-mono truncate">
                        @if($coordenadas = $arbol->coordenadas_gps)
                            {{ number_format($coordenadas[0], 6) }}, {{ number_format($coordenadas[1], 6) }}
                        @else
                            No registradas
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Contenido principal con tabs --}}
        <div x-data="{ tab: 'info' }" class="mt-8">
            {{-- Navegación de tabs --}}
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="tab = 'info'" :class="{ 'border-green-500 text-green-700': tab === 'info', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'info' }" class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                        Información general
                    </button>
                    <button @click="tab = 'metricas'" :class="{ 'border-green-500 text-green-700': tab === 'metricas', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'metricas' }" class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                        Métricas históricas
                    </button>
                    <button @click="tab = 'fitosanitario'" :class="{ 'border-green-500 text-green-700': tab === 'fitosanitario', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'fitosanitario' }" class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                        Historial fitosanitario
                    </button>
                    <button @click="tab = 'eventos'" :class="{ 'border-green-500 text-green-700': tab === 'eventos', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'eventos' }" class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                        Eventos de campo
                    </button>
                </nav>
            </div>

            {{-- Panel: Información general --}}
            <div x-show="tab === 'info'" class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Columna izquierda: datos principales --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Tarjeta de datos del árbol --}}
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-800 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Datos del árbol
                            </h3>
                            <span class="text-xs text-gray-500">ID: {{ $arbol->id }}</span>
                        </div>
                        <div class="p-6 grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Lote</p>
                                <p class="font-medium text-gray-800">{{ $arbol->lote->nombre_lote ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Zona de manejo</p>
                                <p class="font-medium text-gray-800">{{ $arbol->zonaManejo->nombre_zona ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Ciclo productivo</p>
                                <p class="font-medium text-gray-800">{{ $arbol->cicloProductivo->nombre_campana ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Fecha de siembra</p>
                                <p class="font-medium text-gray-800">{{ $arbol->fecha_siembra ? $arbol->fecha_siembra->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Primera cosecha</p>
                                <p class="font-medium text-gray-800">{{ $arbol->fecha_primera_cosecha ? $arbol->fecha_primera_cosecha->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Fecha de muerte/baja</p>
                                <p class="font-medium text-gray-800">{{ $arbol->fecha_baja_muerte ? $arbol->fecha_baja_muerte->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-gray-500">Motivo de baja</p>
                                <p class="font-medium text-gray-800">{{ $arbol->motivo_baja ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Altitud ortométrica</p>
                                <p class="font-medium text-gray-800">{{ number_format($arbol->altitud_ortometrica_msnm ?? 0, 2) }} msnm</p>
                            </div>
                        </div>
                        @if($arbol->observaciones)
                            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                                <p class="text-gray-500 text-xs">Observaciones</p>
                                <p class="text-gray-800 text-sm">{{ $arbol->observaciones }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Tarjeta de ubicación (mapa simbólico) --}}
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-800 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Ubicación GPS
                            </h3>
                        </div>
                        <div class="p-6">
                            @if($coordenadas = $arbol->coordenadas_gps)
                                <div class="aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg overflow-hidden relative">
                                    {{-- Aquí podrías integrar Leaflet u OpenStreetMap con un marcador --}}
                                    <div class="w-full h-full flex items-center justify-center bg-green-50">
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <p class="mt-2 text-sm text-gray-600">Coordenadas:</p>
                                            <p class="font-mono text-sm text-gray-800">{{ number_format($coordenadas[0], 6) }}, {{ number_format($coordenadas[1], 6) }}</p>
                                            <p class="text-xs text-gray-500 mt-1">(Longitud, Latitud)</p>
                                        </div>
                                    </div>
                                    {{-- Para un mapa real, descomenta y usa Leaflet --}}
                                    {{-- 
                                    <div id="map" style="height: 200px;" data-lat="{{ $coordenadas[1] }}" data-lng="{{ $coordenadas[0] }}"></div>
                                    --}}
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-400">
                                    <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                    <p class="mt-2">No se han registrado coordenadas para este árbol.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Columna derecha: relaciones adicionales y resumen --}}
                <div class="space-y-6">
                    {{-- Relaciones: Lote y Zona --}}
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-800">Ubicación en parcela</h3>
                        </div>
                        <div class="p-6 space-y-3 text-sm">
                            <div>
                                <span class="text-gray-500">Fila / Posición</span>
                                <div class="font-medium text-gray-800">
                                    {{ $arbol->fila_indice ?? 'N/A' }} / {{ $arbol->posicion_indice ?? 'N/A' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-500">Lote</span>
                                <div class="font-medium text-gray-800">
                                    <a href="{{ route('lotes-gis.show', $arbol->lote_id) }}" class="text-green-700 hover:underline">
                                        {{ $arbol->lote->nombre ?? 'Sin lote' }}
                                    </a>
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-500">Zona de manejo</span>
                                <div class="font-medium text-gray-800">
                                    {{ $arbol->zonaManejo->nombre ?? 'No asignada' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-500">Ciclo productivo</span>
                                <div class="font-medium text-gray-800">
                                    <a href="{{ route('ciclos-productivos.show', $arbol->ciclo_productivo_id) }}" class="text-green-700 hover:underline">
                                        {{ $arbol->cicloProductivo->nombre ?? 'Sin ciclo' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Datos de reemplazo si aplica --}}
                    @if($arbol->es_reemplazo)
                        <div class="bg-yellow-50 rounded-xl shadow-md overflow-hidden border border-yellow-200">
                            <div class="px-6 py-4 bg-yellow-100 border-b border-yellow-200 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <h3 class="text-lg font-medium text-yellow-800">Árbol de reemplazo</h3>
                            </div>
                            <div class="p-6 space-y-2 text-sm">
                                <div><span class="text-gray-600">Fecha de reemplazo:</span> {{ $arbol->fecha_reemplazo ? $arbol->fecha_reemplazo->format('d/m/Y') : 'N/A' }}</div>
                                <div><span class="text-gray-600">Motivo de muerte:</span> {{ $arbol->causa_muerte ?? 'No especificado' }}</div>
                                <div><span class="text-gray-600">Fecha de muerte:</span> {{ $arbol->fecha_muerte ? $arbol->fecha_muerte->format('d/m/Y') : 'N/A' }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Panel: Métricas históricas --}}
            <div x-show="tab === 'metricas'" class="mt-6">
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            Métricas históricas
                        </h3>
                        <span class="text-sm text-gray-500">{{ $arbol->metricasHistoricas->count() }} registros</span>
                    </div>
                    <div class="p-6">
                        @if($arbol->metricasHistoricas->count())
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diámetro (cm)</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Altura (m)</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rendimiento</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notas</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($arbol->metricasHistoricas as $metrica)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $metrica->fecha_medicion->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($metrica->diametro_cm ?? 0, 1) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($metrica->altura_m ?? 0, 1) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($metrica->rendimiento_kg ?? 0, 2) }} kg</td>
                                                <td class="px-6 py-4 text-sm text-gray-500">{{ $metrica->notas ?? '' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-400">
                                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="mt-2">No hay métricas registradas para este árbol.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Panel: Historial fitosanitario --}}
            <div x-show="tab === 'fitosanitario'" class="mt-6">
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Historial fitosanitario
                        </h3>
                        <span class="text-sm text-gray-500">{{ $arbol->historialFitosanitario->count() }} registros</span>
                    </div>
                    <div class="p-6">
                        @if($arbol->historialFitosanitario->count())
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de evento</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gravedad</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tratamiento</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notas</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($arbol->historialFitosanitario as $registro)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $registro->fecha_evento->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $registro->tipo_evento ?? 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        @if($registro->gravedad == 'baja') bg-green-100 text-green-800
                                                        @elseif($registro->gravedad == 'media') bg-yellow-100 text-yellow-800
                                                        @else bg-red-100 text-red-800 @endif">
                                                        {{ ucfirst($registro->gravedad ?? 'desconocida') }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $registro->tratamiento ?? 'N/A' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-500">{{ $registro->notas ?? '' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-400">
                                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                <p class="mt-2">No hay registros fitosanitarios para este árbol.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Panel: Eventos de campo --}}
            <div x-show="tab === 'eventos'" class="mt-6">
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Eventos de campo asociados
                        </h3>
                        <span class="text-sm text-gray-500">{{ $arbol->eventos->count() }} eventos</span>
                    </div>
                    <div class="p-6">
                        @if($arbol->eventos->count())
                            <div class="grid grid-cols-1 gap-4">
                                @foreach($arbol->eventos as $evento)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                                            <div>
                                                <h4 class="text-md font-medium text-gray-800">{{ $evento->nombre ?? 'Evento' }}</h4>
                                                <p class="text-sm text-gray-500">{{ $evento->fecha_inicio ? $evento->fecha_inicio->format('d/m/Y') : 'Fecha no definida' }}</p>
                                                @if($evento->pivot->novedad_arbol)
                                                    <p class="text-sm text-gray-700 mt-1"><span class="font-semibold">Novedad:</span> {{ $evento->pivot->novedad_arbol }}</p>
                                                @endif
                                                @if($evento->pivot->nota_individual)
                                                    <p class="text-sm text-gray-600"><span class="font-semibold">Nota:</span> {{ $evento->pivot->nota_individual }}</p>
                                                @endif
                                            </div>
                                            <div class="mt-2 md:mt-0">
                                                <a href="{{ route('eventos_campo.show', $evento) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    Ver evento
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-400">
                                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="mt-2">Este árbol no está asociado a ningún evento de campo.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Botones de acción --}}
        <div class="mt-8 flex flex-wrap gap-4">
            <a href="{{ route('arboles.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver al listado
            </a>
          
        </div>
    </div>

@push('scripts')
    {{-- Si usas Leaflet, puedes agregar el código aquí --}}
    {{-- 
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mapElement = document.getElementById('map');
            if (mapElement) {
                const lat = parseFloat(mapElement.dataset.lat);
                const lng = parseFloat(mapElement.dataset.lng);
                // Inicializar mapa...
            }
        });
    </script>
    --}}
@endpush
</x-app-layout>