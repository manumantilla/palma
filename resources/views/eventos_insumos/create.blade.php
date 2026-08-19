<x-app-layout>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="consumoInsumosApp()">

    <!-- Header / Banner Agrícola -->
    <div class="mb-8 bg-gradient-to-r from-emerald-800 to-teal-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 text-emerald-200 text-sm font-medium mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Evento de Campo: <strong class="text-white">{{ $evento->tipoEvento->nombre ?? 'N/A' }}</strong></span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Aplicación y Salida de Insumos (Kardex) 🌱</h1>
                <p class="text-emerald-100 text-sm mt-1">Asigna los lotes de inventario correspondientes y calcula el costo operativo de la labor.</p>
            </div>
            
            <!-- Resumen Total General Flotante -->
            <div class="bg-emerald-950/60 backdrop-blur border border-emerald-500/30 rounded-xl p-4 text-right min-w-[200px]">
                <span class="block text-xs text-emerald-300 font-medium uppercase tracking-wider">Costo Estimado Evento</span>
                <span class="text-2xl font-black text-emerald-300" x-text="formatCurrency(costoTotalGeneral)">$0.00</span>
            </div>
        </div>
    </div>

    <!-- Errores de Validación -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-sm font-bold text-red-800">Se encontraron errores en la solicitud:</h3>
            </div>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('eventos_campo.insumos.store', $evento->id) }}" method="POST">
        @csrf

        <div class="space-y-6">
            
            <!-- Contenedor Dinámico de Insumos -->
            <template x-for="(insumoRow, iIndex) in insumosAgregados" :key="iIndex">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden transition-all duration-200 hover:shadow-md">
                    
                    <!-- Cabeza del Insumo -->
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center space-x-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold text-sm" x-text="iIndex + 1"></span>
                            <h2 class="text-base font-bold text-slate-800" x-text="insumoRow.insumo_id ? getInsumoNombre(insumoRow.insumo_id) : 'Nuevo Insumo a Aplicar'"></h2>
                        </div>
                        <button type="button" @click="removeInsumo(iIndex)" class="inline-flex items-center text-xs font-semibold text-red-600 hover:text-red-800 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Quitar Insumo
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Campos Principales del Insumo -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            
                            <!-- Seleccionar Insumo -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Insumo Catálogo *</label>
                                <select :name="`insumos[${iIndex}][insumo_id]`" 
                                        x-model="insumoRow.insumo_id" 
                                        @change="onInsumoChange(iIndex)" 
                                        class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm shadow-sm" required>
                                    <option value="">-- Seleccionar Insumo --</option>
                                    <template x-for="item in catálogoInsumos" :key="item.id">
                                        <option :value="item.id" x-text="item.nombre"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Método de Aplicación -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Método Aplicación *</label>
                                <select :name="`insumos[${iIndex}][metodo_aplicacion]`" 
                                        x-model="insumoRow.metodo_aplicacion" 
                                        class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm shadow-sm" required>
                                    <option value="terrestre">Terrestre</option>
                                    <option value="foliar">Foliar</option>
                                    <option value="dron">Dron</option>
                                    <option value="fertirriego">Fertirriego</option>
                                    <option value="drench">Drench</option>
                                </select>
                            </div>

                            <!-- Unidad de Medida -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Unidad Medida *</label>
                                <select :name="`insumos[${iIndex}][unidad_medida]`" 
                                        x-model="insumoRow.unidad_medida" 
                                        class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm shadow-sm" required>
                                    <option value="kg">Kilogramos (kg)</option>
                                    <option value="litros">Litros (L)</option>
                                    <option value="unidades">Unidades (Und)</option>
                                </select>
                            </div>

                            <!-- Área Aplicada -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Área Aplicada (Ha)</label>
                                <input type="number" step="0.01" min="0" 
                                       :name="`insumos[${iIndex}][area_aplicada]`" 
                                       x-model="insumoRow.area_aplicada" 
                                       placeholder="Ej: 2.5" 
                                       class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm shadow-sm">
                            </div>

                            <!-- Observaciones -->
                            <div class="md:col-span-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Observaciones</label>
                                <input type="text" 
                                       :name="`insumos[${iIndex}][observaciones]`" 
                                       x-model="insumoRow.observaciones" 
                                       placeholder="Notas sobre dosis, clima o mezcla..." 
                                       class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm shadow-sm">
                            </div>
                        </div>

                        <!-- Sección de Selección de Lotes (Kardex) -->
                        <div class="border-t border-slate-100 pt-5">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    Desglose de Lotes Usados (Kardex)
                                </h3>
                                <button type="button" @click="addLote(iIndex)" 
                                        :disabled="!insumoRow.insumo_id || getLotesDisponibles(insumoRow.insumo_id).length === 0"
                                        class="inline-flex items-center text-xs font-bold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg disabled:opacity-50 transition-colors">
                                    + Agregar Lote
                                </button>
                            </div>

                            <!-- Tabla de Lotes -->
                            <div class="overflow-x-auto rounded-xl border border-slate-200">
                                <table class="w-full text-left border-collapse text-sm">
                                    <thead>
                                        <tr class="bg-slate-100 text-slate-600 text-xs uppercase font-bold border-b border-slate-200">
                                            <th class="py-2.5 px-4">Lote / Código</th>
                                            <th class="py-2.5 px-4">Stock Disponible</th>
                                            <th class="py-2.5 px-4">Costo Unit.</th>
                                            <th class="py-2.5 px-4 w-40">Cantidad Extraer *</th>
                                            <th class="py-2.5 px-4 text-right">Subtotal</th>
                                            <th class="py-2.5 px-4 text-center w-12"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <template x-for="(loteRow, lIndex) in insumoRow.lotes" :key="lIndex">
                                            <tr class="hover:bg-slate-50/80">
                                                <!-- Dropdown Lotes -->
                                                <td class="py-2.5 px-4">
                                                    <select :name="`insumos[${iIndex}][lotes][${lIndex}][lote_insumo_id]`" 
                                                            x-model="loteRow.lote_insumo_id" 
                                                            @change="onLoteChange(iIndex, lIndex)" 
                                                            class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-xs shadow-sm" required>
                                                        <option value="">-- Lote --</option>
                                                        <template x-for="lote in getLotesDisponibles(insumoRow.insumo_id)" :key="lote.id">
                                                            <option :value="lote.id" x-text="`${lote.codigo_lote} (Disp: ${lote.cantidad_actual})`"></option>
                                                        </template>
                                                    </select>
                                                </td>

                                                <!-- Stock Disponible -->
                                                <td class="py-2.5 px-4 text-slate-600 text-xs font-semibold">
                                                    <span x-text="loteRow.stock_max ? loteRow.stock_max : '-'"></span>
                                                </td>

                                                <!-- Costo Unitario -->
                                                <td class="py-2.5 px-4 text-slate-600 text-xs">
                                                    <span x-text="loteRow.costo_unitario ? formatCurrency(loteRow.costo_unitario) : '$0.00'"></span>
                                                </td>

                                                <!-- Cantidad Extraer -->
                                                <td class="py-2.5 px-4">
                                                    <input type="number" step="0.01" min="0.01" :max="loteRow.stock_max"
                                                           :name="`insumos[${iIndex}][lotes][${lIndex}][cantidad]`" 
                                                           x-model.number="loteRow.cantidad" 
                                                           placeholder="0.00" 
                                                           class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-xs shadow-sm" required>
                                                </td>

                                                <!-- Subtotal calculado del lote -->
                                                <td class="py-2.5 px-4 text-right font-bold text-slate-700 text-xs">
                                                    <span x-text="formatCurrency((loteRow.cantidad || 0) * (loteRow.costo_unitario || 0))"></span>
                                                </td>

                                                <!-- Eliminar Fila Lote -->
                                                <td class="py-2.5 px-4 text-center">
                                                    <button type="button" @click="removeLote(iIndex, lIndex)" class="text-slate-400 hover:text-red-500 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>

                                        <!-- Estado Vacío Lotes -->
                                        <tr x-show="insumoRow.lotes.length === 0">
                                            <td colspan="6" class="py-4 text-center text-xs text-slate-400 italic">
                                                Selecciona un insumo y presiona "+ Agregar Lote" para extraer stock.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Totales por Insumo -->
                            <div class="mt-3 flex justify-end space-x-6 text-xs font-bold text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <div>Total Cantidad Insumo: <span class="text-emerald-700 font-extrabold" x-text="calcularTotalCantidadInsumo(insumoRow)"></span></div>
                                <div>Subtotal Insumo: <span class="text-emerald-700 font-extrabold" x-text="formatCurrency(calcularSubtotalInsumo(insumoRow))"></span></div>
                            </div>
                        </div>

                    </div>
                </div>
            </template>

            <!-- Botón Agregar Nuevo Insumo -->
            <div class="text-center py-4">
                <button type="button" @click="addInsumo()" class="inline-flex items-center px-5 py-2.5 border-2 border-dashed border-emerald-600 text-emerald-800 hover:bg-emerald-50 rounded-2xl font-bold text-sm transition-colors shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Añadir Otro Insumo a la Labor
                </button>
            </div>

        </div>

        <!-- Botones Acción Final -->
        <div class="mt-8 flex items-center justify-end space-x-4 border-t border-slate-200 pt-6">
            <a href="{{ route('eventos_campo.show', $evento->id) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-100 text-sm transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-lg shadow-emerald-600/30 transition-all flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Guardar y Descontar Kardex
            </button>
        </div>
    </form>
</div>

<!-- Lógica interactiva Alpine.js -->
<script>
    function consumoInsumosApp() {
        return {
            catálogoInsumos: @json($insumosConLotes),
            insumosAgregados: [],

            init() {
                // Iniciar con un item de insumo por defecto
                this.addInsumo();
            },

            addInsumo() {
                this.insumosAgregados.push({
                    insumo_id: '',
                    metodo_aplicacion: 'terrestre',
                    unidad_medida: 'kg',
                    area_aplicada: '',
                    observaciones: '',
                    lotes: []
                });
            },

            removeInsumo(index) {
                this.insumosAgregados.splice(index, 1);
            },

            onInsumoChange(iIndex) {
                // Al cambiar el insumo resetear sus lotes asociados
                this.insumosAgregados[iIndex].lotes = [];
                // Agregar automáticamente el primer lote disponible si existe
                this.addLote(iIndex);
            },

            getInsumoNombre(insumoId) {
                const found = this.catálogoInsumos.find(i => i.id == insumoId);
                return found ? found.nombre : 'Insumo';
            },

            getLotesDisponibles(insumoId) {
                if (!insumoId) return [];
                const insumo = this.catálogoInsumos.find(i => i.id == insumoId);
                return insumo && insumo.lotes ? insumo.lotes : [];
            },

            addLote(iIndex) {
                const insumoId = this.insumosAgregados[iIndex].insumo_id;
                const lotesDisp = this.getLotesDisponibles(insumoId);
                
                if (lotesDisp.length === 0) return;

                this.insumosAgregados[iIndex].lotes.push({
                    lote_insumo_id: '',
                    cantidad: '',
                    costo_unitario: 0,
                    stock_max: 0
                });
            },

            removeLote(iIndex, lIndex) {
                this.insumosAgregados[iIndex].lotes.splice(lIndex, 1);
            },

            onLoteChange(iIndex, lIndex) {
                const loteId = this.insumosAgregados[iIndex].lotes[lIndex].lote_insumo_id;
                const insumoId = this.insumosAgregados[iIndex].insumo_id;
                const lotesDisp = this.getLotesDisponibles(insumoId);
                
                const loteEncontrado = lotesDisp.find(l => l.id == loteId);

                if (loteEncontrado) {
                    // Mapeo con el campo corregido `cantidad_actual`
                    this.insumosAgregados[iIndex].lotes[lIndex].costo_unitario = parseFloat(loteEncontrado.costo_unitario) || 0;
                    this.insumosAgregados[iIndex].lotes[lIndex].stock_max = parseFloat(loteEncontrado.cantidad_actual) || 0;
                } else {
                    this.insumosAgregados[iIndex].lotes[lIndex].costo_unitario = 0;
                    this.insumosAgregados[iIndex].lotes[lIndex].stock_max = 0;
                }
            },

            calcularSubtotalInsumo(insumoRow) {
                return insumoRow.lotes.reduce((sum, l) => {
                    return sum + ((parseFloat(l.cantidad) || 0) * (parseFloat(l.costo_unitario) || 0));
                }, 0);
            },

            calcularTotalCantidadInsumo(insumoRow) {
                const total = insumoRow.lotes.reduce((sum, l) => sum + (parseFloat(l.cantidad) || 0), 0);
                return total.toFixed(2);
            },

            get costoTotalGeneral() {
                return this.insumosAgregados.reduce((sum, insumoRow) => {
                    return sum + this.calcularSubtotalInsumo(insumoRow);
                }, 0);
            },

            formatCurrency(amount) {
                return new Intl.NumberFormat('es-CO', {
                    style: 'currency',
                    currency: 'COP',
                    minimumFractionDigits: 2
                }).format(amount || 0);
            }
        }
    }
</script>
</x-app-layout>