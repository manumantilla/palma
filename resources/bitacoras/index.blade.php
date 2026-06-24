@extends('layouts.app')

@section('title', 'Listado de Bitácoras')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Bitácoras</h1>
        <a href="{{ route('bitacoras.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            + Nueva Bitácora
        </a>
    </div>

    <!-- Formulario de búsqueda -->
    <form method="GET" action="{{ route('bitacoras.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2">Título</label>
            <input type="text" name="titulo" value="{{ request('titulo') }}" class="w-full border rounded px-3 py-2" placeholder="Buscar por título">
        </div>
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2">Fecha</label>
            <input type="date" name="fecha" value="{{ request('fecha') }}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2">Registrado por</label>
            <input type="text" name="registrado_por" value="{{ request('registrado_por') }}" class="w-full border rounded px-3 py-2" placeholder="Nombre de usuario">
        </div>
        <div class="flex items-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded w-full">Buscar</button>
        </div>
    </form>

    <!-- Tabla de resultados -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border">
            <thead>
                <tr class="bg-gray-200 text-gray-700">
                    <th class="py-2 px-4 border">ID</th>
                    <th class="py-2 px-4 border">Título</th>
                    <th class="py-2 px-4 border">Tipo</th>
                    <th class="py-2 px-4 border">Prioridad</th>
                    <th class="py-2 px-4 border">Estado</th>
                    <th class="py-2 px-4 border">Registrado por</th>
                    <th class="py-2 px-4 border">Fecha</th>
                    <th class="py-2 px-4 border">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bitacoras as $bitacora)
                <tr class="hover:bg-gray-50">
                    <td class="py-2 px-4 border text-center">{{ $bitacora->id }}</td>
                    <td class="py-2 px-4 border">{{ $bitacora->titulo ?? 'Sin título' }}</td>
                    <td class="py-2 px-4 border">{{ ucfirst($bitacora->tipo) }}</td>
                    <td class="py-2 px-4 border">
                        <span class="px-2 py-1 rounded text-xs 
                            @if($bitacora->prioridad == 'baja') bg-green-200 text-green-800
                            @elseif($bitacora->prioridad == 'media') bg-yellow-200 text-yellow-800
                            @elseif($bitacora->prioridad == 'alta') bg-orange-200 text-orange-800
                            @else bg-red-200 text-red-800 @endif">
                            {{ ucfirst($bitacora->prioridad) }}
                        </span>
                    </td>
                    <td class="py-2 px-4 border">{{ ucfirst($bitacora->estado) }}</td>
                    <td class="py-2 px-4 border">{{ $bitacora->user->name ?? 'N/A' }}</td>
                    <td class="py-2 px-4 border">{{ $bitacora->created_at->format('d/m/Y') }}</td>
                    <td class="py-2 px-4 border text-center space-x-2">
                        <a href="{{ route('bitacoras.show', $bitacora) }}" class="text-blue-600 hover:underline">Ver</a>
                        <a href="{{ route('bitacoras.edit', $bitacora) }}" class="text-yellow-600 hover:underline">Editar</a>
                        <form action="{{ route('bitacoras.destroy', $bitacora) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar esta bitácora?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-gray-500">No se encontraron bitácoras.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $bitacoras->appends(request()->query())->links() }}
    </div>
</div>
@endsection