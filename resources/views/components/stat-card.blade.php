@props(['value', 'label', 'icon', 'color' => 'osaka-red'])

<div class="card">
    <div class="card-body flex items-center space-x-4">
        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-{{ $color }}/10 flex items-center justify-center">
            {!! $icon !!}
        </div>
        <div>
            <div class="text-2xl font-bold text-osaka-charcoal">{{ $value }}</div>
            <div class="text-sm text-gray-500">{{ $label }}</div>
        </div>
    </div>
</div>
