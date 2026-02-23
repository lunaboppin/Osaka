@props(['title' => 'Nothing here yet', 'message' => '', 'action' => null, 'actionUrl' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-16 px-6']) }}>
    <div class="w-24 h-24 rounded-full bg-osaka-cream flex items-center justify-center mb-6">
        <svg class="w-12 h-12 text-osaka-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    </div>
    <h3 class="text-lg font-semibold text-osaka-charcoal mb-2">{{ $title }}</h3>
    @if($message)
        <p class="text-gray-500 text-center max-w-sm mb-6">{{ $message }}</p>
    @endif
    @if($action && $actionUrl)
        <a href="{{ $actionUrl }}" class="btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            {{ $action }}
        </a>
    @endif
</div>
