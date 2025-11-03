<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
<?php require_once __DIR__ . '/../../../Helpers/ImageHelper.php'; ?>
    <?php $currentRoute = 'admin.vouchers'; ?>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <a href="?route=admin.vouchers" class="text-blue-600 mb-4 inline-block"><i class="fas fa-arrow-left mr-2"></i>Back</a>
        
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <h1 class="text-3xl font-bold mb-4">Voucher Usage: <?php echo e($voucher['code']); ?></h1>
            <div class="grid grid-cols-3 gap-4">
                <div><span class="font-bold">Discount:</span> <?php echo $voucher['discount_percent']; ?>%</div>
                <div><span class="font-bold">Used:</span> <?php echo $voucher['used_count']; ?> / <?php echo $voucher['usage_limit']; ?></div>
                <div><span class="font-bold">Remaining:</span> <?php echo $voucher['usage_limit'] - $voucher['used_count']; ?></div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <?php if (!empty($usageHistory)): ?>
                <table class="w-full">
                    <thead class="bg-blue-600 text-white">
                        <tr>
                            <th class="px-4 py-3">Order</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Discount</th>
                            <th class="px-4 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usageHistory as $usage): ?>
                            <tr class="border-b">
                                <td class="px-4 py-3"><?php echo e($usage['order_number'] ?? 'N/A'); ?></td>
                                <td class="px-4 py-3"><?php echo e($usage['user_name'] ?? 'Unknown'); ?></td>
                                <td class="px-4 py-3">Rp <?php echo number_format($usage['discount_amount'] ?? 0, 0, ',', '.'); ?></td>
                                <td class="px-4 py-3"><?php echo date('d M Y', strtotime($usage['used_at'] ?? 'now')); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center py-12"><p>No usage yet</p></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
