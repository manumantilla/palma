<x-app-layout>
<div class="card-agri p-6 max-w-4xl mx-auto">
    <div class="flex items-start justify-between">
        <h2 class="text-2xl font-bold text-agri-green">Registro #{{ $registro->id }}</h2>
        <a href="{{ route('mano-obra.index') }}" class="btn-outline-agri px-4 py-2 rounded-lg">Volver</a>
    </div>

    @if(session('error'))
        <div class="mt-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
        <div class="space-y-3">
            <div><span class="font-semibold text-gray-700">Trabajador:</span> {{ $registro->nombre_trabajador }}</div>
            <div><span class="font-semibold text-gray-700">Documento:</span> {{ $registro->cedula ?? 'No registrado' }}</div>
            <div><span class="font-semibold text-gray-700">Día trabajado:</span> {{ $registro->fecha_labor->format('d/m/Y') }}</div>
            <div><span class="font-semibold text-gray-700">Forma de pago:</span> <span class="capitalize">{{ str_replace('_', ' ', $registro->tipo_labor) }}</span></div>
            <div><span class="font-semibold text-gray-700">Cantidad:</span> {{ rtrim(rtrim(number_format($registro->cantidad, 2, ',', '.'), '0'), ',') }} {{ $registro->unidad_destajo ?? '' }}</div>
            @if($registro->kilos_pesados)
                <div><span class="font-semibold text-gray-700">Báscula:</span> {{ number_format($registro->kilos_pesados, 2, ',', '.') }} kg</div>
            @endif
            <div><span class="font-semibold text-gray-700">Valor unitario:</span> ${{ number_format($registro->valor_unitario, 0, ',', '.') }}</div>
            <div>
                <span class="font-semibold text-gray-700">Valor pagado:</span>
                <span class="text-agri-green font-bold">${{ number_format($registro->costo_total, 0, ',', '.') }}</span>
                @if(abs($registro->ajuste) >= 1)
                    <span class="text-gray-400">(calculado ${{ number_format($registro->costo_calculado, 0, ',', '.') }}, ajustado a mano)</span>
                @endif
            </div>
            <div><span class="font-semibold text-gray-700">Estado de pago:</span>
                <span class="badge-estado {{ $registro->estado_pago == 'pendiente' ? 'badge-pendiente' : 'badge-pagado' }}">{{ ucfirst($registro->estado_pago) }}</span>
                @if($registro->fecha_pago) el {{ $registro->fecha_pago->format('d/m/Y') }} @endif
            </div>
        </div>
        <div class="space-y-3">
            <div><span class="font-semibold text-gray-700">Labor:</span>
                <a href="{{ route('eventos_campo.show', $registro->evento_campo_id) }}" class="text-emerald-700 hover:underline">
                    {{ $registro->eventoCampo->tipoEvento->nombre ?? 'Labor' }} #{{ $registro->evento_campo_id }}
                </a>
            </div>
            <div><span class="font-semibold text-gray-700">Ciclo:</span> {{ $registro->eventoCampo->cicloProductivo->nombre_campana ?? 'General (sin cultivo)' }}</div>
            @if($registro->sesionCosecha)
                <div><span class="font-semibold text-gray-700">Sesión de cosecha:</span> {{ $registro->sesionCosecha->fecha?->format('d/m/Y') }}</div>
            @endif
            @if($registro->area_ejecutada_ha || $registro->arboles_atendidos)
                <div><span class="font-semibold text-gray-700">Avance:</span>
                    @if($registro->area_ejecutada_ha) {{ $registro->area_ejecutada_ha }} ha @endif
                    @if($registro->arboles_atendidos) {{ $registro->arboles_atendidos }} árboles @endif
                </div>
            @endif
            <div><span class="font-semibold text-gray-700">Observaciones:</span><br>{{ $registro->observaciones ?? 'Ninguna' }}</div>
        </div>
    </div>

    @if($registro->estado_pago != 'pagado')
        <div class="mt-6 flex gap-3">
            <a href="{{ route('mano-obra.edit', $registro) }}" class="btn-agri px-5 py-2 rounded-lg shadow">Editar</a>
            <form action="{{ route('mano-obra.destroy', $registro) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?')">
                @csrf @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg shadow">Eliminar</button>
            </form>
        </div>
    @endif
</div>
</x-app-layout>
