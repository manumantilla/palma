<x-app-layout>
<div class="container mx-auto px-4 py-8 max-w-4xl">

    {{-- Navegación e Historial --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('ciclos-productivos.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition">← Volver al listado</a>
        <div class="h-4 w-px bg-gray-300"></div>
        <h1 class="text-2xl font-bold text-gray-800">🌱 Apertura de Ciclo Productivo Estratégico</h1>
    </div>
@if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
        <h4 class="font-bold text-red-800">Campos inválidos en el formulario:</h4>
        <ul class="list-disc ml-5 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <form method="POST" action="{{ route('ciclos-productivos.store') }}" class="space-y-6">
        @csrf

        {{-- Bloque 1: Datos de Asignación Base --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                <span>📋</span> Clasificación del Cultivo y Ubicación
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Campaña / Identificador</label>
                    <input type="text" name="nombre_campana" value="{{ old('nombre_campana') }}" placeholder="Ej: Maíz Amarillo Tecnificado 2026 - A" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    @error('nombre_campana') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lote Geográfico de Destino</label>
                    <select name="lote_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500">
                        <option value="">Seleccione el lote</option>
                        @foreach($lotes as $lote)
                            <option value="{{ $lote->id }}" {{ old('lote_id') == $lote->id ? 'selected' : '' }}>{{ $lote->nombre_lote }} ({{ $lote->codigo_lote }})</option>
                        @endforeach
                    </select>
                    @error('lote_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cultivo / Variedad Botánica</label>
                    <select name="cultivo_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500">
                        <option value="">Seleccione cultivo</option>
                        @foreach($cultivos as $cultivo)
                            <option value="{{ $cultivo->id }}" {{ old('cultivo_id') == $cultivo->id ? 'selected' : '' }}>{{ $cultivo->nombre_cultivo }}</option>
                        @endforeach
                    </select>
                    @error('cultivo_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                        Estado del Ciclo Productivo
                    </label>
                    <select 
                        id="estado" 
                        name="estado" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required
                    >
                        <option value="planificado" {{ old('estado', 'planificado') == 'planificado' ? 'selected' : '' }}>
                            Planificado
                        </option>
                        <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>
                            Activo
                        </option>
                        <option value="en_receso" {{ old('estado') == 'en_receso' ? 'selected' : '' }}>
                            En receso
                        </option>
                        <option value="concluido" {{ old('estado') == 'concluido' ? 'selected' : '' }}>
                            Concluido
                        </option>
                        <option value="siniestrado" {{ old('estado') == 'siniestrado' ? 'selected' : '' }}>
                            Siniestrado / Pérdida
                        </option>
                    </select>
                </div>


@error('estado')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Ciclo Vegetal</label>
                    <div class="mt-2 flex gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="tipo" value="transitorio" {{ old('tipo', 'transitorio') === 'transitorio' ? 'checked' : '' }} class="text-green-600 focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Transitorio (Ciclo Corto)</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="tipo" value="perenne" {{ old('tipo') === 'perenne' ? 'checked' : '' }} class="text-green-600 focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Perenne (Largo Rendimiento)</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bloque 2: Cronograma Estimado Temporal --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                <span>📅</span> Cronograma y Planificación Temporal
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio / Siembra</label>
                    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio') }}" class="w-full rounded-lg border-gray-300 shadow-sm">
                    @error('fecha_inicio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Est. Inicio Cosecha</label>
                    <input type="date" name="fecha_estimada_cosecha" value="{{ old('fecha_estimada_cosecha') }}" class="w-full rounded-lg border-gray-300 shadow-sm">
                    @error('fecha_estimada_cosecha') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Est. Fin de Cosecha</label>
                    <input type="date" name="fecha_estimada_fin_cosecha" value="{{ old('fecha_estimada_fin_cosecha') }}" class="w-full rounded-lg border-gray-300 shadow-sm">
                    @error('fecha_estimada_fin_cosecha') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Bloque 3: Marco de Siembra y Arreglo Topológico --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                <span>📐</span> Marco de Plantación y Densidad Sostenible
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modalidad de Siembra</label>
                    <select name="modalidad_siembra" class="w-full rounded-lg border-gray-300 shadow-sm">
                        <option value="semilla_directa" {{ old('modalidad_siembra')=='semilla_directa'?'selected':'' }}>Semilla Directa</option>
                        <option value="plantula_vivero" {{ old('modalidad_siembra')=='plantula_vivero'?'selected':'' }}>Plántula de Vivero</option>
                        <option value="estaca_esqueje" {{ old('modalidad_siembra')=='estaca_esqueje'?'selected':'' }}>Estaca / Esqueje</option>
                        <option value="arbol_injertado" {{ old('modalidad_siembra')=='arbol_injertado'?'selected':'' }}>Árbol Injertado</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Distancia Hileras (m)</label>
                    <input type="number" step="0.01" id="distancia_hileras" name="distancia_entre_hileras_metros" value="{{ old('distancia_entre_hileras_metros') }}" placeholder="Ej: 2.50" class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Distancia Plantas (m)</label>
                    <input type="number" step="0.01" id="distancia_plantas" name="distancia_entre_plantas_metros" value="{{ old('distancia_entre_plantas_metros') }}" placeholder="Ej: 0.80" class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
            </div>

            {{-- Caja de Cálculo en Tiempo Real --}}
            <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase block">Proyección en Campo</span>
                    <p class="text-sm text-gray-600">Densidad de población teórica proyectada por hectárea:</p>
                </div>
                <div class="text-right">
                    <span id="live_density_badge" class="text-xl font-mono font-bold text-green-700 bg-green-50 border border-green-200 px-3 py-1 rounded-lg">0.00</span>
                    <span class="text-xs font-medium text-gray-400 block mt-1">plantas / Ha</span>
                </div>
            </div>
        </div>

        {{-- Bloque 4: Trazabilidad Sanitaria y Certificación --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                <span>🛡️</span> Origen Fitosanitario y Control
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor de Material Vegetal</label>
                    <select name="proveedor_material_vegetal_id" class="w-full rounded-lg border-gray-300 shadow-sm">
                        <option value="">Desconocido / Semilla propia</option>
                        @foreach($proveedores as $prov)
                            <option value="{{ $prov->id }}" {{ old('proveedor_material_vegetal_id') == $prov->id ? 'selected' : '' }}>{{ $prov->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pasaporte Fitosanitario (Lote Vivero)</label>
                    <input type="text" name="codigo_lote_vivero_origen" value="{{ old('codigo_lote_vivero_origen') }}" placeholder="Ej: PAS-2026-9912" class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Registro / Autorización Institucional</label>
                    <input type="text" name="registro_autorizacion_institucional" value="{{ old('registro_autorizacion_institucional') }}" placeholder="Ej: Registro ICA o aval nacional" class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Agrónomo / Responsable del Ciclo</label>
                    <select name="agronomo_responsable_id" class="w-full rounded-lg border-gray-300 shadow-sm">
                        <option value="">Asignar más tarde</option>
                        @foreach($agronomos as $agronomo)
                            <option value="{{ $agronomo->id }}" {{ old('agronomo_responsable_id') == $agronomo->id ? 'selected' : '' }}>{{ $agronomo->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2 pt-2">
                    <label class="flex items-center gap-2 bg-emerald-50/50 p-3 rounded-lg border border-emerald-100 cursor-pointer">
                        <input type="checkbox" name="es_organico_certificado" value="1" {{ old('es_organico_certificado') ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="text-sm font-semibold text-emerald-900 block">¿Este ciclo cuenta con Certificación Orgánica?</span>
                            <span class="text-xs text-emerald-600 block">Activar únicamente si se posee el sello de transición u orgánico vigente para la comercialización externa.</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- Panel de Acciones Finales --}}
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('ciclos-productivos.index') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold py-2.5 px-6 rounded-lg transition">Cancelar</a>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-8 rounded-lg shadow transition">Dar de Alta Ciclo</button>
        </div>
    </form>
</div>

{{-- Script UX Interactivo para cálculo de Densidad --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputHileras = document.getElementById('distancia_hileras');
        const inputPlantas = document.getElementById('distancia_plantas');
        const badgeDensidad = document.getElementById('live_density_badge');

        function re_calcularLiveDensity() {
            const hileras = parseFloat(inputHileras.value);
            const plantas = parseFloat(inputPlantas.value);

            if (hileras > 0 && plantas > 0) {
                // Cálculo estándar: 10,000 / (Marco de siembra)
                const densidad = 10000 / (hileras * plantas);
                badgeDensidad.textContent = densidad.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            } else {
                badgeDensidad.textContent = "0.00";
            }
        }

        inputHileras.addEventListener('input', re_calcularLiveDensity);
        inputPlantas.addEventListener('input', re_calcularLiveDensity);
        
        // Ejecución preventiva (por si hay datos cargados en el old)
        re_calcularLiveDensity();
    });
</script>
</x-app-layout>