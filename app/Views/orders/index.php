<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'My Orders') ?> - GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <?php include __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-box text-green-600"></i> My Orders
            </h1>
            <p class="text-gray-600 mt-2">Track and manage your orders</p>
        </div>

        <?php if (empty($orders)): ?>
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No Orders Yet</h3>
                <p class="text-gray-600 mb-6">Start shopping to see your orders here</p>
                <a href="index.php?route=products" class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
                    <i class="fas fa-shopping-cart mr-2"></i>Start Shopping
                </a>
            </div>
        <?php else: ?>
            <!-- Orders List -->
            <div class="space-y-4">
                <?php foreach ($orders as $order): ?>
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                        <div class="flex justify-between items-start flex-wrap gap-4">
                            <div>
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
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-blue-100 text-blue-800',
                                    'packing' => 'bg-purple-100 text-purple-800',
                                    'shipped' => 'bg-indigo-100 text-indigo-800',
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800'
                                ];
                                $statusColor = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?= $statusColor ?>">
                                    <?= strtoupper($order['status']) ?>
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t flex justify-between items-center flex-wrap gap-4">
                            <div>
                                <p class="text-2xl font-bold text-green-600">
                                    Rp <?= number_format($order['total'], 0, ',', '.') ?>
                                </p>
                            </div>

                            <div class="flex gap-2 flex-wrap">
                                <?php if ($order['status'] === 'shipped'): ?>
                                    <a href="index.php?route=order.track&id=<?= urlencode($order['order_number']) ?>" 
                                       class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                                        <i class="fas fa-map-marker-alt"></i> Track Order
                                    </a>
                                <?php endif; ?>
                                <a href="index.php?route=order.show&id=<?= urlencode($order['order_number']) ?>" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
