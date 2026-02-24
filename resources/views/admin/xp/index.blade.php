<x-app-layout>
    <x-slot name="pageTitle">Manage XP</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-bold text-osaka-charcoal flex items-center">
            <svg class="w-5 h-5 mr-2 text-osaka-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            Manage XP
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

        {{-- Search --}}
        <form method="GET" class="mb-6">
            <div class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..."
                       class="form-input-osaka flex-1">
                <button type="submit" class="btn-primary btn-sm">Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.xp.index') }}" class="btn-secondary btn-sm">Clear</a>
                @endif
            </div>
        </form>

        <p class="text-sm text-gray-500 mb-4">{{ $users->total() }} {{ Str::plural('user', $users->total()) }} with XP</p>

        @if($users->count() > 0)
            <div class="space-y-3">
                @foreach($users as $user)
                    <div class="card">
                        <div class="card-body flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                @if($user->avatar)
                                    <img src="{{ $user->avatar }}" alt="" class="w-10 h-10 rounded-full border-2 border-osaka-gold/30">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-osaka-gold flex items-center justify-center text-sm font-bold text-osaka-charcoal">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <h3 class="font-semibold text-osaka-charcoal">{{ $user->name }}</h3>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-sm text-osaka-gold font-medium">{{ number_format($user->total_xp) }} XP</span>
                                        <span class="text-xs text-gray-400">&middot;</span>
                                        <span class="text-xs text-gray-500">Level {{ $user->level }} — {{ $user->level_name }}</span>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.xp.show', $user) }}" class="btn-secondary btn-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                View / Revoke
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-12">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    <h3 class="text-lg font-semibold text-gray-500 mb-1">No users with XP</h3>
                    <p class="text-sm text-gray-400">Users will appear here once they earn XP.</p>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
