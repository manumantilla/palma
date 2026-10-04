{{-- Modal: Completar Etapa Fenológica --}}
<div x-data="{ open: false }"
     x-on:open-modal.window="if ($event.detail === 'modal-completar-{{ $etapa->id }}') open = true"
     x-on:keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-completar-label-{{ $etapa->id }}"
     role="dialog"
     aria-modal="true">

    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm transition-opacity"
             x-show="open"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-on:click="open = false"></div>

        {{-- Contenido --}}
        <div x-show="open"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl border border-stone-200 overflow-hidden">

            <form action="{{ route('ciclo-etapas.completar', $etapa->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Header --}}
                <div class="relative bg-gradient-to-r from-emerald-700 to-green-600 px-5 py-4 border-b-4 border-amber-500">
                    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:14px_14px]"></div>
                    <div class="relative flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h5 id="modal-completar-label-{{ $etapa->id }}"
                                class="text-base font-black text-white flex items-center gap-2">
                                ✔ Completar Etapa
                            </h5>
                            <p class="text-xs text-emerald-100 mt-0.5 truncate">
                                {{ $etapa->fenologiaEtapa->nombre ?? 'Etapa' }}
                            </p>
                        </div>
                        <button type="button"
                                x-on:click="open = false"
                                class="text-white/70 hover:text-white text-2xl leading-none -mt-1 transition">
                            &times;
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-5 space-y-5 max-h-[65vh] overflow-y-auto">

                    {{-- Resumen de fechas estimadas --}}
                    <div class="bg-stone-50 border border-stone-200 rounded-xl p-3 text-xs text-stone-600 flex items-start gap-2">
                        <span class="text-base">🗓️</span>
                        <div>
                            <span class="font-bold text-stone-700">Cronograma estimado:</span>
                            <span class="font-mono">{{ $etapa->fecha_inicio_estimada }}</span>
                            <span class="text-stone-400">→</span>
                            <span class="font-mono">{{ $etapa->fecha_fin_estimada }}</span>
                        </div>
                    </div>

                    {{-- Fechas reales --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="fecha_inicio_real-{{ $etapa->id }}"
                                   class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">
                                Inicio Real <span class="text-rose-500">*</span>
                            </label>
                            <input type="date"
                                   id="fecha_inicio_real-{{ $etapa->id }}"
                                   name="fecha_inicio_real"
                                   value="{{ old('fecha_inicio_real', $etapa->fecha_inicio_real ?? date('Y-m-d')) }}"
                                   required
                                   class="w-full rounded-xl border-stone-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm shadow-sm">
                            @error('fecha_inicio_real')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="fecha_fin_real-{{ $etapa->id }}"
                                   class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">
                                Fin Real <span class="text-rose-500">*</span>
                            </label>
                            <input type="date"
                                   id="fecha_fin_real-{{ $etapa->id }}"
                                   name="fecha_fin_real"
                                   value="{{ old('fecha_fin_real', $etapa->fecha_fin_real ?? date('Y-m-d')) }}"
                                   required
                                   class="w-full rounded-xl border-stone-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm shadow-sm">
                            @error('fecha_fin_real')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Métricas reales --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="rendimiento_real-{{ $etapa->id }}"
                                   class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">
                                Rendimiento Real <span class="text-stone-400 font-normal normal-case">(opcional)</span>
                            </label>
                            <div class="relative">
                                <input type="number"
                                       step="0.01"
                                       min="0"
                                       id="rendimiento_real-{{ $etapa->id }}"
                                       name="rendimiento_real"
                                       value="{{ old('rendimiento_real', $etapa->rendimiento_real) }}"
                                       placeholder="0.00"
                                       class="w-full rounded-xl border-stone-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm shadow-sm pr-14">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-stone-400">
                                    kg/ha
                                </span>
                            </div>
                        </div>

                        <div>
                            <label for="incidencia_sanitaria-{{ $etapa->id }}"
                                   class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">
                                Incidencia Sanitaria <span class="text-stone-400 font-normal normal-case">(opcional)</span>
                            </label>
                            <select id="incidencia_sanitaria-{{ $etapa->id }}"
                                    name="incidencia_sanitaria"
                                    class="w-full rounded-xl border-stone-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm shadow-sm">
                                <option value="">— Sin registrar —</option>
                                <option value="ninguna"   @selected(old('incidencia_sanitaria') === 'ninguna')>Ninguna</option>
                                <option value="baja"      @selected(old('incidencia_sanitaria') === 'baja')>Baja</option>
                                <option value="media"     @selected(old('incidencia_sanitaria') === 'media')>Media</option>
                                <option value="alta"      @selected(old('incidencia_sanitaria') === 'alta')>Alta</option>
                            </select>
                        </div>
                    </div>

                    {{-- Motivo de desviación (opcional) --}}
                    <div>
                        <label for="motivo_desviacion-{{ $etapa->id }}"
                               class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">
                            Motivo de Desviación
                            <span class="text-stone-400 font-normal normal-case">— si difiere del cronograma</span>
                        </label>
                        <textarea id="motivo_desviacion-{{ $etapa->id }}"
                                  name="motivo_desviacion"
                                  rows="2"
                                  placeholder="Ej: Retraso por lluvias intensas, ajuste por fitosanidad…"
                                  class="w-full rounded-xl border-stone-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm shadow-sm resize-none">{{ old('motivo_desviacion') }}</textarea>
                    </div>

                    {{-- Observaciones --}}
                    <div>
                        <label for="observaciones-{{ $etapa->id }}"
                               class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">
                            Observaciones Técnicas
                        </label>
                        <textarea id="observaciones-{{ $etapa->id }}"
                                  name="observaciones"
                                  rows="3"
                                  placeholder="Notas agronómicas, aplicación de insumos, labores realizadas…"
                                  class="w-full rounded-xl border-stone-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm shadow-sm resize-none">{{ old('observaciones') }}</textarea>
                    </div>

                    {{-- Aviso --}}
                    <div class="flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800">
                        <span class="text-base leading-none">⚠️</span>
                        <p>Al completar la etapa se registrará el cierre en la línea de tiempo y se habilitará la siguiente fase del ciclo.</p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-stone-200 bg-stone-50/60">
                    <button type="button"
                            x-on:click="open = false"
                            class="px-4 py-2 rounded-xl text-sm font-semibold text-stone-600 bg-white border border-stone-300 hover:bg-stone-100 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition">
                        ✔ Confirmar Cierre
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>