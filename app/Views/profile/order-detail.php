<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?php 
    // Load Midtrans config for client key
    $midtransConfig = require __DIR__ . '/../../../config/midtrans.php';
    $snapUrl = $midtransConfig['is_production'] ? $midtransConfig['snap_url']['production'] : $midtransConfig['snap_url']['sandbox'];
    ?>
    <!-- Midtrans Snap.js -->
    <script src="<?= $snapUrl ?>" data-client-key="<?= $midtransConfig['client_key'] ?>"></script>
</head>
<body class="bg-gray-50">
<?php require_once __DIR__ . '/../../Helpers/ImageHelper.php'; ?>

    <!-- Navbar -->
    <?php include __DIR__ . '../../layouts/navbar.php'; ?>
    

    <!-- Order Detail Content -->
    <div class="max-w-5xl mx-auto px-4 py-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="?route=profile.orders" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pesanan
            </a>
        </div>

        <!-- Order Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-start flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">
                        Order #<?= htmlspecialchars($order['order_number']) ?>
                    </h1>
                    <p class="text-gray-600">
                        <i class="fas fa-calendar"></i> 
                        <?= date('d M Y, H:i', strtotime($order['created_at'])) ?>
                    </p>
                </div>
                <div class="text-right">
                    <?php
                    $paymentStatusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'paid' => 'bg-green-100 text-green-800',
                        'failed' => 'bg-red-100 text-red-800',
                        'expired' => 'bg-gray-100 text-gray-800',
                        'cancelled' => 'bg-red-100 text-red-800'
                    ];
                    $paymentColor = $paymentStatusColors[$order['payment_status']] ?? 'bg-gray-100 text-gray-800';
                    
                    $orderStatusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'confirmed' => 'bg-blue-100 text-blue-800',
                        'packing' => 'bg-purple-100 text-purple-800',
                        'shipped' => 'bg-indigo-100 text-indigo-800',
                        'delivered' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800'
                    ];
                    $orderColor = $orderStatusColors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                    ?>
                    <div class="space-y-2">
                        <div>
                            <span class="text-xs text-gray-600">Pembayaran:</span>
                            <span class="block px-3 py-1 rounded-full text-sm font-semibold <?= $paymentColor ?>">
                                <?= strtoupper($order['payment_status']) ?>
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-600">Status Order:</span>
                            <span class="block px-3 py-1 rounded-full text-sm font-semibold <?= $orderColor ?>">
                                <?= strtoupper($order['status']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-4 pt-4 border-t flex gap-2 flex-wrap">
                <?php if ($order['payment_status'] === 'pending' && !empty($order['snap_token'])): ?>
                    <button onclick="payNow('<?= htmlspecialchars($order['snap_token']) ?>')" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold">
                        <i class="fas fa-credit-card"></i> Bayar Sekarang
                    </button>
                    <div class="w-full text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <i class="fas fa-info-circle"></i> Pesanan Anda menunggu pembayaran. Klik "Bayar Sekarang" untuk melanjutkan pembayaran.
                    </div>
                <?php endif; ?>
                
                <?php if ($order['payment_status'] === 'paid'): ?>
                    <a href="?route=profile.invoice&order_number=<?= urlencode($order['order_number']) ?>" 
                       target="_blank"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-file-invoice"></i> Lihat Invoice
                    </a>
                    <?php if ($order['status'] === 'shipped'): ?>
                        <a href="index.php?route=order.track&id=<?= urlencode($order['order_number']) ?>" 
                           class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg inline-block">
                            <i class="fas fa-map-marker-alt"></i> Lacak Pengiriman
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Order Items & Summary -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Produk yang Dibeli</h2>
                    <div class="space-y-4">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="flex items-center gap-4 pb-4 border-b last:border-b-0">
                                <?php
                                $itemImageUrl = ImageHelper::getImageUrl($item['product_image']);
                                if ($itemImageUrl): ?>
                                    <img src="<?= htmlspecialchars($itemImageUrl) ?>" 
                                         alt="<?= htmlspecialchars($item['product_name']) ?>"
                                         class="w-20 h-20 object-cover rounded"
                                         onerror="this.onerror=null; this.src='/public/assets/images/placeholder.jpg'">
                                <?php else: ?>
                                    <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                        <span class="text-3xl">📦</span>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($item['product_name']) ?></h3>
                                    <p class="text-sm text-gray-600">
                                        Rp <?= number_format($item['price'], 0, ',', '.') ?> × <?= $item['quantity'] ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-800">
                                        Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Ringkasan Pembayaran</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal</span>
                            <span>Rp <?= number_format($order['subtotal'], 0, ',', '.') ?></span>
                        </div>
                        <?php if ($order['discount_amount'] > 0): ?>
                            <div class="flex justify-between text-green-600">
                                <span>Diskon</span>
                                <span>- Rp <?= number_format($order['discount_amount'], 0, ',', '.') ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="border-t pt-3 flex justify-between text-lg font-bold text-gray-800">
                            <span>Total</span>
                            <span>Rp <?= number_format($order['total'], 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Shipping & Payment Info -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-map-marker-alt text-blue-600"></i> Alamat Pengiriman
                    </h2>
                    <div class="text-gray-700 space-y-2">
                        <p class="font-semibold"><?= htmlspecialchars($order['shipping_name']) ?></p>
                        <p><?= htmlspecialchars($order['shipping_phone']) ?></p>
                        <p class="text-sm"><?= htmlspecialchars($order['shipping_address']) ?></p>
                        <p class="text-sm"><?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_postal_code']) ?></p>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-credit-card text-blue-600"></i> Informasi Pembayaran
                    </h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Metode:</span>
                            <span class="font-semibold text-gray-800 uppercase">
                                <?= htmlspecialchars($order['payment_method'] ?? 'N/A') ?>
                            </span>
                        </div>
                        <?php if ($order['transaction_id']): ?>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Transaction ID:</span>
                                <span class="font-mono text-xs text-gray-800">
                                    <?= htmlspecialchars($order['transaction_id']) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <?php if ($order['paid_at']): ?>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Dibayar:</span>
                                <span class="text-gray-800">
                                    <?= date('d M Y, H:i', strtotime($order['paid_at'])) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tracking Info (if shipped) -->
                <?php if ($order['status'] === 'shipped' && $order['tracking_number']): ?>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-blue-800 mb-4">
                            <i class="fas fa-truck"></i> Informasi Pengiriman
                        </h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-blue-700">Kurir:</span>
                                <span class="font-semibold text-blue-900 uppercase">
                                    <?= htmlspecialchars($order['courier'] ?? 'N/A') ?>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">No. Resi:</span>
                                <span class="font-mono text-blue-900">
                                    <?= htmlspecialchars($order['tracking_number']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white shadow-lg mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-gray-600">
            <p>&copy; 2025 GoRefill. All rights reserved.</p>
        </div>
    </footer>

    <!-- Midtrans Snap -->
    <script>
        function payNow(snapToken) {
            if (!snapToken) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Token pembayaran tidak tersedia'
                });
                return;
            }
            
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    window.location.href = 'index.php?route=payment.success&order_id=' + result.order_id;
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Menunggu pembayaran',
                        text: 'Silakan selesaikan pembayaran Anda'
                    });
                },
                onError: function(result) {
                    console.log('Payment error:', result);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat memproses pembayaran.'
                    });
                },
                onClose: function() {
                    console.log('Payment popup closed');
                    Swal.fire({
                        icon: 'info',
                        title: 'Anda menutup popup pembayaran',
                        text: 'Anda bisa melanjutkan pembayaran kapan saja dari halaman pesanan.'
                    });
                }
            });
        }
    </script>
</body>
</html>
