<x-app-layout>
    <x-slot name="pageTitle">{{ $user->name }}'s Profile</x-slot>

    @php
        $themeConfig = $user->theme_config;
    @endphp

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        {{-- Profile Header Card with Banner --}}
        <div class="card overflow-hidden {{ $themeConfig['card_border'] ?? 'border-osaka-gold/20' }}"
             @if($user->accent_color) style="border-color: {{ $user->accent_color }}33" @endif>
            {{-- Banner --}}
            <div class="relative h-36 sm:h-44 {{ $user->banner_url ? '' : ($themeConfig['banner_bg'] ?? 'bg-gradient-to-r from-osaka-charcoal to-osaka-charcoal-light') }}">
                @if($user->banner_url)
                    <img src="{{ $user->banner_url }}" alt="" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                @endif
            </div>

            <div class="card-body -mt-14 relative">
                <div class="flex flex-col sm:flex-row items-start gap-6">
                    {{-- Framed Avatar --}}
                    <div class="shrink-0">
                        <x-framed-avatar :user="$user" size="lg" />
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 pt-8 sm:pt-0">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="text-2xl font-bold text-osaka-charcoal">{{ $user->name }}</h1>
                            {{-- Role badges --}}
                            @foreach($user->roles->sortByDesc('priority') as $role)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold text-white" style="background-color: {{ $role->color }}">
                                    {{ $role->display_name }}
                                </span>
                            @endforeach
                        </div>

                        @if($user->bio)
                            <p class="mt-2 text-gray-600 leading-relaxed">{{ $user->bio }}</p>
                        @endif

                        {{-- Displayed badges --}}
                        @if(count($user->displayed_badge_details) > 0)
                            <div class="mt-3">
                                <x-profile-badges :badges="$user->displayed_badge_details" />
                            </div>
                        @endif

                        <div class="flex items-center gap-4 mt-3 text-sm text-gray-400">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Joined {{ $user->created_at?->format('j F Y') }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $pinStats['total'] }} {{ Str::plural('pin', $pinStats['total']) }}
                            </span>
                        </div>

                        @auth
                            @if(auth()->id() === $user->id)
                                <a href="{{ route('profile.edit') }}" class="inline-flex items-center mt-3 text-sm font-medium text-osaka-red hover:text-osaka-red-dark transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit Profile
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>

            {{-- Theme accent bar --}}
            <div class="h-1" style="background-color: {{ $user->effective_accent_color }}"></div>
        </div>

        {{-- XP & Level Card --}}
        <div class="card">
            <div class="card-body">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="flex items-center gap-4 flex-1">
                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-osaka-gold to-osaka-gold-light flex items-center justify-center text-xl font-bold text-osaka-charcoal shadow shrink-0">
                            {{ $user->level }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <x-level-badge :user="$user" size="sm" />
                                <span class="text-sm text-gray-500">{{ number_format($user->total_xp) }} XP</span>
                            </div>
                            <x-xp-progress-bar :user="$user" :showLabel="false" class="mt-2" />
                        </div>
                    </div>
                    @auth
                        <a href="{{ route('users.activity', $user) }}" class="btn-secondary text-sm shrink-0">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            View Activity
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Pin Statistics --}}
        <div>
            <h3 class="text-lg font-bold text-osaka-charcoal mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-osaka-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Pin Statistics
            </h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <x-stat-card
                    :value="$pinStats['total']"
                    label="Total Pins"
                    color="osaka-charcoal"
                    icon='<svg class="w-6 h-6 text-osaka-charcoal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'
                />
                <x-stat-card
                    :value="$pinStats['new']"
                    label="New"
                    color="emerald-600"
                    icon='<svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                />
                <x-stat-card
                    :value="$pinStats['worn']"
                    label="Worn"
                    color="amber-500"
                    icon='<svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>'
                />
                <x-stat-card
                    :value="$pinStats['needs_replaced']"
                    label="Needs Replaced"
                    color="red-500"
                    icon='<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                />
            </div>
        </div>

        {{-- Recent Pins --}}
        @if($recentPins->count() > 0)
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-bold text-osaka-charcoal flex items-center">
                        <svg class="w-5 h-5 mr-2 text-osaka-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Recent Pins
                    </h3>
                    <a href="{{ route('pins.index', ['user' => $user->id]) }}" class="text-sm font-medium text-osaka-red hover:text-osaka-red-dark transition-colors">
                        View all &rarr;
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($recentPins as $pin)
                        <x-pin-card :pin="$pin" />
                    @endforeach
                </div>
            </div>
        @else
            <x-empty-state
                title="No pins yet"
                message="{{ $user->name }} hasn't added any pins yet."
            />
        @endif
    </div>
</x-app-layout>
