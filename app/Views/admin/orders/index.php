<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    <!-- Admin Navbar -->
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-box"></i> Order Management
            </h1>
            <p class="text-gray-600 mt-1">Manage customer orders and assign couriers</p>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" action="index.php" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="hidden" name="route" value="admin.orders">
                
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Order #, customer name..."
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Order Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Status</label>
                    <select name="status" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="confirmed" <?= $status === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="packing" <?= $status === 'packing' ? 'selected' : '' ?>>Packing</option>
                        <option value="shipped" <?= $status === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                        <option value="delivered" <?= $status === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                        <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>

                <!-- Payment Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                    <select name="payment_status" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Payments</option>
                        <option value="pending" <?= $paymentStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="paid" <?= $paymentStatus === 'paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="failed" <?= $paymentStatus === 'failed' ? 'selected' : '' ?>>Failed</option>
                        <option value="expired" <?= $paymentStatus === 'expired' ? 'selected' : '' ?>>Expired</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <?php if (empty($orders)): ?>
                <div class="p-12 text-center">
                    <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-600 text-lg">No orders found</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($orders as $order): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">#<?= htmlspecialchars($order['order_number']) ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($order['customer_name']) ?></div>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($order['customer_email']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">Rp <?= number_format($order['total'] ?? 0, 0, ',', '.') ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $paymentColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'paid' => 'bg-green-100 text-green-800',
                                            'failed' => 'bg-red-100 text-red-800',
                                            'expired' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $color = $paymentColors[$order['payment_status']] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $color ?>">
                                            <?= strtoupper($order['payment_status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'confirmed' => 'bg-blue-100 text-blue-800',
                                            'packing' => 'bg-purple-100 text-purple-800',
                                            'shipped' => 'bg-indigo-100 text-indigo-800',
                                            'delivered' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800'
                                        ];
                                        $color = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $color ?>">
                                            <?= strtoupper($order['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('d M Y', strtotime($order['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <a href="index.php?route=admin.orderDetail&id=<?= urlencode($order['order_number']) ?>" 
                                           class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="px-6 py-4 border-t flex justify-center">
                        <nav class="flex gap-2">
                            <?php if ($currentPage > 1): ?>
                                <a href="?route=admin.orders&page=<?= $currentPage - 1 ?>&status=<?= $status ?>&payment_status=<?= $paymentStatus ?>&search=<?= urlencode($search) ?>" 
                                   class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                                    <i class="fas fa-chevron-left"></i> Prev
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i == $currentPage): ?>
                                    <span class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold">
                                        <?= $i ?>
                                    </span>
                                <?php elseif ($i == 1 || $i == $totalPages || abs($i - $currentPage) <= 2): ?>
                                    <a href="?route=admin.orders&page=<?= $i ?>&status=<?= $status ?>&payment_status=<?= $paymentStatus ?>&search=<?= urlencode($search) ?>" 
                                       class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                                        <?= $i ?>
                                    </a>
                                <?php elseif (abs($i - $currentPage) == 3): ?>
                                    <span class="px-4 py-2">...</span>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?route=admin.orders&page=<?= $currentPage + 1 ?>&status=<?= $status ?>&payment_status=<?= $paymentStatus ?>&search=<?= urlencode($search) ?>" 
                                   class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

        <?php require_once __DIR__ . "/../partials/footer.php"; ?>

</body>
</html>
