<x-app-layout>
<div class="card-agri p-6 max-w-4xl mx-auto">
    <div class="flex items-start justify-between">
        <h2 class="text-2xl font-bold text-agri-green flex items-center gap-2">
            <i class="fas fa-user-tag text-3xl"></i>
            Detalle del Registro #{{ $registro->id }}
        </h2>
        <a href="{{ route('mano-obra.index') }}" class="btn-outline-agri px-4 py-2 rounded-lg">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-3">
            <div><span class="font-semibold text-gray-700">Trabajador:</span> {{ $registro->nombre_trabajador ?? ($registro->trabajador ? $registro->trabajador->nombre . ' ' . $registro->trabajador->apellido : 'N/A') }}</div>
            <div><span class="font-semibold text-gray-700">Cédula:</span> {{ $registro->cedula }}</div>
            <div><span class="font-semibold text-gray-700">Tipo de Labor:</span> <span class="capitalize">{{ str_replace('_', ' ', $registro->tipo_labor) }}</span></div>
            <div><span class="font-semibold text-gray-700">Cantidad:</span> {{ $registro->cantidad }} {{ $registro->unidad_destajo ?? '' }}</div>
            <div><span class="font-semibold text-gray-700">Valor Unitario:</span> ${{ number_format($registro->valor_unitario, 0, ',', '.') }}</div>
            <div><span class="font-semibold text-gray-700">Costo Total:</span> <span class="text-agri-green font-bold">${{ number_format($registro->costo_total, 0, ',', '.') }}</span></div>
            <div><span class="font-semibold text-gray-700">Estado de Pago:</span> 
                <span class="badge-estado {{ $registro->estado_pago == 'pendiente' ? 'badge-pendiente' : 'badge-pagado' }}">
                    {{ ucfirst($registro->estado_pago) }}
                </span>
            </div>
            @if($registro->fecha_pago)
                <div><span class="font-semibold text-gray-700">Fecha de Pago:</span> {{ \Carbon\Carbon::parse($registro->fecha_pago)->format('d/m/Y') }}</div>
            @endif
        </div>
        <div class="space-y-3">
            <div><span class="font-semibold text-gray-700">Evento de Campo:</span> 
                @if($registro->eventoCampo)
                    ID: {{ $registro->eventoCampo->id }} - {{ $registro->eventoCampo->fecha_programada }}
                @else
                    No asociado
                @endif
            </div>
            <div><span class="font-semibold text-gray-700">Sesión de Cosecha:</span> 
                @if($registro->sesionCosecha)
                    {{ $registro->sesionCosecha->nombre ?? 'Sesión #'.$registro->sesionCosecha->id }}
                @else
                    No asociada
                @endif
            </div>
            <div><span class="font-semibold text-gray-700">Observaciones:</span> <br> {{ $registro->observaciones ?? 'Ninguna' }}</div>
            <div><span class="font-semibold text-gray-700">Creado:</span> {{ $registro->created_at->format('d/m/Y H:i') }}</div>
            <div><span class="font-semibold text-gray-700">Última actualización:</span> {{ $registro->updated_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    @if($registro->estado_pago != 'pagado')
        <div class="mt-6 flex gap-3">
            <a href="{{ route('mano-obra.edit', $registro) }}" class="btn-agri px-5 py-2 rounded-lg shadow">
                <i class="fas fa-edit"></i> Editar
            </a>
            <form action="{{ route('mano-obra.destroy', $registro) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este registro?')">
                @csrf @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg shadow">
                    <i class="fas fa-trash-alt"></i> Eliminar
                </button>
            </form>
        </div>
    @endif
</div>
</x-app-layout>