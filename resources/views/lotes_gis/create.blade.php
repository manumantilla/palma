<x-app-layout>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>
<style>
    #geometry-map { height: 450px; border-radius: 0.75rem; z-index: 0; }
    .required:after { content: " *"; color: red; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script>
    // Helper: Convierte GeoJSON (Polygon) a WKT
    function geoJSONtoWKT(geoJson) {
        if (geoJson.type !== 'Polygon') return '';
        const coords = geoJson.coordinates[0];
        let wkt = 'POLYGON((';
        coords.forEach((coord, idx) => {
            wkt += coord[0] + ' ' + coord[1];
            if (idx < coords.length - 1) wkt += ',';
        });
        wkt += '))';
        return wkt;
    }

    let drawnLayer;
    const wktInput = document.getElementById('geometria_gps');

    window.initMap = function() {
        const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        });

        const streets = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        });

        const map = L.map('geometry-map', {
            center: [4.5709, -74.2973],
            zoom: 6,
            layers: [satellite] // Carga satelital por defecto
        });

        const baseMaps = {
            "Vista Satelital": satellite,
            "Mapa de Calles": streets
        };
        L.control.layers(baseMaps).addTo(map);

        const featureGroup = new L.FeatureGroup();
        map.addLayer(featureGroup);

        const drawControl = new L.Control.Draw({
            draw: {
                polygon: true,
                polyline: false,
                rectangle: true,
                circle: false,
                marker: false,
                circlemarker: false
            },
            edit: {
                featureGroup: featureGroup
            }
        });
        map.addControl(drawControl);

        function updateWktValue() {
            if (drawnLayer) {
                const geoJson = drawnLayer.toGeoJSON().geometry;
                wktInput.value = geoJSONtoWKT(geoJson);
            } else {
                wktInput.value = '';
            }
        }

        map.on(L.Draw.Event.CREATED, (e) => {
            if (drawnLayer) featureGroup.removeLayer(drawnLayer);
            drawnLayer = e.layer;
            featureGroup.addLayer(drawnLayer);
            updateWktValue();
        });

        map.on(L.Draw.Event.EDITED, (e) => {
            updateWktValue();
        });

        map.on(L.Draw.Event.DELETED, (e) => {
            drawnLayer = null;
            updateWktValue();
        });

        const oldWkt = wktInput.value;
        if (oldWkt && oldWkt.startsWith('POLYGON((')) {
            try {
                const coordsString = oldWkt.replace('POLYGON((', '').replace('))', '');
                const pairs = coordsString.split(',');
                const latLngs = pairs.map(pair => {
                    const [lng, lat] = pair.trim().split(' ').map(Number);
                    return [lat, lng];
                });
                
                drawnLayer = L.polygon(latLngs);
                featureGroup.addLayer(drawnLayer);
                map.fitBounds(drawnLayer.getBounds());
            } catch (error) {
                console.error("Error al procesar el WKT previo:", error);
            }
        }
    };
    
    document.addEventListener('DOMContentLoaded', initMap);
</script>
@endpush

