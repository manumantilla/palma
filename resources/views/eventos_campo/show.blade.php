<x-app-layout>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header con acciones -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-emerald-100 text-emerald-800 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-black text-slate-800">
                        {{ $evento->tipoEvento->nombre ?? 'Evento de Campo' }} #{{ $evento->id }}
                    </h1>
                    <!-- Estado Badge -->
                    @php
                        $estadoColor = match(strtolower($evento->estado)) {
                            'completado', 'finalizado' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                            'en proceso' => 'bg-amber-100 text-amber-800 border-amber-300',
                            'programado' => 'bg-blue-100 text-blue-800 border-blue-300',
                            'cancelado' => 'bg-red-100 text-red-800 border-red-300',
                            default => 'bg-slate-100 text-slate-800 border-slate-300'
                        };
                    @endphp
                    <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $estadoColor }}">
                        {{ ucfirst($evento->estado) }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    Ciclo Productivo: <strong class="text-slate-700">{{ $evento->cicloProductivo->nombre_campana ?? 'N/A' }}</strong>
                </p>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex items-center space-x-3">
            <a href="{{ route('eventos_campo.insumos.create', $evento->id) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-md transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Registrar Insumos
            </a>
            <a href="{{ route('eventos_campo.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-colors">
                Volver
            </a>
        </div>
    </div>

    <!-- Grid de Métricas Básicas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Lote & Zona -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Ubicación Agrícola</span>
            <div class="mt-2 text-slate-800 font-bold text-base">
                {{ $evento->lote->nombre_lote ?? 'Sin Lote' }}
            </div>
            <div class="text-xs text-emerald-700 font-semibold mt-1">
                Zona: {{ $evento->zona->nombre ?? 'Toda la zona' }}
            </div>
        </div>

        <!-- Fechas -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Fecha Programada</span>
            <div class="mt-2 text-slate-800 font-bold text-base">
                {{ $evento->fecha_programada ? $evento->fecha_programada->format('d/m/Y') : 'Sin fecha' }}
            </div>
            <div class="text-xs text-slate-500 mt-1">
                Ejecución: {{ $evento->fecha_ejecucion ? $evento->fecha_ejecucion->format('d/m/Y') : 'Pendiente' }}
            </div>
        </div>

        <!-- Horario -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Horario Laboral</span>
            <div class="mt-2 text-slate-800 font-bold text-base">
                {{ $evento->hora_inicio ? \Carbon\Carbon::parse($evento->hora_inicio)->format('H:i') : '--:--' }} 
                - 
                {{ $evento->hora_fin ? \Carbon\Carbon::parse($evento->hora_fin)->format('H:i') : '--:--' }}
            </div>
            <div class="text-xs text-slate-500 mt-1">Jornada de Campo</div>
        </div>

        <!-- Geolocalización -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Coordenadas GPS</span>
            <div class="mt-2 text-slate-800 font-bold text-sm truncate">
                @if($evento->latitud && $evento->longitud)
                    <span>{{ $evento->latitud }}, {{ $evento->longitud }}</span>
                @else
                    <span class="text-slate-400 font-normal">Sin GPS registrado</span>
                @endif
            </div>
            @if($evento->latitud && $evento->longitud)
                <a href="https://maps.google.com/?q={{ $evento->latitud }},{{ $evento->longitud }}" target="_blank" class="text-xs text-blue-600 font-semibold hover:underline mt-1 inline-block">Ver en Google Maps &rarr;</a>
            @endif
        </div>
    </div>

    <!-- Observaciones -->
    @if($evento->observaciones)
        <div class="bg-amber-50/60 border border-amber-200 rounded-2xl p-4 text-sm text-amber-900">
            <strong class="font-bold uppercase text-xs text-amber-700 block mb-1">Observaciones de Campo:</strong>
            {{ $evento->observaciones }}
        </div>
    @endif

    <!-- TABLA 1: Insumos Consumidos en el Evento (Relación eventoInsumos) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base font-extrabold text-slate-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                Insumos Aplicados y Salidas de Kardex
            </h2>
            <span class="text-xs font-bold bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full">
                {{ $evento->eventoInsumos->count() }} Insumo(s)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 text-xs uppercase font-bold border-b border-slate-200">
                        <th class="py-3 px-4">Insumo</th>
                        <th class="py-3 px-4">Método</th>
                        <th class="py-3 px-4">Cantidad Total</th>
                        <th class="py-3 px-4">Área Aplicada</th>
                        <th class="py-3 px-4">Desglose de Lotes (Kardex)</th>
                        <th class="py-3 px-4 text-right">Costo Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($evento->eventoInsumos as $insumoAplicado)
                        <tr class="hover:bg-slate-50/80">
                            <td class="py-3 px-4 font-bold text-slate-800">
                                {{ $insumoAplicado->insumo->nombre ?? 'Insumo #' . $insumoAplicado->insumo_id }}
                            </td>
                            <td class="py-3 px-4 capitalize text-slate-600">
                                <span class="bg-slate-100 px-2 py-0.5 rounded text-xs border border-slate-200">
                                    {{ $insumoAplicado->metodo_aplicacion }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-emerald-700">
                                {{ number_format($insumoAplicado->cantidad, 2) }} {{ $insumoAplicado->unidad_medida }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $insumoAplicado->area_aplicada ? $insumoAplicado->area_aplicada . ' Ha' : 'N/A (Por Árbol)' }}
                            </td>
                            <!-- Desglose por lotes de inventario -->
                            <td class="py-3 px-4">
                                @if($insumoAplicado->lotes && $insumoAplicado->lotes->count() > 0)
                                    <div class="space-y-1">
                                        @foreach($insumoAplicado->lotes as $loteItem)
                                            <div class="text-xs bg-slate-50 border border-slate-200 rounded px-2 py-1 flex items-center justify-between">
                                                <span class="font-mono text-slate-700">Lote: {{ $loteItem->codigo_lote ?? $loteItem->lote_insumo_id }}</span>
                                                <span class="font-bold text-slate-800 ml-2">{{ $loteItem->pivot->cantidad ?? $loteItem->cantidad }} {{ $insumoAplicado->unidad_medida }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Sin desglose registrado</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold text-slate-800">
                                ${{ number_format($insumoAplicado->costo_total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400 text-sm">
                                No se han registrado consumo de insumos para este evento.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TABLA 2: Árboles Intervenidos (Relación eventoArboles) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base font-extrabold text-slate-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                Árboles / Plantas Intervenidas en la Labor
            </h2>
            <span class="text-xs font-bold bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full">
                {{ $evento->eventoArboles->count() }} Árbol(es)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 text-xs uppercase font-bold border-b border-slate-200">
                        <th class="py-3 px-4">Código / ID Árbol</th>
                        <th class="py-3 px-4">Lote / Surco</th>
                        <th class="py-3 px-4">Observaciones del Árbol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($evento->eventoArboles as $itemArbol)
                        <tr class="hover:bg-slate-50/80">
                            <td class="py-3 px-4 font-bold text-slate-800">
                                Árbol #{{ $itemArbol->arbol->codigo ?? $itemArbol->arbol_id }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                Surco: {{ $itemArbol->arbol->numero_surco ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-slate-500 text-xs">
                                {{ $itemArbol->observaciones ?? 'Sin novedades' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-slate-400 text-sm">
                                Este evento fue de aplicación general (por área/zona) o no tiene árboles registrados individualmente.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</x-app-layout>