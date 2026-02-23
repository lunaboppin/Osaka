<x-app-layout>
    {{-- Hero / Welcome Section --}}
    <div class="bg-osaka-charcoal text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        @auth
                            Welcome back, <span class="text-osaka-gold">{{ Auth::user()->name }}</span>
                        @else
                            <span class="text-osaka-gold">Osaka</span> Sticker Tracker
                        @endauth
                    </h1>
                    <p class="mt-1 text-osaka-cream/60">
                        @auth
                            Track and monitor sticker conditions across the city.
                        @else
                            Log in to manage pins.
                        @endauth
                        @if(isset($currentStickerType) && $currentStickerType)
                            <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium text-white" style="background-color: {{ $currentStickerType->color }}">
                                {{ $currentStickerType->display_name }}
                            </span>
                        @endif
                    </p>
                </div>
                @auth
                    <a href="{{ route('pins.create') }}" class="btn-gold shrink-0">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Add New Pin
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-gold shrink-0">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        Login to Start
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        {{-- Stats Row --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <x-stat-card
                :value="$stats['total']"
                label="Total Pins"
                color="osaka-charcoal"
                icon='<svg class="w-6 h-6 text-osaka-charcoal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'
            />
            <x-stat-card
                :value="$stats['new']"
                label="New"
                color="emerald-600"
                icon='<svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
            />
            <x-stat-card
                :value="$stats['worn']"
                label="Worn"
                color="amber-500"
                icon='<svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>'
            />
            <x-stat-card
                :value="$stats['needs_replaced']"
                label="Needs Replaced"
                color="red-500"
                icon='<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
            />
            <x-stat-card
                :value="$stats['overdue']"
                label="Overdue"
                color="osaka-red"
                icon='<svg class="w-6 h-6 text-osaka-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>'
            />
        </div>

        {{-- Overdue Callout Banner --}}
        @auth
            @if($stats['overdue'] > 0)
                <div class="rounded-xl bg-gradient-to-r from-red-600 to-osaka-red p-5 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-lg">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </div>
                        <div>
                            <div class="font-bold text-lg">{{ $stats['overdue'] }} sticker{{ $stats['overdue'] !== 1 ? 's' : '' }} overdue for a check</div>
                            <div class="text-white/80 text-sm">These stickers haven't been verified in over {{ config('osaka.reminders.overdue_days') }} days.</div>
                        </div>
                    </div>
                    <a href="{{ route('reminders.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white text-osaka-red font-semibold rounded-lg hover:bg-osaka-cream transition-colors shrink-0 shadow">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        View Reminders
                    </a>
                </div>
            @endif
        @endauth

        {{-- Map Section --}}
        <div class="card">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-osaka-charcoal flex items-center">
                    <svg class="w-5 h-5 mr-2 text-osaka-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    Sticker Map
                </h2>
                {{-- Map Legend --}}
                <div class="flex items-center space-x-4 text-xs">
                    <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-emerald-500 mr-1.5"></span> New</span>
                    <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-amber-500 mr-1.5"></span> Worn</span>
                    <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-red-500 mr-1.5"></span> Needs Replaced</span>
                </div>
            </div>
            <div class="relative">
                <div id="map" class="w-full" style="height: 500px;"></div>
                {{-- Map loading spinner --}}
                <div id="map-loading" class="absolute inset-0 flex items-center justify-center bg-osaka-cream">
                    <div class="flex flex-col items-center">
                        <svg class="animate-spin h-10 w-10 text-osaka-red mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="text-sm text-gray-500">Loading map...</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Pins --}}
        @if($recentPins->count() > 0)
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-osaka-charcoal flex items-center">
                        <svg class="w-5 h-5 mr-2 text-osaka-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Recently Added
                    </h2>
                    @auth
                        <a href="{{ route('pins.index') }}" class="text-sm font-medium text-osaka-red hover:text-osaka-red-dark transition-colors">
                            View all &rarr;
                        </a>
                    @endauth
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($recentPins as $pin)
                        <x-pin-card :pin="$pin" />
                    @endforeach
                </div>
            </div>
        @else
            <x-empty-state
                title="No stickers tracked yet"
                message="Be the first to pin an Osaka sticker! Snap a photo and add it to the map."
                action="Add First Pin"
                :actionUrl="auth()->check() ? route('pins.create') : route('login')"
            />
        @endif
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initMap" async defer></script>
    <script>
        let map;
        let markers = [];
        let infoWindow;

        function initMap() {
            // Hide loading spinner
            const loadingEl = document.getElementById('map-loading');

            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 54.5973, lng: -5.9301 },
                zoom: 12,
                styles: [
                    { featureType: "poi", stylers: [{ visibility: "simplified" }] },
                    { featureType: "transit", stylers: [{ visibility: "simplified" }] },
                ],
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
            });

            infoWindow = new google.maps.InfoWindow();

            fetch('/pins/json')
                .then(response => response.json())
                .then(pins => {
                    if (loadingEl) loadingEl.style.display = 'none';

                    pins.forEach(pin => {
                        let color = '#10B981';
                        if (pin.status === 'Worn') color = '#F59E0B';
                        else if (pin.status === 'Needs replaced') color = '#EF4444';

                        const marker = new google.maps.Marker({
                            position: { lat: parseFloat(pin.latitude), lng: parseFloat(pin.longitude) },
                            map: map,
                            title: pin.title || '',
                            icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                scale: 10,
                                fillColor: color,
                                fillOpacity: 0.9,
                                strokeWeight: 2,
                                strokeColor: '#fff',
                            }
                        });

                        markers.push({ marker, status: pin.status });

                        marker.addListener('click', function() {
                            let photoHtml = '';
                            if (pin.photo) {
                                photoHtml = `<img src="/storage/${pin.photo}" alt="${pin.title || ''}" style="width:100%;max-height:180px;object-fit:cover;border-radius:8px;margin-bottom:8px;">`;
                            }
                            let userHtml = '';
                            if (pin.user && pin.user.name) {
                                const avatar = pin.user.avatar
                                    ? `<img src="${pin.user.avatar}" style="width:20px;height:20px;border-radius:50%;margin-right:6px;">`
                                    : `<span style="width:20px;height:20px;border-radius:50%;background:#D4A843;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#2D2D2D;margin-right:6px;">${pin.user.name.charAt(0).toUpperCase()}</span>`;
                                userHtml = `<div style="display:flex;align-items:center;font-size:12px;color:#64748B;margin-bottom:6px;">${avatar}${pin.user.name}</div>`;
                            }

                            const statusColors = { 'New': '#10B981', 'Worn': '#F59E0B', 'Needs replaced': '#EF4444' };
                            const statusBg = { 'New': '#ECFDF5', 'Worn': '#FFFBEB', 'Needs replaced': '#FEF2F2' };

                            const content = `
                                <div style="max-width:280px;font-family:Figtree,sans-serif;">
                                    ${photoHtml}
                                    ${userHtml}
                                    <div style="font-size:16px;font-weight:700;color:#2D2D2D;margin-bottom:4px;">${pin.title || 'Untitled'}</div>
                                    <span style="display:inline-block;padding:2px 10px;border-radius:999px;font-size:11px;font-weight:600;background:${statusBg[pin.status] || '#F3F4F6'};color:${statusColors[pin.status] || '#374151'};">${pin.status}</span>
                                    <div style="font-size:12px;color:#9CA3AF;margin-top:6px;">${pin.created_at ? new Date(pin.created_at).toLocaleDateString('en-GB', {day:'numeric',month:'short',year:'numeric'}) : ''}</div>
                                    <a href="/pins/${pin.id}" style="display:inline-block;margin-top:8px;font-size:12px;font-weight:600;color:#C41E3A;text-decoration:none;">View Details &rarr;</a>
                                </div>
                            `;
                            infoWindow.setContent(content);
                            infoWindow.open(map, marker);
                        });
                    });
                })
                .catch(() => {
                    if (loadingEl) loadingEl.innerHTML = '<span class="text-sm text-red-500">Failed to load pins</span>';
                });
        }
    </script>
</x-app-layout>
