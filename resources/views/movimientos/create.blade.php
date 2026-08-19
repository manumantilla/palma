<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        
        <!-- Alertas de Éxito o Error -->
        @if(session('success'))
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Panel Superior: Selección de Orden -->
        <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">1. Seleccionar Orden de Cosecha</h2>
            <form action="{{ route('movimientos.create') }}" method="GET" class="flex items-center space-x-4">
                <select name="orden_id" class="form-select rounded-md border-gray-300 w-1/2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Selecciona una Orden en Proceso --</option>
                    @foreach($ordenesActivas as $orden)
                        <option value="{{ $orden->id }}" {{ (isset($ordenSeleccionada) && $ordenSeleccionada->id == $orden->id) ? 'selected' : '' }}>
                            Orden #{{ $orden->id }} - Lote: {{ $orden->loteCultivo->nombre ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Cargar Costales
                </button>
            </form>
        </div>

        <!-- Panel de Asignación (Solo visible si hay una orden seleccionada) -->
        @if(isset($ordenSeleccionada))
            <form action="{{ route('movimientos.store') }}" method="POST">
                @csrf
                <!-- Para el redirect de éxito -->
                <input type="hidden" name="orden_id" value="{{ $ordenSeleccionada->id }}">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Lado Izquierdo: Lista de Costales (2/3 del espacio) -->
                    <div class="md:col-span-2 bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-bold text-gray-800">2. Costales Pendientes</h2>
                            <label class="flex items-center text-sm font-medium text-gray-700 cursor-pointer">
                                <input type="checkbox" id="checkAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 mr-2">
                                Seleccionar Todos
                            </label>
                        </div>
                        
                        <div class="overflow-y-auto max-h-96 border rounded-md">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selec.</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código/ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trabajador</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Peso Neto (Kg)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($recepcionesPendientes as $recepcion)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" name="recepcion_ids[]" value="{{ $recepcion->id }}" data-peso="{{ $recepcion->peso_neto }}" class="costal-checkbox rounded border-gray-300 text-blue-600 shadow-sm">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $recepcion->costal_codigo ?? 'S/C' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $recepcion->trabajador->nombre ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-semibold">
                                                {{ number_format($recepcion->peso_neto, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No hay costales pendientes para esta orden.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @error('recepcion_ids')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Lado Derecho: Destino y Confirmación (1/3 del espacio) -->
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-col justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800 mb-4">3. Destino (Tolva)</h2>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Contenedor</label>
                            <select name="contenedor_id" required class="w-full form-select rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Elige una Tolva --</option>
                                @foreach($contenedores as $contenedor)
                                    <option value="{{ $contenedor->id }}">
                                        {{ $contenedor->codigo }} (Actual: {{ number_format($contenedor->kilos_acumulados, 2) }} kg)
                                    </option>
                                @endforeach
                            </select>
                            @error('contenedor_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-8 bg-gray-100 p-4 rounded-lg text-center border-2 border-dashed border-gray-300">
                            <span class="block text-sm text-gray-500 uppercase tracking-wide">Total a Asignar</span>
                            <span id="displayTotalKilos" class="block text-4xl font-extrabold text-blue-600 mt-2">0.00</span>
                            <span class="block text-sm text-gray-500 mt-1">Kg / <span id="displayTotalCostales">0</span> costales</span>
                        </div>

                        <button type="submit" id="btnSubmit" class="mt-6 w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg disabled:opacity-50 transition-all">
                            Confirmar Asignación
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>

    <!-- Script para sumar en tiempo real -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.costal-checkbox');
            const checkAll = document.getElementById('checkAll');
            const displayTotalKilos = document.getElementById('displayTotalKilos');
            const displayTotalCostales = document.getElementById('displayTotalCostales');
            const btnSubmit = document.getElementById('btnSubmit');

            function actualizarTotales() {
                let totalKilos = 0;
                let totalCostales = 0;

                checkboxes.forEach(cb => {
                    if (cb.checked) {
                        // Rescatamos el data-peso del HTML y lo sumamos
                        totalKilos += parseFloat(cb.dataset.peso);
                        totalCostales++;
                    }
                });

                // Actualizamos UI
                displayTotalKilos.innerText = totalKilos.toFixed(2);
                displayTotalCostales.innerText = totalCostales;

                // Deshabilitar botón si no hay nada seleccionado
                btnSubmit.disabled = totalCostales === 0;
            }

            // Evento para cada checkbox individual
            checkboxes.forEach(cb => {
                cb.addEventListener('change', actualizarTotales);
            });

            // Evento para "Seleccionar Todos"
            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                    actualizarTotales();
                });
            }

            // Ejecutar al inicio por si el navegador recuerda los checks
            actualizarTotales();
        });
    </script>
</x-app-layout>