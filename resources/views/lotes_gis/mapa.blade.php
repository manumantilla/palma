<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GIS Fitosanitario | Leaflet</title>
    @vite('resources/css/app.css')
    
    <!-- CSS de Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Ajuste para los popups de Leaflet con Tailwind */
        .leaflet-popup-content-wrapper { border-radius: 0.75rem; padding: 0; overflow: hidden; }
        .leaflet-popup-content { margin: 0; width: 100% !important; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 h-screen flex flex-col">

    <!-- Header / Navbar -->
    <header class="bg-slate-800 border-b border-slate-700 p-4 flex justify-between items-center z-10 shadow-lg">
        <div>
            <h1 class="text-2xl font-bold text-emerald-400">AgriGIS - Monitoreo de Lotes</h1>
            <p class="text-sm text-slate-400">Renderizado espacial de alta densidad</p>
        </div>
        <div class="flex gap-4 text-sm font-medium bg-slate-900/50 p-2 rounded-lg border border-slate-700">
            <span class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-emerald-500"></div> Sano</span>
            <span class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-amber-400"></div> Estrés</span>
            <span class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-red-500"></div> Crítico</span>
            <span class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-slate-500"></div> Muerto</span>
        </div>
    </header>

    <!-- Contenedor del Mapa (Ocupa el resto de la pantalla) -->
    <div id="map" class="flex-1 w-full z-0"></div>

    <!-- JS de Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Recibir el JSON desde Laravel
            const arboles = @json($arboles);

            // 2. Inicializar mapa. 
            // Buscamos el centro promedio para iniciar la cámara (si hay datos)
            let centerLat = 7.1193; // Default (Ej: Bucaramanga)
            let centerLng = -73.1227;
            
            if(arboles.length > 0) {
                centerLat = arboles[0].lat;
                centerLng = arboles[0].lng;
            }

            // Configuramos maxZoom hasta 24 para árboles muy pegados
            const map = L.map('map', {
                preferCanvas: true, // CLAVE PARA RENDIMIENTO: Dibuja en Canvas, no en DOM
                maxZoom: 24 
            }).setView([centerLat, centerLng], 18);

            // 3. Capa Base Satelital (Esri World Imagery)
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri',
                maxNativeZoom: 17, // Límite de las fotos satelitales
                maxZoom: 24        // Hasta dónde te deja hacer zoom el mapa (overzoom digital)
            }).addTo(map);

            // 4. Diccionario de colores según el ENUM de tu BD
            const coloresEstado = {
                'excelente': '#10b981',       // Emerald 500
                'con_estres': '#fbbf24',      // Amber 400
                'enfermo_critico': '#ef4444', // Red 500
                'muerto': '#64748b',          // Slate 500
                'erradicado': '#0f172a'       // Slate 900
            };

            // 5. Renderizar los árboles (Nodos)
            arboles.forEach(arbol => {
                if(arbol.lat && arbol.lng) {
                    const color = coloresEstado[arbol.estado_vital] || '#ffffff';

                    // Usamos CircleMarker (Canvas) que es mucho más rápido y preciso que un icono de imagen
                    const marker = L.circleMarker([arbol.lat, arbol.lng], {
                        radius: 5,        // Tamaño del nodo
                        fillColor: color,
                        color: '#ffffff', // Borde blanco
                        weight: 1.5,      // Grosor del borde
                        opacity: 1,
                        fillOpacity: 0.9
                    }).addTo(map);

                    // Template HTML para el Popup estilizado con Tailwind
                    const popupHtml = `
                        <div class="bg-white text-slate-800 p-4 min-w-[200px]">
                            <h3 class="font-bold text-lg border-b pb-1 mb-2 text-slate-900">${arbol.codigo_unico}</h3>
                            <div class="space-y-1 text-sm">
                                <p><span class="text-slate-500 font-medium">Ubicación:</span> Fila ${arbol.fila_indice}, Pos ${arbol.posicion_indice}</p>
                                <p><span class="text-slate-500 font-medium">Estado:</span> <span class="capitalize font-semibold uppercase px-2 py-0.5 rounded text-xs bg-slate-100">${arbol.estado_vital.replace('_', ' ')}</span></p>
                                <p><span class="text-slate-500 font-medium">Etapa:</span> <span class="capitalize">${arbol.etapa_biologica.replace('_', ' ')}</span></p>
                            </div>
                            <button class="mt-3 w-full bg-slate-800 text-white text-xs font-bold py-1.5 rounded hover:bg-slate-700 transition">
                                Ver Historial Fitosanitario
                            </button>
                        </div>
                    `;

                    marker.bindPopup(popupHtml);
                }
            });
        });
    </script>
</body>
</html>