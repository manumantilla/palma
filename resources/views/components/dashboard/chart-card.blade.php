@props(['title', 'id', 'height' => 'h-64', 'empty' => false])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ $title }}</h3>
    @if($empty)
        <div class="flex items-center justify-center {{ $height }} text-gray-400 text-sm">
            No existen datos suficientes para este período.
        </div>
    @else
        <canvas id="{{ $id }}" class="{{ $height }} w-full"></canvas>
    @endif
</div>