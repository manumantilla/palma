<x-app-layout>
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-stone-200 p-6">
            <div class="border-b border-stone-100 pb-4 mb-6">
                <h2 class="text-2xl font-bold text-stone-800">Planificar Nueva Orden de Cosecha</h2>
                <p class="text-sm text-stone-500 mt-1">Vincula los requerimientos del comprador con la disponibilidad biológica de tus lotes.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-lg text-sm">
                    <span class="font-bold">Por favor corrige los siguientes errores:</span>
                    <ul class="list-disc list-inside mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('ordenes-cosecha.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="bg-stone-50 p-4 rounded-xl border border-stone-200">
                    <h3 class="text-xs font-bold text-emerald-800 uppercase tracking-wider mb-3">📍 Ubicación y Ciclo Biológico</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-stone-700">Ciclo Productivo</label>
                            <select name="ciclo_productivo_id" class="mt-1 block w-full rounded-lg border-stone-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                                <option value="">Seleccione el ciclo...</option>
                                @foreach($ciclos as $ciclo)
                                </select>
                        </div>
                        <!-- <div>
                            <label class="block text-sm font-medium text-stone-700">Lote de Cultivo</label>
                            <select name="lote_cultivo_id" class="mt-1 block w-full rounded-lg border-stone-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                                <option value="">Seleccione el lote...</option>
                                </select>
                        </div> -->
                        <div>
                            <label class="block text-sm font-medium text-stone-700">Zona de Manejo</label>
                            <select name="lote_zona" class="mt-1 block w-full rounded-lg border-stone-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                                <option value="">Seleccione la zona...</option>
                                </select>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-stone-700">Comprador / Cliente Vinculado</label>
                        <select name="cliente_id" class="mt-1 block w-full rounded-lg border-stone-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                            <option value="">Seleccione el comprador...</option>
                            </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700">Fecha de Ejecución Programada</label>
                        <input type="date" name="fecha_programada" value="{{ old('fecha_programada') }}" class="mt-1 block w-full rounded-lg border-stone-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 bg-amber-50/50 p-4 rounded-xl border border-amber-200">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider">Variedad Requerida</label>
                        <input type="text" name="variedad_requerida" placeholder="Ej: Aguacate Hass Extra, Café Supremo" value="{{ old('variedad_requerida') }}" class="mt-1 block w-full rounded-lg border-amber-300 text-sm shadow-sm bg-white focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider">Kilos Solicitados (Meta)</label>
                        <input type="number" step="0.01" name="cantidad_planificada_kg" placeholder="0.00" value="{{ old('cantidad_planificada_kg') }}" class="mt-1 block w-full rounded-lg border-amber-300 text-sm shadow-sm bg-white focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider">Precio Unitario acordado ($/Kg)</label>
                        <input type="number" step="0.01" name="precio_unitario" placeholder="0.00" value="{{ old('precio_unitario') }}" class="mt-1 block w-full rounded-lg border-amber-300 text-sm shadow-sm bg-white focus:border-amber-500 focus:ring-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-700">Operario Responsable de Cuadrilla</label>
                    <input type="text" name="responsable" placeholder="Nombre del mayordomo o jefe de corte" value="{{ old('responsable') }}" class="mt-1 block w-full rounded-lg border-stone-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-700">Instrucciones de Despacho u Observaciones</label>
                    <textarea name="notas" rows="3" placeholder="Restricciones de madurez, tipo de canastillas, etc..." class="mt-1 block w-full rounded-lg border-stone-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('notas') }}</textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-stone-100">
                    <a href="{{ route('ordenes-cosecha.index') }}" class="px-4 py-2 border border-stone-300 rounded-lg text-sm font-medium text-stone-700 bg-white hover:bg-stone-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-5 py-2 border border-transparent rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        Crear Orden Guardada
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>