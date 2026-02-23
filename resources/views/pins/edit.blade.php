<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-osaka-charcoal flex items-center">
            <svg class="w-5 h-5 mr-2 text-osaka-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Pin
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="editPinForm()">
        {{-- Back button --}}
        <div class="mb-4">
            <a href="{{ route('pins.show', $pin) }}" class="inline-flex items-center text-sm font-medium text-osaka-red hover:text-osaka-red-dark transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Pin
            </a>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span class="text-sm font-semibold text-red-700">Please fix the following errors:</span>
                </div>
                <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('pins.update', $pin) }}" enctype="multipart/form-data" class="card">
            @csrf
            @method('PUT')

            <div class="p-6 sm:p-8 space-y-6">
                {{-- Current Photo --}}
                <div>
                    <label class="form-label">Photo</label>
                    @if($pin->photo)
                        <div x-show="!removePhoto" class="mb-3">
                            <div class="relative inline-block">
                                <img src="{{ asset('storage/' . $pin->photo) }}" alt="{{ $pin->title }}" class="max-h-48 rounded-lg shadow-sm border border-gray-100">
                                <button type="button" @click="removePhoto = true" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center shadow hover:bg-red-600 transition" title="Remove photo">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                </button>
                            </div>
                        </div>
                        <div x-show="removePhoto" class="mb-3">
                            <div class="bg-red-50 rounded-lg p-3 text-sm text-red-600 flex items-center justify-between">
                                <span>Photo will be removed on save.</span>
                                <button type="button" @click="removePhoto = false" class="text-red-700 underline text-xs">Undo</button>
                            </div>
                        </div>
                        <input type="hidden" name="remove_photo" :value="removePhoto ? '1' : '0'">
                    @endif
                    {{-- New photo upload --}}
                    <div>
                        <div x-show="newPhotoPreview" class="mb-3">
                            <img :src="newPhotoPreview" class="max-h-48 rounded-lg shadow-sm border border-gray-100" alt="New photo preview">
                        </div>
                        <label for="photo" class="btn-secondary btn-sm cursor-pointer inline-flex">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $pin->photo ? 'Replace Photo' : 'Add Photo' }}
                        </label>
                        <input type="file" name="photo" id="photo" accept="image/*" capture="environment" class="hidden" @change="previewNewPhoto($event)">
                    </div>
                </div>

                {{-- Title --}}
                <div>
                    <label class="form-label" for="title">Title <span class="text-osaka-red">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $pin->title) }}" class="form-input-osaka" required>
                    @error('title')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="form-label" for="description">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-input-osaka">{{ old('description', $pin->description) }}</textarea>
                    @error('description')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="form-label">Status <span class="text-osaka-red">*</span></label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach(['New', 'Worn', 'Needs replaced'] as $statusOption)
                            @php
                                $colors = ['New' => 'emerald', 'Worn' => 'amber', 'Needs replaced' => 'red'];
                                $color = $colors[$statusOption];
                            @endphp
                            <label class="relative cursor-pointer">
                                <input type="radio" name="status" value="{{ $statusOption }}" class="sr-only peer" {{ old('status', $pin->status) === $statusOption ? 'checked' : '' }}>
                                <div class="flex items-center justify-center px-3 py-2.5 rounded-lg border-2 border-gray-200 peer-checked:border-{{ $color }}-500 peer-checked:bg-{{ $color }}-50 transition-all">
                                    <span class="w-2.5 h-2.5 rounded-full bg-{{ $color }}-500 mr-2"></span>
                                    <span class="text-sm font-medium text-center leading-tight">{{ $statusOption }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('status')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Location --}}
                <div>
                    <label class="form-label">Location</label>
                    <div class="text-sm text-gray-500 mb-2">
                        Current: <span class="font-mono">{{ number_format($pin->latitude, 5) }}, {{ number_format($pin->longitude, 5) }}</span>
                    </div>
                    <button type="button" @click="updateLocation()" class="btn-secondary btn-sm" :disabled="gettingLocation">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        <span x-text="gettingLocation ? 'Getting...' : 'Update Location'"></span>
                    </button>
                    <div x-show="locationStatus" class="text-sm mt-2 text-emerald-600" x-text="locationStatus"></div>
                    <input type="hidden" name="latitude" x-model="latitude">
                    <input type="hidden" name="longitude" x-model="longitude">
                </div>
            </div>

            {{-- Actions --}}
            <div class="px-6 sm:px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <button type="button" @click="confirmDelete = true" class="text-sm text-red-500 hover:text-red-700 font-medium transition-colors">
                    Delete Pin
                </button>
                <button type="submit" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Changes
                </button>
            </div>
        </form>

        {{-- Delete Confirmation Modal --}}
        <template x-if="confirmDelete">
            <div class="fixed inset-0 flex items-center justify-center z-50" @click.self="confirmDelete = false">
                <div class="bg-black/40 absolute inset-0"></div>
                <div class="relative bg-white rounded-xl shadow-2xl px-8 py-6 max-w-sm w-full text-center mx-4">
                    <div class="w-16 h-16 mx-auto rounded-full bg-red-100 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-osaka-charcoal mb-2">Delete this pin?</h3>
                    <p class="text-sm text-gray-500 mb-6">This action cannot be undone. The pin and its photo will be permanently removed.</p>
                    <div class="flex items-center justify-center space-x-3">
                        <button @click="confirmDelete = false" class="btn-secondary">Cancel</button>
                        <form method="POST" action="{{ route('pins.destroy', $pin) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
    function editPinForm() {
        return {
            removePhoto: false,
            newPhotoPreview: null,
            confirmDelete: false,
            gettingLocation: false,
            locationStatus: '',
            latitude: '{{ $pin->latitude }}',
            longitude: '{{ $pin->longitude }}',

            previewNewPhoto(event) {
                const file = event.target.files[0];
                if (file) {
                    this.removePhoto = false;
                    const reader = new FileReader();
                    reader.onload = (e) => { this.newPhotoPreview = e.target.result; };
                    reader.readAsDataURL(file);
                }
            },

            updateLocation() {
                this.gettingLocation = true;
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition((pos) => {
                        this.latitude = pos.coords.latitude;
                        this.longitude = pos.coords.longitude;
                        this.locationStatus = `Updated to: ${pos.coords.latitude.toFixed(5)}, ${pos.coords.longitude.toFixed(5)}`;
                        this.gettingLocation = false;
                    }, () => {
                        this.locationStatus = 'Unable to get location.';
                        this.gettingLocation = false;
                    }, { enableHighAccuracy: true, timeout: 10000 });
                } else {
                    this.locationStatus = 'Geolocation not supported.';
                    this.gettingLocation = false;
                }
            },
        }
    }
    </script>
</x-app-layout>
