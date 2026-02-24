<x-app-layout>
    <x-slot name="pageTitle">Create Webhook</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-bold text-osaka-charcoal flex items-center">
            <svg class="w-5 h-5 mr-2 text-osaka-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Create Discord Webhook
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form method="POST" action="{{ route('admin.discord-webhooks.store') }}">
            @csrf

            <div class="card">
                <div class="card-body space-y-6">
                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="form-input-osaka w-full" placeholder="e.g. General Notifications">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- URL --}}
                    <div>
                        <label for="url" class="block text-sm font-medium text-gray-700 mb-1">Webhook URL</label>
                        <input type="url" name="url" id="url" value="{{ old('url') }}" required
                               class="form-input-osaka w-full" placeholder="https://discord.com/api/webhooks/...">
                        <p class="mt-1 text-xs text-gray-400">Must be a valid Discord webhook URL.</p>
                        @error('url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Events --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Events</label>
                        <p class="text-xs text-gray-400 mb-3">Choose which events trigger this webhook. Leave empty to receive nothing.</p>
                        <div class="space-y-2">
                            @foreach($availableEvents as $key => $label)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="events[]" value="{{ $key }}"
                                           {{ in_array($key, old('events', [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-osaka-red focus:ring-osaka-red">
                                    <span class="text-sm text-gray-700">{{ $label }}</span>
                                    <code class="text-xs text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded">{{ $key }}</code>
                                </label>
                            @endforeach
                        </div>
                        @error('events')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Active --}}
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-osaka-red focus:ring-osaka-red">
                            <span class="text-sm font-medium text-gray-700">Active</span>
                        </label>
                        <p class="text-xs text-gray-400 mt-1">Inactive webhooks won't receive any notifications.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('admin.discord-webhooks.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Create Webhook
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
