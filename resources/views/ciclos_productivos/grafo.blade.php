{{-- resources/views/ciclos_productivos/grafo_dijkstra.blade.php --}}
<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">

                    {{-- Título y estadísticas --}}
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                            🌳 Grafo y Dijkstra · Ciclo #{{ $cicloProductivo->id }}
                        </h1>
                        <div class="flex items-center gap-2">
                            <span id="loading-spinner" class="text-sm text-gray-500 dark:text-gray-400 hidden">
                                <svg class="animate-spin h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Cargando...
                            </span>
                            <span id="node-count" class="text-sm font-mono text-gray-500 dark:text-gray-400">0 nodos</span>
                        </div>
                    </div>

                    {{-- Panel de estadísticas --}}
                    <div id="estadisticas" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-200 dark:border-blue-800">
                            <p class="text-xs text-gray-600 dark:text-gray-300">Total árboles</p>
                            <p id="total-arboles" class="text-xl font-bold text-blue-700 dark:text-blue-300">-</p>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-lg border border-green-200 dark:border-green-800">
                            <p class="text-xs text-gray-600 dark:text-gray-300">Producción total (kg)</p>
                            <p id="produccion-total" class="text-xl font-bold text-green-700 dark:text-green-300">-</p>
                        </div>
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <p class="text-xs text-gray-600 dark:text-gray-300">Producción promedio (kg)</p>
                            <p id="produccion-promedio" class="text-xl font-bold text-yellow-700 dark:text-yellow-300">-</p>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900/20 p-3 rounded-lg border border-purple-200 dark:border-purple-800">
                            <p class="text-xs text-gray-600 dark:text-gray-300">Conexiones en red</p>
                            <p id="total-conexiones" class="text-xl font-bold text-purple-700 dark:text-purple-300">-</p>
                        </div>
                    </div>

                    {{-- Desglose de estados --}}
                    <div id="desglose-estados" class="flex flex-wrap items-center gap-2 mb-6"></div>

                    {{-- Grafo Cytoscape --}}
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg shadow-inner overflow-hidden mb-6">
                        <div id="cy" class="h-[600px] w-full"></div>
                        <div class="p-2 text-xs text-gray-500 dark:text-gray-400 text-center border-t border-gray-200 dark:border-gray-700">
                            Grafo de vecindad. Arrastra para mover, rueda para zoom.
                            <span class="ml-2 font-mono" id="zoom-level">Zoom: 1.00x</span>
                        </div>
                    </div>

                    {{-- Controles de Zoom y Carga --}}
                    <div class="flex flex-wrap items-center gap-3 mb-6">
                        <button id="zoom-in" class="px-3 py-1.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm font-medium rounded-md shadow-sm transition">🔍+</button>
                        <button id="zoom-out" class="px-3 py-1.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm font-medium rounded-md shadow-sm transition">🔍−</button>
                        <button id="fit-view" class="px-3 py-1.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm font-medium rounded-md shadow-sm transition">⊡ Ajustar vista</button>
                        <button id="load-more" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition">➕ Cargar más árboles</button>
                        <span id="load-status" class="text-xs text-gray-500 dark:text-gray-400">(0 de X cargados)</span>
                    </div>

                    {{-- Panel de control de Dijkstra --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="font-semibold text-gray-700 dark:text-gray-200 mb-2">🛤️ RUTA ÓPTIMA</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-3">Dijkstra · Camino mínimo desde el origen al destino.</div>
                            <div class="mb-2">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">ORIGEN</label>
                                <select id="dij-src" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></select>
                            </div>
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">DESTINO</label>
                                <select id="dij-dst" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></select>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button id="btn-dijkstra" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md shadow-sm transition">▶ EJECUTAR</button>
                                <button id="btn-allpaths" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md shadow-sm transition">🗺 TODAS</button>
                                <button id="btn-tsp" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-medium rounded-md shadow-sm transition">🔄 RUTA</button>
                                <button id="btn-clear" class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium rounded-md shadow-sm transition">✖ LIMPIAR</button>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 lg:col-span-2">
                            <div class="font-semibold text-gray-700 dark:text-gray-200 mb-2">📊 RESULTADO</div>
                            <div id="dij-result" class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                Ejecuta el algoritmo para ver el resultado...
                            </div>
                        </div>
                    </div>

                    {{-- Log colapsable --}}
                    <details class="mt-2">
                        <summary class="cursor-pointer text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100">
                            📋 Ver Log de ejecución
                        </summary>
                        <div id="algo-log" class="mt-2 bg-gray-900 dark:bg-black rounded-lg p-3 border border-gray-700 max-h-48 overflow-y-auto text-xs font-mono text-gray-400 space-y-0.5">
                            <div>Esperando acciones...</div>
                        </div>
                    </details>

                </div>
            </div>
        </div>
    </div>

{{-- 1. Cytoscape Base --}}
<script src="https://unpkg.com/cytoscape@3.28.1/dist/cytoscape.min.js"></script>

{{-- 2. Dependencias requeridas por cose-bilkent --}}
<script src="https://unpkg.com/layout-base/layout-base.js"></script>
<script src="https://unpkg.com/cose-base/cose-base.js"></script>

{{-- 3. Extensión cose-bilkent --}}
<script src="https://unpkg.com/cytoscape-cose-bilkent@4.1.0/cytoscape-cose-bilkent.js"></script>
    <style>
        #cy { background: #0d1510; border-radius: 0.5rem; }
        .stat-row { display: flex; justify-content: space-between; padding: 2px 0; border-bottom: 1px solid #e5e7eb; }
        .dark .stat-row { border-bottom-color: #374151; }
        #algo-log::-webkit-scrollbar { width: 4px; }
        #algo-log::-webkit-scrollbar-track { background: #1a1a1a; }
        #algo-log::-webkit-scrollbar-thumb { background: #4a6a4a; border-radius: 4px; }
        .dij-node-highlight { border-color: #00e676 !important; border-width: 3px !important; }
        .dij-node-source { border-color: #00c8ff !important; border-width: 3px !important; }
        .dij-node-target { border-color: #c084fc !important; border-width: 3px !important; }
    </style>

    <script>
        (function() {
            // Esperar a que Cytoscape y la extensión estén cargadas
            if (typeof cytoscape === 'undefined') {
                console.error('Cytoscape no se cargó correctamente.');
                return;
            }

            // Registrar la extensión cose-bilkent si está disponible
            if (typeof cytoscapeCoseBilkent !== 'undefined') {
                cytoscape.use(cytoscapeCoseBilkent);
                console.log('✅ Extensión cose-bilkent registrada.');
            } else {
                console.warn('⚠️ Extensión cose-bilkent no encontrada. Se usará layout "grid" como fallback.');
            }

            // --- Resto del código ---
            document.addEventListener('DOMContentLoaded', function() {
                // ... (todo el código anterior, pero ahora con layout condicional)
            });
        })();
    </script>

    <script>
        // Código completo (igual que antes, pero con layout condicional)
        document.addEventListener('DOMContentLoaded', function() {
            const CICLO_ID = {{ $cicloProductivo->id }};
            const API_URL = '{{ route("ciclos-productivos.grafo-estadisticas", $cicloProductivo) }}';
            const CHUNK_SIZE = 150;

            let allNodes = [], allEdges = [], loadedNodes = [], loadedEdges = [];
            let cy = null, currentPath = null;

            const cyContainer = document.getElementById('cy');
            const loadingSpinner = document.getElementById('loading-spinner');
            const nodeCountSpan = document.getElementById('node-count');
            const loadStatusSpan = document.getElementById('load-status');
            const zoomLevelSpan = document.getElementById('zoom-level');
            const logDiv = document.getElementById('algo-log');
            const resultDiv = document.getElementById('dij-result');

            function clearLog() { logDiv.innerHTML = '<div>📋 Log de ejecución</div>'; }
            function appendLog(msg, cls = '') {
                const p = document.createElement('div');
                p.className = cls || 'text-gray-400';
                p.textContent = msg;
                logDiv.appendChild(p);
                logDiv.scrollTop = logDiv.scrollHeight;
            }
            function statRow(label, value) {
                return `<div class="stat-row"><span>${label}</span><span class="font-mono">${value}</span></div>`;
            }
            function setResult(html) { resultDiv.innerHTML = html; }

            const estadoColors = {
                'excelente': '#22c55e', 'bueno': '#3b82f6', 'regular': '#f59e0b',
                'malo': '#ef4444', 'muerto': '#1f2937'
            };

            function initCytoscape() {
                // Determinar qué layout usar
                let layoutName = 'cose-bilkent';
                // Verificar si la extensión está registrada
                try {
                    // Intentar crear una instancia temporal para probar
                    const testCy = cytoscape({
                        container: document.createElement('div'),
                        elements: [],
                        layout: { name: 'cose-bilkent' }
                    });
                    testCy.destroy();
                } catch (e) {
                    console.warn('Layout cose-bilkent no disponible, usando "grid".');
                    layoutName = 'grid';
                }

                const cyInstance = cytoscape({
                    container: cyContainer,
                    style: [
                        {
                            selector: 'node',
                            style: {
                                'background-color': function(ele) {
                                    const estado = ele.data('estado') || 'regular';
                                    return estadoColors[estado] || '#9ca3af';
                                },
                                'label': 'data(label)',
                                'width': 32,
                                'height': 32,
                                'font-size': '8px',
                                'text-valign': 'bottom',
                                'text-halign': 'center',
                                'color': '#e0f0e0',
                                'text-outline-width': 2,
                                'text-outline-color': '#0d1510',
                                'border-width': 1.5,
                                'border-color': '#162419',
                            }
                        },
                        {
                            selector: 'edge',
                            style: {
                                'width': 1.5,
                                'line-color': '#2d4a38',
                                'target-arrow-color': '#2d4a38',
                                'target-arrow-shape': 'none',
                                'curve-style': 'bezier',
                                'opacity': 0.6,
                                'label': function(ele) {
                                    return ele.data('distancia') ? ele.data('distancia').toFixed(0) + 'm' : '';
                                },
                                'font-size': '7px',
                                'text-rotation': 'autorotate',
                                'text-margin-y': -6,
                                'color': '#4a6a4a',
                                'text-outline-width': 1,
                                'text-outline-color': '#0d1510',
                            }
                        },
                        {
                            selector: '.ruta-dijkstra',
                            style: {
                                'line-color': '#00e676',
                                'target-arrow-color': '#00e676',
                                'width': 4,
                                'opacity': 1,
                                'background-color': '#00e676',
                                'border-color': '#00e676',
                                'border-width': 2,
                            }
                        },
                        {
                            selector: '.nodo-origen',
                            style: { 'border-color': '#00c8ff', 'border-width': 3 }
                        },
                        {
                            selector: '.nodo-destino',
                            style: { 'border-color': '#c084fc', 'border-width': 3 }
                        }
                    ],
                    layout: {
                        name: layoutName,
                        idealEdgeLength: 120,
                        nodeRepulsion: 8000,
                        gravity: 0.25,
                        numIter: 500,
                        animate: true,
                        animationDuration: 500,
                        fit: true,
                        padding: 30,
                    },
                    wheelSensitivity: 0.5,
                    minZoom: 0.1,
                    maxZoom: 4.0,
                });

                cyInstance.on('zoom', function() {
                    zoomLevelSpan.textContent = `Zoom: ${cyInstance.zoom().toFixed(2)}x`;
                });

                cyInstance.on('mouseover', 'node', function(evt) {
                    const node = evt.target;
                    cyContainer.title = `${node.data('label')} · Estado: ${node.data('estado')} · Producción: ${node.data('produccion')||0} kg`;
                });
                cyInstance.on('mouseout', 'node', function() {
                    cyContainer.title = '';
                });

                return cyInstance;
            }

            async function fetchData() {
                loadingSpinner.classList.remove('hidden');
                try {
                    const response = await fetch(API_URL);
                    const data = await response.json();
                    if (!data.success) throw new Error(data.message || 'Error al cargar datos');

                    const { leaflet, estadisticas, grafo_dijkstra } = data;
                    const markers = leaflet.marcadores;
                    const aristas = grafo_dijkstra.aristas;

                    if (markers.length === 0) throw new Error('No hay árboles para mostrar');

                    allNodes = markers.map(m => ({
                        id: m.id.toString(),
                        label: m.codigo,
                        estado: m.estado_vital,
                        produccion: m.produccion_kg,
                        lat: m.lat,
                        lng: m.lng,
                    }));

                    allEdges = aristas.map(e => ({
                        id: `${e.origen}-${e.destino}`,
                        source: e.origen.toString(),
                        target: e.destino.toString(),
                        distancia: parseFloat(e.peso_distancia),
                        contagio: parseFloat(e.peso_contagio),
                        tipo: e.tipo_contacto,
                    }));

                    document.getElementById('total-arboles').textContent = estadisticas.total_arboles;
                    document.getElementById('produccion-total').textContent = estadisticas.produccion_total_kg;
                    document.getElementById('produccion-promedio').textContent = estadisticas.produccion_promedio_kg;
                    document.getElementById('total-conexiones').textContent = estadisticas.total_conexiones_red;

                    const desgloseDiv = document.getElementById('desglose-estados');
                    desgloseDiv.innerHTML = '';
                    for (const [estado, count] of Object.entries(estadisticas.desglose_estado_vital)) {
                        const badge = document.createElement('span');
                        badge.className = 'px-3 py-1 rounded-full text-xs font-semibold text-white';
                        badge.style.backgroundColor = estadoColors[estado] || '#9ca3af';
                        badge.textContent = `${estado}: ${count}`;
                        desgloseDiv.appendChild(badge);
                    }
                    const saludable = document.createElement('span');
                    saludable.className = 'px-3 py-1 text-xs text-gray-700 dark:text-gray-300';
                    saludable.textContent = `✅ Saludables (excelente): ${estadisticas.porcentaje_saludables}%`;
                    desgloseDiv.appendChild(saludable);

                    if (!cy) cy = initCytoscape();
                    loadChunk(0);
                    populateSelects();
                    updateCounters();

                    loadingSpinner.classList.add('hidden');
                    return true;
                } catch (error) {
                    loadingSpinner.classList.add('hidden');
                    console.error(error);
                    cyContainer.innerHTML = `<div class="text-red-500 p-4">${error.message}</div>`;
                    return false;
                }
            }

            function loadChunk(startIndex) {
                const endIndex = Math.min(startIndex + CHUNK_SIZE, allNodes.length);
                const chunkNodes = allNodes.slice(startIndex, endIndex);
                const chunkNodeIds = new Set(chunkNodes.map(n => n.id));
                const loadedNodeIds = new Set(loadedNodes.map(n => n.id));

                const chunkEdges = allEdges.filter(e => {
                    const sourceIn = chunkNodeIds.has(e.source);
                    const targetIn = chunkNodeIds.has(e.target);
                    const sourceLoaded = loadedNodeIds.has(e.source);
                    const targetLoaded = loadedNodeIds.has(e.target);
                    return (sourceIn && targetIn) || (sourceIn && targetLoaded) || (targetIn && sourceLoaded);
                });

                const nodesToAdd = chunkNodes.map(n => ({
                    group: 'nodes',
                    data: { id: n.id, label: n.label, estado: n.estado, produccion: n.produccion, lat: n.lat, lng: n.lng },
                    style: { 'background-color': estadoColors[n.estado] || '#9ca3af' }
                }));

                const edgesToAdd = chunkEdges.map(e => ({
                    group: 'edges',
                    data: { id: e.id, source: e.source, target: e.target, distancia: e.distancia, contagio: e.contagio, tipo: e.tipo }
                }));

                cy.batch(() => {
                    cy.add(nodesToAdd);
                    cy.add(edgesToAdd);
                });

                loadedNodes = loadedNodes.concat(chunkNodes);
                const existingEdgeIds = new Set(loadedEdges.map(e => e.id));
                const newEdges = chunkEdges.filter(e => !existingEdgeIds.has(e.id));
                loadedEdges = loadedEdges.concat(newEdges);

                // Re-layout (incremental no es trivial, se vuelve a ejecutar)
                const layout = cy.layout({
                    name: 'cose-bilkent',
                    idealEdgeLength: 120,
                    nodeRepulsion: 8000,
                    gravity: 0.25,
                    numIter: 300,
                    animate: true,
                    animationDuration: 400,
                    fit: false,
                });
                layout.run();

                updateCounters();
                return chunkNodes.length;
            }

            function loadMore() {
                if (loadedNodes.length >= allNodes.length) {
                    appendLog('✅ Todos los árboles ya están cargados.', 'text-green-400');
                    return;
                }
                const added = loadChunk(loadedNodes.length);
                appendLog(`📦 Cargados ${added} nuevos árboles (total: ${loadedNodes.length}/${allNodes.length})`, 'text-blue-300');
            }

            function updateCounters() {
                nodeCountSpan.textContent = `${loadedNodes.length} nodos`;
                loadStatusSpan.textContent = `(${loadedNodes.length} de ${allNodes.length} cargados)`;
                populateSelects();
            }

            function populateSelects() {
                const srcSelect = document.getElementById('dij-src');
                const dstSelect = document.getElementById('dij-dst');
                const srcVal = srcSelect.value;
                const dstVal = dstSelect.value;
                srcSelect.innerHTML = '';
                dstSelect.innerHTML = '';
                loadedNodes.forEach((n, i) => {
                    const opt1 = document.createElement('option');
                    opt1.value = n.id;
                    opt1.textContent = `${n.label} (${n.estado})`;
                    srcSelect.appendChild(opt1);
                    const opt2 = document.createElement('option');
                    opt2.value = n.id;
                    opt2.textContent = `${n.label} (${n.estado})`;
                    dstSelect.appendChild(opt2);
                });
                if (srcVal && srcSelect.querySelector(`option[value="${srcVal}"]`)) srcSelect.value = srcVal;
                else if (loadedNodes.length > 0) srcSelect.value = loadedNodes[0].id;
                if (dstVal && dstSelect.querySelector(`option[value="${dstVal}"]`)) dstSelect.value = dstVal;
                else if (loadedNodes.length > 1) dstSelect.value = loadedNodes[1].id;
                else if (loadedNodes.length > 0) dstSelect.value = loadedNodes[0].id;
            }

            function clearPath() {
                if (currentPath) { cy.remove(currentPath); currentPath = null; }
                cy.elements('.ruta-dijkstra').removeClass('ruta-dijkstra');
                cy.elements('.nodo-origen').removeClass('nodo-origen');
                cy.elements('.nodo-destino').removeClass('nodo-destino');
                setResult('Ejecuta el algoritmo para ver el resultado...');
            }

            function runDijkstra() {
                const srcId = document.getElementById('dij-src').value;
                const dstId = document.getElementById('dij-dst').value;
                if (!srcId || !dstId || srcId === dstId) {
                    appendLog('⚠️ Selecciona origen y destino diferentes.', 'text-yellow-400');
                    return;
                }
                clearLog();
                appendLog(`🔍 Dijkstra desde ${srcId} → ${dstId}`);
                const srcNode = cy.getElementById(srcId);
                const dstNode = cy.getElementById(dstId);
                if (!srcNode || !dstNode || srcNode.length === 0 || dstNode.length === 0) {
                    appendLog('⚠️ Uno de los nodos no está cargado.', 'text-yellow-400');
                    return;
                }
                try {
                    const dijkstra = cy.elements().dijkstra({
                        root: srcNode,
                        weight: edge => edge.data('distancia') || 1,
                        directed: false,
                    });
                    const path = dijkstra.pathTo(dstNode);
                    const distance = dijkstra.distanceTo(dstNode);
                    if (!path || path.length === 0 || distance === Infinity) {
                        appendLog('❌ No se encontró ruta.', 'text-red-400');
                        setResult(`<div class="text-red-400 font-bold">❌ No hay ruta disponible</div>`);
                        return;
                    }
                    clearPath();
                    path.addClass('ruta-dijkstra');
                    srcNode.addClass('nodo-origen');
                    dstNode.addClass('nodo-destino');
                    currentPath = path;
                    const pathLabels = path.nodes().map(n => n.data('label')).join(' → ');
                    setResult(`
                        <div class="text-green-400 font-bold mb-1">✅ RUTA ENCONTRADA</div>
                        ${statRow('Distancia total', distance.toFixed(2) + ' m')}
                        ${statRow('Nodos en ruta', path.length)}
                        ${statRow('Ruta', pathLabels)}
                        ${statRow('Tiempo estimado (60m/min)', Math.round(distance / 60 * 2) + ' min')}
                    `);
                    appendLog(`✅ Ruta: ${pathLabels} (${distance.toFixed(2)} m)`, 'text-green-400');
                    cy.fit(path, 50);
                } catch (e) {
                    appendLog(`❌ Error: ${e.message}`, 'text-red-400');
                }
            }

            function runAllPaths() {
                const srcId = document.getElementById('dij-src').value;
                if (!srcId) { appendLog('⚠️ Selecciona origen.', 'text-yellow-400'); return; }
                clearLog();
                appendLog(`🗺 Calculando distancias desde ${srcId}...`);
                const srcNode = cy.getElementById(srcId);
                if (!srcNode || srcNode.length === 0) { appendLog('⚠️ Nodo no cargado.', 'text-yellow-400'); return; }
                try {
                    const dijkstra = cy.elements().dijkstra({
                        root: srcNode,
                        weight: edge => edge.data('distancia') || 1,
                        directed: false,
                    });
                    const nodes = cy.nodes();
                    let results = [];
                    nodes.forEach(node => {
                        const d = dijkstra.distanceTo(node);
                        results.push({ id: node.id(), label: node.data('label'), distance: d });
                        appendLog(`  ${node.data('label')}: ${d === Infinity ? '∞' : d.toFixed(2) + ' m'}`);
                    });
                    const reachable = results.filter(r => r.distance !== Infinity).length;
                    setResult(`
                        <div class="text-blue-400 font-bold mb-1">📊 DISTANCIAS DESDE ${srcNode.data('label')}</div>
                        ${statRow('Nodos alcanzables', `${reachable}/${results.length}`)}
                        ${statRow('Distancia máxima', Math.max(...results.filter(r => r.distance !== Infinity).map(r => r.distance)).toFixed(2) + ' m')}
                    `);
                    cy.nodes().forEach(node => {
                        const d = dijkstra.distanceTo(node);
                        let color = '#162419';
                        if (d === Infinity) color = '#ff3d57';
                        else if (d < 50) color = '#00e676';
                        else if (d < 100) color = '#ffb700';
                        else color = '#c084fc';
                        node.style('border-color', color);
                        node.style('border-width', 2);
                    });
                } catch (e) {
                    appendLog(`❌ Error: ${e.message}`, 'text-red-400');
                }
            }

            function runTSP() {
                // Implementación simplificada (igual que antes)
                const srcId = document.getElementById('dij-src').value;
                if (!srcId) { appendLog('⚠️ Selecciona origen.', 'text-yellow-400'); return; }
                clearLog();
                appendLog(`🔄 Ruta de cosecha (vecino más cercano) desde ${srcId}...`);
                const srcNode = cy.getElementById(srcId);
                if (!srcNode || srcNode.length === 0) { appendLog('⚠️ Nodo no cargado.', 'text-yellow-400'); return; }
                try {
                    const nodes = cy.nodes();
                    const n = nodes.length;
                    if (n < 2) { appendLog('⚠️ Se necesitan al menos 2 nodos.', 'text-yellow-400'); return; }
                    const nodeIds = nodes.map(n => n.id());
                    const distMatrix = {};
                    nodeIds.forEach(id1 => {
                        distMatrix[id1] = {};
                        const d = cy.elements().dijkstra({
                            root: cy.getElementById(id1),
                            weight: edge => edge.data('distancia') || 1,
                            directed: false,
                        });
                        nodeIds.forEach(id2 => {
                            distMatrix[id1][id2] = d.distanceTo(cy.getElementById(id2));
                        });
                    });
                    let current = srcId;
                    const visited = new Set([current]);
                    const path = [current];
                    let totalDist = 0;
                    while (visited.size < n) {
                        let nearest = null, minDist = Infinity;
                        for (const id of nodeIds) {
                            if (!visited.has(id)) {
                                const d = distMatrix[current][id];
                                if (d !== Infinity && d < minDist) { minDist = d; nearest = id; }
                            }
                        }
                        if (nearest === null) break;
                        visited.add(nearest);
                        path.push(nearest);
                        totalDist += minDist;
                        current = nearest;
                    }
                    const pathLabels = path.map(id => cy.getElementById(id).data('label')).join(' → ');
                    setResult(`
                        <div class="text-purple-400 font-bold mb-1">🔄 RUTA DE COSECHA (Vecino más cercano)</div>
                        ${statRow('Distancia total', totalDist.toFixed(2) + ' m')}
                        ${statRow('Nodos visitados', path.length)}
                        ${statRow('Secuencia', pathLabels)}
                    `);
                    appendLog(`✅ Ruta: ${pathLabels} (${totalDist.toFixed(2)} m)`, 'text-purple-400');
                    clearPath();
                    path.forEach(id => cy.getElementById(id).addClass('ruta-dijkstra'));
                    // Resaltar aristas (opcional)
                    for (let i = 0; i < path.length - 1; i++) {
                        const edge = cy.edges().filter(e => {
                            return (e.data('source') === path[i] && e.data('target') === path[i+1]) ||
                                   (e.data('source') === path[i+1] && e.data('target') === path[i]);
                        });
                        if (edge.length > 0) edge.addClass('ruta-dijkstra');
                    }
                    cy.fit(path.map(id => cy.getElementById(id)), 50);
                } catch (e) {
                    appendLog(`❌ Error: ${e.message}`, 'text-red-400');
                }
            }

            function setupEventListeners() {
                document.getElementById('zoom-in').addEventListener('click', () => {
                    const z = cy.zoom();
                    cy.zoom(Math.min(z * 1.3, cy.maxZoom()));
                });
                document.getElementById('zoom-out').addEventListener('click', () => {
                    const z = cy.zoom();
                    cy.zoom(Math.max(z / 1.3, cy.minZoom()));
                });
                document.getElementById('fit-view').addEventListener('click', () => cy.fit(undefined, 30));
                document.getElementById('load-more').addEventListener('click', loadMore);
                document.getElementById('btn-dijkstra').addEventListener('click', runDijkstra);
                document.getElementById('btn-allpaths').addEventListener('click', runAllPaths);
                document.getElementById('btn-tsp').addEventListener('click', runTSP);
                document.getElementById('btn-clear').addEventListener('click', () => {
                    clearPath();
                    clearLog();
                    appendLog('🧹 Ruta limpiada.', 'text-gray-400');
                    cy.nodes().forEach(node => {
                        node.style('border-color', '#162419');
                        node.style('border-width', 1.5);
                    });
                });
            }

            // Inicialización
            fetchData().then(() => {
                setupEventListeners();
                appendLog('🚀 Sistema listo. Usa los controles.', 'text-green-300');
            });
        });
    </script>
</x-app-layout>