<div id="modalGastoPolimorfico" class="fixed inset-0 bg-slate-900 bg-opacity-50 flex items-center justify-center hidden z-50 transition-opacity duration-300">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative">
        
        <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-2">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Registrar Egreso / Gasto</h3>
                <p class="text-xs text-gray-500">Afecta a: <span id="gasto_origen_texto" class="font-bold text-green-600"></span></p>
            </div>
            <button type="button" onclick="cerrarModalGasto()" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
        </div>

        <form action="{{ route('gastos.store') }}" method="POST">
            @csrf
            
            <input type="hidden" name="gastable_type" id="poly_type">
            <input type="hidden" name="gastable_id" id="poly_id">

            <div class="mb-3">
                <label for="gasto_concepto" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Concepto / Motivo *</label>
                <input type="text" name="concepto" id="gasto_concepto" placeholder="Ej: Compra de Fertilizante Urea, Pago Jornal"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm" required>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label for="gasto_monto" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Monto ($ COP) *</label>
                    <input type="number" step="0.01" name="monto" id="gasto_monto" placeholder="0.00"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm" required>
                </div>
                <div>
                    <label for="gasto_fecha" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Fecha *</label>
                    <input type="date" name="fecha" id="gasto_fecha" value="{{ date('Y-m-d') }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label for="gasto_categoria" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Categoría *</label>
                    <select name="categoria" id="gasto_categoria" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm" required>
                        <option value="otros">Otros</option>
                        <option value="insumos">Insumos</option>
                        <option value="mano_obra">Mano de Obra</option>
                        <option value="maquinaria">Maquinaria</option>
                        <option value="transporte">Transporte</option>
                        <option value="mantenimiento">Mantenimiento</option>
                        <option value="servicios_publicos">Servicios Públicos</option>
                        <option value="arriendos">Arriendos</option>
                        <option value="administrativos">Administrativos</option>
                    </select>
                </div>
                <div>
                    <label for="gasto_metodo" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Método de Pago</label>
                    <select name="metodo_pago" id="gasto_metodo" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="tarjeta">Tarjeta Bancaria</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="gasto_ciclo" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Asociar a Ciclo Productivo (Opcional)</label>
                <select name="ciclo_producto_id" id="gasto_ciclo" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
                    <option value="">-- No aplica / Gasto administrativo general --</option>
                    @if(isset($ciclosGlobales)) 
                        @foreach($ciclosGlobales as $ciclo)
                            <option value="{{ $ciclo->id }}">{{ $ciclo->nombre_ciclo ?? 'Ciclo #'.$ciclo->id }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mb-5">
                <label for="gasto_desc" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Observaciones / Detalles</label>
                <textarea name="descripcion" id="gasto_desc" rows="2" placeholder="Detalles adicionales del pago..." 
                          class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm"></textarea>
            </div>

            <div class="flex justify-end space-x-2 border-t border-gray-100 pt-3">
                <button type="button" onclick="cerrarModalGasto()" 
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-medium transition">
                    Cancelar
                </button>
                <button type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium shadow transition">
                    Guardar Gasto
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalGasto(modelType, modelId, entityName) {
        // Asignar valores ocultos de la estructura polimórfica
        document.getElementById('poly_type').value = modelType;
        document.getElementById('poly_id').value = modelId;
        
        // Cambiar dinámicamente el texto informativo del header
        document.getElementById('gasto_origen_texto').innerText = entityName;
        
        // Mostrar el modal removiendo el 'hidden'
        document.getElementById('modalGastoPolimorfico').classList.remove('hidden');
    }

    function cerrarModalGasto() {
        // Ocultar modal
        document.getElementById('modalGastoPolimorfico').classList.add('hidden');
        
        // Limpiar inputs clave por seguridad y para evitar duplicaciones cruzadas
        document.getElementById('gasto_concepto').value = '';
        document.getElementById('gasto_monto').value = '';
        document.getElementById('gasto_desc').value = '';
    }
</script>


<!--Button for use in each view-->
<button type="button" 
        onclick="abrirModalGasto('App\\Models\\Trabajador', {{ $trabajador->id }}, '{{ $trabajador->nombres }} {{ $trabajador->apellidos }}')"
        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-semibold shadow">
    + Cargar Gasto/Anticipo
</button>