@props(['user' => null, 'level' => null, 'levelName' => null, 'size' => 'sm'])

@php
    if ($user) {
        $level = $user->level;
        $levelName = $user->level_name;
    }

    $sizeClasses = match($size) {
        'xs' => 'px-1.5 py-0.5 text-[10px]',
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-1 text-sm',
        'lg' => 'px-3 py-1.5 text-base',
        default => 'px-2 py-0.5 text-xs',
    };

    $iconSize = match($size) {
        'xs' => 'w-3 h-3',
        'sm' => 'w-3.5 h-3.5',
        'md' => 'w-4 h-4',
        'lg' => 'w-5 h-5',
        default => 'w-3.5 h-3.5',
    };
@endphp

<span class="inline-flex items-center gap-1 rounded-full font-semibold bg-gradient-to-r from-osaka-gold to-osaka-gold-light text-osaka-charcoal {{ $sizeClasses }}">
    <svg class="{{ $iconSize }}" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
    </svg>
    Lvl {{ $level }} &middot; {{ $levelName }}
</span>
