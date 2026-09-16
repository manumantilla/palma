<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8 bg-gradient-to-r from-indigo-800 to-purple-800 rounded-2xl p-6 text-white shadow-xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">{{ $proveedor->nombre }}</h1>
                    <p class="text-indigo-200 text-sm mt-1">Detalles del proveedor</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('proveedor.edit', $proveedor->id) }}" class="inline-flex items-center px-4 py-2 bg-white text-indigo-800 hover:bg-indigo-50 rounded-xl font-bold text-sm shadow-md transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Editar
                    </a>
                    <a href="{{ route('proveedor.index') }}" class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl font-bold text-sm transition-colors">
                        Volver
                    </a>
                </div>
            </div>
        </div>

        <!-- Datos del proveedor -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 space-y-6">
                <!-- Información general -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">NIT</span>
                        <p class="text-slate-800 font-medium">{{ $proveedor->nit }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Categoría</span>
                        <p class="text-slate-800 font-medium">{{ $proveedor->categoria_principal ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Contacto</span>
                        <p class="text-slate-800 font-medium">{{ $proveedor->contacto_principal ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Teléfono</span>
                        <p class="text-slate-800 font-medium">{{ $proveedor->telefono ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Email</span>
                        <p class="text-slate-800 font-medium">{{ $proveedor->email ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Dirección</span>
                        <p class="text-slate-800 font-medium">{{ $proveedor->direccion ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Estado</span>
                        <p class="text-slate-800 font-medium">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $proveedor->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ $proveedor->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tiene crédito</span>
                        <p class="text-slate-800 font-medium">{{ $proveedor->tiene_credito ? 'Sí' : 'No' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Días de plazo</span>
                        <p class="text-slate-800 font-medium">{{ $proveedor->dias_plazo }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Límite de crédito</span>
                        <p class="text-slate-800 font-medium">${{ number_format($proveedor->limite_credito, 2) }}</p>
                    </div>
                </div>

                <!-- Cuentas bancarias -->
                <div class="border-t border-slate-100 pt-6">
                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Cuentas Bancarias</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Banco 1</span>
                            <p class="text-slate-800 font-medium">{{ $proveedor->banco_1 ?? '-' }}</p>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Cuenta</span>
                            <p class="text-slate-800 font-medium">{{ $proveedor->cuenta_bancaria_1 ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Banco 2</span>
                            <p class="text-slate-800 font-medium">{{ $proveedor->banco_2 ?? '-' }}</p>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Cuenta</span>
                            <p class="text-slate-800 font-medium">{{ $proveedor->cuenta_bancaria_2 ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Notas -->
                @if($proveedor->notas)
                    <div class="border-t border-slate-100 pt-6">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Notas</span>
                        <p class="text-slate-700 mt-1">{{ $proveedor->notas }}</p>
                    </div>
                @endif

                <!-- Lotes asociados (opcional) -->
                @if($proveedor->lotesInsumos->count() > 0)
                    <div class="border-t border-slate-100 pt-6">
                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Lotes de Insumos</h3>
                        <ul class="list-disc list-inside space-y-1 text-sm text-slate-600">
                            @foreach($proveedor->lotesInsumos as $lote)
                                <li>{{ $lote->codigo_lote }} - {{ $lote->insumo->nombre ?? 'N/A' }} (Stock: {{ $lote->cantidad_actual }})</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>