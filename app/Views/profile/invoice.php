<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
<?php require_once __DIR__ . '/../../Helpers/ImageHelper.php'; ?>

    <!-- Print Button (hidden when printing) -->
    <div class="no-print fixed top-4 right-4 z-50">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-lg">
            <i class="fas fa-print"></i> Print Invoice
        </button>
        <button onclick="window.close()" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg shadow-lg ml-2">
            <i class="fas fa-times"></i> Close
        </button>
    </div>

    <!-- Invoice -->
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white shadow-lg rounded-lg p-8">
            <!-- Header -->
            <div class="flex justify-between items-start mb-8 pb-6 border-b-2">
                <div>
                    <h1 class="text-4xl font-bold text-blue-600 mb-2">🌊 GoRefill</h1>
                    <p class="text-gray-600">Eco-Friendly Refill Products</p>
                    <p class="text-sm text-gray-500 mt-2">
                        Jl. Contoh No. 123, Semarang<br>
                        Email: info@gorefill.com<br>
                        Phone: (024) 1234-5678
                    </p>
                </div>
                <div class="text-right">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">INVOICE</h2>
                    <p class="text-gray-600">
                        <strong>Invoice #:</strong> <?= htmlspecialchars($order['order_number']) ?><br>
                        <strong>Tanggal:</strong> <?= date('d M Y', strtotime($order['created_at'])) ?><br>
                        <strong>Status:</strong> <span class="text-green-600 font-semibold">PAID</span>
                    </p>
                </div>
            </div>

            <!-- Customer & Shipping Info -->
            <div class="grid grid-cols-2 gap-8 mb-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Bill To:</h3>
                    <p class="text-gray-700">
                        <strong><?= htmlspecialchars($_SESSION['name']) ?></strong><br>
                        <?= htmlspecialchars($_SESSION['email']) ?><br>
                        <?php if (!empty($_SESSION['phone'])): ?>
                            <?= htmlspecialchars($_SESSION['phone']) ?><br>
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Ship To:</h3>
                    <p class="text-gray-700">
                        <strong><?= htmlspecialchars($order['shipping_name']) ?></strong><br>
                        <?= htmlspecialchars($order['shipping_phone']) ?><br>
                        <?= htmlspecialchars($order['shipping_address']) ?><br>
                        <?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_postal_code']) ?>
                    </p>
                </div>
            </div>

            <!-- Order Items Table -->
            <table class="w-full mb-8">
                <thead>
                    <tr class="bg-gray-100 border-b-2 border-gray-300">
                        <th class="text-left py-3 px-4">Item</th>
                        <th class="text-center py-3 px-4">Qty</th>
                        <th class="text-right py-3 px-4">Harga</th>
                        <th class="text-right py-3 px-4">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                        <tr class="border-b">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <?php
                                    $itemImageUrl = ImageHelper::getImageUrl($item['product_image']);
                                    if ($itemImageUrl): ?>
                                        <img src="<?= htmlspecialchars($itemImageUrl) ?>" 
                                             alt="<?= htmlspecialchars($item['product_name']) ?>"
                                             class="w-12 h-12 object-cover rounded no-print"
                                             onerror="this.style.display='none'">
                                    <?php endif; ?>
                                    <div>
                                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($item['product_name']) ?></p>
                                        <p class="text-sm text-gray-500">SKU: PRD-<?= str_pad($item['product_id'], 4, '0', STR_PAD_LEFT) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center py-4 px-4 text-gray-700">
                                <?= $item['quantity'] ?>
                            </td>
                            <td class="text-right py-4 px-4 text-gray-700">
                                Rp <?= number_format($item['price'], 0, ',', '.') ?>
                            </td>
                            <td class="text-right py-4 px-4 font-semibold text-gray-800">
                                Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Totals -->
            <div class="flex justify-end mb-8">
                <div class="w-64">
                    <div class="flex justify-between py-2 text-gray-700">
                        <span>Subtotal:</span>
                        <span>Rp <?= number_format($order['subtotal'], 0, ',', '.') ?></span>
                    </div>
                    <?php if ($order['discount_amount'] > 0): ?>
                        <div class="flex justify-between py-2 text-green-600">
                            <span>Diskon:</span>
                            <span>- Rp <?= number_format($order['discount_amount'], 0, ',', '.') ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="flex justify-between py-3 border-t-2 border-gray-300 text-xl font-bold text-gray-800">
                        <span>Total:</span>
                        <span>Rp <?= number_format($order['total'], 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Payment Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Payment Method:</p>
                        <p class="font-semibold text-gray-800 uppercase"><?= htmlspecialchars($order['payment_method'] ?? 'Midtrans') ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Payment Status:</p>
                        <p class="font-semibold text-green-600">PAID</p>
                    </div>
                    <?php if ($order['transaction_id']): ?>
                        <div>
                            <p class="text-gray-600">Transaction ID:</p>
                            <p class="font-mono text-xs text-gray-800"><?= htmlspecialchars($order['transaction_id']) ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($order['paid_at']): ?>
                        <div>
                            <p class="text-gray-600">Payment Date:</p>
                            <p class="text-gray-800"><?= date('d M Y, H:i', strtotime($order['paid_at'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Payment Link (for reference) -->
                <div class="mt-4 pt-4 border-t no-print">
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-link"></i> Payment Link: 
                        <a href="index.php?route=profile.orderDetail&order_number=<?= urlencode($order['order_number']) ?>" 
                           class="text-blue-600 hover:underline">
                            View Order Details
                        </a>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center pt-6 border-t">
                <p class="text-gray-600 text-sm mb-2">
                    Terima kasih atas pembelian Anda!
                </p>
                <p class="text-gray-500 text-xs">
                    Invoice ini digenerate secara otomatis dan sah tanpa tanda tangan.<br>
                    Untuk pertanyaan, hubungi customer service kami di support@gorefill.com
                </p>
            </div>

            <!-- Barcode/QR Code (Optional) -->
            <div class="text-center mt-6 no-print">
                <p class="text-xs text-gray-400">
                    Order ID: <?= htmlspecialchars($order['order_number']) ?>
                </p>
            </div>
        </div>
    </div>

    <script src="public/assets/js/cart.js"></script>
    <script src="public/assets/js/favorites.js"></script>
</body>
</html>