<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('lotes-gis.index') }}" class="text-gray-600 hover:text-gray-900">← Volver</a>
        <h1 class="text-2xl font-bold text-gray-800">➕ Nuevo Lote Maestro con Geometría</h1>
    </div>

    <form method="POST" action="{{ route('lotes-gis.store') }}" class="bg-white rounded-xl shadow-lg p-6 space-y-6">
        @csrf

        {{-- Campos básicos --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 required">Finca</label>
                <select name="finca_id" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">Seleccione</option>
                    @foreach($fincas as $finca)
                        <option value="{{ $finca->id }}" {{ old('finca_id') == $finca->id ? 'selected' : '' }}>{{ $finca->nombre }}</option>
                    @endforeach
                </select>
                @error('finca_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 required">Nombre del Lote</label>
                <input type="text" name="nombre_lote" value="{{ old('nombre_lote') }}" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm">
                @error('nombre_lote') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 required">Código único</label>
                <input type="text" name="codigo_lote" value="{{ old('codigo_lote') }}" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm">
                @error('codigo_lote') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 required">Área declarada (Ha)</label>
                <input type="number" step="0.01" name="area_hectareas_declaradas" value="{{ old('area_hectareas_declaradas') }}" class="mt-1 w-full rounded-lg border-gray-300">
                @error('area_hectareas_declaradas') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Área GIS (Ha) - opcional</label>
                <input type="number" step="0.01" name="area_hectareas_gis" value="{{ old('area_hectareas_gis') }}" class="mt-1 w-full rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 required">Altitud mediana (msnm)</label>
                <input type="number" step="1" name="altitud_mediana_msnm" value="{{ old('altitud_mediana_msnm') }}" class="mt-1 w-full rounded-lg border-gray-300">
                @error('altitud_mediana_msnm') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 required">Pendiente promedio (%)</label>
                <input type="number" step="0.1" name="pendiente_promedio_porcentaje" value="{{ old('pendiente_promedio_porcentaje') }}" class="mt-1 w-full rounded-lg border-gray-300">
                @error('pendiente_promedio_porcentaje') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 required">Tipo pendiente</label>
                <select name="pendiente_terreno" class="mt-1 w-full rounded-lg border-gray-300">
                    <option value="plano" {{ old('pendiente_terreno')=='plano' ? 'selected':'' }}>Plano</option>
                    <option value="ondulado" {{ old('pendiente_terreno')=='ondulado' ? 'selected':'' }}>Ondulado</option>
                    <option value="escarpado" {{ old('pendiente_terreno')=='escarpado' ? 'selected':'' }}>Escarpado</option>
                    <option value="muy_escarpado" {{ old('pendiente_terreno')=='muy_escarpado' ? 'selected':'' }}>Muy escarpado</option>
                </select>
                @error('pendiente_terreno') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 required">Tipo de suelo</label>
                <select name="tipo_suelo" class="mt-1 w-full rounded-lg border-gray-300">
                    <option value="arenoso" {{ old('tipo_suelo')=='arenoso' ? 'selected':'' }}>Arenoso</option>
                    <option value="arcilloso" {{ old('tipo_suelo')=='arcilloso' ? 'selected':'' }}>Arcilloso</option>
                    <option value="limoso" {{ old('tipo_suelo')=='limoso' ? 'selected':'' }}>Limoso</option>
                    <option value="franco" {{ old('tipo_suelo')=='franco' ? 'selected':'' }}>Franco</option>
                    <option value="franco_arenoso" {{ old('tipo_suelo')=='franco_arenoso' ? 'selected':'' }}>Franco arenoso</option>
                    <option value="franco_arcilloso" {{ old('tipo_suelo')=='franco_arcilloso' ? 'selected':'' }}>Franco arcilloso</option>
                </select>
                @error('tipo_suelo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 required">pH suelo</label>
                <input type="number" step="0.1" name="ph_suelo" value="{{ old('ph_suelo') }}" class="mt-1 w-full rounded-lg border-gray-300">
                @error('ph_suelo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tenencia</label>
                <select name="tenencia" class="mt-1 w-full rounded-lg border-gray-300">
                    <option value="propio" {{ old('tenencia')=='propio' ? 'selected':'' }}>Propio</option>
                    <option value="arrendado" {{ old('tenencia')=='arrendado' ? 'selected':'' }}>Arrendado</option>
                    <option value="comodato" {{ old('tenencia')=='comodato' ? 'selected':'' }}>Comodato</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Registro ICA</label>
                <input type="text" name="registro_ica" value="{{ old('registro_ica') }}" class="mt-1 w-full rounded-lg border-gray-300">
            </div>

            <div class="col-span-2">
                <label class="flex items-center gap-2">
                    <input type="checkbox" id="tiene_riego_instalado" name="tiene_riego_instalado" value="1" {{ old('tiene_riego_instalado') ? 'checked' : '' }} class="rounded border-gray-300">
                    <span class="text-sm text-gray-700">¿Tiene sistema de riego instalado?</span>
                </label>
            </div>

            <div id="fuenteAguaGroup" class="col-span-1" style="display: none;">
                <label class="block text-sm font-medium text-gray-700">Fuente de agua</label>
                <select name="fuente_agua" class="mt-1 w-full rounded-lg border-gray-300">
                    <option value="acueducto" {{ old('fuente_agua')=='acueducto' ? 'selected':'' }}>Acueducto</option>
                    <option value="pozo" {{ old('fuente_agua')=='pozo' ? 'selected':'' }}>Pozo</option>
                    <option value="rio" {{ old('fuente_agua')=='rio' ? 'selected':'' }}>Río</option>
                    <option value="nacimiento" {{ old('fuente_agua')=='nacimiento' ? 'selected':'' }}>Nacimiento</option>
                    <option value="lluvia" {{ old('fuente_agua')=='lluvia' ? 'selected':'' }}>Lluvia</option>
                </select>
            </div>
        </div>

        {{-- Mapa para dibujar geometría --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Dibuje el polígono del lote (WKT)</label>
            <div id="geometry-map"></div>
            <input type="hidden" name="geometria_gps" id="geometria_gps" value="{{ old('geometria_gps') }}">
            <p class="text-xs text-gray-500 mt-2">Dibuje un polígono en el mapa. Se generará automáticamente el formato WKT.</p>
            @error('geometria_gps') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow transition">Guardar Lote GIS</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxRiego = document.getElementById('tiene_riego_instalado');
        const fuenteAguaGroup = document.getElementById('fuenteAguaGroup');

        function toggleFuenteAgua() {
            if (checkboxRiego.checked) {
                fuenteAguaGroup.style.display = 'block';
            } else {
                fuenteAguaGroup.style.display = 'none';
            }
        }

        // Escuchar el cambio en tiempo real
        checkboxRiego.addEventListener('change', toggleFuenteAgua);
        
        // Ejecutar al cargar la página por si viene de un error de validación
        toggleFuenteAgua();
    });
</script>
</x-app-layout>