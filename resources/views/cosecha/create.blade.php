<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-2xl font-bold tracking-tight text-green-900">
                    Recepción de cosecha
                </h2>

                <p class="text-sm text-gray-500">
                    Registro de pesajes y trazabilidad en campo
                </p>
            </div>

            {{-- Estado de conexión --}}
            <div id="connectionStatus"
                 class="inline-flex items-center gap-2 rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-800">

                <span id="connectionDot"
                      class="h-2.5 w-2.5 rounded-full bg-green-500"></span>

                <span id="connectionText">
                    Conectado
                </span>
            </div>

        </div>
    </x-slot>


    <div class="min-h-screen bg-gradient-to-br from-green-50 via-stone-50 to-amber-50 py-6">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Mensajes --}}
            @if(session('success'))
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-green-800 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 text-xl">✓</div>

                        <div>
                            <p class="font-semibold">Registro exitoso</p>
                            <p class="text-sm">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif


            @if(session('error'))
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 text-xl">!</div>

                        <div>
                            <p class="font-semibold">Ocurrió un error</p>
                            <p class="text-sm">
                                {{ session('error') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif


            @if(session('error_batch'))
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm">

                    <p class="mb-2 font-semibold">
                        No se pudieron procesar algunos registros:
                    </p>

                    <ul class="list-disc space-y-1 pl-5 text-sm">
                        @foreach(session('error_batch') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>
            @endif


            {{-- ========================================================= --}}
            {{-- SESIONES DE COSECHA --}}
            {{-- ========================================================= --}}

            <div class="mb-6 overflow-hidden rounded-2xl border border-green-200 bg-white shadow-sm">

                <div class="border-b border-green-100 bg-green-900 px-5 py-4">

                    <div class="flex items-center gap-3 text-white">

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-xl">
                            🌱
                        </div>

                        <div>
                            <h3 class="font-bold">
                                Sesión de cosecha
                            </h3>

                            <p class="text-xs text-green-100">
                                Seleccione la jornada de trabajo activa
                            </p>
                        </div>

                    </div>

                </div>


                <div class="p-5">

                    <label for="sesion_selector"
                           class="mb-2 block text-sm font-semibold text-gray-700">
                        Sesión activa
                    </label>

                    <select id="sesion_selector"
                            onchange="cambiarSesion(this.value)"
                            class="w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-800 shadow-sm transition focus:border-green-600 focus:ring-green-600">

                        @foreach($sesiones as $sesion)

                            <option value="{{ $sesion->id }}"
                                {{ $sesionSeleccionada->id == $sesion->id ? 'selected' : '' }}>

                                Sesión #{{ $sesion->id }}
                                @if($sesion->fecha)
                                    - {{ \Carbon\Carbon::parse($sesion->fecha)->format('d/m/Y') }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- INFORMACIÓN DE SESIÓN --}}
            {{-- ========================================================= --}}

            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">

                {{-- Estado --}}
                <div class="rounded-2xl border border-green-200 bg-white p-5 shadow-sm">

                    <div class="mb-3 flex items-center justify-between">

                        <span class="text-sm font-medium text-gray-500">
                            Estado
                        </span>

                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">
                            {{ ucfirst($sesionSeleccionada->estado ?? 'Abierta') }}
                        </span>

                    </div>

                    <p class="text-lg font-bold text-green-900">
                        Jornada activa
                    </p>

                </div>


                {{-- Fecha --}}
                <div class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm">

                    <div class="mb-2 text-sm font-medium text-gray-500">
                        Fecha de cosecha
                    </div>

                    <p class="text-lg font-bold text-gray-900">

                        @if($sesionSeleccionada->fecha)
                            {{ \Carbon\Carbon::parse($sesionSeleccionada->fecha)->format('d/m/Y') }}
                        @else
                            Sin fecha
                        @endif

                    </p>

                </div>


                {{-- Lote --}}
                <div class="rounded-2xl border border-lime-200 bg-white p-5 shadow-sm">

                    <div class="mb-2 text-sm font-medium text-gray-500">
                        Lote
                    </div>

                    <p class="text-lg font-bold text-green-900">

                        {{ $sesionSeleccionada->lote?->nombre_lote ?? 'Lote no definido' }}

                    </p>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- FORMULARIO --}}
            {{-- ========================================================= --}}

            <form method="POST"
                  action="{{ route('cosecha.store') }}"
                  enctype="multipart/form-data"
                  id="recepcionForm">

                @csrf

                {{-- ID único --}}
                <input type="hidden"
                       name="id"
                       id="recepcion_id">

                {{-- Sesión --}}
                <input type="hidden"
                       name="sesion_cosecha_id"
                       value="{{ $sesionSeleccionada->id }}">


                {{-- ===================================================== --}}
                {{-- TRAZABILIDAD --}}
                {{-- ===================================================== --}}

                <div class="mb-6 overflow-hidden rounded-2xl border border-green-200 bg-white shadow-sm">

                    <div class="border-b border-green-100 bg-gradient-to-r from-green-800 to-green-700 px-5 py-4">

                        <h3 class="font-bold text-white">
                            Trazabilidad del producto
                        </h3>

                        <p class="text-xs text-green-100">
                            Identifique quién, dónde y de qué árbol proviene la cosecha
                        </p>

                    </div>


                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-3">

                        {{-- Trabajador --}}
                        <div>

                            <label for="trabajador_id"
                                   class="mb-2 block text-sm font-semibold text-gray-700">

                                Trabajador <span class="text-red-500">*</span>

                            </label>

                            <select name="trabajador_id"
                                    id="trabajador_id"
                                    required
                                    class="w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-green-600 focus:ring-green-600">

                                <option value="">
                                    Seleccionar trabajador
                                </option>

                                @foreach($trabajadores as $trabajador)

                                    <option value="{{ $trabajador->id }}">
                                        {{ $trabajador->nombre ?? $trabajador->nombres ?? 'Trabajador #' . $trabajador->id }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Zona --}}
                        <div>

                            <label for="lote_zona_id"
                                   class="mb-2 block text-sm font-semibold text-gray-700">

                                Zona de manejo

                            </label>

                            <select name="lote_zona_id"
                                    id="lote_zona_id"
                                    class="w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-green-600 focus:ring-green-600">

                                <option value="">
                                    Seleccionar zona
                                </option>

                                @foreach($zonas as $zona)

                                    <option value="{{ $zona->id }}">

                                        {{ $zona->codigo_zona }}
                                        -
                                        {{ $zona->nombre_zona }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Árbol --}}
                        <div>

                            <label for="arbol_id"
                                   class="mb-2 block text-sm font-semibold text-gray-700">

                                Árbol / planta

                            </label>

                            <select name="arbol_id"
                                    id="arbol_id"
                                    class="w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-green-600 focus:ring-green-600">

                                <option value="">
                                    Seleccionar árbol
                                </option>

                                @foreach($arboles as $arbol)

                                    <option value="{{ $arbol->id }}">
                                        {{ $arbol->codigo_unico }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>

                </div>


                {{-- ===================================================== --}}
                {{-- PESOS --}}
                {{-- ===================================================== --}}

                <div class="mb-6 overflow-hidden rounded-2xl border border-amber-200 bg-white shadow-sm">

                    <div class="border-b border-amber-100 bg-gradient-to-r from-amber-700 to-yellow-600 px-5 py-4">

                        <div class="flex items-center justify-between">

                            <div>

                                <h3 class="font-bold text-white">
                                    Pesaje de cosecha
                                </h3>

                                <p class="text-xs text-amber-100">
                                    Registre los pesos obtenidos en campo
                                </p>

                            </div>

                            <div class="text-3xl">
                                ⚖️
                            </div>

                        </div>

                    </div>


                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-3">

                        {{-- Peso bruto --}}
                        <div>

                            <label for="peso_bruto"
                                   class="mb-2 block text-sm font-semibold text-gray-700">

                                Peso bruto (kg) <span class="text-red-500">*</span>

                            </label>

                            <div class="relative">

                                <input type="number"
                                       name="peso_bruto"
                                       id="peso_bruto"
                                       step="0.01"
                                       min="0.01"
                                       required
                                       inputmode="decimal"
                                       placeholder="0.00"
                                       class="w-full rounded-xl border-gray-300 px-4 py-4 pr-14 text-lg font-bold focus:border-green-600 focus:ring-green-600">

                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400">
                                    kg
                                </span>

                            </div>

                        </div>


                        {{-- Tara --}}
                        <div>

                            <label for="tara_costal"
                                   class="mb-2 block text-sm font-semibold text-gray-700">

                                Tara costal (kg) <span class="text-red-500">*</span>

                            </label>

                            <div class="relative">

                                <input type="number"
                                       name="tara_costal"
                                       id="tara_costal"
                                       step="0.01"
                                       min="0"
                                       required
                                       inputmode="decimal"
                                       value="0"
                                       class="w-full rounded-xl border-gray-300 px-4 py-4 pr-14 text-lg font-bold focus:border-green-600 focus:ring-green-600">

                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400">
                                    kg
                                </span>

                            </div>

                        </div>


                        {{-- Peso neto --}}
                        <div>

                            <label for="peso_neto"
                                   class="mb-2 block text-sm font-semibold text-gray-700">

                                Peso neto (kg) <span class="text-red-500">*</span>

                            </label>

                            <div class="relative">

                                <input type="number"
                                       name="peso_neto"
                                       id="peso_neto"
                                       step="0.01"
                                       min="0"
                                       readonly
                                       class="w-full rounded-xl border-green-300 bg-green-50 px-4 py-4 pr-14 text-lg font-black text-green-900 focus:border-green-600 focus:ring-green-600">

                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-green-600">
                                    kg
                                </span>

                            </div>

                            <p class="mt-1 text-xs text-gray-500">
                                Calculado automáticamente
                            </p>

                        </div>

                    </div>


                    {{-- Indicador peso --}}
                    <div class="mx-5 mb-5 rounded-xl bg-green-50 p-4">

                        <div class="flex items-center justify-between">

                            <span class="text-sm font-medium text-green-800">
                                Peso neto calculado
                            </span>

                            <span id="pesoNetoVisual"
                                  class="text-2xl font-black text-green-900">
                                0.00 kg
                            </span>

                        </div>

                    </div>

                </div>


                {{-- ===================================================== --}}
                {{-- INFORMACIÓN DEL CORTE --}}
                {{-- ===================================================== --}}

                <div class="mb-6 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">

                    <div class="border-b border-stone-200 bg-stone-800 px-5 py-4">

                        <h3 class="font-bold text-white">
                            Información del corte
                        </h3>

                        <p class="text-xs text-stone-300">
                            Datos adicionales para trazabilidad
                        </p>

                    </div>


                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-3">

                        {{-- Merma --}}
                        <div>

                            <label for="peso_merma_campo"
                                   class="mb-2 block text-sm font-semibold text-gray-700">

                                Merma en campo (kg)

                            </label>

                            <input type="number"
                                   name="peso_merma_campo"
                                   id="peso_merma_campo"
                                   step="0.01"
                                   min="0"
                                   value="0"
                                   inputmode="decimal"
                                   placeholder="0.00"
                                   class="w-full rounded-xl border-gray-300 px-4 py-3 focus:border-green-600 focus:ring-green-600">

                        </div>


                        {{-- Costal --}}
                        <div>

                            <label for="costal_codigo"
                                   class="mb-2 block text-sm font-semibold text-gray-700">

                                Código del costal

                            </label>

                            <input type="text"
                                   name="costal_codigo"
                                   id="costal_codigo"
                                   maxlength="100"
                                   placeholder="Ej. COST-00125"
                                   class="w-full rounded-xl border-gray-300 px-4 py-3 focus:border-green-600 focus:ring-green-600">

                        </div>


                        {{-- Número corte --}}
                        <div>

                            <label for="numero_corte"
                                   class="mb-2 block text-sm font-semibold text-gray-700">

                                Número de corte

                            </label>

                            <input type="number"
                                   name="numero_corte"
                                   id="numero_corte"
                                   min="1"
                                   step="1"
                                   placeholder="Ej. 1"
                                   class="w-full rounded-xl border-gray-300 px-4 py-3 focus:border-green-600 focus:ring-green-600">

                        </div>

                    </div>

                </div>


                {{-- ===================================================== --}}
                {{-- GPS --}}
                {{-- ===================================================== --}}

                <div class="mb-6 overflow-hidden rounded-2xl border border-blue-200 bg-white shadow-sm">

                    <div class="border-b border-blue-100 bg-blue-900 px-5 py-4">

                        <div class="flex items-center gap-3">

                            <div class="text-2xl">
                                📍
                            </div>

                            <div>

                                <h3 class="font-bold text-white">
                                    Ubicación GPS
                                </h3>

                                <p class="text-xs text-blue-100">
                                    Registre la posición donde se realizó el pesaje
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5">

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                            <div>

                                <label class="mb-2 block text-sm font-semibold text-gray-700">
                                    Latitud
                                </label>

                                <input type="number"
                                       step="any"
                                       name="latitude"
                                       id="latitude"
                                       readonly
                                       class="w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3">

                            </div>


                            <div>

                                <label class="mb-2 block text-sm font-semibold text-gray-700">
                                    Longitud
                                </label>

                                <input type="number"
                                       step="any"
                                       name="longitude"
                                       id="longitude"
                                       readonly
                                       class="w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3">

                            </div>


                            <div class="flex items-end">

                                <button type="button"
                                        onclick="obtenerGPS()"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-3 font-bold text-white shadow-sm transition hover:bg-blue-800 active:scale-95">

                                    📍 Obtener ubicación

                                </button>

                            </div>

                        </div>


                        <div id="gpsStatus"
                             class="mt-4 hidden rounded-xl bg-blue-50 p-3 text-sm text-blue-800">
                        </div>

                    </div>

                </div>


                {{-- ===================================================== --}}
                {{-- FOTO --}}
                {{-- ===================================================== --}}

                <div class="mb-6 overflow-hidden rounded-2xl border border-purple-200 bg-white shadow-sm">

                    <div class="border-b border-purple-100 bg-purple-900 px-5 py-4">

                        <div class="flex items-center gap-3">

                            <div class="text-2xl">
                                📷
                            </div>

                            <div>

                                <h3 class="font-bold text-white">
                                    Evidencia fotográfica
                                </h3>

                                <p class="text-xs text-purple-100">
                                    Tome una fotografía como soporte del registro
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5">

                        <label for="foto"
                               class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center transition hover:border-green-500 hover:bg-green-50">

                            <div class="mb-3 text-4xl">
                                📸
                            </div>

                            <p class="font-bold text-gray-700">
                                Tomar fotografía
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                Evidencia del pesaje o producto recibido
                            </p>

                            <input type="file"
                                   name="foto"
                                   id="foto"
                                   accept="image/*"
                                   capture="environment"
                                   class="hidden">

                        </label>


                        <div id="photoPreview"
                             class="mt-4 hidden">

                            <img id="previewImage"
                                 class="mx-auto max-h-64 rounded-xl object-cover shadow-md"
                                 alt="Vista previa">

                        </div>

                    </div>

                </div>


                {{-- ===================================================== --}}
                {{-- HORA --}}
                {{-- ===================================================== --}}

                <input type="hidden"
                       name="hora_pesaje"
                       id="hora_pesaje">


                <input type="hidden"
                       name="client_updated_at"
                       id="client_updated_at">


                {{-- ===================================================== --}}
                {{-- BOTONES --}}
                {{-- ===================================================== --}}

                <div class="sticky bottom-4 z-20">

                    <div class="rounded-2xl border border-green-200 bg-white/95 p-4 shadow-xl backdrop-blur">

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                            <div>

                                <p class="text-sm font-bold text-gray-800">
                                    Registro de recepción
                                </p>

                                <p class="text-xs text-gray-500">
                                    Verifique los datos antes de guardar
                                </p>

                            </div>


                            <div class="flex flex-col gap-3 sm:flex-row">

                                <a href="{{ route('sesiones-cosecha.index') }}"
                                   class="rounded-xl border border-gray-300 px-6 py-3 text-center text-sm font-bold text-gray-700 transition hover:bg-gray-50">

                                    Cancelar

                                </a>


                                <button type="submit"
                                        id="submitButton"
                                        class="rounded-xl bg-green-700 px-8 py-3 text-sm font-black text-white shadow-lg shadow-green-700/20 transition hover:bg-green-800 active:scale-95">

                                    Guardar recepción

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- ================================================================ --}}
    {{-- JAVASCRIPT --}}
    {{-- ================================================================ --}}

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            // ------------------------------------------------------------
            // ID UUID
            // ------------------------------------------------------------

            const idInput = document.getElementById('recepcion_id');

            if (idInput) {

                idInput.value = crypto.randomUUID();

            }


            // ------------------------------------------------------------
            // Fecha y hora
            // ------------------------------------------------------------

            const now = new Date();

            document.getElementById('hora_pesaje').value =
                now.toISOString();

            document.getElementById('client_updated_at').value =
                now.toISOString();


            // ------------------------------------------------------------
            // Cálculo peso neto
            // ------------------------------------------------------------

            const bruto = document.getElementById('peso_bruto');
            const tara = document.getElementById('tara_costal');
            const neto = document.getElementById('peso_neto');
            const visual = document.getElementById('pesoNetoVisual');


            function calcularNeto() {

                const pesoBruto = parseFloat(bruto.value) || 0;
                const pesoTara = parseFloat(tara.value) || 0;

                let resultado = pesoBruto - pesoTara;

                if (resultado < 0) {
                    resultado = 0;
                }

                neto.value = resultado.toFixed(2);

                visual.textContent =
                    resultado.toFixed(2) + ' kg';

            }


            bruto.addEventListener('input', calcularNeto);
            tara.addEventListener('input', calcularNeto);


            // ------------------------------------------------------------
            // Preview fotografía
            // ------------------------------------------------------------

            const foto = document.getElementById('foto');
            const previewContainer = document.getElementById('photoPreview');
            const previewImage = document.getElementById('previewImage');


            foto.addEventListener('change', function () {

                const file = this.files[0];

                if (!file) {
                    previewContainer.classList.add('hidden');
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (event) {

                    previewImage.src = event.target.result;

                    previewContainer.classList.remove('hidden');

                };

                reader.readAsDataURL(file);

            });


            // ------------------------------------------------------------
            // Estado conexión
            // ------------------------------------------------------------

            actualizarConexion();

            window.addEventListener('online', actualizarConexion);
            window.addEventListener('offline', actualizarConexion);

        });


        // ================================================================
        // CONEXIÓN
        // ================================================================

        function actualizarConexion() {

            const status = document.getElementById('connectionStatus');
            const dot = document.getElementById('connectionDot');
            const text = document.getElementById('connectionText');

            if (navigator.onLine) {

                status.className =
                    'inline-flex items-center gap-2 rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-800';

                dot.className =
                    'h-2.5 w-2.5 rounded-full bg-green-500';

                text.textContent = 'Conectado';

            } else {

                status.className =
                    'inline-flex items-center gap-2 rounded-full bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-800';

                dot.className =
                    'h-2.5 w-2.5 rounded-full bg-amber-500';

                text.textContent = 'Modo offline';

            }

        }


        // ================================================================
        // GPS
        // ================================================================

        function obtenerGPS() {

            const status = document.getElementById('gpsStatus');

            if (!navigator.geolocation) {

                status.textContent =
                    'Este dispositivo no soporta geolocalización.';

                status.classList.remove('hidden');

                return;

            }


            status.textContent =
                'Obteniendo ubicación...';

            status.classList.remove('hidden');


            navigator.geolocation.getCurrentPosition(

                function (position) {

                    document.getElementById('latitude').value =
                        position.coords.latitude;

                    document.getElementById('longitude').value =
                        position.coords.longitude;


                    status.textContent =
                        'Ubicación registrada correctamente. Precisión aproximada: ' +
                        Math.round(position.coords.accuracy) +
                        ' metros.';

                },

                function (error) {

                    status.textContent =
                        'No fue posible obtener la ubicación. Verifique los permisos GPS.';

                },

                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }

            );

        }


        // ================================================================
        // CAMBIAR SESIÓN
        // ================================================================

        function cambiarSesion(id) {

            if (!id) {
                return;
            }

            window.location.href =
                "{{ url('/cosecha/create') }}/" + id;

        }


        // ================================================================
        // VALIDACIÓN
        // ================================================================

        document.getElementById('recepcionForm')
            .addEventListener('submit', function (event) {

                const bruto =
                    parseFloat(document.getElementById('peso_bruto').value) || 0;

                const tara =
                    parseFloat(document.getElementById('tara_costal').value) || 0;

                const neto =
                    parseFloat(document.getElementById('peso_neto').value) || 0;


                if (bruto <= 0) {

                    event.preventDefault();

                    alert('El peso bruto debe ser mayor que cero.');

                    return;

                }


                if (tara < 0) {

                    event.preventDefault();

                    alert('La tara no puede ser negativa.');

                    return;

                }


                const calculado =
                    bruto - tara;


                if (Math.abs(neto - calculado) > 0.05) {

                    event.preventDefault();

                    alert(
                        'El peso neto no coincide con Peso Bruto - Tara.'
                    );

                    return;

                }


                // Actualizar fecha justo antes de enviar

                const now = new Date();

                document.getElementById('hora_pesaje').value =
                    now.toISOString();

                document.getElementById('client_updated_at').value =
                    now.toISOString();


                const button =
                    document.getElementById('submitButton');

                button.disabled = true;

                button.textContent =
                    'Guardando...';

            });

    </script>

</x-app-layout>
