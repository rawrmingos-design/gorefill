<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
<?php require_once __DIR__ . '/../../Helpers/ImageHelper.php'; ?>

    <!-- Navbar -->
    <?php include __DIR__ . '../../layouts/navbar.php'; ?>

    <!-- Success Message -->
    <div class="max-w-3xl mx-auto px-4 py-12">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Success Icon -->
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-6xl text-green-500"></i>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-3">Pembayaran Berhasil!</h1>
            <p class="text-gray-600 mb-8">Terima kasih atas pembelian Anda. Order Anda sedang diproses.</p>

            <!-- Order Info -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Detail Order</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Order Number:</span>
                        <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($order['order_number']); ?></span>
                    </div>
                    
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Tanggal:</span>
                        <span class="text-gray-800"><?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></span>
                    </div>
                    
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Metode Pembayaran:</span>
                        <span class="text-gray-800 uppercase"><?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?></span>
                    </div>
                    
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Status Pembayaran:</span>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                            <?php echo strtoupper($order['payment_status']); ?>
                        </span>
                    </div>
                    
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Alamat Pengiriman:</span>
                        <span class="text-gray-800 text-right max-w-xs">
                            <?php echo htmlspecialchars($order['shipping_address']); ?>
                        </span>
                    </div>
                    
                    <div class="flex justify-between pt-2">
                        <span class="text-lg font-semibold text-gray-800">Total:</span>
                        <span class="text-lg font-bold text-green-600">
                            Rp <?php echo number_format($order['total'], 0, ',', '.'); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <?php if (!empty($order['items'])): ?>
            <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Produk yang Dibeli</h2>
                <div class="space-y-3">
                    <?php foreach ($order['items'] as $item): ?>
                            <div class="flex items-center gap-4 pb-3 border-b last:border-b-0">
                                <?php
                                $itemImageUrl = ImageHelper::getImageUrl($item['product_image']);
                                if ($itemImageUrl): ?>
                                    <img src="<?= htmlspecialchars($itemImageUrl) ?>" 
                                         alt="<?= htmlspecialchars($item['product_name']) ?>"
                                         class="w-16 h-16 object-cover rounded"
                                         onerror="this.onerror=null; this.src='/public/assets/images/placeholder.jpg'">
                                <?php else: ?>
                                    <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                        <span class="text-2xl">📦</span>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-800"><?= htmlspecialchars($item['product_name']) ?></h3>
                                    <p class="text-sm text-gray-600">
                                        Rp <?= number_format($item['price'], 0, ',', '.') ?> × <?= $item['quantity'] ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">
                                        Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="?route=home" 
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    Kembali ke Beranda
                </a>
                <a href="?route=profile" 
                   class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors">
                    <i class="fas fa-list mr-2"></i>
                    Lihat Pesanan Saya
                </a>
            </div>

            <!-- Additional Info -->
            <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Pesanan Anda akan segera diproses. Anda akan menerima email konfirmasi dan update status pengiriman.
                </p>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
