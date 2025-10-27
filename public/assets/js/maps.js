/**
 * GoRefill - Maps Integration with Leaflet.js & OpenStreetMap
 * Week 3 Day 11: Interactive Address Picker
 */

let map;
let marker;
const DEFAULT_CENTER = [-6.9667, 110.4167]; // Semarang, Indonesia
const DEFAULT_ZOOM = 13;

/**
 * Initialize Leaflet map in the modal
 */
function initMap() {
    // Check if map container exists
    const mapContainer = document.getElementById('map');
    if (!mapContainer) {
        console.error('Map container not found');
        return;
    }

    // Destroy existing map instance if any
    if (map) {
        map.remove();
    }

    // Initialize map with default center (Semarang)
    map = L.map('map').setView(DEFAULT_CENTER, DEFAULT_ZOOM);

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Handle map click to place marker
    map.on('click', function(e) {
        placeMarker(e.latlng);
    });

    // Fix map rendering issue in modal
    setTimeout(() => {
        map.invalidateSize();
    }, 100);
}

/**
 * Place marker on map and update hidden inputs
 * @param {Object} latlng - Leaflet LatLng object
 */
function placeMarker(latlng) {
    // Remove existing marker if any
    if (marker) {
        map.removeLayer(marker);
    }

    // Add new marker
    marker = L.marker(latlng, {
        draggable: true
    }).addTo(map);

    // Add popup with loading state
    marker.bindPopup(`
        <div class="text-center">
            <strong>📍 Lokasi Pengiriman</strong><br>
            <small>Lat: ${latlng.lat.toFixed(6)}<br>Lng: ${latlng.lng.toFixed(6)}</small><br>
            <small class="text-blue-600">🔄 Mencari alamat...</small>
        </div>
    `).openPopup();

    // Update hidden inputs
    document.getElementById('latitude').value = latlng.lat;
    document.getElementById('longitude').value = latlng.lng;

    // Call reverse geocoding API
    reverseGeocode(latlng.lat, latlng.lng);

    // Handle marker drag
    marker.on('dragend', function(e) {
        const newLatLng = e.target.getLatLng();
        document.getElementById('latitude').value = newLatLng.lat;
        document.getElementById('longitude').value = newLatLng.lng;
        
        // Update popup
        marker.setPopupContent(`
            <div class="text-center">
                <strong>📍 Lokasi Pengiriman</strong><br>
                <small>Lat: ${newLatLng.lat.toFixed(6)}<br>Lng: ${newLatLng.lng.toFixed(6)}</small><br>
                <small class="text-blue-600">🔄 Mencari alamat...</small>
            </div>
        `);
        
        // Call reverse geocoding for new position
        reverseGeocode(newLatLng.lat, newLatLng.lng);
    });

    console.log('Marker placed at:', latlng.lat, latlng.lng);
}

/**
 * Reverse geocode coordinates to get address details
 * Uses Kodepos API: https://kodepos.vercel.app/detect
 * @param {number} lat - Latitude
 * @param {number} lng - Longitude
 */
