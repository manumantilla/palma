<x-app-layout>
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Configurar Nuevo Tipo de Evento</h2>
            <p class="text-sm text-gray-500 mb-6">Define las reglas lógicas que controlarán el comportamiento operativo de las labores en campo.</p>

            <form action="{{ route('tipos-evento.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre de la Labor</label>
                        <input type="text" name="nombre" placeholder="Ej: Fumigación Roya, Poda de Formación" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Categoría Agronómica</label>
                        <select name="categoria" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" required>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="border-gray-200">

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Comportamiento en Sistema (Reglas de Negocio)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <div class="space-y-3 bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Impacto de Recursos</h4>
                            
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="consume_insumos" value="1" class="h-4 w-4 rounded text-green-600 focus:ring-green-500 border-gray-300">
                                <span class="text-sm text-gray-700 font-medium">Consume Insumos (Agroquímicos, Fertilizantes)</span>
                            </label>

                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="consume_mano_obra" value="1" class="h-4 w-4 rounded text-green-600 focus:ring-green-500 border-gray-300">
                                <span class="text-sm text-gray-700 font-medium">Consume Mano de Obra (Jornales/Destajo)</span>
                            </label>

                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="genera_movimiento_stock" value="1" class="h-4 w-4 rounded text-green-600 focus:ring-green-500 border-gray-300">
                                <span class="text-sm text-gray-700 font-medium">Afecta Kárdex / Inventario Bodega</span>
                            </label>

                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="genera_ingreso" value="1" class="h-4 w-4 rounded text-green-600 focus:ring-green-500 border-gray-300">
                                <span class="text-sm text-gray-700 font-medium">Genera Ingreso Líquido (Venta de Cosecha)</span>
                            </label>
                        </div>

                        <div class="space-y-3 bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Granularidad de Aplicación</h4>

                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="requiere_area_ha" value="1" class="h-4 w-4 rounded text-green-600 focus:ring-green-500 border-gray-300">
                                <span class="text-sm text-gray-700 font-medium">Requiere medición de área afectada (Hectáreas)</span>
                            </label>

                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="aplica_a_arbol" value="1" class="h-4 w-4 rounded text-green-600 focus:ring-green-500 border-gray-300">
                                <span class="text-sm text-gray-700 font-medium">Aplica a árbol individual (Manejo Perennes/Grafos)</span>
                            </label>

                            <label class="flex items-center space-x-3">
                                <input type="checkbox" name="aplica_a_ciclo" value="1" checked class="h-4 w-4 rounded text-green-600 focus:ring-green-500 border-gray-300">
                                <span class="text-sm text-gray-700 font-medium">Aplica a Ciclo Productivo cerrado (Transitorios)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                    <h3 class="text-sm font-semibold text-amber-800 flex items-center mb-3">
                        ⚠️ Parámetros de Seguridad Agropecuaria (ICA / GlobalGAP)
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-amber-950">Período de Reingreso (Horas)</label>
                            <input type="number" name="periodo_reingreso_horas" placeholder="Ej: 24" class="mt-1 block w-full rounded-md border-amber-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-amber-950">Período de Carencia (Días pre-cosecha)</label>
                            <input type="number" name="periodo_carencia_dias" placeholder="Ej: 15" class="mt-1 block w-full rounded-md border-amber-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm bg-white">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('tipos-evento.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancelar</a>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Guardar Configuración</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>