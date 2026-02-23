<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-osaka-charcoal flex items-center">
            <svg class="w-5 h-5 mr-2 text-osaka-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            Edit Roles — {{ $user->name }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-4">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm font-medium text-osaka-red hover:text-osaka-red-dark transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Users
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                {{-- User info --}}
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" class="w-14 h-14 rounded-full object-cover border-2 border-osaka-gold/30">
                    @else
                        <div class="w-14 h-14 rounded-full bg-osaka-gold flex items-center justify-center text-xl font-bold text-osaka-charcoal">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h3 class="text-lg font-bold text-osaka-charcoal">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>

                {{-- Role assignment form --}}
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <label class="form-label mb-3">Assigned Roles</label>
                    <div class="space-y-2">
                        @foreach($roles as $role)
                            <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:bg-osaka-cream/50 cursor-pointer transition-colors">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                       class="rounded border-gray-300 text-osaka-red focus:ring-osaka-red"
                                       {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold text-white" style="background-color: {{ $role->color }}">
                                    {{ $role->display_name }}
                                </span>
                                <span class="text-xs text-gray-400">{{ count($role->permissions ?? []) }} {{ Str::plural('permission', count($role->permissions ?? [])) }}</span>
                            </label>
                        @endforeach
                    </div>

                    @if($roles->isEmpty())
                        <p class="text-sm text-gray-400 italic">No roles exist yet. <a href="{{ route('admin.roles.create') }}" class="text-osaka-red hover:text-osaka-red-dark">Create one</a>.</p>
                    @endif

                    <div class="mt-6 flex items-center justify-between">
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-osaka-charcoal transition-colors">Cancel</a>
                        <button type="submit" class="btn-primary btn-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Save Roles
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
