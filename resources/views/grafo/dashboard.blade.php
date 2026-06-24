<x-app-layout>

<div
    id="grafo-root"
    x-data="{
        arbolSeleccionado:null
    }"
    style="position:relative;"
>

    {{-- MAPA --}}
    <div id="grafo-map"></div>

    {{-- LOADER --}}
    <div
        id="map-loading"
        class="hidden"
    >
        <div class="spinner"></div>
        <span>Cargando nodos...</span>
    </div>

    {{-- PANEL INFO --}}
    <div
        id="info-panel"
        x-show="arbolSeleccionado"
        x-cloak
    >

        <template x-if="arbolSeleccionado">

            <div>

                <h3
                    style="
                    font-size:18px;
                    font-weight:bold;
                    margin-bottom:10px;"
                    x-text="arbolSeleccionado.codigo">
                </h3>

                <p>
                    <strong>Fila:</strong>

                    <span
                        x-text="
                        arbolSeleccionado.fila_pos">
                    </span>
                </p>

                <p
                    style="margin-top:10px;"
                >
                    <strong>Estado:</strong>

                    <span
                        x-text="
                        arbolSeleccionado.estado">
                    </span>
                </p>

            </div>

        </template>

    </div>

</div>


<style>

html,
body{
    margin:0;
    height:100%;
}

#grafo-root{
    height:100%;
}

#grafo-map{
    width:100%;
    height:100vh;
}

#info-panel{

    position:absolute;

    top:20px;

    right:20px;

    width:260px;

    background:white;

    padding:18px;

    border-radius:14px;

    box-shadow:
    0 10px 40px
    rgba(0,0,0,.15);

    z-index:999;

}

#map-loading{

    position:absolute;

    inset:0;

    display:flex;

    flex-direction:column;

    align-items:center;

    justify-content:center;

    background:
    rgba(255,255,255,.7);

    z-index:9999;

}

.spinner{

    width:42px;

    height:42px;

    border:
    4px solid #ddd;

    border-top:
    4px solid #2563eb;

    border-radius:50%;

    animation:
    spin .9s linear infinite;

    margin-bottom:12px;

}

@keyframes spin{

from{
transform:rotate(0deg);
}

to{
transform:rotate(360deg);
}

}

.hidden{
display:none!important;
}

[x-cloak]{
display:none!important;
}

</style>


<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ─────────────────────────────────────────────
    // 1. Inicializar mapa
    // ─────────────────────────────────────────────
    const map = L.map('grafo-map', {
        zoomControl: false,
        attributionControl: false
    }).setView(
        [
            {{ $ciclo->lat_centro ?? 2.44 }},
            {{ $ciclo->lng_centro ?? -76.60 }}
        ],
        17
    );

    L.control.zoom({
        position: 'bottomright'
    }).addTo(map);

    L.control.attribution({
        position: 'bottomright',
        prefix: false
    })
    .addAttribution('Tiles © Esri')
    .addTo(map);

    const loadingEl = document.getElementById('map-loading');

    // ─────────────────────────────────────────────
    // 2. Capa satelital
    // ─────────────────────────────────────────────
    L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        {
            maxZoom: 21
        }
    ).addTo(map);

    // ─────────────────────────────────────────────
    // 3. Capas
    // ─────────────────────────────────────────────
    let capaNodos = L.layerGroup().addTo(map);
    let capaAristas = L.layerGroup().addTo(map);

    let primeraCarga = true;

    // ─────────────────────────────────────────────
    // 4. Obtener Alpine
    // ─────────────────────────────────────────────
    function getAlpine() {
        const el = document.getElementById('grafo-root');

        if (!el || !window.Alpine) {
            return null;
        }

        return Alpine.$data(el);
    }

    // ─────────────────────────────────────────────
    // 5. Cargar GeoJSON
    // ─────────────────────────────────────────────
    window.cargarGrafo = function () {

        loadingEl?.classList.remove('hidden');

        const bounds = map.getBounds();

        const alpine = getAlpine();

        const filtro =
            alpine?.modoFiltro ?? 'todos';

        const params = new URLSearchParams({
            sw_lat: bounds.getSouthWest().lat,
            sw_lng: bounds.getSouthWest().lng,
            ne_lat: bounds.getNorthEast().lat,
            ne_lng: bounds.getNorthEast().lng,
            filtro
        });

        const url =
`{{ route('grafo.geojson', ['ciclo_productivo_id' => $ciclo->id]) }}?${params}`;

        console.log('[Grafo] URL:', url);

        fetch(url)

            .then(response => {

                if (!response.ok) {
                    throw new Error(
                        `Error HTTP ${response.status}`
                    );
                }

                return response.json();

            })

            .then(geoJson => {

                console.log(
                    '[Grafo] Datos recibidos:',
                    geoJson
                );

                capaNodos.clearLayers();
                capaAristas.clearLayers();

                const features =
                    geoJson.features || [];

                console.log(
                    '[Grafo] Total features:',
                    features.length
                );

                if (!features.length) {
                    console.warn(
                        'No llegaron árboles'
                    );
                    return;
                }

                const geoLayer =
                    L.geoJSON(
                        geoJson,
                        {

                            pointToLayer(
                                feature,
                                latlng
                            ) {

                                const estado =
                                    feature.properties?.estado;

                                let color =
                                    'red';

                                if (
                                    estado === 'sano' ||
                                    estado === 'excelente'
                                ) {
                                    color =
                                        '#16a34a';
                                }

                                return L.circleMarker(
                                    latlng,
                                    {
                                        radius: 10,
                                        fillColor: color,
                                        color: 'white',
                                        weight: 2,
                                        fillOpacity: 1
                                    }
                                );

                            }

                        }
                    );

                geoLayer.addTo(capaNodos);

                // Actualizar Alpine
                if (alpine) {

                    alpine.totalNodos =
                        features.length;

                    alpine.nodosSanos =
                        features.filter(
                            f =>
                                ['sano','excelente']
                                .includes(
                                    f.properties.estado
                                )
                        ).length;
                }

                // Ajustar vista solo una vez
                if (
                    primeraCarga &&
                    geoLayer.getBounds().isValid()
                ) {

                    map.fitBounds(
                        geoLayer.getBounds(),
                        {
                            padding: [50, 50]
                        }
                    );

                    primeraCarga = false;
                }

            })

            .catch(error => {

                console.error(
                    '[GrafoMapa]',
                    error
                );

            })

            .finally(() => {

                loadingEl?.classList.add(
                    'hidden'
                );

            });

    };

    // ─────────────────────────────────────────────
    // 6. Eventos
    // ─────────────────────────────────────────────

    let timeout;

    map.on('moveend', () => {

        clearTimeout(timeout);

        timeout =
            setTimeout(
                window.cargarGrafo,
                300
            );

    });

    map.on('zoomend', () => {

        clearTimeout(timeout);

        timeout =
            setTimeout(
                window.cargarGrafo,
                300
            );

    });

    map.on('click', () => {

        const alpine =
            getAlpine();

        if (alpine) {
            alpine.arbolSeleccionado =
                null;
        }

        document
            .getElementById(
                'info-panel'
            )
            ?.classList
            .add('hidden');

    });

    // ─────────────────────────────────────────────
    // 7. Primera carga
    // ─────────────────────────────────────────────

    setTimeout(
        window.cargarGrafo,
        500
    );

});
</script>
</x-app-layout>
