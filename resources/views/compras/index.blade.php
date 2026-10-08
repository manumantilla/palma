<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-md shadow-emerald-600/30">
                    {{-- Icono hoja --}}
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 21c0-9 6-15 15-16-1 9-7 15-15 16zm0 0l7-7" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-emerald-900">Compras</h2>
                    <p class="text-sm text-stone-500">Registro de compras de insumos y servicios del campo</p>
                </div>
            </div>
            <a href="{{ route('compras.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Nueva compra
            </a>
        </div>
    </x-slot>

    @php
        $money = fn ($v) => '$ ' . number_format((float) $v, 0, ',', '.');
        $estadoPagoClases = [
            'pendiente' => 'bg-amber-100 text-amber-800 ring-amber-600/20',
            'parcial'   => 'bg-sky-100 text-sky-800 ring-sky-600/20',
            'pagada'    => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
            'pagado'    => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
            'vencida'   => 'bg-red-100 text-red-800 ring-red-600/20',
        ];
    @endphp

    <div class="min-h-screen bg-gradient-to-b from-lime-50/60 via-stone-50 to-stone-100 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Mensaje de éxito --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-sm">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="flex-1 text-sm font-medium">{{ session('success') }}</p>
                    <button @click="show = false" class="text-emerald-600 hover:text-emerald-800">&times;</button>
                </div>
            @endif

            {{-- Tarjetas resumen (página actual) --}}
            @php
                $pageTotal  = $compras->sum('total');
                $pagePagado = $compras->sum('pagos_sum_monto');
            @endphp
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Compras registradas</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-900">{{ $compras->total() }}</p>
                    <p class="mt-1 text-xs text-stone-400">en total</p>
                </div>
                <div class="rounded-2xl border border-lime-100 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Valor comprado</p>
                    <p class="mt-2 text-2xl font-bold text-lime-800">{{ $money($pageTotal) }}</p>
                    <p class="mt-1 text-xs text-stone-400">en esta página</p>
                </div>
                <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Saldo por pagar</p>
                    <p class="mt-2 text-2xl font-bold text-amber-700">{{ $money($pageTotal - $pagePagado) }}</p>
                    <p class="mt-1 text-xs text-stone-400">en esta página</p>
                </div>
            </div>

            {{-- Tabla --}}
            <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-stone-200 bg-emerald-900/[0.03] px-6 py-4">
                    <h3 class="font-semibold text-emerald-900">Historial de compras</h3>
                    <span class="text-xs text-stone-500">
                        Mostrando {{ $compras->firstItem() ?? 0 }}–{{ $compras->lastItem() ?? 0 }} de {{ $compras->total() }}
                    </span>
                </div>

                @if ($compras->isEmpty())
                    <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-lime-100 text-lime-700">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 5h12m-8 3a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z" />
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-stone-800">Aún no hay compras</h4>
                        <p class="mt-1 max-w-sm text-sm text-stone-500">Registra tu primera compra de insumos para empezar a llevar el KARDEX de tu finca.</p>
                        <a href="{{ route('compras.create') }}" class="mt-5 inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            Registrar compra
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-stone-200 text-sm">
                            <thead class="bg-stone-50 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                                <tr>
                                    <th class="px-6 py-3">Fecha</th>
                                    <th class="px-6 py-3">Factura</th>
                                    <th class="px-6 py-3">Proveedor</th>
                                    <th class="px-6 py-3">Tipo</th>
                                    <th class="px-6 py-3 text-right">Total</th>
                                    <th class="px-6 py-3 text-right">Pagado</th>
                                    <th class="px-6 py-3 text-right">Saldo</th>
                                    <th class="px-6 py-3 text-center">Estado pago</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                @foreach ($compras as $compra)
                                    @php
                                        $pagado = (float) ($compra->pagos_sum_monto ?? 0);
                                        $saldo  = (float) $compra->total - $pagado;
                                    @endphp
                                    <tr class="transition hover:bg-lime-50/50">
                                        <td class="whitespace-nowrap px-6 py-4 text-stone-700">
                                            {{ \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y') }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 font-mono text-xs text-stone-600">
                                            {{ $compra->numero_factura ?? 'S/N' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-stone-900">{{ $compra->proveedor->nombre ?? '—' }}</div>
                                            <div class="text-xs text-stone-400">Por {{ $compra->user->name ?? '—' }}</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span class="inline-flex items-center gap-1 rounded-md bg-lime-100 px-2 py-1 text-xs font-medium text-lime-800">
                                                {{ ucfirst(str_replace('_', ' ', $compra->tipo)) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right font-semibold text-stone-900">{{ $money($compra->total) }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-emerald-700">{{ $money($pagado) }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right font-medium {{ $saldo > 0 ? 'text-amber-700' : 'text-stone-400' }}">
                                            {{ $money($saldo) }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-center">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $estadoPagoClases[$compra->estado_pago] ?? 'bg-stone-100 text-stone-700 ring-stone-500/20' }}">
                                                {{ ucfirst($compra->estado_pago) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right">
                                            <a href="{{ route('compras.show', $compra->id) }}"
                                               class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-600 hover:text-white">
                                                Ver
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($compras->hasPages())
                        <div class="border-t border-stone-200 bg-stone-50 px-6 py-4">
                            {{ $compras->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>