<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar Consumo de Insumos (Kardex) - Evento #' . $evento->id) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow">
                    <span class="font-bold">¡Error!</span> {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <div class="mb-8 p-4 bg-gray-50 border border-gray-200 rounded-lg grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase text-gray-500">Fecha Programada</span>
                        <p class="text-gray-800 font-medium">{{ \Carbon\Carbon::parse($evento->fecha_programada)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase text-gray-500">Ubicación</span>
                        <p class="text-gray-800 font-medium">
                            Lote: {{ $evento->lote->nombre_lote ?? 'N/A' }} 
                            {{ $evento->zona ? ' - Zona: '.$evento->zona->nombre_zona : '' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase text-gray-500">Estado Actual</span>
                        <span class="px-2 py-1 text-xs font-bold rounded bg-yellow-100 text-yellow-800">
                            {{ $evento->estado }}
                        </span>
                    </div>
                </div>

                <form action="{{ route('eventos_insumos.store', $evento->id) }}" method="POST" id="form-insumos">
                    @csrf

                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-700">Insumos Aplicados en la Labor</h3>
                        <button type="button" onclick="agregarInsumo()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-semibold flex items-center gap-1">
                            ➕ Añadir Insumo
                        </button>
                    </div>

                    <div id="contenedor-insumos" class="space-y-6">
                        </div>

                    <div class="mt-8 pt-6 border-t flex justify-end gap-4">
                        <a href="{{ route('eventos_campo.show', $evento->id) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white font-bold rounded-md hover:bg-green-700 shadow">
                            Confirmar y Descontar Inventario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Inyectamos el catálogo de insumos y sus lotes de inventario desde Laravel
        const catalogoInsumos = @json($insumosConLotes);
        let contadorInsumos = 0;

        // Agregar una nueva estructura de Insumo al contenedor
        function agregarInsumo() {
            const contenedor = document.getElementById('contenedor-insumos');
            const idInsumo = contadorInsumos;
            
            let opcionesInsumos = `<option value="">-- Seleccione un Insumo --</option>`;
            catalogoInsumos.forEach(insumo => {
                opcionesInsumos += `<option value="${insumo.id}">${insumo.nombre}</option>`;
            });

            const htmlInsumo = `
                <div class="p-5 border border-gray-200 rounded-xl bg-gray-50 shadow-sm relative" id="bloque-insumo-${idInsumo}">
                    <button type="button" onclick="eliminarBloque('bloque-insumo-${idInsumo}')" class="absolute top-4 right-4 text-red-500 hover:text-red-700 font-bold text-sm">
                        ✕ Eliminar Insumo
                    </button>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Insumo *</label>
                            <select name="insumos[${idInsumo}][insumo_id]" class="w-full border-gray-300 rounded-md shadow-sm text-sm" required onchange="cargarLotesInsumo(this, ${idInsumo})">
                                ${opcionesInsumos}
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Método Aplicación *</label>
                            <select name="insumos[${idInsumo}][metodo_aplicacion]" class="w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                                <option value="terrestre">Terrestre</option>
                                <option value="foliar">Foliar</option>
                                <option value="dron">Dron</option>
                                <option value="fertirriego">Fertirriego</option>
                                <option value="drench">Drench</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Unidad Medida *</label>
                            <select name="insumos[${idInsumo}][unidad_medida]" class="w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                                <option value="kg">Kilogramos (kg)</option>
                                <option value="litros">Litros</option>
                                <option value="unidades">Unidades</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Área Aplicada (Hectáreas)</label>
                            <input type="number" step="0.01" name="insumos[${idInsumo}][area_aplicada]" class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Ej: 2.5">
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-white border border-dashed rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-blue-700 uppercase">Salida de Lotes de Inventario (Kardex)</span>
                            <button type="button" onclick="agregarLoteFila(${idInsumo})" class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 font-semibold">
                                + Asignar Lote Físico
                            </button>
                        </div>
                        <div id="contenedor-lotes-${idInsumo}" class="space-y-2">
                            <p class="text-xs text-gray-400 italic text-center py-2 instruction-lote">Selecciona primero un insumo para ver sus lotes disponibles.</p>
                        </div>
                    </div>
                </div>
            `;
            
            contenedor.insertAdjacentHTML('beforeend', htmlInsumo);
            contadorInsumos++;
        }

        // Carga y filtra los lotes correspondientes al insumo seleccionado
        function cargarLotesInsumo(selectElement, idInsumo) {
            const insumoId = selectElement.value;
            const contenedorLotes = document.getElementById(`contenedor-lotes-${idInsumo}`);
            contenedorLotes.innerHTML = ''; // Limpiar campo

            if (!insumoId) {
                contenedorLotes.innerHTML = '<p class="text-xs text-gray-400 italic text-center py-2">Selecciona primero un insumo para ver sus lotes disponibles.</p>';
                return;
            }

            const insumoSeleccionado = catalogoInsumos.find(i => i.id == insumoId);
            
            if (!insumoSeleccionado || insumoSeleccionado.lotes_insumos.length === 0) {
                contenedorLotes.innerHTML = '<p class="text-xs text-red-500 italic text-center py-2">⚠️ No hay lotes de este insumo con stock en el almacén.</p>';
                return;
            }

            // Al seleccionar el insumo, añadimos automáticamente la primera fila de lote para agilizar el flujo
            agregarLoteFila(idInsumo, insumoSeleccionado.lotes_insumos);
        }

        // Añadir una sub-fila de asignación de lote físico
        function agregarLoteFila(idInsumo, lotesDisponibles = null) {
            const contenedorLotes = document.getElementById(`contenedor-lotes-${idInsumo}`);
            
            // Remover texto instructivo si existe
            const instruccion = contenedorLotes.querySelector('.instruction-lote');
            if (instruccion) instruccion.remove();

            if (!lotesDisponibles) {
                const selectInsumoId = document.getElementsByName(`insumos[${idInsumo}][insumo_id]`)[0].value;
                const insumo = catalogoInsumos.find(i => i.id == selectInsumoId);
                lotesDisponibles = insumo ? insumo.lotes_insumos : [];
            }

            if (lotesDisponibles.length === 0) return;

            let opcionesLotes = '';
            lotesDisponibles.forEach(l => {
                opcionesLotes += `<option value="${l.id}" data-stock="${l.stock}">Lote: ${l.codigo_lote} (Disponible: ${l.stock})</option>`;
            });

            const idLoteFila = Date.now() + Math.floor(Math.random() * 100);
            const htmlLote = `
                <div class="flex items-center gap-4 bg-gray-50 p-2 rounded border" id="fila-lote-${idLoteFila}">
                    <div class="flex-1">
                        <select name="insumos[${idInsumo}][lotes][${idLoteFila}][lote_insumo_id]" class="w-full border-gray-300 rounded-md text-xs shadow-sm" required onchange="validarStockMaximo(this)">
                            ${opcionesLotes}
                        </select>
                    </div>
                    <div class="w-1/3">
                        <input type="number" step="0.01" min="0.01" name="insumos[${idInsumo}][lotes][${idLoteFila}][amount]" 
                               placeholder="Cantidad a usar" class="w-full border-gray-300 rounded-md text-xs shadow-sm cantidad-lote-input" required oninput="validarStockMaximo(this)">
                    </div>
                    <button type="button" onclick="eliminarBloque('fila-lote-${idLoteFila}')" class="text-red-500 hover:text-red-700 text-sm font-bold">
                        🗑️
                    </button>
                </div>
            `;
            contenedorLotes.insertAdjacentHTML('beforeend', htmlLote);
        }

        // Validar en tiempo real que el operario no digite más de lo que posee el lote en el Kardex
        function validarStockMaximo(element) {
            const fila = element.closest('.flex');
            const selectLote = fila.querySelector('select');
            const inputCantidad = fila.querySelector('.cantidad-lote-input');
            
            if(!selectLote.value || !inputCantidad.value) return;

            const opcionSeleccionada = selectLote.options[selectLote.selectedIndex];
            const stockDisponible = parseFloat(opcionSeleccionada.getAttribute('data-stock'));
            const cantidadIngresada = parseFloat(inputCantidad.value);

            if (cantidadIngresada > stockDisponible) {
                inputCantidad.classList.add('border-red-500', 'bg-red-50');
                inputCantidad.setCustomValidity(`¡Error! El stock máximo de este lote es de ${stockDisponible}`);
                inputCantidad.reportValidity();
            } else {
                inputCantidad.classList.remove('border-red-500', 'bg-red-50');
                inputCantidad.setCustomValidity('');
            }
        }

        function eliminarBloque(id) {
            document.getElementById(id).remove();
        }

        // Inicializar con una fila vacía al cargar la vista
        document.addEventListener("DOMContentLoaded", function() {
            agregarInsumo();
        });
    </script>
</x-app-layout>