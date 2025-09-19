<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add a Pin') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-10">
        <div x-data="pinForm()">
            <!-- Popup Notification -->
            <template x-if="showPopup">
                <div class="fixed inset-0 flex items-center justify-center z-50">
                    <div class="bg-black bg-opacity-40 absolute inset-0"></div>
                    <div class="relative bg-white rounded-lg shadow-lg px-8 py-6 max-w-sm w-full text-center">
                        <div :class="popupSuccess ? 'text-green-600' : 'text-red-600'" class="text-lg font-semibold mb-2" x-text="popupMessage"></div>
                        <button @click="showPopup = false" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">OK</button>
                    </div>
                </div>
            </template>
            <form @submit.prevent="submitForm" method="POST" action="{{ route('pins.store') }}" enctype="multipart/form-data" id="add-pin-form" class="bg-white shadow-lg rounded-xl p-8 space-y-6">
                @csrf
                <div>
                    <label class="block text-gray-800 font-semibold mb-1" for="title">Title</label>
                    <input type="text" name="title" id="title" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 transition" required>
                </div>
                <div>
                    <label class="block text-gray-800 font-semibold mb-1" for="status">Status</label>
                    <select name="status" id="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 transition" required>
                        <option value="New">New</option>
                        <option value="Worn">Worn</option>
                        <option value="Needs replaced">Needs replaced</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-800 font-semibold mb-1" for="photo">Photo</label>
                    <input type="file" name="photo" id="photo" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>
                <div>
                    <label class="block text-gray-800 font-semibold mb-1">Location</label>
                    <button type="button" id="get-location" @click="getLocation" class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">Use My Location</button>
                    <div id="location-status" class="text-sm text-gray-500 mt-2" x-text="locationStatus"></div>
                    <input type="hidden" name="latitude" id="latitude" x-model="latitude">
                    <input type="hidden" name="longitude" id="longitude" x-model="longitude">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-8 py-2 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition">Add Pin</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
    function pinForm() {
        return {
            showPopup: false,
            popupMessage: '',
            popupSuccess: false,
            locationStatus: '',
            latitude: '',
            longitude: '',
            getLocation() {
                this.locationStatus = 'Getting location...';
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition((pos) => {
                        this.latitude = pos.coords.latitude;
                        this.longitude = pos.coords.longitude;
                        this.locationStatus = `Location set: (${pos.coords.latitude.toFixed(5)}, ${pos.coords.longitude.toFixed(5)})`;
                    }, () => {
                        this.locationStatus = 'Unable to retrieve your location.';
                    });
                } else {
                    this.locationStatus = 'Geolocation is not supported by your browser.';
                }
            },
            async submitForm(e) {
                const form = document.getElementById('add-pin-form');
                const formData = new FormData(form);
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': form.querySelector('input[name=_token]').value
                        },
                        body: formData
                    });
                    if (response.ok) {
                        this.popupMessage = 'Pin added successfully!';
                        this.popupSuccess = true;
                        form.reset();
                        this.latitude = '';
                        this.longitude = '';
                        this.locationStatus = '';
                    } else {
                        const data = await response.json().catch(() => ({}));
                        this.popupMessage = data.message || 'Failed to add pin.';
                        this.popupSuccess = false;
                    }
                } catch (error) {
                    this.popupMessage = 'An error occurred. Please try again.';
                    this.popupSuccess = false;
                }
                this.showPopup = true;
            }
        }
    }
    </script>
</x-app-layout>
