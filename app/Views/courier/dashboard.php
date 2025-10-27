<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Courier Dashboard') ?> - GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-green-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="fas fa-truck text-3xl"></i>
                    <div>
                        <h1 class="text-2xl font-bold">Courier Dashboard</h1>
                        <p class="text-sm text-green-100">
                            Welcome, <?= htmlspecialchars($_SESSION['name']) ?>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Location Status -->
                    <div id="locationStatus" class="bg-green-700 px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-satellite-dish mr-1"></i>
                        <span>Checking GPS...</span>
                    </div>
                    <a href="index.php?route=auth.logout" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-6">
        
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Ready to Ship</p>
                        <p class="text-2xl font-bold text-blue-600">
                            <?= count(array_filter($orders, fn($o) => $o['status'] === 'packing')) ?>
                        </p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-box text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">In Delivery</p>
                        <p class="text-2xl font-bold text-orange-600">
                            <?= count(array_filter($orders, fn($o) => $o['status'] === 'shipped')) ?>
                        </p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-shipping-fast text-orange-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Delivered Today</p>
                        <p class="text-2xl font-bold text-green-600">
                            <?= count(array_filter($orders, fn($o) => $o['status'] === 'delivered')) ?>
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b">
                <h2 class="text-xl font-semibold flex items-center">
                    <i class="fas fa-list-ul text-green-600 mr-2"></i>
                    My Deliveries
                </h2>
            </div>

            <?php if (empty($orders)): ?>
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-6xl mb-4 text-gray-300"></i>
                    <p class="text-lg">No deliveries assigned yet</p>
                    <p class="text-sm">Orders will appear here when assigned to you</p>
                </div>
            <?php else: ?>
                <div class="divide-y">
                    <?php foreach ($orders as $order): ?>
                        <div class="p-4 hover:bg-gray-50 transition" id="order-<?= $order['id'] ?>">
                            <div class="flex items-start justify-between gap-4">
                                <!-- Order Info -->
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="font-semibold text-lg">
                                            <?= htmlspecialchars($order['order_number']) ?>
                                        </h3>
                                        <?php
                                        $statusColors = [
                                            'packing' => 'bg-blue-100 text-blue-800',
                                            'shipped' => 'bg-orange-100 text-orange-800',
                                            'delivered' => 'bg-green-100 text-green-800'
                                        ];
                                        $statusIcons = [
                                            'packing' => 'fa-box',
                                            'shipped' => 'fa-shipping-fast',
                                            'delivered' => 'fa-check-circle'
                                        ];
                                        $statusLabels = [
                                            'packing' => 'Ready to Ship',
                                            'shipped' => 'In Delivery',
                                            'delivered' => 'Delivered'
                                        ];
                                        ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-medium <?= $statusColors[$order['status']] ?>">
                                            <i class="fas <?= $statusIcons[$order['status']] ?> mr-1"></i>
                                            <?= $statusLabels[$order['status']] ?>
                                        </span>
                                    </div>

                                    <!-- Customer Info -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <p class="text-gray-600">
                                                <i class="fas fa-user text-gray-400 mr-1"></i>
                                                <strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?>
                                            </p>
                                            <p class="text-gray-600">
                                                <i class="fas fa-phone text-gray-400 mr-1"></i>
                                                <strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone'] ?? $order['shipping_phone']) ?>
                                            </p>
                                            <p class="text-gray-600">
                                                <i class="fas fa-shopping-bag text-gray-400 mr-1"></i>
                                                <strong>Items:</strong> <?= $order['item_count'] ?> item(s)
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">
                                                <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                                                <strong>Address:</strong>
                                            </p>
                                            <p class="text-gray-700 ml-5">
                                                <?= htmlspecialchars($order['shipping_address']) ?><br>
                                                <?= htmlspecialchars($order['shipping_city']) ?> 
                                                <?php if ($order['shipping_postal_code']): ?>
                                                    - <?= htmlspecialchars($order['shipping_postal_code']) ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Total & Time -->
                                    <div class="flex items-center gap-4 mt-3 text-sm">
                                        <span class="font-semibold text-green-600 text-lg">
                                            Rp <?= number_format($order['total'], 0, ',', '.') ?>
                                        </span>
                                        <span class="text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>
                                            <?= date('d M Y, H:i', strtotime($order['created_at'])) ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col gap-2">
                                    <?php if ($order['status'] === 'packing'): ?>
                                        <button onclick="startDelivery(<?= $order['id'] ?>)"
                                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap">
                                            <i class="fas fa-play mr-1"></i> Start Delivery
                                        </button>
                                    <?php elseif ($order['status'] === 'shipped'): ?>
                                        <button onclick="completeDelivery(<?= $order['id'] ?>)"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap">
                                            <i class="fas fa-check mr-1"></i> Mark Delivered
                                        </button>
                                    <?php elseif ($order['status'] === 'delivered'): ?>
                                        <div class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium text-center">
                                            <i class="fas fa-check-circle mr-1 text-green-600"></i> Completed
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($order['shipping_latitude'] && $order['shipping_longitude']): ?>
                                        <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $order['shipping_latitude'] ?>,<?= $order['shipping_longitude'] ?>" 
                                           target="_blank"
                                           class="bg-white border-2 border-blue-600 text-blue-600 hover:bg-blue-50 px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap text-center">
                                            <i class="fas fa-map-marked-alt mr-1"></i> Navigate
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Location Tracking Script -->
    <script>
        let locationInterval;

        // Check if location tracking is supported
        function checkLocationSupport() {
            if (!navigator.geolocation) {
                updateLocationStatus('error', 'GPS not supported');
                return false;
            }
            return true;
        }

        // Update location status indicator
        function updateLocationStatus(status, message) {
            const statusEl = document.getElementById('locationStatus');
            const icons = {
                'active': 'fa-satellite-dish',
                'error': 'fa-exclamation-triangle',
                'inactive': 'fa-times-circle'
            };
            const colors = {
                'active': 'bg-green-700',
                'error': 'bg-red-600',
                'inactive': 'bg-gray-600'
            };

            statusEl.className = `${colors[status]} px-4 py-2 rounded-lg text-sm`;
            statusEl.innerHTML = `<i class="fas ${icons[status]} mr-1"></i><span>${message}</span>`;
        }

        // Send location to server
        function updateLocation() {
            if (!checkLocationSupport()) return;

            navigator.geolocation.getCurrentPosition(
                // Success callback
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    fetch('index.php?route=courier.updateLocation', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            latitude: lat,
                            longitude: lng
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateLocationStatus('active', 'GPS Active');
                            console.log('Location updated:', lat, lng);
                        } else {
                            updateLocationStatus('error', 'Update failed');
                        }
                    })
                    .catch(error => {
                        console.error('Location update error:', error);
                        updateLocationStatus('error', 'Network error');
                    });
                },
                // Error callback
                function(error) {
                    console.error('Geolocation error:', error);
                    updateLocationStatus('error', 'GPS error');
                },
                // Options
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        // Start delivery
        function startDelivery(orderId) {
            Swal.fire({
                title: 'Start Delivery?',
                text: 'You will begin delivering this order',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Start Delivery',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('index.php?route=courier.startDelivery', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'order_id=' + orderId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Delivery Started!',
                                text: 'Good luck with your delivery',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Failed to start delivery', 'error');
                    });
                }
            });
        }

        // Complete delivery
        function completeDelivery(orderId) {
            Swal.fire({
                title: 'Mark as Delivered?',
                text: 'Confirm that this order has been delivered',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Delivered',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('index.php?route=courier.completeDelivery', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'order_id=' + orderId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Delivery Completed!',
                                text: 'Great job!',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Failed to complete delivery', 'error');
                    });
                }
            });
        }

        // Initialize location tracking with watchPosition for continuous updates
        let watchId;
        if (checkLocationSupport()) {
            // Show initial status
            updateLocationStatus('active', 'Initializing GPS...');

            // Use watchPosition for continuous real-time tracking
            watchId = navigator.geolocation.watchPosition(
                // Success callback
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    fetch('index.php?route=courier.updateLocation', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            latitude: lat,
                            longitude: lng
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateLocationStatus('active', 'GPS Active • Live Tracking');
                            console.log('Location updated:', lat, lng);
                        } else {
                            updateLocationStatus('error', 'Update failed');
                        }
                    })
                    .catch(error => {
                        console.error('Location update error:', error);
                        updateLocationStatus('error', 'Network error');
                    });
                },
                // Error callback
                function(error) {
                    console.error('Geolocation error:', error);
                    updateLocationStatus('error', 'GPS error');
                },
                // Options for continuous tracking
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (watchId) {
                navigator.geolocation.clearWatch(watchId);
            }
        });
    </script>
</body>
</html>
