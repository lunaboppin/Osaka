@props(['user', 'size' => 'md', 'showFrame' => true, 'class' => ''])

@php
    $sizes = [
        'xs' => 'w-6 h-6 text-xs',
        'sm' => 'w-8 h-8 text-sm',
        'md' => 'w-12 h-12 text-lg',
        'lg' => 'w-24 h-24 text-3xl',
        'xl' => 'w-32 h-32 text-4xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $frameConfig = $showFrame ? $user->avatar_frame_config : null;
    $borderClass = $frameConfig ? $frameConfig['border_class'] : 'border-4 border-osaka-gold/30';
    $ringClass = $frameConfig ? ($frameConfig['ring_class'] ?? '') : '';
@endphp

<div class="relative inline-flex shrink-0 {{ $class }}">
    @if($user->avatar)
        <img src="{{ $user->avatar }}"
             alt="{{ $user->name }}"
             class="rounded-full object-cover shadow {{ $sizeClass }} {{ $borderClass }} {{ $ringClass }}">
    @else
        <div class="rounded-full bg-osaka-gold flex items-center justify-center font-bold text-osaka-charcoal shadow {{ $sizeClass }} {{ $borderClass }} {{ $ringClass }}">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
    @endif
</div>
