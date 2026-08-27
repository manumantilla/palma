@props(['title', 'value', 'icon', 'subtitle' => null, 'change' => null, 'changeType' => null, 'color' => 'blue'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex items-start">
    <div class="flex-1">
        <p class="text-sm text-gray-500 font-medium">{{ $title }}</p>
        <p class="text-2xl font-bold mt-1">{{ $value }}</p>
        @if($subtitle)
            <p class="text-xs text-gray-400 mt-1">{{ $subtitle }}</p>
        @endif
        @if($change)
            <div class="flex items-center mt-2">
                <span class="text-xs font-medium {{ $changeType === 'positive' ? 'text-green-600' : ($changeType === 'negative' ? 'text-red-600' : 'text-gray-500') }}">
                    @if($changeType === 'positive') <i class="fas fa-arrow-up mr-1"></i> @endif
                    @if($changeType === 'negative') <i class="fas fa-arrow-down mr-1"></i> @endif
                    {{ $change }}
                </span>
            </div>
        @endif
    </div>
    <div class="ml-3 p-3 rounded-full bg-{{ $color }}-100 text-{{ $color }}-600">
        <i class="fas fa-{{ $icon }} text-xl"></i>
    </div>
</div>