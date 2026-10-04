<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-xl bg-emerald-100 text-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v18m0 0l-4-4m4 4l4-4M5 8c2 0 4 1 4 4m10-4c-2 0-4 1-4 4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-emerald-900 leading-tight">
                        Historial de Etapas Fenológicas
                    </h2>
                    <p class="text-sm text-emerald-600">Seguimiento del desarrollo del cultivo 🌾</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-b from-lime-50 via-emerald-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Alertas --}}
            @if (session('success'))
                <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-sm">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Filtros --}}
            <div class="rounded-2xl bg-white/80 backdrop-blur border border-emerald-100 shadow-sm p-5">
                <form method="GET" action="{{ route('ciclo-etapas.index') }}"
                      class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-emerald-700 mb-1">
                            Ciclo productivo
                        </label>
                        <input type="number" name="ciclo_productivo_id"
                               value="{{ request('ciclo_productivo_id') }}"
                               placeholder="ID del ciclo..."
                               class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm bg-white shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-emerald-700 mb-1">
                            Estado
                        </label>
                        <select name="estado"
                                class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm bg-white shadow-sm">
                            <option value="">Todos</option>
                            @foreach (['pendiente', 'en_progreso', 'completada', 'omitida'] as $estado)
                                <option value="{{ $estado }}" @selected(request('estado') === $estado)>
                                    {{ ucfirst(str_replace('_', ' ', $estado)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="flex-1 inline-flex justify-center items-center gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Filtrar
                        </button>
                        <a href="{{ route('ciclo-etapas.index') }}"
                           class="inline-flex justify-center items-center rounded-lg border border-emerald-200 text-emerald-700 hover:bg-emerald-50 text-sm font-medium px-3 py-2 transition">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabla --}}
            <div class="rounded-2xl bg-white border border-emerald-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-emerald-100">
                        <thead class="bg-emerald-50/60">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">Etapa</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">Cultivo / Ciclo</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">Fechas estimadas</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">Fechas reales</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">Estado</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-emerald-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-emerald-50">
                            @forelse ($etapas as $etapa)
                                @php
                                    $badge = [
                                        'pendiente'   => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'en_progreso' => 'bg-sky-100 text-sky-800 border-sky-200',
                                        'completada'  => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'omitida'     => 'bg-stone-200 text-stone-700 border-stone-300',
                                    ][$etapa->estado] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                @endphp
                                <tr class="hover:bg-emerald-50/40 transition">
                                    <td class="px-4 py-3 text-sm text-emerald-900 font-medium">
                                        {{ $etapa->fenologiaEtapa->orden ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-semibold text-emerald-900">
                                            {{ $etapa->fenologiaEtapa->nombre ?? 'Etapa #'.$etapa->id }}
                                        </div>
                                        @if ($etapa->fenologiaEtapa?->descripcion)
                                            <div class="text-xs text-emerald-600 line-clamp-1">
                                                {{ $etapa->fenologiaEtapa->descripcion }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-emerald-800">
                                        <div class="font-medium">{{ $etapa->cicloProductivo->cultivo->nombre ?? '—' }}</div>
                                        <div class="text-xs text-emerald-500">
                                            Ciclo #{{ $etapa->ciclo_productivo_id }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-emerald-700">
                                        <div class="flex items-center gap-1">
                                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                            {{ \Carbon\Carbon::parse($etapa->fecha_inicio_estimada)->format('d/m/Y') }}
                                        </div>
                                        <div class="flex items-center gap-1 mt-1">
                                            <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                                            {{ \Carbon\Carbon::parse($etapa->fecha_fin_estimada)->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-emerald-700">
                                        @if ($etapa->fecha_inicio_real)
                                            <div class="flex items-center gap-1">
                                                <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                                                {{ \Carbon\Carbon::parse($etapa->fecha_inicio_real)->format('d/m/Y') }}
                                            </div>
                                        @else
                                            <div class="text-emerald-400">— inicio —</div>
                                        @endif
                                        @if ($etapa->fecha_fin_real)
                                            <div class="flex items-center gap-1 mt-1">
                                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                                {{ \Carbon\Carbon::parse($etapa->fecha_fin_real)->format('d/m/Y') }}
                                            </div>
                                        @else
                                            <div class="text-emerald-400">— fin —</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $badge }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
                                            {{ ucfirst(str_replace('_', ' ', $etapa->estado)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('ciclo-etapas.show', $etapa->id) }}"
                                           class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 hover:text-emerald-900 hover:underline">
                                            Ver detalle
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3 text-emerald-500">
                                            <div class="p-4 rounded-full bg-emerald-50">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                          d="M12 3v18m0 0l-4-4m4 4l4-4M5 8c2 0 4 1 4 4m10-4c-2 0-4 1-4 4"/>
                                                </svg>
                                            </div>
                                            <p class="text-sm font-medium">No hay etapas registradas todavía</p>
                                            <p class="text-xs text-emerald-400">Inicializa el plan fenológico desde un ciclo productivo.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>