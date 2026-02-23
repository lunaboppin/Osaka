<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-xl font-bold text-osaka-charcoal flex items-center">
                <svg class="w-5 h-5 mr-2 text-osaka-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Sticker Reminders
            </h2>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Stickers not checked in over <strong class="text-osaka-charcoal">{{ $overdueDays }} days </strong> need attention
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        {{-- Flash Message --}}
        @if(session('success'))
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700 flex items-center" x-data="{ show: true }" x-show="show" x-transition>
                <svg class="w-5 h-5 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
                <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        @endif

        {{-- Stats Row --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-stat-card
                :value="$totalNeedAttention"
                label="Need Attention"
                color="osaka-red"
                icon='<svg class="w-6 h-6 text-osaka-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>'
            />
            <x-stat-card
                :value="$overdueCount"
                label="Overdue ({{ $overdueDays }}+ days)"
                color="red-500"
                icon='<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>'
            />
            <x-stat-card
                :value="$warningCount"
                label="Warning ({{ $warningDays }}–{{ $overdueDays }} days)"
                color="amber-500"
                icon='<svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
            />
        </div>

        {{-- Configuration Panel --}}
        <div class="card" x-data="reminderConfig()">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-osaka-charcoal flex items-center">
                        <svg class="w-4 h-4 mr-2 text-osaka-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Configure Thresholds
                    </h3>
                    <button @click="expanded = !expanded" class="text-sm text-osaka-red hover:text-osaka-red-dark font-medium transition-colors">
                        <span x-text="expanded ? 'Hide' : 'Adjust'"></span>
                    </button>
                </div>

                <div x-show="expanded" x-transition class="space-y-5">
                    <form method="GET" action="{{ route('reminders.index') }}" class="space-y-5">
                        {{-- Overdue Threshold --}}
                        <div>
                            <label class="form-label flex items-center justify-between">
                                <span>Overdue after</span>
                                <span class="text-osaka-red font-bold" x-text="overdueDays + ' days'"></span>
                            </label>
                            <input type="range" name="overdue_days" x-model="overdueDays"
                                   min="{{ $defaults['min_days'] }}" max="{{ $defaults['max_days'] }}" step="1"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-osaka-red"
                                   @input="if(warningDays >= overdueDays) warningDays = Math.max({{ $defaults['min_days'] }}, overdueDays - 5)">
                            <div class="flex justify-between text-xs text-gray-400 mt-1">
                                <span>{{ $defaults['min_days'] }} days</span>
                                <span>{{ $defaults['max_days'] }} days</span>
                            </div>
                        </div>

                        {{-- Warning Threshold --}}
                        <div>
                            <label class="form-label flex items-center justify-between">
                                <span>Warning after</span>
                                <span class="text-amber-500 font-bold" x-text="warningDays + ' days'"></span>
                            </label>
                            <input type="range" name="warning_days" x-model="warningDays"
                                   min="{{ $defaults['min_days'] }}" :max="overdueDays - 1" step="1"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-amber-500">
                            <div class="flex justify-between text-xs text-gray-400 mt-1">
                                <span>{{ $defaults['min_days'] }} days</span>
                                <span x-text="(overdueDays - 1) + ' days'"></span>
                            </div>
                        </div>

                        {{-- Status Filter --}}
                        <div>
                            <label class="form-label">Filter by Status</label>
                            <select name="status" class="form-input-osaka sm:w-48">
                                <option value="all" {{ ($statusFilter ?? '') === 'all' || !$statusFilter ? 'selected' : '' }}>All Statuses</option>
                                <option value="New" {{ $statusFilter === 'New' ? 'selected' : '' }}>New</option>
                                <option value="Worn" {{ $statusFilter === 'Worn' ? 'selected' : '' }}>Worn</option>
                                <option value="Needs replaced" {{ $statusFilter === 'Needs replaced' ? 'selected' : '' }}>Needs Replaced</option>
                            </select>
                        </div>

                        <div class="flex items-center space-x-3">
                            <button type="submit" class="btn-primary btn-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Apply Thresholds
                            </button>
                            <a href="{{ route('reminders.index') }}" class="btn-secondary btn-sm">
                                Reset to Defaults
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Bulk Actions --}}
        @if($pins->count() > 0)
            <div x-data="bulkActions()" class="space-y-5">
                {{-- Bulk toolbar --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" @change="toggleAll($event)" class="rounded border-gray-300 text-osaka-red shadow-sm focus:ring-osaka-red">
                            <span class="ml-2 text-sm text-osaka-charcoal font-medium">Select All</span>
                        </label>
                        <span x-show="selected.length > 0" class="text-sm text-gray-500" x-text="selected.length + ' selected'"></span>
                    </div>
                    <form x-show="selected.length > 0" method="POST" action="{{ route('reminders.bulk-check') }}" x-transition>
                        @csrf
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="pin_ids[]" :value="id">
                        </template>
                        <button type="submit" class="btn-success btn-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Mark Selected as Checked
                        </button>
                    </form>
                </div>

                {{-- Pin List --}}
                <div class="space-y-3">
                    @foreach($pins as $pin)
                        @php
                            $urgency = $pin->urgencyWithThreshold($overdueDays, $warningDays);
                            $borderColor = $urgency === 'overdue' ? 'border-l-red-500' : 'border-l-amber-400';
                            $bgColor = $urgency === 'overdue' ? 'bg-red-50/50' : 'bg-amber-50/50';
                        @endphp
                        <div class="card border-l-4 {{ $borderColor }} {{ $bgColor }} hover:shadow-md transition-shadow">
                            <div class="p-4 sm:p-5">
                                <div class="flex items-start gap-4">
                                    {{-- Checkbox --}}
                                    <label class="mt-1 shrink-0">
                                        <input type="checkbox" value="{{ $pin->id }}" @change="togglePin({{ $pin->id }}, $event)" :checked="selected.includes({{ $pin->id }})"
                                               class="rounded border-gray-300 text-osaka-red shadow-sm focus:ring-osaka-red">
                                    </label>

                                    {{-- Photo thumbnail --}}
                                    <a href="{{ route('pins.show', $pin) }}" class="shrink-0">
                                        @if($pin->photo)
                                            <img src="{{ asset('storage/' . $pin->photo) }}" alt="{{ $pin->title }}" class="w-16 h-16 rounded-lg object-cover border border-gray-100">
                                        @else
                                            <div class="w-16 h-16 rounded-lg bg-osaka-cream flex items-center justify-center text-gray-300 border border-gray-100">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                        @endif
                                    </a>

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <a href="{{ route('pins.show', $pin) }}" class="font-semibold text-osaka-charcoal hover:text-osaka-red transition-colors truncate">
                                                {{ $pin->title ?: 'Untitled Pin' }}
                                            </a>
                                            <x-status-badge :status="$pin->status" />
                                        </div>

                                        <div class="flex items-center gap-4 mt-1.5 text-sm">
                                            {{-- Age indicator --}}
                                            <span class="inline-flex items-center {{ $urgency === 'overdue' ? 'text-red-600 font-semibold' : 'text-amber-600' }}">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ $pin->days_since_checked }} days ago
                                            </span>

                                            {{-- Last checked --}}
                                            <span class="text-gray-400 hidden sm:inline">
                                                {{ $pin->last_checked_at ? 'Checked ' . $pin->last_checked_at->format('M j, Y') : 'Never checked' }}
                                            </span>

                                            {{-- User --}}
                                            @if($pin->user)
                                                <span class="text-gray-400 hidden sm:inline-flex items-center">
                                                    @if($pin->user->avatar)
                                                        <img src="{{ $pin->user->avatar }}" class="w-4 h-4 rounded-full mr-1">
                                                    @endif
                                                    {{ $pin->user->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="shrink-0 flex items-center space-x-2">
                                        <form method="POST" action="{{ route('pins.check', $pin) }}">
                                            @csrf
                                            <button type="submit" class="btn-success btn-sm" title="Mark as checked">
                                                <svg class="w-4 h-4 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span class="hidden sm:inline">Checked</span>
                                            </button>
                                        </form>
                                        <a href="{{ route('pins.show', $pin) }}" class="btn-secondary btn-sm" title="View details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $pins->links() }}
                </div>
            </div>
        @else
            <x-empty-state
                title="All stickers are up to date!"
                message="No stickers need attention right now. Great work keeping everything checked!"
            />
        @endif
    </div>

    {{-- Legend --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <div class="card">
            <div class="card-body">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">How Reminders Work</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div class="flex items-start space-x-3">
                        <span class="w-3 h-3 mt-1 rounded-full bg-emerald-500 shrink-0"></span>
                        <div>
                            <div class="font-semibold text-osaka-charcoal">OK</div>
                            <div class="text-gray-500">Checked within the last {{ $warningDays }} days</div>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <span class="w-3 h-3 mt-1 rounded-full bg-amber-400 shrink-0"></span>
                        <div>
                            <div class="font-semibold text-osaka-charcoal">Warning</div>
                            <div class="text-gray-500">{{ $warningDays }}–{{ $overdueDays }} days since last check</div>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <span class="w-3 h-3 mt-1 rounded-full bg-red-500 shrink-0"></span>
                        <div>
                            <div class="font-semibold text-osaka-charcoal">Overdue</div>
                            <div class="text-gray-500">{{ $overdueDays }}+ days since last check</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function reminderConfig() {
        return {
            expanded: {{ request('overdue_days') || request('warning_days') ? 'true' : 'false' }},
            overdueDays: {{ $overdueDays }},
            warningDays: {{ $warningDays }},
        }
    }

    function bulkActions() {
        return {
            selected: [],
            togglePin(id, event) {
                if (event.target.checked) {
                    if (!this.selected.includes(id)) this.selected.push(id);
                } else {
                    this.selected = this.selected.filter(i => i !== id);
                }
            },
            toggleAll(event) {
                if (event.target.checked) {
                    this.selected = [{{ $pins->pluck('id')->join(',') }}];
                } else {
                    this.selected = [];
                }
            }
        }
    }
    </script>
</x-app-layout>
