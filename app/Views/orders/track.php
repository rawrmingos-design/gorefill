<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Track Order') ?> - GoRefill</title>
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
        #trackingMap {
            height: 500px;
            width: 100%;
            border-radius: 0.5rem;
        }
        .courier-marker, .customer-marker {
            background: none;
            border: none;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php require_once __DIR__ . '/../../Helpers/ImageHelper.php'; ?>
    <!-- Navbar -->
    <?php include __DIR__ . '/../layouts/navbar.php'; ?>

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 text-white py-6 shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center gap-3 mb-2">
                <a href="index.php?route=profile.orders" class="hover:text-green-200">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold">Track Order</h1>
                    <p class="text-sm text-green-100 mt-1">Order #<?= htmlspecialchars($order['order_number']) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-6">
        
        <?php if ($order['status'] === 'shipped' && $courierLocation): ?>
            <!-- Real-Time Tracking Section -->
            <div class="bg-white rounded-lg shadow-lg mb-6 overflow-hidden">
                <!-- Tracking Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4 text-white">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <h2 class="text-xl font-bold flex items-center">
                                <i class="fas fa-map-marked-alt mr-2"></i>
                                Live Tracking
                            </h2>
                            <p class="text-sm text-blue-100">Your order is on the way!</p>
                        </div>
                        <div id="trackingStatus" class="bg-green-100 text-green-800 px-4 py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-check-circle mr-1"></i> Tracking active
                        </div>
                    </div>
                </div>

                <!-- Tracking Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 border-b">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-route text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Distance</p>
                            <p class="text-lg font-bold text-gray-800" id="distanceDisplay">Calculating...</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-clock text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Est. Arrival</p>
                            <p class="text-lg font-bold text-gray-800" id="etaDisplay">Calculating...</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="bg-purple-100 p-3 rounded-full">
                            <i class="fas fa-sync-alt text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Last Updated</p>
                            <p class="text-sm font-medium text-gray-800" id="lastUpdated">Just now</p>
                        </div>
                    </div>
                </div>

                <!-- Map Container -->
                <div class="p-4">
                    <div id="trackingMap" class="shadow-md"></div>
                    
                    <!-- Map Controls -->
                    <div class="flex gap-2 mt-3">
                        <button onclick="centerOnCourier()" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-truck mr-1"></i> Center on Courier
                        </button>
                        <button onclick="centerOnCustomer()" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-home mr-1"></i> Center on Destination
                        </button>
                        <button onclick="fitBothMarkers()" 
                                class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-expand mr-1"></i> View All
                        </button>
                    </div>
                </div>

                <!-- Courier Info -->
                <?php if ($courier): ?>
                    <div class="p-4 bg-blue-50 border-t">
                        <h3 class="font-semibold text-gray-800 mb-2 flex items-center">
                            <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                            Your Courier
                        </h3>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($courier['name']) ?></p>
                                <?php if ($courier['phone']): ?>
                                    <p class="text-sm text-gray-600">
                                        <i class="fas fa-phone mr-1"></i>
                                        <?= htmlspecialchars($courier['phone']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <?php if ($courier['phone']): ?>
                                <a href="tel:<?= htmlspecialchars($courier['phone']) ?>" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                    <i class="fas fa-phone-alt mr-1"></i> Call
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($order['status'] === 'shipped' && !$courierLocation): ?>
            <!-- No Location Available -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-3xl"></i>
                    <div>
                        <h3 class="font-semibold text-gray-800">Tracking Temporarily Unavailable</h3>
                        <p class="text-sm text-gray-600">Courier location is not currently available. Please check back shortly.</p>
                    </div>
                </div>
            </div>

        <?php elseif ($order['status'] === 'delivered'): ?>
            <!-- Order Delivered -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-600 text-3xl"></i>
                    <div>
                        <h3 class="font-semibold text-gray-800">Order Delivered!</h3>
                        <p class="text-sm text-gray-600">Your order has been successfully delivered.</p>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Order Not Shipped Yet -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <div class="flex items-center gap-3">
                    <i class="fas fa-info-circle text-blue-600 text-3xl"></i>
                    <div>
                        <h3 class="font-semibold text-gray-800">Order <?= ucfirst($order['status']) ?></h3>
                        <p class="text-sm text-gray-600">Real-time tracking will be available once your order is shipped.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Order Details -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-shopping-bag text-green-600 mr-2"></i>
                Order Details
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Order Info -->
                <div>
                    <h3 class="font-semibold text-gray-700 mb-3">Order Information</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order Number:</span>
                            <span class="font-medium"><?= htmlspecialchars($order['order_number']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium capitalize"><?= htmlspecialchars($order['status']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order Date:</span>
                            <span class="font-medium"><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total:</span>
                            <span class="font-bold text-green-600">Rp <?= number_format($order['total'], 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Delivery Address -->
                <div>
                    <h3 class="font-semibold text-gray-700 mb-3">Delivery Address</h3>
                    <div class="text-sm text-gray-600">
                        <p class="font-medium text-gray-800 mb-1"><?= htmlspecialchars($order['shipping_name']) ?></p>
                        <p><?= htmlspecialchars($order['shipping_phone']) ?></p>
                        <p class="mt-2"><?= htmlspecialchars($order['shipping_address']) ?></p>
                        <p><?= htmlspecialchars($order['shipping_city']) ?> <?= htmlspecialchars($order['shipping_postal_code']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <h3 class="font-semibold text-gray-700 mb-3 border-t pt-4">Order Items</h3>
            <div class="space-y-3">
                <?php foreach ($items as $item): ?>
                    <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                        <?php
                        $imageUrl = ImageHelper::getImageUrl($item['product_image'] ?? null);
                        if ($imageUrl): ?>
                            <img src="<?= htmlspecialchars($imageUrl) ?>" 
                                 alt="<?= htmlspecialchars($item['product_name']) ?>"
                                 class="w-16 h-16 object-cover rounded">
                        <?php endif; ?>
                        <div class="flex-1">
                            <p class="font-medium"><?= htmlspecialchars($item['product_name']) ?></p>
                            <p class="text-sm text-gray-600">Qty: <?= $item['quantity'] ?> × Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                        </div>
                        <p class="font-semibold">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../layouts/footer.php'; ?>
<script src="public/assets/js/cart.js"></script>
    <script src="public/assets/js/favorites.js"></script>
    <!-- Load tracking.js -->
    <script src="public/assets/js/tracking.js"></script>
    
    <?php if ($order['status'] === 'shipped' && $courierLocation && $order['shipping_latitude'] && $order['shipping_longitude']): ?>
        <!-- Initialize tracking map -->
        <script>
            console.log('🗺️ Initializing map with data:', {
                courierId: <?= $order['courier_id'] ?>,
                courierLat: <?= $courierLocation['lat'] ?>,
                courierLng: <?= $courierLocation['lng'] ?>,
                customerLat: <?= $order['shipping_latitude'] ?>,
                customerLng: <?= $order['shipping_longitude'] ?>
            });
            
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof initTrackingMap === 'function') {
                    initTrackingMap(
                        <?= $order['courier_id'] ?>,
                        <?= $courierLocation['lat'] ?>,
                        <?= $courierLocation['lng'] ?>,
                        <?= $order['shipping_latitude'] ?>,
                        <?= $order['shipping_longitude'] ?>
                    );
                } else {
                    console.error('❌ initTrackingMap function not found! tracking.js may not be loaded.');
                }
            });
        </script>
    <?php else: ?>
        <script>
            console.warn('⚠️ Map not initialized. Debug info:', {
                status: '<?= $order['status'] ?>',
                hasCourierLocation: <?= $courierLocation ? 'true' : 'false' ?>,
                hasShippingLat: <?= $order['shipping_latitude'] ? 'true' : 'false' ?>,
                hasShippingLng: <?= $order['shipping_longitude'] ? 'true' : 'false' ?>
            });
        </script>
    <?php endif; ?>
</body>
</html>
