<div id="modalBitacoraPolimorfica" class="fixed inset-0 bg-slate-900 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
        
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Nueva Anotación / Bitácora</h3>
                <p class="text-xs text-gray-500">Origen: <span id="bitacora_origen_texto" class="font-bold text-blue-600"></span></p>
            </div>
            <button type="button" onclick="cerrarModalBitacora()" class="text-gray-400 text-xl font-bold">&times;</button>
        </div>

        <form action="{{ route('bitacoras.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="bitacorable_type" id="bitacora_poly_type">
            <input type="hidden" name="bitacorable_id" id="bitacora_poly_id">
            
            <input type="hidden" name="latitud" id="bitacora_lat">
            <input type="hidden" name="longitud" id="bitacora_lng">

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tipo de Evento *</label>
                    <select name="tipo" id="bitacora_tipo" onchange="ajustarFormularioBitacora()" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500" required>
                        <option value="observacion">Observación General</option>
                        <option value="alerta">Alerta Temprana</option>
                        <option value="incidente">Incidente (Plaga/Daño)</option>
                        <option value="decision">Decisión Agronómica</option>
                        <option value="condicion_clima">Condición Climática</option>
                        <option value="visita_tecnica">Visita Técnica</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Prioridad *</label>
                    <select name="prioridad" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500" required>
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                        <option value="critica">Crítica 🚨</option>
                    </select>
                </div>
            </div>


            <div class="mb-3">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Título Resumido *</label>
                <input type="text" name="titulo" placeholder="Ej: Avistamiento de broca del café" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Descripción / Hallazgos *</label>
                <textarea name="contenido" rows="4" placeholder="Describe detalladamente lo observado..." class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500" required></textarea>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Capturar Evidencias (Fotos)</label>
                <input type="file" name="fotos[]" multiple accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-[10px] text-gray-400 mt-1">Puedes seleccionar o tomar múltiples fotos simultáneamente.</p>
            </div>

            <div class="flex justify-end space-x-2 border-t pt-3">
                <button type="button" onclick="cerrarModalBitacora()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded text-sm font-medium">Cancelar</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium shadow">Registrar Bitácora</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalBitacora(type, id, nombre) {
        document.getElementById('bitacora_poly_type').value = type;
        document.getElementById('bitacora_poly_id').value = id;
        document.getElementById('bitacora_origen_texto').innerText = nombre;
        
        // --- GEOLOCALIZACIÓN MÓVIL AUTOMÁTICA ---
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('bitacora_lat').value = position.coords.latitude;
                document.getElementById('bitacora_lng').value = position.coords.longitude;
            }, function(error) {
                console.log("GPS no autorizado o no disponible.");
            });
        }

        document.getElementById('modalBitacoraPolimorfica').classList.remove('hidden');
    }

    function cerrarModalBitacora() {
        document.getElementById('modalBitacoraPolimorfica').classList.add('hidden');
    }

    function ajustarFormularioBitacora() {
        const tipo = document.getElementById('bitacora_tipo').value;
        const divClima = document.getElementById('wrapper_clima');
        const divVisita = document.getElementById('wrapper_visita');

        // Reset inputs
        divClima.classList.add('hidden');
        divVisita.classList.add('hidden');

        if (tipo === 'condicion_clima') {
            divClima.classList.remove('hidden');
        } else if (tipo === 'visita_tecnica') {
            divVisita.classList.remove('hidden');
        }
    }
</script>