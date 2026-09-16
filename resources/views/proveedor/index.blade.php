<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8 bg-gradient-to-r from-indigo-800 to-purple-800 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Proveedores</h1>
                    <p class="text-indigo-200 text-sm mt-1">Gestión de proveedores de insumos y servicios</p>
                </div>
                <a href="{{ route('proveedor.create') }}" class="inline-flex items-center px-4 py-2 bg-white text-indigo-800 hover:bg-indigo-50 rounded-xl font-bold text-sm shadow-md transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nuevo Proveedor
                </a>
            </div>
        </div>

        <!-- Mensajes de éxito/error -->
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-medium text-emerald-800">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-medium text-red-800">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Tabla -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="py-3 px-6 text-xs font-bold text-slate-600 uppercase tracking-wider">Nombre</th>
                            <th class="py-3 px-6 text-xs font-bold text-slate-600 uppercase tracking-wider">NIT</th>
                            <th class="py-3 px-6 text-xs font-bold text-slate-600 uppercase tracking-wider">Contacto</th>
                            <th class="py-3 px-6 text-xs font-bold text-slate-600 uppercase tracking-wider">Teléfono</th>
                            <th class="py-3 px-6 text-xs font-bold text-slate-600 uppercase tracking-wider">Estado</th>
                            <th class="py-3 px-6 text-xs font-bold text-slate-600 uppercase tracking-wider text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($proveedores as $proveedor)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-6 font-medium text-slate-800">{{ $proveedor->nombre }}</td>
                                <td class="py-3 px-6 text-slate-600">{{ $proveedor->nit }}</td>
                                <td class="py-3 px-6 text-slate-600">{{ $proveedor->contacto_principal ?? '-' }}</td>
                                <td class="py-3 px-6 text-slate-600">{{ $proveedor->telefono ?? '-' }}</td>
                                <td class="py-3 px-6">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $proveedor->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $proveedor->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="py-3 px-6">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('proveedor.show', $proveedor->id) }}" class="text-slate-400 hover:text-indigo-600 transition-colors" title="Ver">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('proveedor.edit', $proveedor->id) }}" class="text-slate-400 hover:text-amber-600 transition-colors" title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('proveedor.destroy', $proveedor->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este proveedor?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors" title="Eliminar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-sm text-slate-400">No hay proveedores registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>