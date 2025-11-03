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
<?php
$isEdit = ($action === 'edit' && $voucher);
$submitUrl = $isEdit ? "?route=admin.editVoucher&id={$voucher['id']}" : "?route=admin.createVoucher"; ?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-2xl">
        <a href="?route=admin.vouchers" class="text-blue-600 mb-4 inline-block"><i class="fas fa-arrow-left mr-2"></i>Back</a>
        
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h1 class="text-3xl font-bold mb-6"><?php echo $isEdit ? 'Edit' : 'Create'; ?> Voucher</h1>
            
            <form method="POST" action="<?php echo $submitUrl; ?>" class="space-y-4">
                <div>
                    <label class="block font-bold mb-2">Code *</label>
                    <input type="text" name="code" value="<?php echo e($voucher['code'] ?? ''); ?>" 
                           class="w-full px-4 py-3 border rounded-lg uppercase" required maxlength="50">
                </div>
                
                <div>
                    <label class="block font-bold mb-2">Discount % *</label>
                    <input type="number" name="discount_percent" value="<?php echo e($voucher['discount_percent'] ?? ''); ?>" 
                           class="w-full px-4 py-3 border rounded-lg" min="1" max="100" required>
                </div>
                
                <div>
                    <label class="block font-bold mb-2">Min Purchase (Rp)</label>
                    <input type="number" name="min_purchase" value="<?php echo e($voucher['min_purchase'] ?? 0); ?>" 
                           class="w-full px-4 py-3 border rounded-lg" min="0">
                </div>
                
                <div>
                    <label class="block font-bold mb-2">Usage Limit *</label>
                    <input type="number" name="usage_limit" value="<?php echo e($voucher['usage_limit'] ?? 100); ?>" 
                           class="w-full px-4 py-3 border rounded-lg" min="1" required>
                </div>
                
                <div>
                    <label class="block font-bold mb-2">Expires At</label>
                    <input type="date" name="expires_at" value="<?php echo e($voucher['expires_at'] ?? ''); ?>" 
                           class="w-full px-4 py-3 border rounded-lg">
                </div>
                
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700">
                    <?php echo $isEdit ? 'Update' : 'Create'; ?> Voucher
                </button>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
