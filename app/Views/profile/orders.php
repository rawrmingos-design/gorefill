<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php 
    // Load Midtrans config for client key
    $midtransConfig = require __DIR__ . '/../../../config/midtrans.php';
    $snapUrl = $midtransConfig['is_production'] ? $midtransConfig['snap_url']['production'] : $midtransConfig['snap_url']['sandbox'];
    ?>
    <!-- Midtrans Snap.js -->
    <script src="<?= $snapUrl ?>" data-client-key="<?= $midtransConfig['client_key'] ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">

   <!-- Navbar -->
    <?php include __DIR__ . '/../layouts/navbar.php'; ?>
    
    <!-- Orders List -->
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="?route=profile" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left"></i> Kembali ke Profile
            </a>
        </div>

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-box"></i> Semua Pesanan
            </h1>
            <p class="text-gray-600 mt-2">Riwayat pesanan Anda</p>
        </div>

        <!-- Orders List -->
        <?php if (empty($orders)): ?>
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-600 text-lg mb-4">Belum ada pesanan</p>
                <a href="?route=products" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
                    <i class="fas fa-shopping-cart"></i> Mulai Belanja
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($orders as $order): ?>
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <div class="flex justify-between items-start flex-wrap gap-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-800 mb-2">
                                    Order #<?= htmlspecialchars($order['order_number']) ?>
                                </h3>
                                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                    <span>
                                        <i class="fas fa-calendar"></i> 
                                        <?= date('d M Y, H:i', strtotime($order['created_at'])) ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-credit-card"></i> 
                                        <?= htmlspecialchars($order['payment_method'] ?? 'N/A') ?>
                                    </span>
                                </div>
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
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?= $paymentColor ?>">
                                        <?= strtoupper($order['payment_status']) ?>
                                    </span>
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?= $orderColor ?>">
                                        <?= strtoupper($order['status']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t flex justify-between items-center flex-wrap gap-4">
                            <div>
                                <p class="text-2xl font-bold text-gray-800">
                                    Rp <?= number_format($order['total'], 0, ',', '.') ?>
                                </p>
                                <?php if ($order['discount_amount'] > 0): ?>
                                    <p class="text-sm text-green-600">
                                        Hemat Rp <?= number_format($order['discount_amount'], 0, ',', '.') ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="flex gap-2 flex-wrap">
                                <?php if ($order['payment_status'] === 'pending' && !empty($order['snap_token'])): ?>
                                    <button onclick="payNow('<?= htmlspecialchars($order['snap_token']) ?>')" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                                        <i class="fas fa-credit-card"></i> Bayar Sekarang
                                    </button>
                                <?php endif; ?>
                                <?php if ($order['status'] === 'shipped'): ?>
                                    <a href="index.php?route=order.track&id=<?= urlencode($order['order_number']) ?>" 
                                       class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                                        <i class="fas fa-map-marker-alt"></i> Track Order
                                    </a>
                                <?php endif; ?>
                                <a href="?route=profile.orderDetail&order_number=<?= urlencode($order['order_number']) ?>" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                                <?php if ($order['payment_status'] === 'paid'): ?>
                                    <a href="?route=profile.invoice&order_number=<?= urlencode($order['order_number']) ?>" 
                                       target="_blank"
                                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                                        <i class="fas fa-file-invoice"></i> Invoice
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="mt-8 flex justify-center">
                    <nav class="flex gap-2">
                        <?php if ($currentPage > 1): ?>
                            <a href="?route=profile.orders&page=<?= $currentPage - 1 ?>" 
                               class="px-4 py-2 bg-white border rounded-lg hover:bg-gray-50">
                                <i class="fas fa-chevron-left"></i> Prev
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == $currentPage): ?>
                                <span class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold">
                                    <?= $i ?>
                                </span>
                            <?php elseif ($i == 1 || $i == $totalPages || abs($i - $currentPage) <= 2): ?>
                                <a href="?route=profile.orders&page=<?= $i ?>" 
                                   class="px-4 py-2 bg-white border rounded-lg hover:bg-gray-50">
                                    <?= $i ?>
                                </a>
                            <?php elseif (abs($i - $currentPage) == 3): ?>
                                <span class="px-4 py-2">...</span>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?route=profile.orders&page=<?= $currentPage + 1 ?>" 
                               class="px-4 py-2 bg-white border rounded-lg hover:bg-gray-50">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>
<script src="public/assets/js/cart.js"></script>
    <script src="public/assets/js/favorites.js"></script>
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
