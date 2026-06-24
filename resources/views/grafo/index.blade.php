<x-app-layout>
<div id="grafo-container" style="position: relative; width: 100%; height: 100vh;">
    <div id="map" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;"></div>
    <div id="cy" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 2; pointer-events: none;"></div>

    {{-- Panel de control --}}
    <div id="panel-control" style="position: absolute; top: 10px; right: 10px; z-index: 10; background: white; padding: 12px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,.2);">
        <label>Lote:
            <select id="filtro-lote">
                <option value="">Todos</option>
                {{-- poblar dinámicamente o con loop de lotes --}}
            </select>
        </label>
        <div id="info-modo" style="margin-top: 8px; font-size: 12px; color: #555;"></div>
    </div>

    {{-- Panel de detalle del árbol --}}
    <div id="panel-arbol" style="display:none; position: absolute; bottom: 10px; left: 10px; z-index: 10; background: white; padding: 12px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,.2); max-width: 320px;"></div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/cytoscape@3.28.1/dist/cytoscape.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Inicializar Leaflet ---
    const map = L.map('map', { zoomControl: true });

const satelital = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    maxZoom: 22,
    attribution: 'Tiles &copy; Esri'
});

const calles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 22,
    attribution: '&copy; OpenStreetMap'
});

// Etiquetas/referencias superpuestas sobre el satelital (carreteras, nombres)
const etiquetas = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
    maxZoom: 22,
    attribution: 'Esri'
});

// Capa por defecto = satelital
satelital.addTo(map);

L.control.layers({
    'Satelital': satelital,
    'Calles': calles,
}, {
    'Etiquetas': etiquetas
}, { position: 'topleft', collapsed: true }).addTo(map);
    function centrarMapaEnArboles() {
        const loteId = document.getElementById('filtro-lote').value;
        const params = new URLSearchParams();
        if (loteId) params.append('lote_id', loteId);

        return fetch(`/api/grafo/extent?${params.toString()}`)
            .then(res => {
                if (!res.ok) throw new Error('Sin árboles');
                return res.json();
            })
            .then(data => {
                const bounds = L.latLngBounds(
                    [data.min_lat, data.min_lng],
                    [data.max_lat, data.max_lng]
                );
                map.fitBounds(bounds, { padding: [40, 40], maxZoom: 19 });
            })
            .catch(err => {
                console.warn('No se pudo centrar en árboles:', err);
                // Fallback de emergencia
                map.setView([4.60, -74.08], 17);
            });
    }
    // --- Inicializar Cytoscape (capa superpuesta) ---
    const cy = cytoscape({
        container: document.getElementById('cy'),
        style: [
            {
                selector: 'node',
                style: {
                    'background-color': ele => colorPorEstado(ele.data('estado_vital')),
                    'width': 8,
                    'height': 8,
                    'label': '',
                }
            },
            {
                selector: 'node[tipo = "supernodo"]',
                style: {
                    'width': ele => 10 + Math.min(ele.data('total_arboles') / 50, 30),
                    'height': ele => 10 + Math.min(ele.data('total_arboles') / 50, 30),
                    'background-color': '#3388ff',
                    'label': 'data(total_arboles)',
                    'font-size': '10px',
                    'color': '#fff',
                    'text-valign': 'center',
                }
            },
            {
                selector: 'edge',
                style: {
                    'width': 1,
                    'line-color': '#999',
                    'opacity': 0.4,
                }
            },
        ],
        userPanningEnabled: false,
        userZoomingEnabled: false,
        boxSelectionEnabled: false,
    });

    function colorPorEstado(estado) {
        const colores = {
            'sano': '#4caf50',
            'enfermo': '#f44336',
            'estresado': '#ff9800',
            'muerto': '#9e9e9e',
        };
        return colores[estado] || '#2196f3';
    }

    // --- Sincronizar Cytoscape con el viewport de Leaflet ---
    function proyectarYRenderizar(nodos, edges = []) {
        cy.elements().remove();

        const elementos = nodos.map(n => {
            const point = map.latLngToContainerPoint([n.data.lat, n.data.lng]);
            return {
                data: n.data,
                position: { x: point.x, y: point.y },
            };
        });

        cy.add([...elementos, ...edges]);
    }

    // --- Cargar datos según viewport actual ---
    let abortController = null;

    function cargarDatos() {
        const bounds = map.getBounds();
        const params = new URLSearchParams({
            min_lng: bounds.getWest(),
            min_lat: bounds.getSouth(),
            max_lng: bounds.getEast(),
            max_lat: bounds.getNorth(),
        });

        const loteId = document.getElementById('filtro-lote').value;
        if (loteId) params.append('lote_id', loteId);

        if (abortController) abortController.abort();
        abortController = new AbortController();

        fetch(`/api/grafo/nodos?${params.toString()}`, { signal: abortController.signal })
            .then(res => res.json())
            .then(data => {
                document.getElementById('info-modo').textContent =
                    `Modo: ${data.modo} | Total: ${data.total}`;

                if (data.modo === 'jerarquico') {
                    return cargarClusters(params);
                }

                proyectarYRenderizar(data.nodos, data.edges);
            })
            .catch(err => {
                if (err.name !== 'AbortError') console.error(err);
            });
    }

    function cargarClusters(params) {
        params.append('precision', precisionSegunZoom(map.getZoom()));

        fetch(`/api/grafo/clusters?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                proyectarYRenderizar(data.clusters);
            });
    }

    function precisionSegunZoom(zoom) {
        // A mayor zoom, celdas más pequeñas (más precisión)
        if (zoom >= 18) return 6;
        if (zoom >= 15) return 5;
        if (zoom >= 12) return 4;
        return 3;
    }

    // --- Eventos del mapa ---
    map.on('moveend zoomend', cargarDatos);
    document.getElementById('filtro-lote').addEventListener('change', cargarDatos);

    // --- Click en nodo: mostrar detalle ---
    cy.on('tap', 'node', function (evt) {
        const node = evt.target;
        if (node.data('tipo') === 'supernodo') {
            // Zoom-in al cluster
            map.setView([node.data('lat'), node.data('lng')], map.getZoom() + 2);
            return;
        }

        fetch(`/api/grafo/arboles/${node.id()}`)
            .then(res => res.json())
            .then(data => mostrarPanelArbol(data));
    });

    function mostrarPanelArbol(data) {
        const panel = document.getElementById('panel-arbol');
        const a = data.arbol;
        const m = data.ultima_metrica;

        panel.innerHTML = `
            <h4>Árbol #${a.id}</h4>
            <p><strong>Lote:</strong> ${a.lote_id} | <strong>Variedad:</strong> ${a.variedad}</p>
            <p><strong>Estado:</strong> ${a.estado_vital} | <strong>Etapa:</strong> ${a.etapa_biologica}</p>
            <p><strong>Producción acumulada:</strong> ${a.produccion_acumulada_kg} kg</p>
            ${m ? `<p><strong>NDVI:</strong> ${m.indice_ndvi_medido ?? 'N/A'} | <strong>Altura:</strong> ${m.altura_metros ?? 'N/A'} m</p>` : ''}
            ${data.incidencias_recientes.length > 0 ? `<p style="color:#f44336;"><strong>⚠ Incidencias recientes:</strong> ${data.incidencias_recientes.length}</p>` : ''}
            <button onclick="document.getElementById('panel-arbol').style.display='none'">Cerrar</button>
        `;
        panel.style.display = 'block';
    }

    // Carga inicial
    centrarMapaEnArboles().then(() => {
        cargarDatos();
    });
});
</script>
</x-app-layout>