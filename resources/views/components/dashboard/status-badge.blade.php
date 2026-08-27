@props(['status'])

@php
    $colors = [
        'excelente' => 'bg-green-100 text-green-800',
        'con estrés' => 'bg-yellow-100 text-yellow-800',
        'enfermo crítico' => 'bg-red-100 text-red-800',
        'muerto' => 'bg-gray-200 text-gray-800',
        'erradicado' => 'bg-gray-300 text-gray-800',
        'crítica' => 'bg-red-100 text-red-800',
        'moderada' => 'bg-yellow-100 text-yellow-800',
        'leve' => 'bg-blue-100 text-blue-800',
        'pendiente' => 'bg-yellow-100 text-yellow-800',
        'realizado' => 'bg-green-100 text-green-800',
        'atrasado' => 'bg-red-100 text-red-800',
    ];
    $class = $colors[$status] ?? 'bg-gray-100 text-gray-800';
@endphp
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class }}">
    {{ ucfirst($status) }}
</span>