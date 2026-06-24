@php
    // Detectamos si es edición o creación
    $isEdit = isset($insumo);
    $route = $isEdit ? route('insumos.update', $insumo->id) : route('insumos.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8" x-data="insumoForm()">
    <form action="{{ $route }}" method="POST" class="space-y-8">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="bg-white shadow sm:rounded-lg p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-3 mb-6">
                <i class="fas fa-flask text-green-600 mr-2"></i> Información Básica del Insumo
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre Comercial *</label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $insumo->nombre ?? '') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm @error('nombre') border-red-500 @enderror">
                    @error('nombre') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="categoria_id" class="block text-sm font-medium text-gray-700">Categoría de Insumo *</label>
                    <select name="categoria_id" id="categoria_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        <option value="">Seleccione una categoría...</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_id', $insumo->categoria_id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="proveedor_id" class="block text-sm font-medium text-gray-700">Proveedor Habitual</label>
                    <select name="proveedor_id" id="proveedor_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        <option value="">Ninguno / Varios</option>
                        @foreach($proveedores as $prov)
                            <option value="{{ $prov->id }}" {{ old('proveedor_id', $insumo->proveedor_id ?? '') == $prov->id ? 'selected' : '' }}>
                                {{ $prov->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="ingrediente_principal" class="block text-sm font-medium text-gray-700">Ingrediente Activo / Principal</label>
                    <input type="text" name="ingrediente_principal" id="ingrediente_principal" placeholder="Ej: Glifosato, Urea, Cobre"
                        value="{{ old('ingrediente_principal', $insumo->ingrediente_principal ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                </div>

                <div>
                    <label for="unidad_base" class="block text-sm font-medium text-gray-700">Unidad de Compra/Base *</label>
                    <select name="unidad_base" id="unidad_base" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        <option value="kg" {{ old('unidad_base', $insumo->unidad_base ?? '') == 'kg' ? 'selected' : '' }}>Kilogramos (kg)</option>
                        <option value="l" {{ old('unidad_base', $insumo->unidad_base ?? '') == 'l' ? 'selected' : '' }}>Litros (l)</option>
                        <option value="unidad" {{ old('unidad_base', $insumo->unidad_base ?? 'unidad') == 'unidad' ? 'selected' : '' }}>Unidad / Empaque</option>
                    </select>
                </div>

                <div>
                    <label for="factor_conversion" class="block text-sm font-medium text-gray-700">Factor de Conversión (a gramos/cc)</label>
                    <input type="number" step="0.0001" name="factor_conversion" id="factor_conversion" 
                        value="{{ old('factor_conversion', $insumo->factor_conversion ?? '1000.0000') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">Ej: Si compras en kg y aplicas en gramos, el factor es 1000.</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-3 mb-6">
                <i class="fas fa-biohazard text-red-600 mr-2"></i> Seguridad y Parámetros Fitosanitarios
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label for="clasificacion_toxicologica" class="block text-sm font-medium text-gray-700">Clasificación OMS</label>
                    <select name="clasificacion_toxicologica" id="clasificacion_toxicologica"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        <option value="">Seleccione...</option>
                        @foreach(['Ia' => 'Ia (Extremadamente Peligroso)', 'Ib' => 'Ib (Altamente Peligroso)', 'II' => 'II (Moderadamente Peligroso)', 'III' => 'III (Ligeramente Peligroso)', 'IV' => 'IV (No presenta peligro agudo)'] as $key => $label)
                            <option value="{{ $key }}" {{ old('clasificacion_toxicologica', $insumo->clasificacion_toxicologica ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="nivel_toxicidad" class="block text-sm font-medium text-gray-700">Nivel de Toxicidad Interno</label>
                    <select name="nivel_toxicidad" id="nivel_toxicidad"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        <option value="">Seleccione...</option>
                        <option value="bajo" {{ old('nivel_toxicidad', $insumo->nivel_toxicidad ?? '') == 'bajo' ? 'selected' : '' }}>Bajo</option>
                        <option value="medio" {{ old('nivel_toxicidad', $insumo->nivel_toxicidad ?? '') == 'medio' ? 'selected' : '' }}>Medio</option>
                        <option value="alto" {{ old('nivel_toxicidad', $insumo->nivel_toxicidad ?? '') == 'alto' ? 'selected' : '' }}>Alto</option>
                    </select>
                </div>

                <div>
                    <label for="franja_color" class="block text-sm font-medium text-gray-700">Color de la Franja (Etiqueta)</label>
                    <select name="franja_color" id="franja_color"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        <option value="">Seleccione...</option>
                        <option value="rojo" {{ old('franja_color', $insumo->franja_color ?? '') == 'rojo' ? 'selected' : '' }}>Rojo (Peligro)</option>
                        <option value="amarillo" {{ old('franja_color', $insumo->franja_color ?? '') == 'amarillo' ? 'selected' : '' }}>Amarillo (Advertencia)</option>
                        <option value="azul" {{ old('franja_color', $insumo->franja_color ?? '') == 'azul' ? 'selected' : '' }}>Azul (Cuidado)</option>
                        <option value="verde" {{ old('franja_color', $insumo->franja_color ?? '') == 'verde' ? 'selected' : '' }}>Verde (Seguro)</option>
                    </select>
                </div>

                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700">Estado Operativo</label>
                    <select name="estado" id="estado" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        <option value="activo" {{ old('estado', $insumo->estado ?? 'activo') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado', $insumo->estado ?? '') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>

                <div>
                    <label for="rei_horas" class="block text-sm font-medium text-gray-700">Periodo Reentrada (REI - Horas)</label>
                    <input type="number" name="rei_horas" id="rei_horas" placeholder="Ej: 24" value="{{ old('rei_horas', $insumo->rei_horas ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                </div>

                <div>
                    <label for="phi_dias" class="block text-sm font-medium text-gray-700">Periodo Carencia (PHI - Días)</label>
                    <input type="number" name="phi_dias" id="phi_dias" placeholder="Ej: 15" value="{{ old('phi_dias', $insumo->phi_dias ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                </div>

                <div class="md:col-span-2">
                    <label for="equipo_proteccion" class="block text-sm font-medium text-gray-700">Equipo de Protección Obligatorio (EPI)</label>
                    <input type="text" name="equipo_proteccion" id="equipo_proteccion" placeholder="Ej: Respirador con filtro, Guantes de nitrilo, Careta"
                        value="{{ old('equipo_proteccion', $insumo->equipo_proteccion ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-3 mb-6">
                <i class="fas fa-warehouse text-blue-600 mr-2"></i> Bodega, Almacenamiento y Alertas
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-4">
                <div>
                    <label for="almacenamiento_temp_min" class="block text-sm font-medium text-gray-700">Temp. Mínima (°C)</label>
                    <input type="text" name="almacenamiento_temp_min" id="almacenamiento_temp_min" placeholder="15" value="{{ old('almacenamiento_temp_min', $insumo->almacenamiento_temp_min ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                </div>

                <div>
                    <label for="almacenamiento_temp_max" class="block text-sm font-medium text-gray-700">Temp. Máxima (°C)</label>
                    <input type="text" name="almacenamiento_temp_max" id="almacenamiento_temp_max" placeholder="30" value="{{ old('almacenamiento_temp_max', $insumo->almacenamiento_temp_max ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                </div>

                <div>
                    <label for="almacenamiento_humedad" class="block text-sm font-medium text-gray-700">Humedad Max Recomendada</label>
                    <input type="text" name="almacenamiento_humedad" id="almacenamiento_humedad" placeholder="Max 65%" value="{{ old('almacenamiento_humedad', $insumo->almacenamiento_humedad ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                </div>

                <div>
                    <label for="dias_aviso_vencimiento" class="block text-sm font-medium text-gray-700">Anticipación Alerta Vencimiento</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <input type="number" name="dias_aviso_vencimiento" id="dias_aviso_vencimiento" value="{{ old('dias_aviso_vencimiento', $insumo->dias_aviso_vencimiento ?? '30') }}"
                            class="block w-full min-w-0 flex-1 rounded-none rounded-l-md border-gray-300 focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        <span class="inline-flex items-center rounded-r-md border border-l-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500">Días</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                <div>
                    <label for="stock_minimo" class="block text-sm font-medium text-gray-700">Stock Mínimo Global de Alerta</label>
                    <input type="number" step="0.01" name="stock_minimo" id="stock_minimo" value="{{ old('stock_minimo', $insumo->stock_minimo ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                </div>

                <div class="flex items-center space-x-6 h-full pt-6">
                    <div class="flex items-center">
                        <input id="requiere_refrigeracion" name="requiere_refrigeracion" type="checkbox" value="1" {{ old('requiere_refrigeracion', $insumo->requiere_refrigeracion ?? false) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <label for="requiere_refrigeracion" class="ml-2 block text-sm text-gray-900 font-medium">Requiere Cadena de Frío</label>
                    </div>

                    <div class="flex items-center">
                        <input id="sensible_luz" name="sensible_luz" type="checkbox" value="1" {{ old('sensible_luz', $insumo->sensible_luz ?? false) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <label for="sensible_luz" class="ml-2 block text-sm text-gray-900 font-medium">Sensible a la Luz (Fotosensible)</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg p-6">
            <div class="flex justify-between items-center border-b pb-3 mb-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                    <i class="fas fa-vial text-purple-600 mr-2"></i> Composición Química y Riqueza (Ficha Técnica)
                </h3>
                <button type="button" @click="addComponente()" 
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-purple-600 hover bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <i class="fas fa-plus mr-1"></i> Añadir Componente
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(comp, index) in componentes" :key="index">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-lg relative border border-gray-200">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Tipo Componente</label>
                            <select :name="`componentes[${index}][tipo_componente]` x-model="comp.tipo_componente" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-xs">
                                <option value="nutriente">Nutriente (N, P, K, Ca, etc.)</option>
                                <option value="activo">Principio Activo</option>
                                <option value="coadyuvante">Coadyuvante / Adherente</option>
                                <option value="carga">Inerte / Carga</option>
                                <option value="otros">Otros</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Nombre (Elemento/Microorganismo)</label>
                            <input type="text" :name="`componentes[${index}][componente]`" x-model="comp.componente" required placeholder="Ej: Nitrógeno Total, Trichoderma"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-xs">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Concentración</label>
                            <input type="number" step="0.0001" :name="`componentes[${index}][concentracion]`" x-model="comp.concentracion" required placeholder="Ej: 46.00"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-xs">
                        </div>

                        <div class="flex items-end space-x-2">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Unidad Riqueza</label>
                                <input type="text" :name="`componentes[${index}][unidad]`" x-model="comp.unidad" required placeholder="Ej: %, UFC/g, g/L"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-xs">
                            </div>
                            
                            <button type="button" @click="removeComponente(index)"
                                class="p-2 text-red-600 hover:bg-red-100 rounded-md transition-colors duration-150">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </template>

                <div x-show="componentes.length === 0" class="text-center py-6 text-sm text-gray-500 border-2 border-dashed border-gray-300 rounded-lg">
                    No se han registrado componentes químicos específicos para este insumo. Haz clic en "Añadir Componente" si deseas registrar su desglose técnico.
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('insumos.index') }}" 
                class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                Cancelar
            </a>
            <button type="submit" 
                class="inline-flex justify-center rounded-md border border-transparent bg-green-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                {{ $isEdit ? 'Actualizar Ficha de Insumo' : 'Guardar Nuevo Insumo' }}
            </button>
        </div>
    </form>
</div>

<script>
    function insumoForm() {
        return {
            // Inicializar con componentes existentes si estamos en modo Edición (vienen de la BD mapeados a JSON)
            // o con un array vacío si es una creación desde cero.
            componentes: @json($isEdit ? $insumo->componentes : (old('componentes') ?? [])),
            
            addComponente() {
                this.componentes.push({
                    tipo_componente: 'nutriente',
                    componente: '',
                    concentracion: '',
                    unidad: '%'
                });
            },
            removeComponente(index) {
                this.componentes.splice(index, 1);
            }
        }
    }
</script>