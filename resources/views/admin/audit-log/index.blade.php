<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-osaka-charcoal flex items-center">
            <svg class="w-5 h-5 mr-2 text-osaka-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            Audit Log
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Filters --}}
        <div class="card mb-6">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.audit-log.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search descriptions..." class="form-input-osaka w-full text-sm">
                    </div>
                    <div>
                        <select name="action" class="form-input-osaka text-sm w-full sm:w-auto">
                            <option value="">All Actions</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $action)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="model" class="form-input-osaka text-sm w-full sm:w-auto">
                            <option value="">All Models</option>
                            @foreach($modelTypes as $type)
                                <option value="{{ $type }}" {{ request('model') === $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="user" class="form-input-osaka text-sm w-full sm:w-auto">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary btn-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'action', 'model', 'user']))
                            <a href="{{ route('admin.audit-log.index') }}" class="btn-secondary btn-sm">Clear</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Results count --}}
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-gray-500">
                {{ $logs->total() }} {{ Str::plural('entry', $logs->total()) }}
                @if(request()->hasAny(['search', 'action', 'model', 'user']))
                    <span class="text-gray-400">(filtered)</span>
                @endif
            </p>
        </div>

        {{-- Audit Log Entries --}}
        @if($logs->count() > 0)
            <div class="space-y-2">
                @foreach($logs as $log)
                    <div class="card" x-data="{ expanded: false }">
                        <div class="card-body">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3 min-w-0 flex-1">
                                    {{-- User avatar --}}
                                    <div class="shrink-0">
                                        @if($log->user)
                                            @if($log->user->avatar)
                                                <img src="{{ $log->user->avatar }}" alt="" class="w-8 h-8 rounded-full border border-gray-200">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-osaka-gold flex items-center justify-center text-xs font-bold text-osaka-charcoal">
                                                    {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-xs font-bold text-gray-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Details --}}
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            {{-- Action badge --}}
                                            @php
                                                $actionColor = $log->action_color;
                                                $badgeClasses = match($actionColor) {
                                                    'emerald' => 'bg-emerald-100 text-emerald-700',
                                                    'amber' => 'bg-amber-100 text-amber-700',
                                                    'red' => 'bg-red-100 text-red-700',
                                                    'blue' => 'bg-blue-100 text-blue-700',
                                                    'purple' => 'bg-purple-100 text-purple-700',
                                                    default => 'bg-gray-100 text-gray-700',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                            </span>

                                            {{-- Model type badge --}}
                                            @if($log->auditable_type)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                    {{ $log->model_display_name }}
                                                </span>
                                            @endif

                                            {{-- User name --}}
                                            <span class="text-xs text-gray-500">
                                                by <span class="font-medium text-gray-700">{{ $log->user?->name ?? 'System' }}</span>
                                            </span>
                                        </div>

                                        {{-- Description --}}
                                        <p class="text-sm text-osaka-charcoal">{{ $log->description ?? 'No description' }}</p>

                                        {{-- Timestamp --}}
                                        <p class="text-xs text-gray-400 mt-1" title="{{ $log->created_at->format('Y-m-d H:i:s T') }}">
                                            {{ $log->created_at->diffForHumans() }}
                                            <span class="text-gray-300">&middot;</span>
                                            {{ $log->created_at->format('M j, Y g:i A') }}
                                            @if($log->ip_address)
                                                <span class="text-gray-300">&middot;</span>
                                                {{ $log->ip_address }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                {{-- Expand button (only if old/new values exist) --}}
                                @if($log->old_values || $log->new_values)
                                    <button @click="expanded = !expanded" class="shrink-0 p-1 text-gray-400 hover:text-gray-600 transition-colors" title="Show details">
                                        <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                @endif
                            </div>

                            {{-- Expandable old/new values diff --}}
                            @if($log->old_values || $log->new_values)
                                <div x-show="expanded" x-transition x-cloak class="mt-4 pt-4 border-t border-gray-100">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @if($log->old_values)
                                            <div>
                                                <h4 class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-2">Old Values</h4>
                                                <div class="bg-red-50 rounded-lg p-3 overflow-x-auto">
                                                    <dl class="text-xs space-y-1">
                                                        @foreach($log->old_values as $key => $value)
                                                            <div class="flex gap-2">
                                                                <dt class="font-medium text-gray-600 shrink-0">{{ $key }}:</dt>
                                                                <dd class="text-gray-800 break-all">
                                                                    @if(is_array($value))
                                                                        {{ json_encode($value) }}
                                                                    @elseif(is_null($value))
                                                                        <span class="text-gray-400 italic">null</span>
                                                                    @else
                                                                        {{ $value }}
                                                                    @endif
                                                                </dd>
                                                            </div>
                                                        @endforeach
                                                    </dl>
                                                </div>
                                            </div>
                                        @endif

                                        @if($log->new_values)
                                            <div>
                                                <h4 class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-2">New Values</h4>
                                                <div class="bg-emerald-50 rounded-lg p-3 overflow-x-auto">
                                                    <dl class="text-xs space-y-1">
                                                        @foreach($log->new_values as $key => $value)
                                                            <div class="flex gap-2">
                                                                <dt class="font-medium text-gray-600 shrink-0">{{ $key }}:</dt>
                                                                <dd class="text-gray-800 break-all">
                                                                    @if(is_array($value))
                                                                        {{ json_encode($value) }}
                                                                    @elseif(is_null($value))
                                                                        <span class="text-gray-400 italic">null</span>
                                                                    @else
                                                                        {{ $value }}
                                                                    @endif
                                                                </dd>
                                                            </div>
                                                        @endforeach
                                                    </dl>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        @else
            <x-empty-state
                title="No audit log entries"
                message="No actions have been logged yet, or no entries match your filters."
            />
        @endif
    </div>
</x-app-layout>
