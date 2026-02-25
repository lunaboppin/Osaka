@props(['badges', 'size' => 'sm'])

@php
    $sizeClasses = [
        'xs' => 'px-1.5 py-0.5 text-xs gap-0.5',
        'sm' => 'px-2 py-0.5 text-xs gap-1',
        'md' => 'px-2.5 py-1 text-sm gap-1.5',
    ];
    $badgeSize = $sizeClasses[$size] ?? $sizeClasses['sm'];
    $iconSize = $size === 'xs' ? 'w-3 h-3' : ($size === 'md' ? 'w-4 h-4' : 'w-3.5 h-3.5');
@endphp

@if(count($badges) > 0)
    <div class="flex flex-wrap gap-1.5">
        @foreach($badges as $badge)
            <span class="inline-flex items-center rounded-full font-medium text-white {{ $badgeSize }}"
                  style="background-color: {{ $badge['color'] }}">
                @if(($badge['icon'] ?? '') === 'star')
                    <svg class="{{ $iconSize }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                @elseif(($badge['icon'] ?? '') === 'map-pin')
                    <svg class="{{ $iconSize }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                @elseif(($badge['icon'] ?? '') === 'bolt')
                    <svg class="{{ $iconSize }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                @elseif(($badge['icon'] ?? '') === 'clock')
                    <svg class="{{ $iconSize }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                @elseif(($badge['icon'] ?? '') === 'user-check')
                    <svg class="{{ $iconSize }}" fill="currentColor" viewBox="0 0 20 20"><path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z"/><path fill-rule="evenodd" d="M16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" clip-rule="evenodd"/></svg>
                @endif
                {{ $badge['label'] }}
            </span>
        @endforeach
    </div>
@endif
