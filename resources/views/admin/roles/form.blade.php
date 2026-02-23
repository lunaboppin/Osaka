<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-osaka-charcoal flex items-center">
            <svg class="w-5 h-5 mr-2 text-osaka-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            {{ $role ? 'Edit Role' : 'Create Role' }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-4">
            <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center text-sm font-medium text-osaka-red hover:text-osaka-red-dark transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Roles
            </a>
        </div>

        <form method="POST"
              action="{{ $role ? route('admin.roles.update', $role) : route('admin.roles.store') }}"
              class="card" x-data="{ color: '{{ old('color', $role?->color ?? '#6B7280') }}' }">
            @csrf
            @if($role)
                @method('PUT')
            @endif

            <div class="p-6 sm:p-8 space-y-6">
                {{-- Name --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="form-label">Slug <span class="text-osaka-red">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $role?->name) }}" class="form-input-osaka" placeholder="e.g. admin" required pattern="[a-zA-Z0-9_-]+">
                        <p class="text-xs text-gray-400 mt-1">Lowercase, no spaces (used internally)</p>
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="display_name" class="form-label">Display Name <span class="text-osaka-red">*</span></label>
                        <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $role?->display_name) }}" class="form-input-osaka" placeholder="e.g. Administrator" required>
                        @error('display_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Color + Priority --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="color" class="form-label">Badge Colour</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="color" id="color" x-model="color" class="w-10 h-10 rounded-lg border border-gray-200 cursor-pointer p-0.5">
                            <input type="text" x-model="color" class="form-input-osaka flex-1 font-mono text-sm" maxlength="7">
                        </div>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold text-white" :style="'background-color: ' + color">
                                Preview
                            </span>
                        </div>
                        @error('color') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="priority" class="form-label">Priority</label>
                        <input type="number" name="priority" id="priority" value="{{ old('priority', $role?->priority ?? 0) }}" class="form-input-osaka" min="0" max="999">
                        <p class="text-xs text-gray-400 mt-1">Higher = shown first on profiles</p>
                        @error('priority') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Permissions --}}
                <div>
                    <label class="form-label">Permissions</label>
                    <p class="text-xs text-gray-400 mb-3">Select which actions users with this role can perform.</p>

                    @php
                        $currentPerms = old('permissions', $role?->permissions ?? []);
                        // Group permissions by prefix
                        $grouped = collect($permissions)->groupBy(function ($label, $key) {
                            return explode('.', $key)[0];
                        });
                    @endphp

                    <div class="space-y-4">
                        @foreach($grouped as $group => $items)
                            <div class="bg-osaka-cream rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-osaka-charcoal capitalize mb-2">{{ $group }}</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($items as $key => $label)
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="permissions[]" value="{{ $key }}"
                                                   class="rounded border-gray-300 text-osaka-red focus:ring-osaka-red"
                                                   {{ in_array($key, $currentPerms) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Wildcard option --}}
                    <div class="mt-3 bg-red-50 rounded-lg p-4 border border-red-100">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="*"
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-500"
                                   {{ in_array('*', $currentPerms) ? 'checked' : '' }}>
                            <span class="text-sm font-semibold text-red-700">All permissions (superadmin wildcard)</span>
                        </label>
                        <p class="text-xs text-red-400 mt-1 ml-6">Grants every permission, including future ones</p>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="px-6 sm:px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('admin.roles.index') }}" class="text-sm text-gray-500 hover:text-osaka-charcoal transition-colors">Cancel</a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ $role ? 'Update Role' : 'Create Role' }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
