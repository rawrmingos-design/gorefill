/**
 * tracking.js
 * Week 3 Day 13: Real-Time Courier Tracking UI
 * 
 * Handles real-time courier location updates and map visualization
 */

let trackingMap;
let courierMarker;
let customerMarker;
let routeLine;
let updateInterval;
let courierId;
let customerLat;
let customerLng;

/**
 * Initialize tracking map
 */
function initTrackingMap(courierIdParam, courierLat, courierLng, custLat, custLng) {
    courierId = courierIdParam;
    customerLat = custLat;
    customerLng = custLng;

    // Calculate map center
    const centerLat = (courierLat + custLat) / 2;
    const centerLng = (courierLng + custLng) / 2;

    // Initialize map
    trackingMap = L.map('trackingMap').setView([centerLat, centerLng], 13);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(trackingMap);

    // Create courier icon
    const courierIcon = L.divIcon({
        html: '<i class="fas fa-shipping-fast text-3xl text-blue-600"></i>',
        className: 'courier-marker',
        iconSize: [40, 40],
        iconAnchor: [20, 20]
    });

    // Create customer icon
    const customerIcon = L.divIcon({
        html: '<i class="fas fa-home text-3xl text-green-600"></i>',
        className: 'customer-marker',
        iconSize: [40, 40],
        iconAnchor: [20, 40]
    });

    // Add courier marker
    courierMarker = L.marker([courierLat, courierLng], { 
        icon: courierIcon,
        title: 'Courier Location'
    }).addTo(trackingMap);

    courierMarker.bindPopup('<div class="text-center"><i class="fas fa-truck text-blue-600"></i> Courier</div>');

    // Add customer marker
    customerMarker = L.marker([custLat, custLng], {
        icon: customerIcon,
        title: 'Delivery Address'
    }).addTo(trackingMap);

    customerMarker.bindPopup('<div class="text-center"><i class="fas fa-home text-green-600"></i> Your Location</div>').openPopup();

    // Draw route line and calculate initial distance
    drawRouteLine(courierLat, courierLng, custLat, custLng);

    // Fit map bounds
    const bounds = L.latLngBounds([courierLat, courierLng], [custLat, custLng]);
    trackingMap.fitBounds(bounds, { padding: [50, 50] });

    // Calculate and display initial distance/ETA
    const initialDistance = calculateDistance(courierLat, courierLng, custLat, custLng);
    updateDistanceDisplay(initialDistance);

    // Start real-time updates
    startLocationUpdates();

    console.log('Tracking map initialized - Distance:', initialDistance.toFixed(2), 'km');
}

/**
 * Draw route line between two points
 */
function drawRouteLine(fromLat, fromLng, toLat, toLng) {
    if (routeLine) {
        trackingMap.removeLayer(routeLine);
    }

    routeLine = L.polyline([[fromLat, fromLng], [toLat, toLng]], {
        color: '#3b82f6',
        weight: 3,
        opacity: 0.7,
        dashArray: '10, 10',
        lineJoin: 'round'
    }).addTo(trackingMap);

    const distance = calculateDistance(fromLat, fromLng, toLat, toLng);
    updateDistanceDisplay(distance);
}

/**
 * Calculate distance (Haversine formula)
 */
