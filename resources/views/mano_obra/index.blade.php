<x-app-layout>
<div class="space-y-6" x-data="{ liquidar: false }">
    <!-- Encabezado -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-2xl font-bold text-agri-green">Mano de obra</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('mano-obra.create') }}" class="btn-agri px-5 py-2 rounded-lg shadow">Registrar</a>
            <button type="button" @click="liquidar = true" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2 rounded-lg shadow">
                Liquidar semana
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <!-- Filtros -->
    <div class="card-agri p-5">
        <form method="GET" action="{{ route('mano-obra.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre o documento" class="input-agri" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Estado de pago</label>
                <select name="estado_pago" class="input-agri">
                    <option value="">Todos</option>
                    <option value="pendiente" @selected(request('estado_pago') == 'pendiente')>Pendiente</option>
                    <option value="pagado" @selected(request('estado_pago') == 'pagado')>Pagado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Forma de pago</label>
                <select name="tipo_labor" class="input-agri">
                    <option value="">Todas</option>
                    @foreach(\App\Models\EventoManoObra::TIPOS_LABOR as $tipo)
                        <option value="{{ $tipo }}" @selected(request('tipo_labor') == $tipo)>{{ ucfirst(str_replace('_', ' ', $tipo)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Ciclo productivo</label>
                <select name="ciclo_productivo_id" class="input-agri">
                    <option value="">Todos</option>
                    @foreach($ciclos as $ciclo)
                        <option value="{{ $ciclo->id }}" @selected(request('ciclo_productivo_id') == $ciclo->id)>{{ $ciclo->nombre_campana }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2 flex gap-2 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Trabajado desde</label>
                    <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="input-agri" />
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="input-agri" />
                </div>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-agri px-5 py-2 rounded-lg shadow">Filtrar</button>
                <a href="{{ route('mano-obra.index') }}" class="btn-outline-agri px-4 py-2 rounded-lg shadow">Limpiar</a>
            </div>
        </form>
    </div>

    <!-- Tabla -->
    <div class="card-agri overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-green-50">
                    <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <th class="px-4 py-3">Día</th>
                        <th class="px-4 py-3">Trabajador</th>
                        <th class="px-4 py-3">Labor</th>
                        <th class="px-4 py-3">Cantidad</th>
                        <th class="px-4 py-3">Vlr unitario</th>
                        <th class="px-4 py-3 text-right">Pagado</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($registros as $registro)
                        <tr class="hover:bg-green-50 transition">
                            <td class="px-4 py-3 text-sm whitespace-nowrap">{{ $registro->fecha_labor->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="font-medium text-gray-800">{{ $registro->nombre_trabajador }}</span>
                                @if($registro->cedula) <span class="block text-xs text-gray-400">{{ $registro->cedula }}</span> @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('eventos_campo.show', $registro->evento_campo_id) }}" class="text-emerald-700 hover:underline">
                                    {{ $registro->eventoCampo->tipoEvento->nombre ?? 'Labor' }} #{{ $registro->evento_campo_id }}
                                </a>
                                <span class="block text-xs text-gray-400">{{ $registro->eventoCampo->cicloProductivo->nombre_campana ?? 'General' }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ rtrim(rtrim(number_format($registro->cantidad, 2, ',', '.'), '0'), ',') }}
                                {{ $registro->tipo_labor === 'destajo' ? $registro->unidad_destajo : str_replace('_', ' ', $registro->tipo_labor) }}
                            </td>
                            <td class="px-4 py-3 text-sm">${{ number_format($registro->valor_unitario, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-right font-bold text-agri-green whitespace-nowrap">
                                ${{ number_format($registro->costo_total, 0, ',', '.') }}
                                @if(abs($registro->ajuste) >= 1)
                                    <span class="block text-[11px] font-normal text-gray-400">
                                        {{ $registro->ajuste > 0 ? '+' : '−' }}${{ number_format(abs($registro->ajuste), 0, ',', '.') }} a mano
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge-estado {{ $registro->estado_pago == 'pendiente' ? 'badge-pendiente' : 'badge-pagado' }}">
                                    {{ ucfirst($registro->estado_pago) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-sm whitespace-nowrap">
                                <a href="{{ route('mano-obra.show', $registro) }}" class="text-blue-600 hover:text-blue-800">Ver</a>
                                @if($registro->estado_pago != 'pagado')
                                    <a href="{{ route('mano-obra.edit', $registro) }}" class="ml-2 text-amber-600 hover:text-amber-800">Editar</a>
                                    <form action="{{ route('mano-obra.destroy', $registro) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="ml-2 text-red-600 hover:text-red-800" onclick="return confirm('¿Eliminar este registro?')">Eliminar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">No hay registros de mano de obra.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $registros->links() }}
        </div>
    </div>

    <!-- Modal liquidación -->
    <div x-show="liquidar" x-cloak @keydown.escape.window="liquidar = false"
         class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" @click.self="liquidar = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-agri-green mb-1">Liquidar semana</h3>
            <p class="text-sm text-gray-500 mb-4">Paga todo lo pendiente del trabajador en los días indicados.</p>
            <form action="{{ route('mano-obra.liquidar') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Trabajador registrado</label>
                    <select name="trabajador_id" class="input-agri">
                        <option value="">— o escriba el documento abajo —</option>
                        @foreach($trabajadores as $t)
                            <option value="{{ $t->id }}">{{ $t->nombres }} {{ $t->apellidos }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Documento (jornalero de paso)</label>
                    <input type="text" name="cedula" class="input-agri" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Desde *</label>
                        <input type="date" name="desde" required value="{{ now()->startOfWeek()->toDateString() }}" class="input-agri" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hasta *</label>
                        <input type="date" name="hasta" required value="{{ now()->toDateString() }}" class="input-agri" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de pago *</label>
                    <input type="date" name="fecha_pago" required value="{{ now()->toDateString() }}" class="input-agri" />
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="liquidar = false" class="flex-1 btn-outline-agri py-2 rounded-lg">Cancelar</button>
                    <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2 rounded-lg shadow">Liquidar</button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
