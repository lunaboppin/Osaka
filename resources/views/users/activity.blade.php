<x-app-layout>
    <x-slot name="pageTitle">{{ $user->name }}'s Activity</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-14 h-14 rounded-full object-cover border-4 border-osaka-gold/30 shadow">
                @else
                    <div class="w-14 h-14 rounded-full bg-osaka-gold flex items-center justify-center text-2xl font-bold text-osaka-charcoal border-4 border-osaka-gold/30 shadow">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-osaka-charcoal">{{ $user->name }}'s Activity</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <x-level-badge :user="$user" size="sm" />
                        @foreach($user->roles->sortByDesc('priority') as $role)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold text-white" style="background-color: {{ $role->color }}">
                                {{ $role->display_name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            <a href="{{ route('profile.show', $user) }}" class="btn-secondary text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Profile
            </a>
        </div>

        {{-- Level Progress Card --}}
        <div class="card">
            <div class="card-body">
                <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                    {{-- Level Display --}}
                    <div class="text-center shrink-0">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-osaka-gold to-osaka-gold-light flex items-center justify-center mx-auto shadow-lg">
                            <span class="text-2xl font-bold text-osaka-charcoal">{{ $stats['level'] }}</span>
                        </div>
                        <div class="mt-2 font-bold text-osaka-charcoal">{{ $stats['level_name'] }}</div>
                    </div>
                    {{-- Progress --}}
                    <div class="flex-1 w-full">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-600">Level {{ $stats['level'] }}</span>
                            <span class="text-sm text-gray-400">
                                @if($stats['next_level_threshold'])
                                    Level {{ $stats['level'] + 1 }} at {{ number_format($stats['next_level_threshold']) }} XP
                                @else
                                    Max Level Reached!
                                @endif
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                            <div class="bg-gradient-to-r from-osaka-gold to-osaka-gold-light h-4 rounded-full transition-all duration-500 ease-out"
                                 style="width: {{ round($stats['level_progress'] * 100) }}%">
                            </div>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-sm">
                            <span class="font-bold text-osaka-charcoal">{{ number_format($stats['total_xp']) }} XP</span>
                            @if($stats['xp_for_next_level'])
                                <span class="text-gray-400">{{ number_format($stats['xp_for_next_level']) }} XP to next level</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="card">
                <div class="card-body text-center py-4">
                    <div class="text-2xl font-bold text-osaka-charcoal">{{ $stats['total_pins'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Pins Created</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center py-4">
                    <div class="text-2xl font-bold text-osaka-charcoal">{{ $stats['total_updates'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Updates Posted</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center py-4">
                    <div class="text-2xl font-bold text-osaka-charcoal">{{ $stats['total_checks'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Pins Checked</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center py-4">
                    <div class="text-2xl font-bold text-osaka-charcoal">{{ $stats['total_photos'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Photos Added</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center py-4">
                    <div class="text-2xl font-bold text-osaka-charcoal">{{ $stats['days_active'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Days Active</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center py-4">
                    <div class="text-2xl font-bold {{ $stats['current_streak'] > 0 ? 'text-osaka-gold' : 'text-gray-400' }}">{{ $stats['current_streak'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Day Streak 🔥</div>
                </div>
            </div>
        </div>

        {{-- Action Breakdown --}}
        @if($actionBreakdown->count())
            <div class="card">
                <div class="card-body">
                    <h3 class="text-lg font-bold text-osaka-charcoal mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-osaka-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        XP Breakdown
                    </h3>
                    <div class="space-y-3">
                        @php $maxXp = $actionBreakdown->max('total_xp') ?: 1; @endphp
                        @foreach($actionBreakdown as $item)
                            @php
                                $actionLabels = [
                                    'pin_created' => ['label' => 'Pins Created', 'color' => 'bg-emerald-500', 'icon' => '📍'],
                                    'update_posted' => ['label' => 'Updates Posted', 'color' => 'bg-blue-500', 'icon' => '📝'],
                                    'photo_added' => ['label' => 'Photos Added', 'color' => 'bg-purple-500', 'icon' => '📷'],
                                    'pin_checked' => ['label' => 'Pins Checked', 'color' => 'bg-amber-500', 'icon' => '✅'],
                                    'pin_updated' => ['label' => 'Pins Updated', 'color' => 'bg-cyan-500', 'icon' => '✏️'],
                                    'pin_deleted' => ['label' => 'Pins Deleted', 'color' => 'bg-red-500', 'icon' => '🗑️'],
                                    'profile_completed' => ['label' => 'Profile Completed', 'color' => 'bg-pink-500', 'icon' => '👤'],
                                ];
                                $meta = $actionLabels[$item->action] ?? ['label' => ucfirst(str_replace('_', ' ', $item->action)), 'color' => 'bg-gray-500', 'icon' => '⭐'];
                                $pct = $maxXp > 0 ? round(($item->total_xp / $maxXp) * 100) : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="flex items-center gap-2 text-gray-700">
                                        <span>{{ $meta['icon'] }}</span>
                                        {{ $meta['label'] }}
                                        <span class="text-gray-400">&times;{{ $item->count }}</span>
                                    </span>
                                    <span class="font-bold {{ $item->total_xp >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $item->total_xp >= 0 ? '+' : '' }}{{ number_format($item->total_xp) }} XP
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="{{ $meta['color'] }} h-2 rounded-full transition-all" style="width: {{ max($pct, 2) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Milestones --}}
        @if(!empty($stats['milestones']))
            <div class="card">
                <div class="card-body">
                    <h3 class="text-lg font-bold text-osaka-charcoal mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-osaka-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        Milestones
                    </h3>
                    <div class="space-y-3">
                        @foreach($stats['milestones'] as $milestone)
                            <div class="flex items-center gap-4 p-3 rounded-lg bg-gradient-to-r from-osaka-gold/5 to-transparent">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-osaka-gold to-osaka-gold-light flex items-center justify-center text-sm font-bold text-osaka-charcoal shrink-0">
                                    {{ $milestone['level'] }}
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-osaka-charcoal">{{ $milestone['name'] }}</div>
                                    <div class="text-xs text-gray-400">Level {{ $milestone['level'] }} &middot; {{ number_format($milestone['xp_required']) }} XP required</div>
                                </div>
                                <div class="text-xs text-gray-400 shrink-0">
                                    {{ $milestone['reached_at']->format('j M Y') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Activity Timeline --}}
        <div class="card">
            <div class="card-body">
                <h3 class="text-lg font-bold text-osaka-charcoal mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-osaka-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Activity Feed
                </h3>
                @if($transactions->count())
                    <div class="space-y-1">
                        @foreach($transactions as $txn)
                            @php
                                $actionIcons = [
                                    'pin_created' => ['icon' => '📍', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
                                    'update_posted' => ['icon' => '📝', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'],
                                    'photo_added' => ['icon' => '📷', 'color' => 'text-purple-600', 'bg' => 'bg-purple-50'],
                                    'pin_checked' => ['icon' => '✅', 'color' => 'text-amber-600', 'bg' => 'bg-amber-50'],
                                    'pin_updated' => ['icon' => '✏️', 'color' => 'text-cyan-600', 'bg' => 'bg-cyan-50'],
                                    'pin_deleted' => ['icon' => '🗑️', 'color' => 'text-red-600', 'bg' => 'bg-red-50'],
                                    'profile_completed' => ['icon' => '👤', 'color' => 'text-pink-600', 'bg' => 'bg-pink-50'],
                                ];
                                $meta = $actionIcons[$txn->action] ?? ['icon' => '⭐', 'color' => 'text-gray-600', 'bg' => 'bg-gray-50'];
                                $isBackfilled = $txn->metadata['backfilled'] ?? false;
                            @endphp
                            <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors {{ $isBackfilled ? 'opacity-60' : '' }}">
                                <div class="w-8 h-8 rounded-full {{ $meta['bg'] }} flex items-center justify-center text-sm shrink-0">
                                    {{ $meta['icon'] }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm text-gray-700">
                                        {{ $txn->description ?? ucfirst(str_replace('_', ' ', $txn->action)) }}
                                        @if($isBackfilled)
                                            <span class="text-xs text-gray-400">(retroactive)</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ $txn->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="shrink-0 font-bold text-sm {{ $txn->xp_amount >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $txn->xp_amount >= 0 ? '+' : '' }}{{ $txn->xp_amount }} XP
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        No activity yet. Start contributing to earn XP!
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