function calculateDistance(lat1, lng1, lat2, lng2) {
    const R = 6371; // Earth radius in km
    const dLat = toRad(lat2 - lat1);
    const dLng = toRad(lng2 - lng1);

    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
              Math.sin(dLng / 2) * Math.sin(dLng / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function toRad(degrees) {
    return degrees * (Math.PI / 180);
}

/**
 * Update distance display
 */
function updateDistanceDisplay(distanceKm) {
    const distanceEl = document.getElementById('distanceDisplay');
    if (distanceEl) {
        if (distanceKm < 1) {
            distanceEl.textContent = Math.round(distanceKm * 1000) + ' m';
        } else {
            distanceEl.textContent = distanceKm.toFixed(2) + ' km';
        }
    }

    // Calculate ETA (30 km/h average)
    const etaEl = document.getElementById('etaDisplay');
    if (etaEl) {
        const avgSpeed = 30;
        const etaMinutes = Math.round((distanceKm / avgSpeed) * 60);
        
        if (etaMinutes < 1) {
            etaEl.textContent = 'Arriving soon';
        } else if (etaMinutes < 60) {
            etaEl.textContent = etaMinutes + ' minutes';
        } else {
            const hours = Math.floor(etaMinutes / 60);
            const mins = etaMinutes % 60;
            etaEl.textContent = hours + 'h ' + mins + 'm';
        }
    }
}

/**
 * Start real-time location updates
 */
function startLocationUpdates() {
    updateCourierLocation();
    updateInterval = setInterval(updateCourierLocation, 5000);
    console.log('Started location updates (every 5 seconds)');
}

/**
 * Fetch and update courier location
 */
async function updateCourierLocation() {
    try {
        const response = await fetch(`index.php?route=courier.getLocation&id=${courierId}`);
        const data = await response.json();

        if (data.success && data.data) {
            const { latitude, longitude, updated_at } = data.data;

            updateCourierMarker(latitude, longitude);
            drawRouteLine(latitude, longitude, customerLat, customerLng);
            updateTimestamp(updated_at);
            updateStatusIndicator('active', 'Tracking active');

            console.log('Location updated:', latitude, longitude);
        } else {
            console.warn('Location not available');
            updateStatusIndicator('warning', 'Location unavailable');
        }
    } catch (error) {
        console.error('Error fetching location:', error);
        updateStatusIndicator('error', 'Connection error');
    }
}

/**
 * Update courier marker with animation
 */
function updateCourierMarker(newLat, newLng) {
    if (courierMarker) {
        const currentLatLng = courierMarker.getLatLng();
        
        // Animate marker movement
        const steps = 20;
        const latStep = (newLat - currentLatLng.lat) / steps;
        const lngStep = (newLng - currentLatLng.lng) / steps;
        
        let currentStep = 0;
        const animationInterval = setInterval(() => {
            currentStep++;
            const animLat = currentLatLng.lat + (latStep * currentStep);
            const animLng = currentLatLng.lng + (lngStep * currentStep);
            
            courierMarker.setLatLng([animLat, animLng]);
            
            if (currentStep >= steps) {
                clearInterval(animationInterval);
            }
        }, 25);
    }
}

/**
 * Update timestamp display
 */
function updateTimestamp(timestamp) {
    const timestampEl = document.getElementById('lastUpdated');
    if (timestampEl) {
        const date = new Date(timestamp);
        const now = new Date();
        const diffSeconds = Math.floor((now - date) / 1000);

        let displayText;
        if (diffSeconds < 10) {
            displayText = 'Just now';
        } else if (diffSeconds < 60) {
            displayText = diffSeconds + ' seconds ago';
        } else if (diffSeconds < 3600) {
            const minutes = Math.floor(diffSeconds / 60);
            displayText = minutes + ' minute' + (minutes > 1 ? 's' : '') + ' ago';
        } else {
            displayText = date.toLocaleTimeString();
        }

        timestampEl.textContent = displayText;
    }
}

/**
 * Update status indicator
 */
function updateStatusIndicator(status, message) {
    const statusEl = document.getElementById('trackingStatus');
    if (statusEl) {
        const colors = {
            'active': 'bg-green-100 text-green-800',
            'warning': 'bg-yellow-100 text-yellow-800',
            'error': 'bg-red-100 text-red-800'
        };

        const icons = {
            'active': 'fa-check-circle',
            'warning': 'fa-exclamation-triangle',
            'error': 'fa-times-circle'
        };

        statusEl.className = `px-4 py-2 rounded-lg text-sm font-medium ${colors[status]}`;
        statusEl.innerHTML = `<i class="fas ${icons[status]} mr-1"></i>${message}`;
    }
}

/**
 * Stop location updates
 */
function stopLocationUpdates() {
    if (updateInterval) {
        clearInterval(updateInterval);
        console.log('Stopped location updates');
    }
}

/**
 * Center map on courier
 */
function centerOnCourier() {
    if (courierMarker && trackingMap) {
        const courierPos = courierMarker.getLatLng();
        trackingMap.setView(courierPos, 16, { animate: true });
        courierMarker.openPopup();
    }
}

/**
 * Center map on customer
 */
function centerOnCustomer() {
    if (customerMarker && trackingMap) {
        const customerPos = customerMarker.getLatLng();
        trackingMap.setView(customerPos, 16, { animate: true });
        customerMarker.openPopup();
    }
}

/**
 * Fit map to show both markers
 */
function fitBothMarkers() {
    if (courierMarker && customerMarker && trackingMap) {
        const courierPos = courierMarker.getLatLng();
        const customerPos = customerMarker.getLatLng();
        
        const bounds = L.latLngBounds([courierPos, customerPos]);
        trackingMap.fitBounds(bounds, { padding: [50, 50], animate: true });
    }
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    stopLocationUpdates();
});