async function reverseGeocode(lat, lng) {
    try {
        // Call Kodepos API
        const response = await fetch(`https://kodepos.vercel.app/detect/?latitude=${lat}&longitude=${lng}`);
        const data = await response.json();
        
        if (data.statusCode === 200 && data.data) {
            const location = data.data;
            
            // Auto-fill form fields
            document.getElementById('province').value = location.province || '';
            document.getElementById('regency').value = location.regency || '';
            document.getElementById('district').value = location.district || '';
            document.getElementById('village').value = location.village || '';
            document.getElementById('postal_code').value = location.code || '';
            
            // Update marker popup with location info
            if (marker) {
                marker.setPopupContent(`
                    <div class="text-center">
                        <strong>📍 ${location.village}</strong><br>
                        <small>${location.district}, ${location.regency}</small><br>
                        <small>${location.province} - ${location.code}</small><br>
                        <small class="text-gray-500">📏 ${location.distance.toFixed(2)} km</small>
                    </div>
                `);
            }
            
            // Show success notification
            Swal.fire({
                icon: 'success',
                title: 'Alamat Ditemukan!',
                html: `
                    <div class="text-left">
                        <p><strong>Kelurahan/Desa:</strong> ${location.village}</p>
                        <p><strong>Kecamatan:</strong> ${location.district}</p>
                        <p><strong>Kabupaten/Kota:</strong> ${location.regency}</p>
                        <p><strong>Provinsi:</strong> ${location.province}</p>
                        <p><strong>Kode Pos:</strong> ${location.code}</p>
                    </div>
                `,
                timer: 3000,
                showConfirmButton: false
            });
            
            console.log('Reverse geocoding success:', location);
        } else {
            throw new Error('Location not found');
        }
    } catch (error) {
        console.error('Reverse geocoding error:', error);
        
        // Clear form fields on error
        document.getElementById('province').value = '';
        document.getElementById('regency').value = '';
        document.getElementById('district').value = '';
        document.getElementById('village').value = '';
        document.getElementById('postal_code').value = '';
        
        // Update marker popup
        if (marker) {
            marker.setPopupContent(`
                <div class="text-center">
                    <strong>📍 Lokasi Pengiriman</strong><br>
                    <small>Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}</small><br>
                    <small class="text-red-600">❌ Alamat tidak ditemukan</small>
                </div>
            `);
        }
        
        Swal.fire({
            icon: 'warning',
            title: 'Alamat Tidak Ditemukan',
            text: 'Tidak dapat menemukan data alamat untuk lokasi ini. Silakan isi manual atau pilih lokasi lain.',
            confirmButtonText: 'OK'
        });
    }
}

/**
 * Get user's current location using browser geolocation API
 */
function getCurrentLocation() {
    if (!navigator.geolocation) {
        Swal.fire({
            icon: 'error',
            title: 'Tidak Didukung',
            text: 'Browser Anda tidak mendukung geolocation'
        });
        return;
    }

    // Show loading
    Swal.fire({
        title: 'Mencari Lokasi...',
        text: 'Mohon izinkan akses lokasi',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    navigator.geolocation.getCurrentPosition(
        // Success callback
        function(position) {
            Swal.close();
            
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            // Center map to user location
            map.setView([lat, lng], 16);
            
            // Place marker
            placeMarker(L.latLng(lat, lng));
            
            Swal.fire({
                icon: 'success',
                title: 'Lokasi Ditemukan!',
                text: 'Pin telah ditempatkan di lokasi Anda',
                timer: 2000,
                showConfirmButton: false
            });
        },
        // Error callback
        function(error) {
            Swal.close();
            
            let errorMessage = 'Gagal mendapatkan lokasi';
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage = 'Anda menolak akses lokasi. Silakan klik manual pada peta.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage = 'Informasi lokasi tidak tersedia';
                    break;
                case error.TIMEOUT:
                    errorMessage = 'Waktu permintaan lokasi habis';
                    break;
            }
            
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: errorMessage,
                confirmButtonText: 'OK'
            });
        },
        // Options
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

/**
 * Set map view to a specific location
 * @param {number} lat - Latitude
 * @param {number} lng - Longitude
 * @param {number} zoom - Zoom level (optional)
 */
function setMapView(lat, lng, zoom = 16) {
    if (map) {
        map.setView([lat, lng], zoom);
        placeMarker(L.latLng(lat, lng));
    }
}

/**
 * Initialize map when modal opens
 */
function openAddAddressModal() {
    document.getElementById('addAddressModal').classList.remove('hidden');
    
    // Initialize map after modal is visible
    setTimeout(() => {
        initMap();
    }, 200);
}

/**
 * Close modal and cleanup
 */
function closeAddAddressModal() {
    document.getElementById('addAddressModal').classList.add('hidden');
    document.getElementById('addAddressForm').reset();
    
    // Clear hidden inputs
    document.getElementById('latitude').value = '';
    document.getElementById('longitude').value = '';
    
    // Remove marker
    if (marker && map) {
        map.removeLayer(marker);
        marker = null;
    }
}

// Export functions for global use
window.initMap = initMap;
window.placeMarker = placeMarker;
window.getCurrentLocation = getCurrentLocation;
window.setMapView = setMapView;
window.openAddAddressModal = openAddAddressModal;
window.closeAddAddressModal = closeAddAddressModal;
