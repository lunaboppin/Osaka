<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add a Pin') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-10">
        <form method="POST" action="{{ route('pins.store') }}" enctype="multipart/form-data" id="add-pin-form">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700">Title</label>
                <input type="text" name="title" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Description</label>
                <textarea name="description" class="w-full border rounded px-3 py-2" rows="3"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Photo</label>
                <input type="file" name="photo" accept="image/*" class="w-full">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Location</label>
                <button type="button" id="get-location" class="px-4 py-2 bg-blue-500 rounded">Use My Location</button>
                <div id="location-status" class="text-sm text-gray-500 mt-2"></div>
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
            </div>
            <button type="submit" class="px-6 py-2 bg-green-600 rounded">Add Pin</button>
        </form>
    </div>

    <script>
    document.getElementById('get-location').onclick = function() {
        const status = document.getElementById('location-status');
        status.textContent = 'Getting location...';
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                document.getElementById('latitude').value = pos.coords.latitude;
                document.getElementById('longitude').value = pos.coords.longitude;
                status.textContent = `Location set: (${pos.coords.latitude.toFixed(5)}, ${pos.coords.longitude.toFixed(5)})`;
            }, function() {
                status.textContent = 'Unable to retrieve your location.';
            });
        } else {
            status.textContent = 'Geolocation is not supported by your browser.';
        }
    };
    </script>
</x-app-layout>
