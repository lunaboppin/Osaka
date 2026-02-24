@props(['user', 'showLabel' => true, 'height' => 'h-2.5'])

@php
    $progress = $user->level_progress;
    $percentage = round($progress * 100);
    $currentXp = $user->total_xp ?? 0;
    $nextThreshold = $user->next_level_threshold;
    $currentThreshold = $user->current_level_threshold;
    $isMaxLevel = $nextThreshold === null;
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    @if($showLabel)
        <div class="flex items-center justify-between text-xs mb-1">
            <span class="font-medium text-gray-600">Level {{ $user->level }}</span>
            <span class="text-gray-400">
                @if($isMaxLevel)
                    {{ number_format($currentXp) }} XP &middot; Max Level
                @else
                    {{ number_format($currentXp) }} / {{ number_format($nextThreshold) }} XP
                @endif
            </span>
        </div>
    @endif
    <div class="w-full bg-gray-200 rounded-full {{ $height }} overflow-hidden">
        <div class="bg-gradient-to-r from-osaka-gold to-osaka-gold-light {{ $height }} rounded-full transition-all duration-500 ease-out"
             style="width: {{ $percentage }}%">
        </div>
    </div>
</div>
