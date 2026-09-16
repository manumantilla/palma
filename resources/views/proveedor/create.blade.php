<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8 bg-gradient-to-r from-indigo-800 to-purple-800 rounded-2xl p-6 text-white shadow-xl">
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Nuevo Proveedor</h1>
            <p class="text-indigo-200 text-sm mt-1">Registra un nuevo proveedor de insumos o servicios</p>
        </div>

        <!-- Errores de validación -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="text-sm font-bold text-red-800">Corrige los siguientes errores:</h3>
                </div>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario -->
        <form action="{{ route('proveedor.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-6">
            @csrf

            <!-- Datos básicos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nombre *</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required
                           class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NIT *</label>
                    <input type="text" name="nit" value="{{ old('nit') }}" required
                           class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Categoría Principal</label>
                    <input type="text" name="categoria_principal" value="{{ old('categoria_principal') }}"
                           class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Contacto Principal</label>
                    <input type="text" name="contacto_principal" value="{{ old('contacto_principal') }}"
                           class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}"
                           class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Dirección</label>
                <input type="text" name="direccion" value="{{ old('direccion') }}"
                       class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
            </div>

            <!-- Condiciones de crédito -->
            <div class="border-t border-slate-100 pt-6">
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Información de Crédito</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Días de Plazo</label>
                        <input type="number" name="dias_plazo" value="{{ old('dias_plazo', 0) }}" min="0"
                               class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Límite de Crédito</label>
                        <input type="number" step="0.01" name="limite_credito" value="{{ old('limite_credito') }}" min="0"
                               class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                    </div>
                    <div class="flex items-end space-x-4 pb-1">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="tiene_credito" value="1" {{ old('tiene_credito') ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-slate-700">Tiene crédito</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="activo" value="1" {{ old('activo', true) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-slate-700">Activo</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Cuentas bancarias -->
            <div class="border-t border-slate-100 pt-6">
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Cuentas Bancarias</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Banco 1</label>
                        <input type="text" name="banco_1" value="{{ old('banco_1') }}"
                               class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Cuenta Bancaria 1</label>
                        <input type="text" name="cuenta_bancaria_1" value="{{ old('cuenta_bancaria_1') }}"
                               class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Banco 2</label>
                        <input type="text" name="banco_2" value="{{ old('banco_2') }}"
                               class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Cuenta Bancaria 2</label>
                        <input type="text" name="cuenta_bancaria_2" value="{{ old('cuenta_bancaria_2') }}"
                               class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">
                    </div>
                </div>
            </div>

            <!-- Notas -->
            <div class="border-t border-slate-100 pt-6">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Notas</label>
                <textarea name="notas" rows="3"
                          class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm shadow-sm">{{ old('notas') }}</textarea>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end space-x-4 border-t border-slate-200 pt-6">
                <a href="{{ route('proveedor.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-100 text-sm transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition-all flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Guardar Proveedor
                </button>
            </div>
        </form>
    </div>
</x-app-layout>