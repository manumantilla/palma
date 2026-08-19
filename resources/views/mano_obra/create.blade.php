<x-app-layout>
<div class="card-agri p-6 max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold text-agri-green mb-6 flex items-center gap-2">
        <i class="fas fa-user-plus text-3xl"></i>
        Nuevo Registro de Mano de Obra
    </h2>

    <form action="{{ route('mano-obra.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Trabajador existente -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Trabajador (opcional)</label>
                <select name="trabajador_id" id="trabajador_id" class="input-agri">
                    <option value="">Seleccionar trabajador registrado</option>
                    @foreach($trabajadores as $t)
                        <option value="{{ $t->id }}" {{ old('trabajador_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->nombre }} {{ $t->apellido }} - {{ $t->cedula }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre del trabajador *</label>
                <input type="text" name="nombre_trabajador" value="{{ old('nombre_trabajador') }}" placeholder="Nombre completo" class="input-agri" required />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Cédula *</label>
                <input type="text" name="cedula" value="{{ old('cedula') }}" placeholder="Número de cédula" class="input-agri" required />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tipo de Labor *</label>
                <select name="tipo_labor" id="tipo_labor" class="input-agri" required>
                    <option value="">Seleccionar</option>
                    <option value="jornal_dia_completo" {{ old('tipo_labor') == 'jornal_dia_completo' ? 'selected' : '' }}>Jornal día completo</option>
                    <option value="jornal_medio_dia" {{ old('tipo_labor') == 'jornal_medio_dia' ? 'selected' : '' }}>Jornal medio día</option>
                    <option value="hora_extra" {{ old('tipo_labor') == 'hora_extra' ? 'selected' : '' }}>Hora extra</option>
                    <option value="destajo" {{ old('tipo_labor') == 'destajo' ? 'selected' : '' }}>Destajo</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Cantidad *</label>
                <input type="number" step="0.1" name="cantidad" value="{{ old('cantidad') }}" placeholder="Ej: 8" class="input-agri" required />
            </div>
            <div id="unidad_destajo_container" style="{{ old('tipo_labor') == 'destajo' ? '' : 'display:none' }}">
                <label class="block text-sm font-medium text-gray-700">Unidad de Destajo *</label>
                <input type="text" name="unidad_destajo" value="{{ old('unidad_destajo') }}" placeholder="Ej: cajas, kg, etc." class="input-agri" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Valor Unitario *</label>
                <input type="number" step="0.01" name="valor_unitario" value="{{ old('valor_unitario') }}" placeholder="0.00" class="input-agri" required />
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Evento de Campo (opcional)</label>
                <select name="evento_campo_id" class="input-agri">
                    <option value="">Sin evento</option>
                    @foreach($eventos as $ev)
                        <option value="{{ $ev->id }}" {{ old('evento_campo_id') == $ev->id ? 'selected' : '' }}>
                            ID: {{ $ev->id }} - {{ $ev->fecha_programada }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Sesión de Cosecha (opcional)</label>
                <select name="sesion_id" class="input-agri">
                    <option value="">Sin sesión</option>
                    @foreach($sesiones as $ses)
                        <option value="{{ $ses->id }}" {{ old('sesion_id') == $ses->id ? 'selected' : '' }}>
                            {{ $ses->nombre ?? 'Sesión #'.$ses->id }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Observaciones</label>
                <textarea name="observaciones" rows="2" class="input-agri">{{ old('observaciones') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('mano-obra.index') }}" class="btn-outline-agri px-5 py-2 rounded-lg">
                Cancelar
            </a>
            <button type="submit" class="btn-agri px-6 py-2 rounded-lg shadow">
                <i class="fas fa-save mr-2"></i> Guardar
            </button>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoLabor = document.getElementById('tipo_labor');
        const unidadContainer = document.getElementById('unidad_destajo_container');

        function toggleUnidad() {
            if (tipoLabor.value === 'destajo') {
                unidadContainer.style.display = 'block';
            } else {
                unidadContainer.style.display = 'none';
            }
        }

        tipoLabor.addEventListener('change', toggleUnidad);
        // Inicializar
        toggleUnidad();

        // Auto completar nombre y cédula al seleccionar trabajador
        const trabajadorSelect = document.getElementById('trabajador_id');
        const nombreInput = document.querySelector('input[name="nombre_trabajador"]');
        const cedulaInput = document.querySelector('input[name="cedula"]');

        trabajadorSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (this.value) {
                // Obtener nombre y cédula del texto del option (formato: "Nombre Apellido - Cedula")
                const text = selected.text;
                const parts = text.split(' - ');
                if (parts.length === 2) {
                    nombreInput.value = parts[0];
                    cedulaInput.value = parts[1];
                }
            } else {
                // Si se selecciona "Seleccionar trabajador registrado", no limpiar campos manuales
            }
        });
    });
</script>
</x-app-layout>