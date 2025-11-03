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
    <div class="container mx-auto px-4 max-w-6xl">
        <h1 class="text-4xl font-bold mb-8"><i class="fas fa-ticket-alt text-blue-600 mr-3"></i>My Vouchers</h1>
        
        <!-- Tabs -->
        <div class="flex space-x-4 mb-6 border-b">
            <button onclick="showTab('available')" id="tab-available" 
                    class="tab-btn px-6 py-3 font-bold border-b-2 border-blue-600 text-blue-600">
                Available Vouchers
            </button>
            <button onclick="showTab('history')" id="tab-history" 
                    class="tab-btn px-6 py-3 font-bold text-gray-500">
                Usage History
            </button>
        </div>
        
        <!-- Available Vouchers -->
        <div id="content-available" class="tab-content">
            <?php if (!empty($availableVouchers)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($availableVouchers as $voucher): ?>
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
                            <div class="absolute top-0 right-0 text-9xl opacity-10"><i class="fas fa-ticket-alt"></i></div>
                            <div class="relative z-10">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <p class="text-sm opacity-80">Discount Code</p>
                                        <p class="text-3xl font-bold font-mono"><?php echo e($voucher['code']); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-4xl font-bold"><?php echo $voucher['discount_percent']; ?>%</p>
                                        <p class="text-sm">OFF</p>
                                    </div>
                                </div>
                                <?php if ($voucher['min_purchase'] > 0): ?>
                                    <p class="text-sm opacity-90 mb-2">Min: Rp <?php echo number_format($voucher['min_purchase'], 0, ',', '.'); ?></p>
                                <?php endif; ?>
                                <p class="text-sm opacity-90 mb-4">Valid until: <?php echo $voucher['expires_at'] ? date('d M Y', strtotime($voucher['expires_at'])) : 'No expiry'; ?></p>
                                <button onclick="copyCode('<?php echo e($voucher['code']); ?>')" 
                                        class="w-full bg-white text-blue-600 py-2 rounded-lg font-bold hover:bg-gray-100">
                                    <i class="fas fa-copy mr-2"></i>Copy Code
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12"><p class="text-gray-500">No vouchers available</p></div>
            <?php endif; ?>
        </div>
        
        <!-- Usage History -->
        <div id="content-history" class="tab-content hidden">
            <?php if (!empty($usageHistory)): ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left">Code</th>
                                <th class="px-4 py-3 text-left">Order</th>
                                <th class="px-4 py-3 text-right">Discount</th>
                                <th class="px-4 py-3 text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usageHistory as $usage): ?>
                                <tr class="border-b">
                                    <td class="px-4 py-3 font-mono font-bold text-blue-600"><?php echo e($usage['code']); ?></td>
                                    <td class="px-4 py-3"><?php echo e($usage['order_number']); ?></td>
                                    <td class="px-4 py-3 text-right text-green-600 font-bold">-Rp <?php echo number_format($usage['discount_amount'], 0, ',', '.'); ?></td>
                                    <td class="px-4 py-3 text-right"><?php echo date('d M Y', strtotime($usage['used_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12"><p class="text-gray-500">No usage history</p></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function showTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-600', 'text-blue-600');
        btn.classList.add('text-gray-500', 'border-transparent');
    });
    document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
    
    document.getElementById('tab-' + tab).classList.add('border-blue-600', 'text-blue-600');
    document.getElementById('tab-' + tab).classList.remove('text-gray-500', 'border-transparent');
    document.getElementById('content-' + tab).classList.remove('hidden');
}

function copyCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        Swal.fire({icon: 'success', title: 'Copied!', text: 'Code ' + code + ' copied', timer: 1500});
    });
}
</script>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
