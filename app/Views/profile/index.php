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
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <?php include __DIR__ . '../../layouts/navbar.php'; ?>

    <!-- Profile Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: User Info -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="text-center mb-6">
                        <div class="w-24 h-24 bg-blue-600 rounded-full mx-auto flex items-center justify-center text-white text-3xl font-bold">
                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 mt-4"><?= htmlspecialchars($user['name']) ?></h2>
                        <p class="text-gray-600"><?= htmlspecialchars($user['email']) ?></p>
                        <?php if ($user['phone']): ?>
                            <p class="text-gray-600"><i class="fas fa-phone"></i> <?= htmlspecialchars($user['phone']) ?></p>
                        <?php endif; ?>
                        <p class="text-sm text-gray-500 mt-2">
                            Member sejak <?= date('d M Y', strtotime($user['created_at'])) ?>
                        </p>
                    </div>

                    <div class="border-t pt-4">
                        <a href="?route=profile.edit" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 rounded-lg mb-2">
                            <i class="fas fa-edit"></i> Edit Profile
                        </a>
                        <a href="?route=profile.orders" class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center py-2 rounded-lg">
                            <i class="fas fa-box"></i> Semua Pesanan
                        </a>
                    </div>
                </div>

                <!-- Order Statistics -->
                <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik Pesanan</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Pesanan</span>
                            <span class="font-bold text-gray-800"><?= $stats['total_orders'] ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">✅ Selesai</span>
                            <span class="font-bold text-green-600"><?= $stats['completed_orders'] ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">📦 Dikemas</span>
                            <span class="font-bold text-purple-600"><?= $stats['packing_orders'] ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">🚚 Dikirim</span>
                            <span class="font-bold text-blue-600"><?= $stats['shipped_orders'] ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">⏳ Pending</span>
                            <span class="font-bold text-yellow-600"><?= $stats['pending_orders'] ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">❌ Gagal</span>
                            <span class="font-bold text-red-600"><?= $stats['failed_orders'] ?></span>
                        </div>
                        <div class="border-t pt-3 flex justify-between items-center">
                            <span class="text-gray-600 font-semibold">💰 Total Belanja</span>
                            <span class="font-bold text-blue-600">Rp <?= number_format($stats['total_spent'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Recent Orders -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Pesanan Terbaru</h2>
                        <a href="?route=profile.orders" class="text-blue-600 hover:text-blue-800">
                            Lihat Semua <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <?php if (empty($orders)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-600 text-lg">Belum ada pesanan</p>
                            <a href="?route=products" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                                Mulai Belanja
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($orders as $order): ?>
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="font-semibold text-gray-800">
                                                Order #<?= htmlspecialchars($order['order_number']) ?>
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                <?= date('d M Y, H:i', strtotime($order['created_at'])) ?>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <?php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'paid' => 'bg-green-100 text-green-800',
                                                'failed' => 'bg-red-100 text-red-800',
                                                'expired' => 'bg-gray-100 text-gray-800',
                                                'cancelled' => 'bg-red-100 text-red-800'
                                            ];
                                            $statusColor = $statusColors[$order['payment_status']] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusColor ?>">
                                                <?= strtoupper($order['payment_status']) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center mt-3">
                                        <div>
                                            <p class="text-lg font-bold text-gray-800">
                                                Rp <?= number_format($order['total'] ?? 0, 0, ',', '.') ?>
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                Status: <span class="font-semibold"><?= ucfirst($order['status'] ?? 'pending') ?></span>
                                            </p>
                                        </div>
                                        <div class="flex gap-2">
                                            <?php if ($order['payment_status'] === 'pending' && !empty($order['snap_token'])): ?>
                                                <button onclick="payNow('<?= htmlspecialchars($order['snap_token']) ?>')" 
                                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                                    💳 Bayar Sekarang
                                                </button>
                                            <?php endif; ?>
                                            <a href="?route=profile.orderDetail&order_number=<?= urlencode($order['order_number']) ?>" 
                                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <script src="<?= asset('js/cart.js') ?>"></script>
    <script src="<?= asset('js/favorites.js') ?>"></script>

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
