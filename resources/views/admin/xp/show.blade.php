<x-app-layout>
    <x-slot name="pageTitle">XP — {{ $user->name }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-bold text-osaka-charcoal flex items-center">
            <svg class="w-5 h-5 mr-2 text-osaka-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            XP — {{ $user->name }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700 flex items-center" x-data="{ show: true }" x-show="show" x-transition>
                <svg class="w-5 h-5 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
                <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 flex items-center" x-data="{ show: true }" x-show="show" x-transition>
                <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
                <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
            </div>
        @endif

        {{-- User Summary --}}
        <div class="card mb-6">
            <div class="card-body">
                <div class="flex items-center gap-4">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="" class="w-14 h-14 rounded-full border-2 border-osaka-gold/30">
                    @else
                        <div class="w-14 h-14 rounded-full bg-osaka-gold flex items-center justify-center text-xl font-bold text-osaka-charcoal">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-osaka-charcoal">{{ $user->name }}</h3>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-osaka-gold font-semibold">{{ number_format($user->total_xp) }} XP</span>
                            <span class="text-sm text-gray-500">Level {{ $user->level }} — {{ $user->level_name }}</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.xp.index') }}" class="btn-secondary btn-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Back
                    </a>
                </div>
            </div>
        </div>

        {{-- Revoke XP Form --}}
        @if($user->total_xp > 0)
            <div class="card mb-6" x-data="{ open: false }">
                <div class="card-body">
                    <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            <span class="font-semibold text-osaka-charcoal">Revoke XP</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="open" x-collapse x-cloak class="mt-4 pt-4 border-t border-gray-100">
                        <form method="POST" action="{{ route('admin.xp.revoke', $user) }}" onsubmit="return confirm('Are you sure you want to revoke this XP?');">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount to revoke</label>
                                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required
                                           min="1" max="{{ $user->total_xp }}"
                                           class="form-input-osaka w-full" placeholder="e.g. 50">
                                    <p class="mt-1 text-xs text-gray-400">Max: {{ number_format($user->total_xp) }} XP</p>
                                    @error('amount')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                    <textarea name="reason" id="reason" rows="2" required
                                              class="form-input-osaka w-full" placeholder="Why is this XP being revoked?">{{ old('reason') }}</textarea>
                                    @error('reason')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="btn-danger">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                        Revoke XP
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- Transaction History --}}
        <h3 class="text-lg font-bold text-osaka-charcoal mb-4">Transaction History</h3>

        @if($transactions->count() > 0)
            <div class="space-y-2">
                @foreach($transactions as $tx)
                    <div class="card">
                        <div class="card-body flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $tx->xp_amount >= 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                                    {{ $tx->xp_amount >= 0 ? '+' : '' }}{{ $tx->xp_amount }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-osaka-charcoal">{{ $tx->description ?? ucwords(str_replace('_', ' ', $tx->action)) }}</span>
                                        @if($tx->action === 'xp_revoked')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Revoked</span>
                                        @endif
                                        @if($tx->metadata['backfilled'] ?? false)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Backfilled</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <code class="text-xs text-gray-400 bg-gray-50 px-1 py-0.5 rounded">{{ $tx->action }}</code>
                                        <span class="text-xs text-gray-400">{{ $tx->created_at->format('M j, Y g:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-8">
                    <p class="text-sm text-gray-400">No XP transactions found.</p>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
