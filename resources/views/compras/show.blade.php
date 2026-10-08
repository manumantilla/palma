<x-app-layout>
    @php
        $money   = fn ($v) => '$ ' . number_format((float) $v, 0, ',', '.');
        $pagado  = (float) ($compra->pagos_sum_monto ?? 0);
        $saldo   = (float) $compra->total - $pagado;
        $avance  = $compra->total > 0 ? min(100, round($pagado / $compra->total * 100)) : 0;
        $fecha   = \Carbon\Carbon::parse($compra->fecha);
        $vence   = $fecha->copy()->addDays((int) $compra->plazo_pago_dias);
        $estadoPagoClases = [
            'pendiente' => 'bg-amber-100 text-amber-800 ring-amber-600/20',
            'parcial'   => 'bg-sky-100 text-sky-800 ring-sky-600/20',
            'pagada'    => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
            'pagado'    => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
            'vencida'   => 'bg-red-100 text-red-800 ring-red-600/20',
        ];
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-md shadow-emerald-600/30">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h7l5 5v11a1 1 0 01-1 1H7a1 1 0 01-1-1V5a1 1 0 011-1z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-emerald-900">
                        Compra #{{ $compra->id }}
                        <span class="ml-1 font-mono text-base font-medium text-stone-500">· Factura {{ $compra->numero_factura ?? 'S/N' }}</span>
                    </h2>
                    <p class="text-sm text-stone-500">Registrada por {{ $compra->user->name ?? '—' }} el {{ $compra->created_at?->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <a href="{{ route('compras.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 hover:border-emerald-400 hover:text-emerald-700">
                &larr; Volver a compras
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-b from-lime-50/60 via-stone-50 to-stone-100 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Información general --}}
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Proveedor</p>
                    <p class="mt-2 font-semibold text-stone-900">{{ $compra->proveedor->nombre ?? '—' }}</p>
                </div>
                <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Fecha</p>
                    <p class="mt-2 font-semibold text-stone-900">{{ $fecha->format('d/m/Y') }}</p>
                </div>
                <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Tipo</p>
                    <span class="mt-2 inline-flex rounded-md bg-lime-100 px-2 py-1 text-sm font-semibold text-lime-800">
                        {{ ucfirst(str_replace('_', ' ', $compra->tipo)) }}
                    </span>
                </div>
                <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Estado</p>
                    <p class="mt-2 text-sm font-semibold text-emerald-700">{{ ucfirst(str_replace('_', ' ', $compra->estado)) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                {{-- Ítems --}}
                <div class="space-y-6 lg:col-span-2">
                    <section class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
                        <header class="flex items-center gap-2 border-b border-stone-200 px-6 py-4">
                            <span class="h-2 w-2 rounded-full bg-lime-500"></span>
                            <h3 class="font-semibold text-emerald-900">Ítems comprados</h3>
                            <span class="rounded-full bg-lime-100 px-2 py-0.5 text-xs font-semibold text-lime-800">{{ $compra->items->count() }}</span>
                        </header>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-stone-200 text-sm">
                                <thead class="bg-stone-50 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                                    <tr>
                                        <th class="px-6 py-3">#</th>
                                        <th class="px-6 py-3">Insumo / Concepto</th>
                                        <th class="px-6 py-3 text-right">Cantidad</th>
                                        <th class="px-6 py-3 text-right">Costo unit.</th>
                                        <th class="px-6 py-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-100">
                                    @forelse ($compra->items as $item)
                                        <tr class="hover:bg-lime-50/50">
                                            <td class="px-6 py-4 text-stone-400">{{ $loop->iteration }}</td>
                                            <td class="px-6 py-4 font-medium text-stone-900">{{ $item->insumo->nombre ?? 'Servicio / otro' }}</td>
                                            <td class="px-6 py-4 text-right text-stone-700">{{ rtrim(rtrim(number_format($item->cantidad, 2, ',', '.'), '0'), ',') }}</td>
                                            <td class="px-6 py-4 text-right text-stone-700">{{ $money($item->costo_unitario) }}</td>
                                            <td class="px-6 py-4 text-right font-semibold text-stone-900">{{ $money($item->subtotal) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="px-6 py-8 text-center text-stone-400">Sin ítems registrados.</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-stone-50 text-sm">
                                    <tr>
                                        <td colspan="4" class="px-6 py-2 text-right text-stone-500">Subtotal</td>
                                        <td class="px-6 py-2 text-right font-medium">{{ $money($compra->subtotal) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="px-6 py-2 text-right text-stone-500">IVA ({{ (float) $compra->porcentaje_iva_general }}%)</td>
                                        <td class="px-6 py-2 text-right font-medium">{{ $money($compra->iva_total) }}</td>
                                    </tr>
                                    <tr class="border-t border-stone-200">
                                        <td colspan="4" class="px-6 py-3 text-right font-semibold text-emerald-900">Total</td>
                                        <td class="px-6 py-3 text-right text-lg font-bold text-emerald-800">{{ $money($compra->total) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </section>

                    @if ($compra->observaciones)
                        <section class="rounded-2xl border border-amber-200 bg-amber-50/60 p-6">
                            <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-amber-800">Observaciones</h3>
                            <p class="whitespace-pre-line text-sm text-stone-700">{{ $compra->observaciones }}</p>
                        </section>
                    @endif
                </div>

                {{-- Pagos --}}
                <aside class="space-y-6">
                    <section class="overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-800 to-emerald-950 text-white shadow-lg">
                        <div class="space-y-4 p-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold uppercase tracking-wider text-emerald-200">Estado de pago</h3>
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $estadoPagoClases[$compra->estado_pago] ?? 'bg-stone-100 text-stone-700 ring-stone-500/20' }}">
                                    {{ ucfirst($compra->estado_pago) }}
                                </span>
                            </div>
                            <div>
                                <div class="mb-1 flex justify-between text-xs text-emerald-200">
                                    <span>Pagado</span><span>{{ $avance }}%</span>
                                </div>
                                <div class="h-2.5 overflow-hidden rounded-full bg-emerald-950/60">
                                    <div class="h-full rounded-full bg-lime-400" style="width: {{ $avance }}%"></div>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between"><span class="text-emerald-100">Total</span><span>{{ $money($compra->total) }}</span></div>
                                <div class="flex justify-between"><span class="text-emerald-100">Abonado</span><span class="text-lime-300">{{ $money($pagado) }}</span></div>
                                <div class="flex justify-between border-t border-emerald-600/60 pt-2">
                                    <span class="font-semibold">Saldo</span>
                                    <span class="text-xl font-bold {{ $saldo > 0 ? 'text-amber-300' : 'text-lime-300' }}">{{ $money($saldo) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-px bg-emerald-700/40 text-xs">
                            <div class="bg-emerald-950/60 p-3">
                                <p class="text-emerald-300">Plazo</p>
                                <p class="font-semibold">{{ (int) $compra->plazo_pago_dias }} días</p>
                            </div>
                            <div class="bg-emerald-950/60 p-3">
                                <p class="text-emerald-300">Vence</p>
                                <p class="font-semibold {{ $saldo > 0 && $vence->isPast() ? 'text-red-300' : '' }}">{{ $vence->format('d/m/Y') }}</p>
                            </div>
                            @if ((float) $compra->descuento_pronto_pago > 0)
                                <div class="col-span-2 bg-emerald-950/60 p-3">
                                    <p class="text-emerald-300">Descuento pronto pago</p>
                                    <p class="font-semibold">{{ (float) $compra->descuento_pronto_pago }}</p>
                                </div>
                            @endif
                        </div>
                    </section>

                    <section class="rounded-2xl border border-stone-200 bg-white shadow-sm">
                        <header class="flex items-center gap-2 border-b border-stone-200 px-6 py-4">
                            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                            <h3 class="font-semibold text-emerald-900">Historial de pagos</h3>
                        </header>
                        <ul class="divide-y divide-stone-100">
                            @forelse ($compra->pagos as $pago)
                                <li class="flex items-center justify-between px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <span class="text-sm text-stone-600">{{ \Carbon\Carbon::parse($pago->fecha ?? $pago->created_at)->format('d/m/Y') }}</span>
                                    </div>
                                    <span class="text-sm font-semibold text-emerald-700">{{ $money($pago->monto) }}</span>
                                </li>
                            @empty
                                <li class="px-6 py-8 text-center text-sm text-stone-400">Aún no se han registrado pagos.</li>
                            @endforelse
                        </ul>
                    </section>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout> 