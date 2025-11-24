<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Leaflet.js for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
            crossorigin=""></script>
    
    <style>
        #orderMap {
            height: 300px;
            width: 100%;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body class="bg-gray-50">
<?php require_once __DIR__ . '/../../../Helpers/ImageHelper.php'; ?>
    <!-- Admin Navbar -->
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="index.php?route=admin.orders" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>

        <!-- Order Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-start flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Order #<?= htmlspecialchars($order['order_number']) ?>
                    </h1>
                    <p class="text-gray-600 mt-1">
                        <i class="fas fa-calendar"></i> 
                        <?= date('d M Y, H:i', strtotime($order['created_at'])) ?>
                    </p>
                </div>
                <div class="text-right">
                    <?php
                    $paymentColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'paid' => 'bg-green-100 text-green-800',
                        'failed' => 'bg-red-100 text-red-800'
                    ];
                    $orderColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'confirmed' => 'bg-blue-100 text-blue-800',
                        'packing' => 'bg-purple-100 text-purple-800',
                        'shipped' => 'bg-indigo-100 text-indigo-800',
                        'delivered' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800'
                    ];
                    ?>
                    <div class="space-y-2">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold <?= $paymentColors[$order['payment_status']] ?? 'bg-gray-100 text-gray-800' ?>">
                            Payment: <?= strtoupper($order['payment_status']) ?>
                        </span>
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold <?= $orderColors[$order['status']] ?? 'bg-gray-100 text-gray-800' ?>">
                            Order: <?= strtoupper($order['status']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Order Items -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">
                        <i class="fas fa-box-open"></i> Order Items
                    </h2>
                    <div class="space-y-4">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="flex items-center gap-4 pb-4 border-b last:border-b-0">
                                <?php
                                $imageUrl = ImageHelper::getImageUrl($item['product_image']);
                                if ($imageUrl): ?>
                                    <img src="<?= htmlspecialchars($imageUrl) ?>" 
                                         alt="<?= htmlspecialchars($item['product_name']) ?>"
                                         class="w-16 h-16 object-cover rounded"
                                         onerror="this.onerror=null; this.src='<?= asset('images/placeholder.jpg') ?>'">
                                <?php else: ?>
                                    <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                        <span class="text-2xl">📦</span>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-1">
                                    <h3 class="font-semibold"><?= htmlspecialchars($item['product_name'] ?? 'Product') ?></h3>
                                    <p class="text-sm text-gray-600">
                                        Rp <?= number_format($item['price'] ?? 0, 0, ',', '.') ?> × <?= $item['quantity'] ?? 0 ?>
                                    </p>
                                </div>
                                <div class="text-right font-bold">
                                    Rp <?= number_format($item['subtotal'] ?? 0, 0, ',', '.') ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="mt-4 pt-4 border-t space-y-2">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal</span>
                            <span>Rp <?= number_format($order['subtotal'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                        <?php if (($order['discount_amount'] ?? 0) > 0): ?>
                            <div class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span>- Rp <?= number_format($order['discount_amount'] ?? 0, 0, ',', '.') ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t">
                            <span>Total</span>
                            <span>Rp <?= number_format($order['total'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address on Map -->
                <?php if ($order['shipping_latitude'] && $order['shipping_longitude']): ?>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-4">
                            <i class="fas fa-map-marker-alt"></i> Delivery Location
                        </h2>
                        <div id="orderMap" class="mb-4"></div>
                        <div class="text-sm text-gray-700">
                            <p class="font-semibold"><?= htmlspecialchars($order['shipping_name']) ?></p>
                            <p><?= htmlspecialchars($order['shipping_phone']) ?></p>
                            <p><?= htmlspecialchars($order['shipping_address']) ?></p>
                            <p><?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_postal_code']) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Column -->
            <div class="lg:col-span-1 space-y-6">
                
                <!-- Customer Info -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">
                        <i class="fas fa-user"></i> Customer
                    </h2>
                    <div class="space-y-2 text-sm">
                        <p class="font-semibold text-gray-900"><?= htmlspecialchars($customer['name']) ?></p>
                        <p class="text-gray-600"><?= htmlspecialchars($customer['email']) ?></p>
                        <?php if ($customer['phone']): ?>
                            <p class="text-gray-600"><?= htmlspecialchars($customer['phone']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Status Workflow -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">
                        <i class="fas fa-tasks"></i> Order Status
                    </h2>
                    
                    <?php if ($order['payment_status'] === 'paid'): ?>
                        <div class="space-y-3">
                            <button onclick="<?php 
                                if ($order['payment_status'] === 'paid') {
                                    echo "updateStatus('confirmed')";
                                } else {
                                    echo "alert('Pembayaran belum dikonfirmasi!')";
                                }
                            ?>" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg 
                                <?= $order['status'] !== 'pending' && $order['status'] !== 'confirmed' ? 'opacity-50 cursor-not-allowed' : '' ?>"
    
                            <?php 
                            if ($order['status'] !== 'confirmed') {
                                echo 'disabled';
                            } 
                            ?>
                                >
                                <i class="fas fa-check"></i> Confirm Order
                            </button>
                            <button onclick="<?php 
                                if ($order['status'] === 'packing') {
                                    echo "updateStatus('packing')";
                                } else {
                                    echo "alert('Pembayaran belum dikonfirmasi!')";
                                }
                            ?>" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg 
                                <?= $order['payment_status'] !== 'pending' && $order['status'] !== 'packing' ? 'opacity-50 cursor-not-allowed' : '' ?>"
    
                            <?php 
                            if ($order['status'] !== 'packing') {
                                echo 'disabled';
                            } 
                            ?>
                                >
                                <i class="fas fa-box"></i> Start Packing
                            </button>
                            <button onclick="updateStatus('cancelled')" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-times"></i> Cancel Order
                            </button>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-gray-600">Order status can only be updated after payment is confirmed.</p>
                    <?php endif; ?>
                </div>

                <!-- Courier Assignment -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">
                        <i class="fas fa-motorcycle"></i> Courier Assignment
                    </h2>
                    
                    <?php if ($courier): ?>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <p class="font-semibold text-blue-900"><?= htmlspecialchars($courier['name']) ?></p>
                            <p class="text-sm text-blue-700"><?= htmlspecialchars($courier['email']) ?></p>
                            <?php if ($courier['phone']): ?>
                                <p class="text-sm text-blue-700"><?= htmlspecialchars($courier['phone']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form id="assignCourierForm" class="space-y-3">
                        <select id="courierSelect" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Courier</option>
                            <?php foreach ($couriers as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $courier && $courier['id'] == $c['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" onclick="assignCourier()" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-user-check"></i> Assign Courier
                        </button>
                    </form>
                </div>

                <!-- Tracking Link -->
                <?php if ($order['status'] === 'shipped'): ?>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-4">
                            <i class="fas fa-route"></i> Tracking
                        </h2>
                        <a href="index.php?route=order.track&id=<?= urlencode($order['order_number']) ?>" 
                           target="_blank"
                           class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-map-marked-alt"></i> View Live Tracking
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../../layouts/footer.php'; ?>

    <script>
        const orderNumber = '<?= htmlspecialchars($order['order_number']) ?>';
        
        // Initialize map if coordinates available
        <?php if ($order['shipping_latitude'] && $order['shipping_longitude']): ?>
            const map = L.map('orderMap').setView([<?= $order['shipping_latitude'] ?>, <?= $order['shipping_longitude'] ?>], 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            
            const customerIcon = L.divIcon({
                html: '<i class="fas fa-home text-2xl text-red-600"></i>',
                className: 'delivery-marker',
                iconSize: [30, 30],
                iconAnchor: [15, 30]
            });
            
            L.marker([<?= $order['shipping_latitude'] ?>, <?= $order['shipping_longitude'] ?>], {
                icon: customerIcon
            }).addTo(map).bindPopup('Delivery Location');
        <?php endif; ?>
        
        // Assign Courier
        function assignCourier() {
            const courierId = document.getElementById('courierSelect').value;
            
            if (!courierId) {
                Swal.fire('Error', 'Please select a courier', 'error');
                return;
            }
            
            Swal.fire({
                title: 'Assign Courier?',
                text: 'Are you sure you want to assign this courier?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Assign',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('order_number', orderNumber);
                    formData.append('courier_id', courierId);
                    
                    fetch('index.php?route=admin.assignCourier', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success', data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Failed to assign courier', 'error');
                    });
                }
            });
        }
        
        // Update Order Status
        function updateStatus(newStatus) {
            const statusNames = {
                'confirmed': 'Confirmed',
                'packing': 'Packing',
                'cancelled': 'Cancelled'
            };
            
            Swal.fire({
                title: 'Update Status?',
                text: `Change order status to ${statusNames[newStatus]}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Update',
                cancelButtonText: 'Cancel',
                confirmButtonColor: newStatus === 'cancelled' ? '#dc2626' : '#2563eb'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('order_number', orderNumber);
                    formData.append('status', newStatus);
                    
                    fetch('index.php?route=admin.updateOrderStatus', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success', data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Failed to update status', 'error');
                    });
                }
            });
        }
    </script>
</body>
</html>
