<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'Admin Dashboard'); ?></title>
     <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">

    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <!-- Content -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Dashboard</h2>
            <p class="text-gray-600">Welcome back, <?php echo e($_SESSION['name']); ?>!</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Products -->
            <div class="bg-white rounded-lg shadow p-6 animate__animated animate__fadeInUp">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full mr-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Total Products</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo e($stats['total_products']); ?></h3>
                    </div>
                </div>
            </div>

            <!-- Total Users -->
            <div class="bg-white rounded-lg shadow p-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full mr-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Total Users</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo e($stats['total_users']); ?></h3>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="bg-white rounded-lg shadow p-6 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full mr-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Total Orders</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo e($stats['total_orders']); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="?route=admin.products.create" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg text-center transition">
                    ➕ Add New Product
                </a>
                <a href="?route=admin.products" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg text-center transition">
                    📦 Manage Products
                </a>
                <a href="?route=home" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg text-center transition">
                    🏠 View Website
                </a>
            </div>
        </div>

        <!-- Recent Products -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Recent Products</h3>
                <a href="?route=admin.products" class="text-blue-600 hover:underline">View All →</a>
            </div>
            
            <?php if (empty($stats['recent_products'])): ?>
                <div class="text-center py-8 text-gray-500">
                    <p>No products yet.</p>
                    <a href="?route=admin.products.create" class="text-blue-600 hover:underline">Add your first product →</a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($stats['recent_products'] as $product): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <?php if ($product['image']): ?>
                                                <img src="../uploads/products/<?php echo e($product['image']); ?>" alt="<?php echo e($product['name']); ?>" class="w-10 h-10 rounded object-cover mr-3">
                                            <?php else: ?>
                                                <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center mr-3">
                                                    <span class="text-gray-400">📦</span>
                                                </div>
                                            <?php endif; ?>
                                            <span class="font-medium"><?php echo e($product['name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded"><?php echo e($product['category_name']); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="<?php echo $product['stock'] > 10 ? 'text-green-600' : 'text-red-600'; ?>">
                                            <?php echo e($product['stock']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="?route=admin.products.edit&id=<?php echo e($product['id']); ?>" class="text-blue-600 hover:underline">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

            <?php require_once __DIR__ . "/../partials/footer.php"; ?>

    </div>
</body>
</html>
