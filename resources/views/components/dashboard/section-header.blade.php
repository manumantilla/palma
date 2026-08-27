@props(['title', 'icon' => null])

<div class="flex items-center space-x-2 mb-4">
    @if($icon)<i class="fas fa-{{ $icon }} text-green-600 text-lg"></i>@endif
    <h2 class="text-xl font-semibold text-gray-800">{{ $title }}</h2>
</div>