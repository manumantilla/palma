<x-app-layout>
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-2xl font-bold text-agri-green flex items-center gap-2">
            <i class="fas fa-user-cog text-3xl"></i>
            Registro de Mano de Obra
        </h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('mano-obra.create') }}" class="btn-agri px-5 py-2 rounded-lg shadow flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Nuevo Registro
            </a>
            <button id="btnLiquidar" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2 rounded-lg shadow flex items-center gap-2">
                <i class="fas fa-coins"></i> Liquidar Semana
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card-agri p-5">
        <form method="GET" action="{{ route('mano-obra.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre o cédula..." class="input-agri" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Estado de Pago</label>
                <select name="estado_pago" class="input-agri">
                    <option value="">Todos</option>
                    <option value="pendiente" {{ request('estado_pago') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="pagado" {{ request('estado_pago') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tipo de Labor</label>
                <select name="tipo_labor" class="input-agri">
                    <option value="">Todos</option>
                    <option value="jornal_dia_completo" {{ request('tipo_labor') == 'jornal_dia_completo' ? 'selected' : '' }}>Jornal día completo</option>
                    <option value="jornal_medio_dia" {{ request('tipo_labor') == 'jornal_medio_dia' ? 'selected' : '' }}>Jornal medio día</option>
                    <option value="hora_extra" {{ request('tipo_labor') == 'hora_extra' ? 'selected' : '' }}>Hora extra</option>
                    <option value="destajo" {{ request('tipo_labor') == 'destajo' ? 'selected' : '' }}>Destajo</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Ciclo Productivo</label>
                <select name="ciclo_productivo_id" class="input-agri">
                    <option value="">Todos</option>
                    @foreach($ciclos as $ciclo)
                        <option value="{{ $ciclo->id }}" {{ request('ciclo_productivo_id') == $ciclo->id ? 'selected' : '' }}>{{ $ciclo->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2 flex gap-2 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="input-agri" />
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Fecha Fin</label>
                    <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="input-agri" />
                </div>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-agri px-5 py-2 rounded-lg shadow">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="{{ route('mano-obra.index') }}" class="btn-outline-agri px-4 py-2 rounded-lg shadow">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla -->
    <div class="card-agri overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-green-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Trabajador</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cédula</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Labor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cantidad</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vlr Unitario</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado Pago</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($registros as $registro)
                        <tr class="hover:bg-green-50 transition">
                            <td class="px-4 py-3 text-sm">{{ $registro->id }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                {{ $registro->nombre_trabajador ?? ($registro->trabajador ? $registro->trabajador->nombre . ' ' . $registro->trabajador->apellido : 'N/A') }}
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $registro->cedula }}</td>
                            <td class="px-4 py-3 text-sm capitalize">{{ str_replace('_', ' ', $registro->tipo_labor) }}</td>
                            <td class="px-4 py-3 text-sm">{{ $registro->cantidad }} {{ $registro->unidad_destajo ?? '' }}</td>
                            <td class="px-4 py-3 text-sm">${{ number_format($registro->valor_unitario, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm font-bold text-agri-green">${{ number_format($registro->costo_total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="badge-estado {{ $registro->estado_pago == 'pendiente' ? 'badge-pendiente' : 'badge-pagado' }}">
                                    {{ ucfirst($registro->estado_pago) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('mano-obra.show', $registro) }}" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($registro->estado_pago != 'pagado')
                                        <a href="{{ route('mano-obra.edit', $registro) }}" class="text-amber-600 hover:text-amber-800">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('mano-obra.destroy', $registro) }}" method="POST" class="inline delete-form">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('¿Eliminar este registro?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-3xl block mb-2"></i>
                                No hay registros de mano de obra.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $registros->links() }}
        </div>
    </div>
</div>

<!-- Modal Liquidación -->
<div id="modalLiquidacion" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 transform transition-all">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-agri-green flex items-center gap-2">
                <i class="fas fa-hand-holding-usd"></i> Liquidar Semana
            </h3>
            <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('mano-obra.liquidar') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cédula del Trabajador *</label>
                    <input type="text" name="cedula" required placeholder="Ej: 12345678" class="input-agri" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de Pago *</label>
                    <input type="date" name="fecha_pago" required value="{{ now()->toDateString() }}" class="input-agri" />
                </div>
                <div class="pt-2">
                    <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 rounded-lg shadow transition">
                        <i class="fas fa-check-circle mr-2"></i> Liquidar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('btnLiquidar').addEventListener('click', function() {
        document.getElementById('modalLiquidacion').classList.remove('hidden');
    });
    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('modalLiquidacion').classList.add('hidden');
    });
    window.addEventListener('click', function(e) {
        if (e.target === document.getElementById('modalLiquidacion')) {
            document.getElementById('modalLiquidacion').classList.add('hidden');
        }
    });
</script>
</x-app-layout>