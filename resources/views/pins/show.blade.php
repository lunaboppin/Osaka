<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Flash Message --}}
        @if(session('success'))
            <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700 flex items-center" x-data="{ show: true }" x-show="show" x-transition>
                <svg class="w-5 h-5 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
                <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        @endif

        {{-- Back button --}}
        <div class="mb-4">
            <a href="{{ url()->previous() === url()->current() ? route('dashboard') : url()->previous() }}" class="inline-flex items-center text-sm font-medium text-osaka-red hover:text-osaka-red-dark transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
        </div>

        <div class="card overflow-hidden">
            {{-- Photo Section --}}
            @if($pin->photo)
                <div class="relative bg-osaka-charcoal">
                    <img src="{{ asset('storage/' . $pin->photo) }}"
                         alt="{{ $pin->title }}"
                         class="w-full max-h-[500px] object-contain mx-auto">
                    <div class="absolute top-4 left-4">
                        <x-status-badge :status="$pin->status" class="text-sm" />
                    </div>
                </div>
            @endif

            <div class="p-6 sm:p-8">
                {{-- Header --}}
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
                    <div class="flex-1">
                        @if(!$pin->photo)
                            <div class="mb-2">
                                <x-status-badge :status="$pin->status" class="text-sm" />
                            </div>
                        @endif
                        <h1 class="text-2xl sm:text-3xl font-bold text-osaka-charcoal">{{ $pin->title ?: 'Untitled Pin' }}</h1>
                        <div class="flex items-center mt-2 text-sm text-gray-500 space-x-4">
                            @if($pin->user)
                                <div class="flex items-center">
                                    @if($pin->user->avatar)
                                        <img src="{{ $pin->user->avatar }}" alt="{{ $pin->user->name }}" class="w-5 h-5 rounded-full mr-1.5">
                                    @else
                                        <div class="w-5 h-5 rounded-full bg-osaka-gold flex items-center justify-center text-[10px] font-bold text-osaka-charcoal mr-1.5">
                                            {{ strtoupper(substr($pin->user->name ?? '?', 0, 1)) }}
                                        </div>
                                    @endif
                                    {{ $pin->user->name }}
                                </div>
                            @endif
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $pin->created_at?->format('M j, Y \a\t g:ia') }}
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    @auth
                        @if($pin->user_id === auth()->id())
                            <div class="flex items-center space-x-2 shrink-0">
                                <a href="{{ route('pins.edit', $pin) }}" class="btn-secondary btn-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('pins.destroy', $pin) }}" onsubmit="return confirm('Are you sure you want to delete this pin?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>

                {{-- Description --}}
                @if($pin->description)
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-2">Description</h3>
                        <p class="text-osaka-charcoal leading-relaxed">{{ $pin->description }}</p>
                    </div>
                @endif

                {{-- Details Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="bg-osaka-cream rounded-lg p-4">
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Status</div>
                        <x-status-badge :status="$pin->status" />
                    </div>
                    <div class="bg-osaka-cream rounded-lg p-4">
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Coordinates</div>
                        <div class="text-sm text-osaka-charcoal font-mono">
                            {{ number_format($pin->latitude, 5) }}, {{ number_format($pin->longitude, 5) }}
                        </div>
                    </div>
                    <div class="bg-osaka-cream rounded-lg p-4">
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Last Checked</div>
                        @if($pin->last_checked_at)
                            <div class="text-sm text-osaka-charcoal">{{ $pin->last_checked_at->format('M j, Y') }}</div>
                            <div class="text-xs {{ $pin->urgency === 'overdue' ? 'text-red-600 font-semibold' : ($pin->urgency === 'warning' ? 'text-amber-500' : 'text-gray-400') }}">
                                {{ $pin->days_since_checked }} days ago
                            </div>
                        @else
                            <div class="text-sm text-gray-400">Never checked</div>
                        @endif
                        @auth
                            <form method="POST" action="{{ route('pins.check', $pin) }}" class="mt-2">
                                @csrf
                                <button type="submit" class="btn-success btn-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Mark Checked
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>

                {{-- Mini Map --}}
                <div class="rounded-lg overflow-hidden border border-gray-100">
                    <div id="detail-map" class="w-full" style="height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initDetailMap" async defer></script>
    <script>
        function initDetailMap() {
            const lat = {{ $pin->latitude }};
            const lng = {{ $pin->longitude }};
            const position = { lat, lng };

            const map = new google.maps.Map(document.getElementById('detail-map'), {
                center: position,
                zoom: 15,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
                zoomControl: true,
                styles: [
                    { featureType: "poi", stylers: [{ visibility: "simplified" }] },
                    { featureType: "transit", stylers: [{ visibility: "simplified" }] },
                ],
            });

            const statusColors = { 'New': '#10B981', 'Worn': '#F59E0B', 'Needs replaced': '#EF4444' };

            new google.maps.Marker({
                position: position,
                map: map,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 12,
                    fillColor: statusColors['{{ $pin->status }}'] || '#6B7280',
                    fillOpacity: 0.9,
                    strokeWeight: 3,
                    strokeColor: '#fff',
                }
            });
        }
    </script>
</x-app-layout>
