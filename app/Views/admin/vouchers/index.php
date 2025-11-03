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

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100 py-8">
    <div class="container mx-auto px-4">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-ticket-alt text-blue-600 mr-3"></i>Manage Vouchers
                </h1>
                <p class="text-gray-600">Create and manage discount vouchers</p>
            </div>
            <a href="?route=admin.createVoucher" 
               class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Create New Voucher</span>
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold uppercase">Total Vouchers</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $totalVouchers; ?></p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-4">
                        <i class="fas fa-ticket-alt text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <?php
            $activeCount = count(array_filter($vouchers, function($v) {
                return $v['status'] === 'Active';
            }));
            $expiredCount = count(array_filter($vouchers, function($v) {
                return $v['status'] === 'Expired';
            }));
            ?>
            
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold uppercase">Active Vouchers</p>
                        <p class="text-3xl font-bold text-green-600 mt-2"><?php echo $activeCount; ?></p>
                    </div>
                    <div class="bg-green-100 rounded-full p-4">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold uppercase">Expired</p>
                        <p class="text-3xl font-bold text-red-600 mt-2"><?php echo $expiredCount; ?></p>
                    </div>
                    <div class="bg-red-100 rounded-full p-4">
                        <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vouchers Table -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
            <?php if (!empty($vouchers)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-bold uppercase">Code</th>
                                <th class="px-6 py-4 text-left text-sm font-bold uppercase">Discount</th>
                                <th class="px-6 py-4 text-left text-sm font-bold uppercase">Min Purchase</th>
                                <th class="px-6 py-4 text-center text-sm font-bold uppercase">Usage</th>
                                <th class="px-6 py-4 text-center text-sm font-bold uppercase">Status</th>
                                <th class="px-6 py-4 text-center text-sm font-bold uppercase">Valid Until</th>
                                <th class="px-6 py-4 text-center text-sm font-bold uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($vouchers as $voucher): ?>
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-lg text-blue-600"><?php echo e($voucher['code']); ?></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-2xl font-bold text-green-600"><?php echo $voucher['discount_percent']; ?>%</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-gray-700">Rp <?php echo number_format($voucher['min_purchase'] ?? 0, 0, ',', '.'); ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="font-semibold text-gray-800"><?php echo $voucher['used_count']; ?> / <?php echo $voucher['usage_limit']; ?></span>
                                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1 max-w-[100px]">
                                                <?php 
                                                $percentage = $voucher['usage_limit'] > 0 ? ($voucher['used_count'] / $voucher['usage_limit'] * 100) : 0;
                                                ?>
                                                <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: <?php echo min($percentage, 100); ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php
                                        $statusColors = [
                                            'Active' => 'bg-green-100 text-green-700 border-green-300',
                                            'Expired' => 'bg-red-100 text-red-700 border-red-300',
                                            'Used Up' => 'bg-gray-100 text-gray-700 border-gray-300',
                                            'No Expiry' => 'bg-blue-100 text-blue-700 border-blue-300'
                                        ];
                                        $colorClass = $statusColors[$voucher['status']] ?? 'bg-gray-100 text-gray-700';
                                        ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold border <?php echo $colorClass; ?>">
                                            <?php echo e($voucher['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if ($voucher['expires_at']): ?>
                                            <span class="text-gray-700"><?php echo date('d M Y', strtotime($voucher['expires_at'])); ?></span>
                                        <?php else: ?>
                                            <span class="text-gray-400 italic">No expiry</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center space-x-2">
                                            <!-- View Usage -->
                                            <a href="?route=admin.voucherUsage&id=<?php echo $voucher['id']; ?>" 
                                               class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg transition text-sm flex items-center space-x-1"
                                               title="View Usage">
                                                <i class="fas fa-chart-bar"></i>
                                                <span class="hidden md:inline">Usage</span>
                                            </a>
                                            
                                            <!-- Edit -->
                                            <a href="?route=admin.editVoucher&id=<?php echo $voucher['id']; ?>" 
                                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg transition text-sm flex items-center space-x-1"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                                <span class="hidden md:inline">Edit</span>
                                            </a>
                                            
                                            <!-- Delete -->
                                            <form method="POST" action="?route=admin.deleteVoucher" class="inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this voucher?');">
                                                <input type="hidden" name="id" value="<?php echo $voucher['id']; ?>">
                                                <button type="submit" 
                                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition text-sm flex items-center space-x-1"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                    <span class="hidden md:inline">Delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="bg-gray-50 px-6 py-4 border-t">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-600">
                                Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?>
                            </p>
                            <div class="flex space-x-2">
                                <?php if ($currentPage > 1): ?>
                                    <a href="?route=admin.vouchers&page=<?php echo $currentPage - 1; ?>" 
                                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        <i class="fas fa-chevron-left mr-1"></i> Previous
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($currentPage < $totalPages): ?>
                                    <a href="?route=admin.vouchers&page=<?php echo $currentPage + 1; ?>" 
                                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        Next <i class="fas fa-chevron-right ml-1"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- Empty State -->
                <div class="text-center py-16">
                    <i class="fas fa-ticket-alt text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">No Vouchers Yet</h3>
                    <p class="text-gray-500 mb-6">Create your first voucher to start offering discounts!</p>
                    <a href="?route=admin.createVoucher" 
                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                        <i class="fas fa-plus mr-2"></i>Create First Voucher
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
