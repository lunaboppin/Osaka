<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-xl font-bold text-osaka-charcoal">
                <svg class="w-5 h-5 inline mr-1 text-osaka-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                {{ request('mine') == '1' ? 'My Pins' : 'All Pins' }}
            </h2>
            <a href="{{ route('pins.create') }}" class="btn-primary btn-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Add Pin
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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

        {{-- Filters --}}
        <div class="card mb-6">
            <div class="card-body">
                <form method="GET" action="{{ route('pins.index') }}" class="flex flex-col sm:flex-row gap-3">
                    {{-- Search --}}
                    <div class="relative flex-1">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search pins..."
                               class="form-input-osaka pl-10">
                    </div>
                    {{-- Status Filter --}}
                    <select name="status" class="form-input-osaka sm:w-48">
                        <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                        <option value="New" {{ request('status') === 'New' ? 'selected' : '' }}>New</option>
                        <option value="Worn" {{ request('status') === 'Worn' ? 'selected' : '' }}>Worn</option>
                        <option value="Needs replaced" {{ request('status') === 'Needs replaced' ? 'selected' : '' }}>Needs Replaced</option>
                    </select>
                    {{-- My Pins Toggle --}}
                    <label class="inline-flex items-center cursor-pointer sm:w-auto">
                        <input type="checkbox" name="mine" value="1" {{ request('mine') == '1' ? 'checked' : '' }}
                               class="rounded border-gray-300 text-osaka-red shadow-sm focus:ring-osaka-red">
                        <span class="ml-2 text-sm text-osaka-charcoal font-medium whitespace-nowrap">My Pins Only</span>
                    </label>
                    {{-- Submit --}}
                    <button type="submit" class="btn-secondary btn-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'mine']))
                        <a href="{{ route('pins.index') }}" class="btn-secondary btn-sm text-gray-500">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Pin Cards Grid --}}
        @if($pins->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($pins as $pin)
                    <x-pin-card :pin="$pin" :showActions="true" />
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $pins->links() }}
            </div>
        @else
            <x-empty-state
                title="No pins found"
                message="Try adjusting your search or filter criteria, or add a new pin to get started."
                action="Add a Pin"
                :actionUrl="route('pins.create')"
            />
        @endif
    </div>
</x-app-layout>
