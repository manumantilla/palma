@extends('layouts.app')

@section('title', 'Ver Bitácora')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-3xl mx-auto">
    <div class="flex justify-between items-start mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Detalle de Bitácora</h1>
        <div class="space-x-2">
            <a href="{{ route('bitacoras.edit', $bitacora) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-sm">Editar</a>
            <a href="{{ route('bitacoras.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-1 px-3 rounded text-sm">Volver</a>
        </div>
    </div>

    <div class="border-t pt-4">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="font-semibold text-gray-600">ID</dt>
                <dd class="mt-1">{{ $bitacora->id }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-600">Título</dt>
                <dd class="mt-1">{{ $bitacora->titulo ?? 'Sin título' }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-600">Tipo</dt>
                <dd class="mt-1">{{ ucfirst($bitacora->tipo) }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-600">Prioridad</dt>
                <dd class="mt-1">
                    <span class="px-2 py-1 rounded text-xs 
                        @if($bitacora->prioridad == 'baja') bg-green-200 text-green-800
                        @elseif($bitacora->prioridad == 'media') bg-yellow-200 text-yellow-800
                        @elseif($bitacora->prioridad == 'alta') bg-orange-200 text-orange-800
                        @else bg-red-200 text-red-800 @endif">
                        {{ ucfirst($bitacora->prioridad) }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-600">Estado</dt>
                <dd class="mt-1">{{ ucfirst($bitacora->estado) }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-600">Registrado por</dt>
                <dd class="mt-1">{{ $bitacora->user->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-600">Fecha creación</dt>
                <dd class="mt-1">{{ $bitacora->created_at->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-600">Última actualización</dt>
                <dd class="mt-1">{{ $bitacora->updated_at->format('d/m/Y H:i') }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="font-semibold text-gray-600">Contenido</dt>
                <dd class="mt-1 bg-gray-50 p-3 rounded">{{ nl2br(e($bitacora->contenido)) }}</dd>
            </div>
            @if($bitacora->archivo_adjunto)
            <div class="md:col-span-2">
                <dt class="font-semibold text-gray-600">Archivo adjunto</dt>
                <dd class="mt-1"><a href="{{ asset($bitacora->archivo_adjunto) }}" target="_blank" class="text-blue-600 hover:underline">Ver archivo</a></dd>
            </div>
            @endif
            <div class="md:col-span-2">
                <dt class="font-semibold text-gray-600">Entidad asociada</dt>
                <dd class="mt-1">{{ $bitacora->bitacorable_type }} #{{ $bitacora->bitacorable_id }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection