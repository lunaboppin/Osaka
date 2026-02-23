@props(['pin', 'showActions' => false])

<div {{ $attributes->merge(['class' => 'card group']) }}>
    {{-- Photo --}}
    <a href="{{ route('pins.show', $pin) }}" class="block relative overflow-hidden aspect-[4/3] bg-osaka-cream">
        @if($pin->photo)
            <img src="{{ asset('storage/' . $pin->photo) }}"
                 alt="{{ $pin->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-300">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif
        {{-- Status badge overlay --}}
        <div class="absolute top-3 left-3">
            <x-status-badge :status="$pin->status" />
        </div>
    </a>

    {{-- Content --}}
    <div class="card-body">
        <a href="{{ route('pins.show', $pin) }}" class="block">
            <h3 class="font-semibold text-osaka-charcoal text-lg leading-tight group-hover:text-osaka-red transition-colors truncate">
                {{ $pin->title ?: 'Untitled Pin' }}
            </h3>
        </a>

        @if($pin->description)
            <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $pin->description }}</p>
        @endif

        <div class="flex items-center justify-between mt-3">
            {{-- User --}}
            <div class="flex items-center space-x-2">
                @if($pin->user)
                    @if($pin->user->avatar)
                        <img src="{{ $pin->user->avatar }}" alt="{{ $pin->user->name }}" class="w-6 h-6 rounded-full">
                    @else
                        <div class="w-6 h-6 rounded-full bg-osaka-gold flex items-center justify-center text-xs font-bold text-osaka-charcoal">
                            {{ strtoupper(substr($pin->user->name ?? '?', 0, 1)) }}
                        </div>
                    @endif
                    <span class="text-xs text-gray-500">{{ $pin->user->name }}</span>
                @endif
            </div>

            {{-- Age / Date --}}
            @if($pin->urgency !== 'ok')
                <span class="inline-flex items-center text-xs font-medium {{ $pin->urgency === 'overdue' ? 'text-red-600' : 'text-amber-500' }}">
                    <svg class="w-3.5 h-3.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $pin->days_since_checked }}d
                </span>
            @else
                <span class="text-xs text-gray-400">{{ $pin->created_at?->diffForHumans() }}</span>
            @endif
        </div>

        {{-- Actions --}}
        @if($showActions && auth()->check() && $pin->user_id === auth()->id())
            <div class="flex items-center space-x-2 mt-3 pt-3 border-t border-gray-100">
                <a href="{{ route('pins.edit', $pin) }}" class="btn-secondary btn-sm flex-1 text-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
                <form method="POST" action="{{ route('pins.destroy', $pin) }}" onsubmit="return confirm('Delete this pin?');" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger btn-sm w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
