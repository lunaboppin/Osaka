<x-app-layout>
    <x-slot name="pageTitle">Add a Pin</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-bold text-osaka-charcoal flex items-center">
            <svg class="w-5 h-5 mr-2 text-osaka-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add a Pin
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div x-data="pinForm()">
            {{-- Popup Notification --}}
            <template x-if="showPopup">
                <div class="fixed inset-0 flex items-center justify-center z-50" @click.self="showPopup = false">
                    <div class="bg-black/40 absolute inset-0"></div>
                    <div class="relative bg-white rounded-xl shadow-2xl px-8 py-6 max-w-sm w-full text-center mx-4" x-transition>
                        <div class="mb-4">
                            <template x-if="popupSuccess">
                                <div class="w-16 h-16 mx-auto rounded-full bg-emerald-100 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </template>
                            <template x-if="!popupSuccess">
                                <div class="w-16 h-16 mx-auto rounded-full bg-red-100 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                            </template>
                        </div>
                        <div :class="popupSuccess ? 'text-emerald-700' : 'text-red-700'" class="text-lg font-semibold mb-2" x-text="popupMessage"></div>
                        <template x-if="popupSuccess && redirectUrl">
                            <a :href="redirectUrl" class="btn-primary mt-4 inline-flex">View Pin</a>
                        </template>
                        <button @click="showPopup = false" class="btn-secondary mt-3">
                            <span x-text="popupSuccess ? 'Add Another' : 'Try Again'"></span>
                        </button>
                    </div>
                </div>
            </template>

            <form @submit.prevent="submitForm" method="POST" action="{{ route('pins.store') }}" enctype="multipart/form-data" id="add-pin-form" class="card">
                @csrf
                <div class="p-6 sm:p-8 space-y-6">
                    {{-- Title --}}
                    <div>
                        <label class="form-label" for="title">Title <span class="text-osaka-red">*</span></label>
                        <input type="text" name="title" id="title" class="form-input-osaka" placeholder="e.g. Sticker on Cathedral Quarter lamppost" required
                               @keydown="$el.dataset.autofilled = 'false'">
                        <p class="text-xs text-gray-400 mt-1">Auto-filled from location — edit freely</p>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="form-label" for="description">Description</label>
                        <textarea name="description" id="description" rows="3" class="form-input-osaka" placeholder="Describe the sticker's condition, exact location, or any other notes..."></textarea>
                        <p class="text-xs text-gray-400 mt-1">Optional — up to 2000 characters</p>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="form-label" for="status">Status <span class="text-osaka-red">*</span></label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="status" value="New" x-model="status" class="sr-only peer" checked>
                                <div class="flex items-center justify-center px-3 py-2.5 rounded-lg border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition-all">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-2"></span>
                                    <span class="text-sm font-medium">New</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="status" value="Worn" x-model="status" class="sr-only peer">
                                <div class="flex items-center justify-center px-3 py-2.5 rounded-lg border-2 border-gray-200 peer-checked:border-amber-500 peer-checked:bg-amber-50 transition-all">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 mr-2"></span>
                                    <span class="text-sm font-medium">Worn</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="status" value="Needs replaced" x-model="status" class="sr-only peer">
                                <div class="flex items-center justify-center px-3 py-2.5 rounded-lg border-2 border-gray-200 peer-checked:border-red-500 peer-checked:bg-red-50 transition-all">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 mr-2"></span>
                                    <span class="text-sm font-medium text-center leading-tight">Needs Replaced</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Photo with Preview --}}
                    <div>
                        <label class="form-label">Photo</label>
                        <div class="mt-1">
                            {{-- Preview --}}
                            <div x-show="photoPreview" class="mb-3">
                                <div class="relative inline-block">
                                    <img :src="photoPreview" class="max-h-48 rounded-lg shadow-sm border border-gray-100" alt="Preview">
                                    <button type="button" @click="removePhoto()" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center shadow hover:bg-red-600 transition">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    </button>
                                </div>
                            </div>
                            {{-- Upload area --}}
                            <div x-show="!photoPreview" class="relative">
                                <label for="photo" class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-osaka-cream/50 hover:bg-osaka-cream hover:border-osaka-gold transition-all">
                                    <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="text-sm text-gray-500">Tap to take a photo or choose file</span>
                                    <span class="text-xs text-gray-400 mt-1">Max 100MB · JPG, PNG, WebP</span>
                                </label>
                                <input type="file" name="photo" id="photo" accept="image/*" capture="environment" class="hidden" @change="previewPhoto($event)">
                            </div>
                        </div>
                    </div>

                    {{-- Location --}}
                    <div>
                        <label class="form-label">Location <span class="text-osaka-red">*</span></label>
                        <button type="button" @click="getLocation()" class="btn-secondary w-full sm:w-auto" :disabled="gettingLocation">
                            <svg class="w-4 h-4 mr-2" :class="gettingLocation && 'animate-spin'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <template x-if="!gettingLocation">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </template>
                                <template x-if="gettingLocation">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </template>
                            </svg>
                            <span x-text="gettingLocation ? 'Getting location...' : (latitude ? 'Update Location' : 'Use My Location')"></span>
                        </button>
                        <div class="text-sm mt-2" :class="latitude ? 'text-emerald-600' : 'text-gray-400'" x-text="locationStatus"></div>
                        <input type="hidden" name="latitude" x-model="latitude">
                        <input type="hidden" name="longitude" x-model="longitude">

                        {{-- Mini map preview --}}
                        <div x-show="latitude" class="mt-3 rounded-lg overflow-hidden border border-gray-100" x-transition>
                            <div id="location-preview-map" style="height: 180px;"></div>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="px-6 sm:px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-osaka-charcoal transition-colors">Cancel</a>
                    <button type="submit" class="btn-success" :disabled="submitting">
                        <svg x-show="!submitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <svg x-show="submitting" class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="submitting ? 'Adding...' : 'Add Pin'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}" async defer></script>
    <script>
    function pinForm() {
        return {
            showPopup: false,
            popupMessage: '',
            popupSuccess: false,
            redirectUrl: '',
            locationStatus: '',
            latitude: '',
            longitude: '',
            gettingLocation: false,
            submitting: false,
            status: 'New',
            photoPreview: null,
            previewMap: null,
            previewMarker: null,
            geocoder: null,

            previewPhoto(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => { this.photoPreview = e.target.result; };
                    reader.readAsDataURL(file);
                }
            },

            removePhoto() {
                this.photoPreview = null;
                document.getElementById('photo').value = '';
            },

            reverseGeocode(lat, lng) {
                if (typeof google === 'undefined') return;
                if (!this.geocoder) this.geocoder = new google.maps.Geocoder();

                this.geocoder.geocode({ location: { lat, lng } }, (results, status) => {
                    if (status !== 'OK' || !results || !results.length) return;

                    const titleInput = document.getElementById('title');
                    // Only autofill if the title is empty or was previously autofilled
                    if (titleInput && (!titleInput.value || titleInput.dataset.autofilled === 'true')) {
                        // Try to find a street-level result
                        let streetName = '';

                        // Look through address components for the best street name
                        for (const result of results) {
                            const types = result.types || [];
                            // Prefer route (street), then street_address, then premise
                            if (types.includes('route') || types.includes('street_address')) {
                                // Extract route and street_number components
                                const route = result.address_components.find(c => c.types.includes('route'));
                                const number = result.address_components.find(c => c.types.includes('street_number'));
                                if (route) {
                                    streetName = number ? `${number.long_name} ${route.long_name}` : route.long_name;
                                    break;
                                }
                            }
                        }

                        // Fallback: get route from the first result's address components
                        if (!streetName && results[0]) {
                            const route = results[0].address_components.find(c => c.types.includes('route'));
                            if (route) streetName = route.long_name;
                        }

                        // Fallback: use formatted address minus country/postcode for a short name
                        if (!streetName && results[0]) {
                            streetName = results[0].formatted_address.split(',').slice(0, 2).join(',').trim();
                        }

                        if (streetName) {
                            titleInput.value = streetName;
                            titleInput.dataset.autofilled = 'true';
                            titleInput.dispatchEvent(new Event('input'));
                        }
                    }
                });
            },

            getLocation() {
                this.gettingLocation = true;
                this.locationStatus = 'Getting location...';
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition((pos) => {
                        this.latitude = pos.coords.latitude;
                        this.longitude = pos.coords.longitude;
                        this.locationStatus = `Location set: ${pos.coords.latitude.toFixed(5)}, ${pos.coords.longitude.toFixed(5)}`;
                        this.gettingLocation = false;
                        this.$nextTick(() => this.showLocationMap());
                        this.reverseGeocode(pos.coords.latitude, pos.coords.longitude);
                    }, (err) => {
                        this.locationStatus = 'Unable to retrieve your location. Please allow location access.';
                        this.gettingLocation = false;
                    }, { enableHighAccuracy: true, timeout: 10000 });
                } else {
                    this.locationStatus = 'Geolocation is not supported by your browser.';
                    this.gettingLocation = false;
                }
            },

            showLocationMap() {
                if (!this.latitude || !this.longitude) return;
                if (typeof google === 'undefined') {
                    setTimeout(() => this.showLocationMap(), 500);
                    return;
                }
                const pos = { lat: parseFloat(this.latitude), lng: parseFloat(this.longitude) };
                const mapEl = document.getElementById('location-preview-map');
                if (!mapEl) return;

                if (!this.previewMap) {
                    this.previewMap = new google.maps.Map(mapEl, {
                        center: pos,
                        zoom: 16,
                        mapTypeControl: false,
                        streetViewControl: false,
                        fullscreenControl: false,
                        styles: [
                            { featureType: "poi", stylers: [{ visibility: "off" }] },
                            { featureType: "transit", stylers: [{ visibility: "off" }] }
                        ],
                    });
                } else {
                    this.previewMap.setCenter(pos);
                }

                if (this.previewMarker) this.previewMarker.setMap(null);
                this.previewMarker = new google.maps.Marker({
                    position: pos,
                    map: this.previewMap,
                    draggable: true,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 10,
                        fillColor: '#C41E3A',
                        fillOpacity: 0.9,
                        strokeWeight: 3,
                        strokeColor: '#fff',
                    }
                });

                // Allow user to drag the marker to fine-tune position
                this.previewMarker.addListener('dragend', (event) => {
                    this.latitude = event.latLng.lat();
                    this.longitude = event.latLng.lng();
                    this.locationStatus = `Location set: ${event.latLng.lat().toFixed(5)}, ${event.latLng.lng().toFixed(5)}`;
                    this.reverseGeocode(event.latLng.lat(), event.latLng.lng());
                });
            },

            async submitForm(e) {
                this.submitting = true;
                const form = document.getElementById('add-pin-form');
                const formData = new FormData(form);
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': form.querySelector('input[name=_token]').value,
                            'Accept': 'application/json',
                        },
                        body: formData
                    });
                    if (response.ok) {
                        const data = await response.json();
                        this.popupMessage = 'Pin added successfully!';
                        this.popupSuccess = true;
                        this.redirectUrl = data.pin ? `/pins/${data.pin.id}` : '/';
                        form.reset();
                        this.latitude = '';
                        this.longitude = '';
                        this.locationStatus = '';
                        this.photoPreview = null;
                        this.status = 'New';
                        const titleEl = document.getElementById('title');
                        if (titleEl) titleEl.dataset.autofilled = 'false';
                    } else {
                        const data = await response.json().catch(() => ({}));
                        let msg = data.message || 'Failed to add pin.';
                        if (data.errors) {
                            msg = Object.values(data.errors).flat().join('. ');
                        }
                        this.popupMessage = msg;
                        this.popupSuccess = false;
                        this.redirectUrl = '';
                    }
                } catch (error) {
                    this.popupMessage = 'An error occurred. Please try again.';
                    this.popupSuccess = false;
                    this.redirectUrl = '';
                }
                this.submitting = false;
                this.showPopup = true;
            }
        }
    }
    </script>
</x-app-layout>
