<x-app-layout>
    <x-slot name="pageTitle">{{ $stickerType ? 'Edit Sticker Type: ' . $stickerType->name : 'Create Sticker Type' }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-bold text-osaka-charcoal flex items-center">
            <svg class="w-5 h-5 mr-2 text-osaka-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
            {{ $stickerType ? 'Edit Sticker Type' : 'Create Sticker Type' }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-4">
            <a href="{{ route('admin.sticker-types.index') }}" class="inline-flex items-center text-sm font-medium text-osaka-red hover:text-osaka-red-dark transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Sticker Types
            </a>
        </div>

        <form method="POST"
              action="{{ $stickerType ? route('admin.sticker-types.update', $stickerType) : route('admin.sticker-types.store') }}"
              class="card" x-data="{ color: '{{ old('color', $stickerType?->color ?? '#D97706') }}' }">
            @csrf
            @if($stickerType)
                @method('PUT')
            @endif

            <div class="p-6 sm:p-8 space-y-6">
                {{-- Name fields --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="form-label">Slug <span class="text-osaka-red">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $stickerType?->name) }}" class="form-input-osaka" placeholder="e.g. stickers" required pattern="[a-zA-Z0-9_-]+">
                        <p class="text-xs text-gray-400 mt-1">Lowercase, no spaces (used internally)</p>
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="display_name" class="form-label">Display Name <span class="text-osaka-red">*</span></label>
                        <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $stickerType?->display_name) }}" class="form-input-osaka" placeholder="e.g. Stickers" required>
                        @error('display_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-input-osaka" placeholder="Optional description of this sticker type...">{{ old('description', $stickerType?->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Color --}}
                <div>
                    <label for="color" class="form-label">Colour</label>
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
            </div>

            {{-- Submit --}}
            <div class="px-6 sm:px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('admin.sticker-types.index') }}" class="text-sm text-gray-500 hover:text-osaka-charcoal transition-colors">Cancel</a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ $stickerType ? 'Update Sticker Type' : 'Create Sticker Type' }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
