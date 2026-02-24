<x-app-layout>
    <x-slot name="pageTitle">Leaderboard</x-slot>

    {{-- Hero Section --}}
    <div class="bg-osaka-charcoal text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight flex items-center gap-3">
                        <svg class="w-8 h-8 text-osaka-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        <span class="text-osaka-gold">Leaderboard</span>
                    </h1>
                    <p class="mt-1 text-osaka-cream/60">See who's contributing the most to the sticker map.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        {{-- Period Filter Tabs --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('leaderboard', ['period' => 'all']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $period === 'all' ? 'bg-osaka-charcoal text-osaka-gold shadow' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                All Time
            </a>
            <a href="{{ route('leaderboard', ['period' => 'month']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $period === 'month' ? 'bg-osaka-charcoal text-osaka-gold shadow' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                This Month
            </a>
            <a href="{{ route('leaderboard', ['period' => 'week']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $period === 'week' ? 'bg-osaka-charcoal text-osaka-gold shadow' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
                This Week
            </a>
        </div>

        {{-- Current User Rank Card --}}
        @auth
            @if($myRank)
                <div class="card">
                    <div class="card-body">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-osaka-gold to-osaka-gold-light flex items-center justify-center text-xl font-bold text-osaka-charcoal shadow">
                                    #{{ $myRank }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-osaka-charcoal">Your Rank</h3>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <x-level-badge :user="auth()->user()" size="xs" />
                                        <span class="text-sm text-gray-500">
                                            {{ number_format($period === 'all' ? auth()->user()->total_xp : ($myPeriodXp ?? auth()->user()->total_xp)) }} XP
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <x-xp-progress-bar :user="auth()->user()" class="max-w-xs" />
                        </div>
                    </div>
                </div>
            @endif
        @endauth

        {{-- Podium (Top 3) --}}
        @php
            $topUsers = $period === 'all'
                ? (is_object($users) && method_exists($users, 'items') ? collect($users->items()) : collect($users))->take(3)
                : collect($users)->take(3);
        @endphp

        @if($topUsers->count() >= 1)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($topUsers as $index => $topUser)
                    @php
                        $rankColors = [
                            0 => ['bg' => 'from-yellow-400 to-yellow-600', 'ring' => 'ring-yellow-400', 'icon' => '🥇', 'label' => '1st'],
                            1 => ['bg' => 'from-gray-300 to-gray-500', 'ring' => 'ring-gray-400', 'icon' => '🥈', 'label' => '2nd'],
                            2 => ['bg' => 'from-amber-600 to-amber-800', 'ring' => 'ring-amber-600', 'icon' => '🥉', 'label' => '3rd'],
                        ];
                        $colors = $rankColors[$index];
                        $userXp = $period === 'all' ? $topUser->total_xp : ($topUser->period_xp ?? $topUser->total_xp);
                    @endphp
                    <div class="card {{ $index === 0 ? 'ring-2 ' . $colors['ring'] : '' }}">
                        <div class="card-body text-center">
                            <div class="text-3xl mb-2">{{ $colors['icon'] }}</div>
                            @if($topUser->avatar)
                                <img src="{{ $topUser->avatar }}" alt="{{ $topUser->name }}" class="w-16 h-16 rounded-full mx-auto object-cover border-4 border-white shadow">
                            @else
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br {{ $colors['bg'] }} mx-auto flex items-center justify-center text-2xl font-bold text-white shadow">
                                    {{ strtoupper(substr($topUser->name, 0, 1)) }}
                                </div>
                            @endif
                            <h3 class="mt-3 font-bold text-osaka-charcoal">{{ $topUser->name }}</h3>
                            <div class="mt-1">
                                <x-level-badge :user="$topUser" size="xs" />
                            </div>
                            @if($topUser->roles->count())
                                <div class="mt-1">
                                    @foreach($topUser->roles->sortByDesc('priority')->take(1) as $role)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold text-white" style="background-color: {{ $role->color }}">
                                            {{ $role->display_name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="mt-2 text-2xl font-bold text-osaka-charcoal">{{ number_format($userXp) }}</div>
                            <div class="text-xs text-gray-400">XP</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Full Rankings Table --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">Rank</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Level</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">XP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $allUsers = $period === 'all'
                                ? (is_object($users) && method_exists($users, 'items') ? $users->items() : $users)
                                : $users;
                            $startRank = $period === 'all' && method_exists($users, 'firstItem') ? $users->firstItem() : 1;
                        @endphp
                        @forelse($allUsers as $index => $rankedUser)
                            @php
                                $rank = $startRank + $index;
                                $userXp = $period === 'all' ? $rankedUser->total_xp : ($rankedUser->period_xp ?? $rankedUser->total_xp);
                                $isCurrentUser = auth()->check() && auth()->id() === $rankedUser->id;
                            @endphp
                            <tr class="{{ $isCurrentUser ? 'bg-osaka-gold/5' : '' }} hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold
                                        {{ $rank <= 3 ? 'bg-gradient-to-br from-osaka-gold to-osaka-gold-light text-osaka-charcoal' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $rank }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($rankedUser->avatar)
                                            <img src="{{ $rankedUser->avatar }}" alt="" class="w-8 h-8 rounded-full object-cover border-2 {{ $isCurrentUser ? 'border-osaka-gold' : 'border-gray-200' }}">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-osaka-gold/20 flex items-center justify-center text-sm font-bold text-osaka-charcoal">
                                                {{ strtoupper(substr($rankedUser->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-medium text-osaka-charcoal {{ $isCurrentUser ? 'text-osaka-gold' : '' }}">
                                                {{ $rankedUser->name }}
                                                @if($isCurrentUser)
                                                    <span class="text-xs text-osaka-gold ml-1">(you)</span>
                                                @endif
                                            </div>
                                            @if($rankedUser->roles->count())
                                                <div class="flex gap-1 mt-0.5">
                                                    @foreach($rankedUser->roles->sortByDesc('priority')->take(1) as $role)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold text-white" style="background-color: {{ $role->color }}">
                                                            {{ $role->display_name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 hidden sm:table-cell">
                                    <x-level-badge :user="$rankedUser" size="xs" />
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <span class="font-bold text-osaka-charcoal">{{ number_format($userXp) }}</span>
                                    <span class="text-xs text-gray-400 ml-1">XP</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                    No XP activity for this period yet. Start contributing!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($period === 'all' && method_exists($users, 'links'))
            <div class="mt-4">
                {{ $users->appends(['period' => $period])->links() }}
            </div>
        @endif

        {{-- XP Values Info --}}
        <div class="card">
            <div class="card-body">
                <h3 class="text-lg font-bold text-osaka-charcoal mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-osaka-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    How XP Works
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach(config('osaka.xp.amounts') as $action => $amount)
                        @if($amount > 0)
                            <div class="flex items-center gap-2 text-sm">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs shrink-0">+{{ $amount }}</span>
                                <span class="text-gray-600">{{ str_replace('_', ' ', ucfirst($action)) }}</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2 text-sm">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-700 font-bold text-xs shrink-0">{{ $amount }}</span>
                                <span class="text-gray-600">{{ str_replace('_', ' ', ucfirst($action)) }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
