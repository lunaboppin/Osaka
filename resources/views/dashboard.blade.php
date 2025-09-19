<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                    <!-- Google Maps Container -->
                    <div id="map" style="height: 500px; width: 100%; margin-top: 2rem;"></div>
                    <div id="pin-info" style="display:none; margin-top:1rem; padding:1rem; border:1px solid #e5e7eb; border-radius:0.5rem; background:#f9fafb;"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}"></script>
    <script>
        let map;
        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 54.5973, lng: -5.9301 },
                zoom: 12
            });
            // Fetch pins from backend
            fetch('/pins')
                .then(response => response.json())
                .then(pins => {
                    pins.forEach(pin => {
                        const marker = new google.maps.Marker({
                            position: { lat: parseFloat(pin.latitude), lng: parseFloat(pin.longitude) },
                            map: map,
                            title: pin.title || ''
                        });
                        marker.addListener('click', function() {
                            const infoBox = document.getElementById('pin-info');
                            infoBox.style.display = 'block';
                            let photoHtml = '';
                            if (pin.photo) {
                                photoHtml = `<img src='/storage/${pin.photo}' alt='Pin Photo' style='max-width:100%;max-height:200px;margin-bottom:1rem;border-radius:0.5rem;'>`;
                            }
                            let userHtml = '';
                            if (pin.user && pin.user.name) {
                                userHtml = `<div style='font-size:0.95rem; color:#374151; margin-bottom:0.5rem;'>Added by: <strong>${pin.user.name}</strong></div>`;
                            }
                            infoBox.innerHTML = `
                                ${photoHtml}
                                ${userHtml}
                                <h3 style='font-size:1.25rem; font-weight:600; color:#374151;'>${pin.title || ''}</h3>
                                <p style='color:#6b7280;'>Status: <strong>${pin.status || ''}</strong></p>
                                <div style='font-size:0.9rem; color:#9ca3af;'>Lat: ${pin.latitude}, Lng: ${pin.longitude}</div>
                            `;
                        });
                    });
                });
        }
        window.onload = initMap;
    </script>
</x-app-layout>
