import './bootstrap';

window.initMap = function() {
    const map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 54.5973, lng: -5.9301 },
        zoom: 12
    });
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
                        <p style='color:#6b7280;'>${pin.description || ''}</p>
                        <div style='font-size:0.9rem; color:#9ca3af;'>Lat: ${pin.latitude}, Lng: ${pin.longitude}</div>
                    `;
                });
            });
        });
};
