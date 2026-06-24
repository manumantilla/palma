@extends('layouts.app')

@section('title', 'Nueva Bitácora')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Crear Bitácora</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('bitacoras.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Tipo de entidad asociada (bitacorable_type)</label>
            <input type="text" name="bitacorable_type" value="{{ old('bitacorable_type') }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">ID de entidad (bitacorable_id)</label>
            <input type="number" name="bitacorable_id" value="{{ old('bitacorable_id') }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Tipo de bitácora</label>
            <select name="tipo" class="w-full border rounded px-3 py-2" required>
                <option value="observacion">Observación</option>
                <option value="alerta">Alerta</option>
                <option value="incidente">Incidente</option>
                <option value="decision">Decisión</option>
                <option value="condicion_clima">Condición clima</option>
                <option value="visita_tecnica">Visita técnica</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Prioridad</label>
            <select name="prioridad" class="w-full border rounded px-3 py-2" required>
                <option value="baja">Baja</option>
                <option value="media">Media</option>
                <option value="alta">Alta</option>
                <option value="critica">Crítica</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Título</label>
            <input type="text" name="titulo" value="{{ old('titulo') }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Contenido</label>
            <textarea name="contenido" rows="5" class="w-full border rounded px-3 py-2" required>{{ old('contenido') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Estado</label>
            <select name="estado" class="w-full border rounded px-3 py-2" required>
                <option value="abierto">Abierto</option>
                <option value="en_proceso">En proceso</option>
                <option value="resuelto">Resuelto</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Archivo adjunto (ruta)</label>
            <input type="text" name="archivo_adjunto" value="{{ old('archivo_adjunto') }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('bitacoras.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Guardar</button>
        </div>
    </form>
</div>
@endsection