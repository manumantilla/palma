@extends('layouts.app')

@section('title', 'Editar Bitácora')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Editar Bitácora</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('bitacoras.update', $bitacora) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Tipo de entidad asociada</label>
            <input type="text" name="bitacorable_type" value="{{ old('bitacorable_type', $bitacora->bitacorable_type) }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">ID de entidad</label>
            <input type="number" name="bitacorable_id" value="{{ old('bitacorable_id', $bitacora->bitacorable_id) }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Tipo</label>
            <select name="tipo" class="w-full border rounded px-3 py-2" required>
                @foreach(['observacion','alerta','incidente','decision','condicion_clima','visita_tecnica'] as $tipo)
                    <option value="{{ $tipo }}" @selected(old('tipo', $bitacora->tipo) == $tipo)>{{ ucfirst($tipo) }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Prioridad</label>
            <select name="prioridad" class="w-full border rounded px-3 py-2" required>
                @foreach(['baja','media','alta','critica'] as $prioridad)
                    <option value="{{ $prioridad }}" @selected(old('prioridad', $bitacora->prioridad) == $prioridad)>{{ ucfirst($prioridad) }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Título</label>
            <input type="text" name="titulo" value="{{ old('titulo', $bitacora->titulo) }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Contenido</label>
            <textarea name="contenido" rows="5" class="w-full border rounded px-3 py-2" required>{{ old('contenido', $bitacora->contenido) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Estado</label>
            <select name="estado" class="w-full border rounded px-3 py-2" required>
                @foreach(['abierto','en_proceso','resuelto'] as $estado)
                    <option value="{{ $estado }}" @selected(old('estado', $bitacora->estado) == $estado)>{{ ucfirst($estado) }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Archivo adjunto (ruta)</label>
            <input type="text" name="archivo_adjunto" value="{{ old('archivo_adjunto', $bitacora->archivo_adjunto) }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('bitacoras.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">Cancelar</a>
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">Actualizar</button>
        </div>
    </form>
</div>
@endsection