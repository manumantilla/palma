{{-- Modal: Omitir Etapa Fenológica --}}
<div x-data="{ open: false, confirmado: false }"
     x-on:open-modal.window="if ($event.detail === 'modal-omitir-{{ $etapa->id }}') { open = true; confirmado = false; }"
     x-on:keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-omitir-label-{{ $etapa->id }}"
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
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg border border-stone-200 overflow-hidden">

            <form action="{{ route('ciclo-etapas.omitir', $etapa->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Header --}}
                <div class="relative bg-gradient-to-r from-stone-700 to-stone-600 px-5 py-4 border-b-4 border-amber-500">
                    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:14px_14px]"></div>
                    <div class="relative flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h5 id="modal-omitir-label-{{ $etapa->id }}"
                                class="text-base font-black text-white flex items-center gap-2">
                                🚫 Omitir Etapa
                            </h5>
                            <p class="text-xs text-stone-200 mt-0.5 truncate">
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
                <div class="p-5 space-y-5">

                    {{-- Aviso --}}
                    <div class="flex items-start gap-2 bg-rose-50 border border-rose-200 rounded-xl p-3 text-xs text-rose-800">
                        <span class="text-base leading-none">⚠️</span>
                        <p>
                            Esta acción marcará la etapa como <strong>omitida</strong>. La etapa no se contabilizará en el avance
                            del ciclo y quedará registrada con el motivo indicado. <strong>Esta acción no se puede deshacer.</strong>
                        </p>
                    </div>

                    {{-- Resumen --}}
                    <div class="bg-stone-50 border border-stone-200 rounded-xl p-3 text-xs text-stone-600 flex items-start gap-2">
                        <span class="text-base">🗓️</span>
                        <div>
                            <span class="font-bold text-stone-700">Ventana planificada:</span>
                            <span class="font-mono">{{ $etapa->fecha_inicio_estimada }}</span>
                            <span class="text-stone-400">→</span>
                            <span class="font-mono">{{ $etapa->fecha_fin_estimada }}</span>
                        </div>
                    </div>

                    {{-- Motivo --}}
                    <div>
                        <label for="motivo_omision-{{ $etapa->id }}"
                               class="block text-xs font-bold text-stone-600 uppercase tracking-wider mb-1.5">
                            Motivo de Omisión <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="motivo_omision-{{ $etapa->id }}"
                                  name="motivo_omision"
                                  rows="4"
                                  required
                                  maxlength="500"
                                  placeholder="Describe la razón técnica u operativa por la que se omite esta etapa…"
                                  class="w-full rounded-xl border-stone-300 focus:border-rose-500 focus:ring-rose-500 text-sm shadow-sm resize-none">{{ old('motivo_omision') }}</textarea>
                        @error('motivo_omision')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-[11px] text-stone-400 mt-1">Máximo 500 caracteres.</p>
                    </div>

                    {{-- Checkbox de confirmación --}}
                    <label class="flex items-start gap-3 cursor-pointer select-none bg-stone-50 border border-stone-200 rounded-xl p-3 hover:bg-stone-100 transition">
                        <input type="checkbox"
                               x-model="confirmado"
                               class="mt-0.5 rounded border-stone-300 text-rose-600 focus:ring-rose-500">
                        <span class="text-xs text-stone-700 leading-relaxed">
                            Entiendo que al omitir esta etapa <strong>no podré revertirla</strong> y que quedará registrada
                            de forma permanente en la trazabilidad del ciclo.
                        </span>
                    </label>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-stone-200 bg-stone-50/60">
                    <button type="button"
                            x-on:click="open = false"
                            class="px-4 py-2 rounded-xl text-sm font-semibold text-stone-600 bg-white border border-stone-300 hover:bg-stone-100 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            :disabled="!confirmado"
                            :class="confirmado
                                ? 'bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-600/20 cursor-pointer'
                                : 'bg-rose-300 cursor-not-allowed'"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-bold text-white transition">
                        🚫 Confirmar Omisión
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>