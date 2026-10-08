<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8 bg-gradient-to-r from-amber-700 to-orange-700 rounded-2xl p-6 text-white shadow-xl">
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Editar Proveedor</h1>
            <p class="text-amber-200 text-sm mt-1">Actualiza la información del proveedor</p>
        </div>

        <!-- Errores -->
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

        <form action="{{ route('proveedor.update', $proveedor->id) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Datos básicos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nombre *</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $proveedor->nombre) }}" required
                           class="w-full rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 text-sm shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NIT *</label>
                    <input type="text" name="nit" value="{{ old('nit', $proveedor->nit) }}" required
                           class="w-full rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 text-sm shadow-sm">
                </div>
            </div>

            <!-- El resto de campos igual que en create, pero con `value="{{ old('campo', $proveedor->campo) }}"` -->
            <!-- ... (copia los mismos campos de create.blade.php cambiando el valor por defecto) ... -->

            <!-- Botones -->
            <div class="flex items-center justify-end space-x-4 border-t border-slate-200 pt-6">
                <a href="{{ route('proveedor.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-100 text-sm transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm shadow-lg shadow-amber-600/30 transition-all flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Actualizar Proveedor
                </button>
            </div>
        </form>
    </div>
</x-app-layout>s