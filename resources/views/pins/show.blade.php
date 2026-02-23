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
                                <a href="{{ route('profile.show', $pin->user) }}" class="flex items-center hover:text-osaka-red transition-colors">
                                    @if($pin->user->avatar)
                                        <img src="{{ $pin->user->avatar }}" alt="{{ $pin->user->name }}" class="w-5 h-5 rounded-full mr-1.5">
                                    @else
                                        <div class="w-5 h-5 rounded-full bg-osaka-gold flex items-center justify-center text-[10px] font-bold text-osaka-charcoal mr-1.5">
                                            {{ strtoupper(substr($pin->user->name ?? '?', 0, 1)) }}
                                        </div>
                                    @endif
                                    {{ $pin->user->name }}
                                </a>
                                </div>
                            @endif
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $pin->created_at?->format('j M Y \a\t H:i') }}
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
                            <div class="text-sm text-osaka-charcoal">{{ $pin->last_checked_at->format('j M Y') }}</div>
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

        {{-- Update Timeline --}}
        <div class="mt-8">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-bold text-osaka-charcoal flex items-center">
                    <svg class="w-5 h-5 mr-2 text-osaka-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Timeline
                    <span class="ml-2 text-sm font-normal text-gray-400">({{ $pin->updates->count() }} {{ Str::plural('update', $pin->updates->count()) }})</span>
                </h2>
            </div>

            {{-- Add Update Form --}}
            @auth
                <div class="card mb-6" x-data="{ open: false, photoPreview: null }">
                    <div class="card-body">
                        <button @click="open = !open" class="w-full flex items-center justify-between text-left">
                            <span class="font-semibold text-osaka-charcoal flex items-center">
                                <svg class="w-5 h-5 mr-2 text-osaka-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Add an Update
                            </span>
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <form method="POST" action="{{ route('pins.updates.store', $pin) }}" enctype="multipart/form-data"
                              x-show="open" x-transition class="mt-5 space-y-4">
                            @csrf

                            {{-- Current Status --}}
                            <div>
                                <label class="form-label">Current Status</label>
                                <div class="flex flex-wrap gap-3">
                                    @foreach(['New', 'Worn', 'Needs replaced'] as $status)
                                        @php
                                            $colors = ['New' => 'emerald', 'Worn' => 'amber', 'Needs replaced' => 'red'];
                                            $color = $colors[$status];
                                        @endphp
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="status" value="{{ $status }}" class="peer sr-only"
                                                   {{ $pin->status === $status ? 'checked' : '' }}>
                                            <div class="px-4 py-2 rounded-lg border-2 border-gray-200 text-sm font-medium text-gray-600
                                                        peer-checked:border-{{ $color }}-500 peer-checked:bg-{{ $color }}-50 peer-checked:text-{{ $color }}-700
                                                        hover:border-gray-300 transition-all">
                                                {{ $status }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Photo --}}
                            <div>
                                <label class="form-label">Photo</label>
                                <div class="flex items-start gap-4">
                                    <label class="flex-1 cursor-pointer">
                                        <div class="border-2 border-dashed border-gray-200 rounded-lg p-4 text-center hover:border-osaka-gold hover:bg-osaka-cream/30 transition-all">
                                            <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span class="text-sm text-gray-500">Click to upload a photo</span>
                                        </div>
                                        <input type="file" name="photo" accept="image/*" capture="environment" class="hidden"
                                               @change="const f = $event.target.files[0]; if(f) { const r = new FileReader(); r.onload = e => photoPreview = e.target.result; r.readAsDataURL(f); }">
                                    </label>
                                    <template x-if="photoPreview">
                                        <div class="relative shrink-0">
                                            <img :src="photoPreview" class="w-20 h-20 rounded-lg object-cover border border-gray-200">
                                            <button type="button" @click="photoPreview = null; $el.closest('form').querySelector('input[type=file]').value = ''"
                                                    class="absolute -top-2 -right-2 w-5 h-5 rounded-full bg-red-500 text-white flex items-center justify-center text-xs hover:bg-red-600">
                                                &times;
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                @error('photo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Notes --}}
                            <div>
                                <label class="form-label">Notes <span class="font-normal text-gray-400">(optional)</span></label>
                                <textarea name="notes" rows="2" class="form-input-osaka" placeholder="What's the condition like? Any changes since last time?">{{ old('notes') }}</textarea>
                                @error('notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <button type="submit" class="btn-primary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Post Update
                            </button>
                        </form>
                    </div>
                </div>
            @endauth

            {{-- Timeline Entries --}}
            @if($pin->updates->count() > 0)
                <div class="relative">
                    {{-- Timeline line --}}
                    <div class="absolute left-[19px] top-0 bottom-0 w-0.5 bg-gray-200"></div>

                    <div class="space-y-6">
                        @foreach($pin->updates as $update)
                            <div class="relative flex gap-4">
                                {{-- Timeline dot --}}
                                <div class="relative z-10 shrink-0 mt-1">
                                    @php
                                        $dotColor = match($update->status) {
                                            'New' => 'bg-emerald-500',
                                            'Worn' => 'bg-amber-500',
                                            'Needs replaced' => 'bg-red-500',
                                            default => 'bg-gray-400',
                                        };
                                    @endphp
                                    <div class="w-10 h-10 rounded-full {{ $dotColor }} flex items-center justify-center border-4 border-white shadow-sm">
                                        @if($update->photo)
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        @else
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        @endif
                                    </div>
                                </div>

                                {{-- Card --}}
                                <div class="flex-1 card min-w-0">
                                    {{-- Photo --}}
                                    @if($update->photo)
                                        <div class="relative bg-osaka-charcoal overflow-hidden">
                                            <img src="{{ asset('storage/' . $update->photo) }}"
                                                 alt="Update photo"
                                                 class="max-w-full max-h-[300px] object-contain mx-auto block cursor-pointer"
                                                 @click="$dispatch('open-lightbox', { src: '{{ asset('storage/' . $update->photo) }}' })">
                                        </div>
                                    @endif

                                    <div class="p-4">
                                        {{-- Header row --}}
                                        <div class="flex items-center justify-between gap-2 flex-wrap">
                                            <div class="flex items-center gap-2">
                                                <x-status-badge :status="$update->status" />
                                                @if($loop->first)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-osaka-gold/20 text-osaka-gold">Latest</span>
                                                @endif
                                                @if($loop->last && $pin->updates->count() > 1)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Original</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-3 text-xs text-gray-400">
                                                <span>{{ $update->created_at->format('j M Y \a\t H:i') }}</span>
                                                <span>{{ $update->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>

                                        {{-- Notes --}}
                                        @if($update->notes)
                                            <p class="mt-2 text-sm text-osaka-charcoal leading-relaxed">{{ $update->notes }}</p>
                                        @endif

                                        {{-- Footer --}}
                                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
                                            <div class="flex items-center text-xs text-gray-400">
                                                @if($update->user)
                                                    @if($update->user->avatar)
                                                        <img src="{{ $update->user->avatar }}" class="w-5 h-5 rounded-full mr-1.5">
                                                    @else
                                                        <div class="w-5 h-5 rounded-full bg-osaka-gold flex items-center justify-center text-[10px] font-bold text-osaka-charcoal mr-1.5">
                                                            {{ strtoupper(substr($update->user->name ?? '?', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    {{ $update->user->name }}
                                                @else
                                                    <span class="text-gray-300">Unknown user</span>
                                                @endif
                                            </div>

                                            {{-- Delete button for update owner / pin owner --}}
                                            @auth
                                                @if($update->user_id === auth()->id() || $pin->user_id === auth()->id())
                                                    @if($pin->updates->count() > 1)
                                                        <form method="POST" action="{{ route('pins.updates.destroy', [$pin, $update]) }}"
                                                              onsubmit="return confirm('Remove this timeline entry?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-xs text-gray-300 hover:text-red-500 transition-colors">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <x-empty-state
                    title="No updates yet"
                    message="Post the first update to start tracking this sticker's history."
                />
            @endif
        </div>
    </div>

    {{-- Lightbox for full-size photos --}}
    <div x-data="{ open: false, src: '' }"
         @open-lightbox.window="src = $event.detail.src; open = true"
         @keydown.escape.window="open = false">
        <template x-if="open">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4" @click.self="open = false" x-transition>
                <button @click="open = false" class="absolute top-4 right-4 text-white/80 hover:text-white">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <img :src="src" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl" @click.stop>
            </div>
        </template>
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
