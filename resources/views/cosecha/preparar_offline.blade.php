<x-app-layout>
<div class="container mx-auto p-4 max-w-4xl">
    
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p class="font-bold">Éxito</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
            <p class="font-bold">Error</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if(session('error_batch'))
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
            <p class="font-bold">Errores de Validación en Lote:</p>
            <ul class="list-disc pl-5">
                @foreach(session('error_batch') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tarjeta Principal de Sesión -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">
            Preparación para Campo Offline
        </h2>
        <p class="text-gray-600 mb-4">
            Sesión: <strong>{{ $sesion->nombre ?? 'Cosecha Lote ' . $sesion->lote_id }}</strong> | 
            Fecha: <strong>{{ $sesion->created_at->format('d/m/Y') }}</strong>
        </p>

        <div class="flex items-center space-x-4">
            <button id="btnGuardarOffline" onclick="descargarDatosCampo()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                💾 Descargar Datos al Dispositivo (Modo Offline)
            </button>
            
            <span id="estadoDescarga" class="text-sm text-gray-500">
                Estado: No inicializado en este teléfono.
            </span>
        </div>
    </div>

    <!-- Formulario Oculto/Manejador de Sincronización Manual al Volver a Tener Red -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold mb-3">Registros Pendientes por Sincronizar</h3>
        <p class="text-sm text-gray-500 mb-4">
            Cuando regreses del campo con señal, haz clic en el botón para enviar los datos capturados.
        </p>

        <form id="formSync" action="{{ route('cosecha.recepcion.store') }}" method="POST">
            @csrf
            <!-- Campo oculto donde JS inyectará el JSON comprimido formateado como array de registros -->
            <input type="hidden" name="recepciones" id="inputRecepcionesJSON">
            
            <div class="flex items-center justify-between">
                <span id="contadorPendientes" class="font-bold text-lg text-orange-600">
                    0 pesajes pendientes en este teléfono.
                </span>

                <button type="button" onclick="enviarDatosSincronizacion()" id="btnSync" disabled class="bg-gray-400 text-white font-bold py-2 px-6 rounded cursor-not-allowed">
                    ☁️ Sincronizar Datos al Servidor
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Inyección de catálogos desde Laravel Blade hacia JavaScript
    const paqueteOffline = {
        sesion_cosecha: @json($sesion),
        trabajadores: @json($trabajadores),
        zonas: @json($zonas),
        arboles: @json($arboles),
        descargado_el: new Date().toISOString()
    };

    const STORAGE_KEY_CATALOGO = "cosecha_catalogo_" + paqueteOffline.sesion_cosecha.id;
    const STORAGE_KEY_PENDIENTES = "cosecha_pendientes_" + paqueteOffline.sesion_cosecha.id;

    document.addEventListener("DOMContentLoaded", function() {
        verificarEstadoLocal();
    });

    function descargarDatosCampo() {
        try {
            localStorage.setItem(STORAGE_KEY_CATALOGO, JSON.stringify(paqueteOffline));
            document.getElementById("estadoDescarga").innerHTML = "✅ <strong>Datos listos.</strong> Puedes ir al campo sin señal.";
            document.getElementById("estadoDescarga").className = "text-sm text-green-600";
            alert("Información de trabajadores y zonas guardada en la memoria local del celular.");
        } catch (e) {
            alert("Error al guardar en el almacenamiento local: " + e.message);
        }
    }

    function verificarEstadoLocal() {
        const catalogo = localStorage.getItem(STORAGE_KEY_CATALOGO);
        if (catalogo) {
            document.getElementById("estadoDescarga").innerHTML = "✅ <strong>Datos en caché local.</strong> Listo para offline.";
            document.getElementById("estadoDescarga").className = "text-sm text-green-600";
        }

        const pendientes = JSON.parse(localStorage.getItem(STORAGE_KEY_PENDIENTES) || "[]");
        const count = pendientes.length;
        
        const btnSync = document.getElementById("btnSync");
        const contadorText = document.getElementById("contadorPendientes");

        contadorText.innerText = count + " pesajes pendientes en este teléfono.";

        if (count > 0) {
            btnSync.disabled = false;
            btnSync.className = "bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded cursor-pointer shadow";
        } else {
            btnSync.disabled = true;
            btnSync.className = "bg-gray-400 text-white font-bold py-2 px-6 rounded cursor-not-allowed";
        }
    }

    function enviarDatosSincronizacion() {
        const pendientes = JSON.parse(localStorage.getItem(STORAGE_KEY_PENDIENTES) || "[]");
        if (pendientes.length === 0) {
            alert("No hay datos pendientes para enviar.");
            return;
        }

        // Asignamos la matriz al input oculto y enviamos el formulario HTML tradicional
        document.getElementById("inputRecepcionesJSON").value = JSON.stringify(pendientes);
        
        // Limpiamos la cola local tras la preparación de envío
        localStorage.removeItem(STORAGE_KEY_PENDIENTES);
        
        document.getElementById("formSync").submit();
    }
</script>
</x-app-layout>