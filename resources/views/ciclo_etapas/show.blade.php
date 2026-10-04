<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('ciclo-etapas.index') }}"
                   class="p-2 rounded-xl bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition"
                   title="Volver">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-emerald-900 leading-tight">
                        {{ $etapaHistorial->fenologiaEtapa->nombre ?? 'Etapa del ciclo' }}
                    </h2>
                    <p class="text-sm text-emerald-600">
                        {{ $etapaHistorial->cicloProductivo->cultivo->nombre ?? 'Cultivo' }}
                        · Ciclo #{{ $etapaHistorial->ciclo_productivo_id }}
                    </p>
                </div>
            </div>

            @php
                $badge = [
                    'pendiente'   => 'bg-amber-100 text-amber-800 border-amber-200',
                    'en_progreso' => 'bg-sky-100 text-sky-800 border-sky-200',
                    'completada'  => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'omitida'     => 'bg-stone-200 text-stone-700 border-stone-300',
                ][$etapaHistorial->estado] ?? 'bg-gray-100 text-gray-800 border-gray-200';
            @endphp
            <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold {{ $badge }}">
                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
                {{ ucfirst(str_replace('_', ' ', $etapaHistorial->estado)) }}
            </span>
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Columna principal --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Cronología --}}
                    <div class="rounded-2xl bg-white border border-emerald-100 shadow-sm p-6">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-700 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Cronología de la etapa
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="rounded-xl border border-amber-100 bg-amber-50/50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-amber-700">Inicio estimado</p>
                                <p class="mt-1 text-lg font-bold text-amber-900">
                                    {{ \Carbon\Carbon::parse($etapaHistorial->fecha_inicio_estimada)->format('d M Y') }}
                                </p>
                            </div>
                            <div class="rounded-xl border border-amber-100 bg-amber-50/50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-amber-700">Fin estimado</p>
                                <p class="mt-1 text-lg font-bold text-amber-900">
                                    {{ \Carbon\Carbon::parse($etapaHistorial->fecha_fin_estimada)->format('d M Y') }}
                                </p>
                            </div>
                            <div class="rounded-xl border border-sky-100 bg-sky-50/50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-sky-700">Inicio real</p>
                                <p class="mt-1 text-lg font-bold text-sky-900">
                                    {{ $etapaHistorial->fecha_inicio_real
                                        ? \Carbon\Carbon::parse($etapaHistorial->fecha_inicio_real)->format('d M Y')
                                        : '— sin registrar —' }}
                                </p>
                            </div>
                            <div class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Fin real</p>
                                <p class="mt-1 text-lg font-bold text-emerald-900">
                                    {{ $etapaHistorial->fecha_fin_real
                                        ? \Carbon\Carbon::parse($etapaHistorial->fecha_fin_real)->format('d M Y')
                                        : '— sin registrar —' }}
                                </p>
                            </div>
                        </div>

                        @if ($etapaHistorial->motivo_desviacion)
                            <div class="mt-4 rounded-xl border border-orange-200 bg-orange-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-orange-700">
                                    Motivo de desviación
                                </p>
                                <p class="mt-1 text-sm text-orange-900">{{ $etapaHistorial->motivo_desviacion }}</p>
                            </div>
                        @endif

                        @if ($etapaHistorial->observaciones)
                            <div class="mt-4 rounded-xl border border-emerald-100 bg-white p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">
                                    Observaciones
                                </p>
                                <p class="mt-1 text-sm text-emerald-800 whitespace-pre-line">
                                    {{ $etapaHistorial->observaciones }}
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Recomendaciones --}}
                    <div class="rounded-2xl bg-white border border-emerald-100 shadow-sm p-6">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-700 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"/>
                            </svg>
                            Recomendaciones técnicas
                        </h3>

                        <div class="space-y-3">
                            @forelse ($etapaHistorial->fenologiaEtapa->recomendaciones ?? [] as $recomendacion)
                                <div class="rounded-xl border border-emerald-100 bg-gradient-to-r from-emerald-50/60 to-lime-50/40 p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-emerald-900">
                                                {{ $recomendacion->titulo }}
                                            </p>
                                            @if ($recomendacion->descripcion)
                                                <p class="text-xs text-emerald-700 mt-1">
                                                    {{ $recomendacion->descripcion }}
                                                </p>
                                            @endif
                                        </div>
                                        @if ($recomendacion->genera_evento_automatico)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-600 text-white text-[10px] font-bold uppercase px-2 py-0.5">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Auto
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-2 flex flex-wrap gap-2 text-[11px]">
                                        @if (!is_null($recomendacion->dias_offset))
                                            <span class="rounded-full bg-white border border-emerald-200 text-emerald-700 px-2 py-0.5">
                                                Offset: {{ $recomendacion->dias_offset }} días
                                            </span>
                                        @endif
                                        @if ($recomendacion->prioridad)
                                            <span class="rounded-full bg-white border border-emerald-200 text-emerald-700 px-2 py-0.5">
                                                Prioridad: {{ $recomendacion->prioridad }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-emerald-500 italic">
                                    Sin recomendaciones asociadas a esta etapa.
                                </p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Columna de acciones --}}
                <div class="space-y-6">

                    {{-- Iniciar etapa --}}
                    @if ($etapaHistorial->estado === 'pendiente')
                        <div class="rounded-2xl bg-white border border-sky-100 shadow-sm p-6">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-sky-700 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Iniciar etapa
                            </h3>
                            <form method="POST" action="{{ route('ciclo-etapas.iniciar', $etapaHistorial->id) }}" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-sky-700 mb-1">Fecha real de inicio</label>
                                    <input type="date" name="fecha_inicio_real"
                                           value="{{ now()->toDateString() }}"
                                           class="w-full rounded-lg border-sky-200 focus:border-sky-500 focus:ring-sky-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-sky-700 mb-1">Observaciones</label>
                                    <textarea name="observaciones" rows="3"
                                              class="w-full rounded-lg border-sky-200 focus:border-sky-500 focus:ring-sky-500 text-sm"
                                              placeholder="Notas de campo..."></textarea>
                                </div>
                                <button type="submit"
                                        class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold px-4 py-2 transition">
                                    Iniciar en campo
                                </button>
                                <p class="text-[11px] text-sky-500">
                                    Se programarán automáticamente los eventos recomendados.
                                </p>
                            </form>
                        </div>
                    @endif

                    {{-- Completar etapa --}}
                    @if (in_array($etapaHistorial->estado, ['en_progreso', 'pendiente']))
                        <div class="rounded-2xl bg-white border border-emerald-100 shadow-sm p-6">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-700 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Completar etapa
                            </h3>
                            <form method="POST" action="{{ route('ciclo-etapas.completar', $etapaHistorial->id) }}" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-emerald-700 mb-1">Fecha real de fin</label>
                                    <input type="date" name="fecha_fin_real"
                                           value="{{ now()->toDateString() }}"
                                           class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-emerald-700 mb-1">Motivo de desviación (opcional)</label>
                                    <input type="text" name="motivo_desviacion" maxlength="255"
                                           class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                                           placeholder="Ej. lluvias intensas">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-emerald-700 mb-1">Observaciones</label>
                                    <textarea name="observaciones" rows="3"
                                              class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm"></textarea>
                                </div>
                                <button type="submit"
                                        class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 transition">
                                    Marcar como completada
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Omitir etapa --}}
                    @if (in_array($etapaHistorial->estado, ['pendiente', 'en_progreso']))
                        <div class="rounded-2xl bg-white border border-stone-200 shadow-sm p-6">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-stone-600 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                Omitir etapa
                            </h3>
                            <form method="POST" action="{{ route('ciclo-etapas.omitir', $etapaHistorial->id) }}" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-stone-600 mb-1">Motivo *</label>
                                    <input type="text" name="motivo_desviacion" required maxlength="255"
                                           class="w-full rounded-lg border-stone-200 focus:border-stone-500 focus:ring-stone-500 text-sm"
                                           placeholder="Razón por la que se omite">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-stone-600 mb-1">Observaciones</label>
                                    <textarea name="observaciones" rows="2"
                                              class="w-full rounded-lg border-stone-200 focus:border-stone-500 focus:ring-stone-500 text-sm"></textarea>
                                </div>
                                <button type="submit"
                                        class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-stone-600 hover:bg-stone-700 text-white text-sm font-semibold px-4 py-2 transition">
                                    Marcar como omitida
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Info del ciclo --}}
                    <div class="rounded-2xl bg-gradient-to-br from-emerald-600 to-lime-600 text-white shadow-md p-6">
                        <p class="text-xs font-semibold uppercase tracking-wider opacity-80">Ciclo productivo</p>
                        <p class="mt-1 text-lg font-bold">
                            {{ $etapaHistorial->cicloProductivo->nombre ?? 'Ciclo #'.$etapaHistorial->ciclo_productivo_id }}
                        </p>
                        <p class="text-sm opacity-90 mt-1">
                            Cultivo: {{ $etapaHistorial->cicloProductivo->cultivo->nombre ?? '—' }}
                        </p>
                        @if ($etapaHistorial->fenologiaEtapa?->duracion_dias_estimada)
                            <div class="mt-3 pt-3 border-t border-white/20 text-xs flex justify-between">
                                <span>Duración estimada</span>
                                <span class="font-bold">{{ $etapaHistorial->fenologiaEtapa->duracion_dias_estimada }} días</span>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>