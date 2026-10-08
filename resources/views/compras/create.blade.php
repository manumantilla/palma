<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-md shadow-emerald-600/30">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-emerald-900">Nueva compra</h2>
                    <p class="text-sm text-stone-500">Los insumos ingresan directo al inventario (KARDEX)</p>
                </div>
            </div>
            <a href="{{ route('compras.index') }}" class="text-sm font-medium text-stone-600 hover:text-emerald-700">&larr; Volver</a>
        </div>
    </x-slot>

    @php
        $itemVacio = ['insumo_id' => '', 'cantidad' => '', 'costo_unitario' => '', 'codigo_lote' => '', 'fecha_vencimiento' => ''];
        $itemsIniciales = old('items', [$itemVacio]);
        $input = 'w-full rounded-lg border-stone-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500';
        $label = 'mb-1 block text-xs font-semibold uppercase tracking-wide text-stone-600';
    @endphp

    <div class="min-h-screen bg-gradient-to-b from-lime-50/60 via-stone-50 to-stone-100 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <p class="mb-2 font-semibold">Revisa los siguientes campos:</p>
                    <ul class="list-inside list-disc space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('compras.store') }}"
                  x-data="compraForm(@js($itemsIniciales), @js($itemVacio), @js(old('tipo', 'insumos')), @js(old('porcentaje_iva_general', 0)))"
                  class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                @csrf

                <div class="space-y-6 lg:col-span-2">
                    {{-- Datos generales --}}
                    <section class="rounded-2xl border border-stone-200 bg-white shadow-sm">
                        <header class="flex items-center gap-2 border-b border-stone-200 px-6 py-4">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            <h3 class="font-semibold text-emerald-900">Datos de la compra</h3>
                        </header>
                        <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="{{ $label }}">Proveedor *</label>
                                <select name="proveedor_id" class="{{ $input }}" required>
                                    <option value="">Selecciona un proveedor…</option>
                                    @foreach ($proveedores as $p)
                                        <option value="{{ $p->id }}" @selected(old('proveedor_id') == $p->id)>{{$p->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('proveedor_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="{{ $label }}">Fecha *</label>
                                <input type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}" class="{{ $input }}" required>
                            </div>

                            <div>
                                <label class="{{ $label }}">Tipo de compra *</label>
                                <select name="tipo" x-model="tipo" class="{{ $input }}" required>
                                    @foreach (\App\Models\Compra::TIPOS as $tipo)
                                        <option value="{{ $tipo }}">{{ ucfirst(str_replace('_', ' ', $tipo)) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="{{ $label }}">N.º factura</label>
                                <input type="text" name="numero_factura" value="{{ old('numero_factura') }}" maxlength="100" placeholder="FV-0001" class="{{ $input }}">
                            </div>

                            <div>
                                <label class="{{ $label }}">Ciclo productivo</label>
                                <select name="ciclo_productivo_id" class="{{ $input }}">
                                    <option value="">— Sin asignar —</option>
                                    @foreach ($ciclos as $c)
                                        <option value="{{ $c->id }}" @selected(old('ciclo_productivo_id') == $c->id)>{{$c->nombre_campana }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </section>

                    {{-- Ítems --}}
                    <section class="rounded-2xl border border-stone-200 bg-white shadow-sm">
                        <header class="flex items-center justify-between border-b border-stone-200 px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-lime-500"></span>
                                <h3 class="font-semibold text-emerald-900">Ítems</h3>
                                <span class="rounded-full bg-lime-100 px-2 py-0.5 text-xs font-semibold text-lime-800" x-text="items.length"></span>
                            </div>
                            <button type="button" @click="agregar()"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-lime-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-lime-700">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                Agregar ítem
                            </button>
                        </header>

                        <div class="divide-y divide-stone-100">
                            <template x-for="(item, i) in items" :key="i">
                                <div class="space-y-4 p-6 transition hover:bg-lime-50/30">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700" x-text="'Ítem #' + (i + 1)"></span>
                                        <button type="button" @click="quitar(i)" x-show="items.length > 1"
                                                class="text-xs font-medium text-red-500 hover:text-red-700">Quitar</button>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-12">
                                        <div class="sm:col-span-12" x-show="esInsumos">
                                            <label class="{{ $label }}">Insumo *</label>
                                            <select :name="`items[${i}][insumo_id]`" x-model="item.insumo_id" :required="esInsumos" class="{{ $input }}">
                                                <option value="">Selecciona…</option>
                                                @foreach ($insumos as $ins)
                                                    <option value="{{ $ins->id }}">{{ $ins->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="sm:col-span-4">
                                            <label class="{{ $label }}">Cantidad *</label>
                                            <input type="number" step="0.01" min="0.01" :name="`items[${i}][cantidad]`" x-model="item.cantidad" required class="{{ $input }}">
                                        </div>
                                        <div class="sm:col-span-4">
                                            <label class="{{ $label }}">Costo unitario *</label>
                                            <div class="relative">
                                                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-stone-400">$</span>
                                                <input type="number" step="0.01" min="0" :name="`items[${i}][costo_unitario]`" x-model="item.costo_unitario" required class="{{ $input }} pl-7">
                                            </div>
                                        </div>
                                        <div class="sm:col-span-4">
                                            <label class="{{ $label }}">Subtotal</label>
                                            <div class="rounded-lg border border-emerald-100 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800" x-text="fmt(lineaSubtotal(item))"></div>
                                        </div>

                                        <template x-if="esInsumos">
                                            <div class="grid grid-cols-1 gap-4 rounded-xl border border-dashed border-amber-200 bg-amber-50/50 p-4 sm:col-span-12 sm:grid-cols-2">
                                                <div>
                                                    <label class="{{ $label }}">Código de lote</label>
                                                    <input type="text" maxlength="100" :name="`items[${i}][codigo_lote]`" x-model="item.codigo_lote" placeholder="Se genera automáticamente si se deja vacío" class="{{ $input }}">
                                                </div>
                                                <div>
                                                    <label class="{{ $label }}">Fecha de vencimiento</label>
                                                    <input type="date" :name="`items[${i}][fecha_vencimiento]`" x-model="item.fecha_vencimiento" class="{{ $input }}">
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </section>

                    {{-- Observaciones --}}
                    <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                        <label class="{{ $label }}">Observaciones</label>
                        <textarea name="observaciones" rows="3" class="{{ $input }}" placeholder="Notas sobre la entrega, condiciones, etc.">{{ old('observaciones') }}</textarea>
                    </section>
                </div>

                {{-- Panel lateral --}}
                <aside class="space-y-6">
                    <section class="rounded-2xl border border-stone-200 bg-white shadow-sm">
                        <header class="flex items-center gap-2 border-b border-stone-200 px-6 py-4">
                            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                            <h3 class="font-semibold text-emerald-900">Condiciones de pago</h3>
                        </header>
                        <div class="space-y-4 p-6">
                            <div>
                                <label class="{{ $label }}">Plazo de pago (días)</label>
                                <input type="number" min="0" name="plazo_pago_dias" value="{{ old('plazo_pago_dias', 0) }}" class="{{ $input }}">
                            </div>
                            <div>
                                <label class="{{ $label }}">Descuento pronto pago</label>
                                <input type="number" step="0.01" min="0" name="descuento_pronto_pago" value="{{ old('descuento_pronto_pago', 0) }}" class="{{ $input }}">
                            </div>
                            <div>
                                <label class="{{ $label }}">IVA general (%)</label>
                                <input type="number" step="0.01" min="0" name="porcentaje_iva_general" x-model="iva" class="{{ $input }}">
                                <div class="mt-2 flex gap-2">
                                    <template x-for="p in [0, 5, 19]">
                                        <button type="button" @click="iva = p"
                                                :class="Number(iva) === p ? 'bg-emerald-600 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'"
                                                class="rounded-md px-2.5 py-1 text-xs font-semibold" x-text="p + '%'"></button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="sticky top-6 overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-800 to-emerald-950 text-white shadow-lg">
                        <div class="space-y-3 p-6">
                            <h3 class="text-sm font-semibold uppercase tracking-wider text-emerald-200">Resumen</h3>
                            <div class="flex justify-between text-sm"><span class="text-emerald-100">Subtotal</span><span x-text="fmt(subtotal)"></span></div>
                            <div class="flex justify-between text-sm"><span class="text-emerald-100">IVA (<span x-text="Number(iva) || 0"></span>%)</span><span x-text="fmt(ivaTotal)"></span></div>
                            <div class="border-t border-emerald-600/60 pt-3">
                                <div class="flex items-end justify-between">
                                    <span class="text-sm text-emerald-100">Total</span>
                                    <span class="text-2xl font-bold text-lime-300" x-text="fmt(total)"></span>
                                </div>
                            </div>
                            <p x-show="esInsumos" class="rounded-lg bg-white/10 p-3 text-xs text-emerald-100">
                                Se crearán lotes y entradas en el KARDEX por cada ítem.
                            </p>
                        </div>
                        <div class="bg-emerald-950/50 p-4">
                            <button type="submit"
                                    class="w-full rounded-lg bg-lime-400 px-4 py-3 text-sm font-bold text-emerald-950 shadow transition hover:bg-lime-300 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-offset-2 focus:ring-offset-emerald-900">
                                Registrar compra
                            </button>
                        </div>
                    </section>
                </aside>
            </form>
        </div>
    </div>

    <script>
        function compraForm(items, itemVacio, tipo, iva) {
            return {
                items: items,
                tipo: tipo,
                iva: iva,
                get esInsumos() { return this.tipo === 'insumos'; },
                agregar() { this.items.push({ ...itemVacio }); },
                quitar(i) { this.items.splice(i, 1); },
                lineaSubtotal(it) { return (parseFloat(it.cantidad) || 0) * (parseFloat(it.costo_unitario) || 0); },
                get subtotal() { return this.items.reduce((s, it) => s + this.lineaSubtotal(it), 0); },
                get ivaTotal() { return this.subtotal * ((parseFloat(this.iva) || 0) / 100); },
                get total() { return this.subtotal + this.ivaTotal; },
                fmt(v) { return '$ ' + new Intl.NumberFormat('es-CO', { maximumFractionDigits: 2 }).format(v || 0); },
            };
        }
    </script>
</x-app-layout>