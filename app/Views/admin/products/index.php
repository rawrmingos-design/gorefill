<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
<?php require_once __DIR__ . '/../../../Helpers/ImageHelper.php'; ?>
    <!-- Navbar -->
    <nav class="bg-purple-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-6">
                    <h1 class="text-2xl font-bold">🔧 GoRefill Admin</h1>
                    <a href="?route=admin.dashboard" class="hover:text-purple-200">Dashboard</a>
                    <a href="?route=admin.products" class="text-purple-200 font-semibold">Products</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="?route=home" class="hover:text-purple-200">View Site</a>
                    <a href="?route=auth.logout" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Manage Products</h2>
                <p class="text-gray-600">Total: <?php echo e($totalProducts); ?> products</p>
            </div>
            <a href="?route=admin.products.create" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg">
                ➕ Add Product
            </a>
        </div>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash'])): ?>
            <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                <div class="mb-4 p-4 rounded <?php echo $type === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'; ?>">
                    <?php echo e($message); ?>
                    <?php unset($_SESSION['flash'][$type]); ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Product List -->
        <div class="bg-white rounded-lg shadow">
            <?php if (empty($products)): ?>
                <div class="text-center py-12">
                    <p class="text-gray-500 text-lg mb-4">No products found</p>
                    <a href="?route=admin.products.create" class="text-blue-600 hover:underline">Add your first product →</a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    <a href="?route=admin.products&sort=no&order=<?php echo (isset($_GET['sort']) && $_GET['sort'] === 'no' && (!isset($_GET['order']) || $_GET['order'] === 'asc')) ? 'desc' : 'asc'; ?>" class="flex items-center hover:text-gray-700">
                                        No
                                        <?php if (isset($_GET['sort']) && $_GET['sort'] === 'no'): ?>
                                            <?php if (!isset($_GET['order']) || $_GET['order'] === 'asc'): ?>
                                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"/></svg>
                                            <?php else: ?>
                                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"/></svg>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    <a href="?route=admin.products&sort=name&order=<?php echo (isset($_GET['sort']) && $_GET['sort'] === 'name' && (!isset($_GET['order']) || $_GET['order'] === 'asc')) ? 'desc' : 'asc'; ?>" class="flex items-center hover:text-gray-700">
                                        Product
                                        <?php if (isset($_GET['sort']) && $_GET['sort'] === 'name'): ?>
                                            <?php if (!isset($_GET['order']) || $_GET['order'] === 'asc'): ?>
                                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"/></svg>
                                            <?php else: ?>
                                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"/></svg>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php 
                            $no = ($currentPage - 1) * 10 + 1;
                            foreach ($products as $product): 
                            ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold"><?php echo $no++; ?></td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <?php
                                            $adminImageUrl = ImageHelper::getImageUrl($product['image']);
                                            if ($adminImageUrl): ?>
                                                <img src="<?php echo e($adminImageUrl); ?>" 
                                                     alt="<?php echo e($product['name']); ?>" 
                                                     class="w-12 h-12 rounded object-cover mr-3"
                                                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-12 h-12 bg-gray-200 rounded flex items-center justify-center mr-3\'><span class=\'text-gray-400 text-xl\'>📦</span></div><div><div class=\'font-medium text-gray-900\'><?php echo e($product['name']); ?></div><?php if ($product['description']): ?><div class=\'text-sm text-gray-500\'><?php echo e(substr($product['description'], 0, 50)); ?>...</div><?php endif; ?></div>';">
                                            <?php else: ?>
                                                <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center mr-3">
                                                    <span class="text-gray-400 text-xl">📦</span>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="font-medium text-gray-900"><?php echo e($product['name']); ?></div>
                                                <?php if ($product['description']): ?>
                                                    <div class="text-sm text-gray-500"><?php echo e(substr($product['description'], 0, 50)); ?>...</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded"><?php echo e($product['category']); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-semibold">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-semibold <?php echo $product['stock'] > 10 ? 'text-green-600' : ($product['stock'] > 0 ? 'text-orange-600' : 'text-red-600'); ?>">
                                            <?php echo e($product['stock']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                                        <a href="?route=admin.products.edit&id=<?php echo e($product['id']); ?>" class="text-blue-600 hover:underline">Edit</a>
                                        <button onclick="deleteProduct(<?php echo e($product['id']); ?>)" class="text-red-600 hover:underline">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="px-6 py-4 bg-gray-50 border-t flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            Page <?php echo e($currentPage); ?> of <?php echo e($totalPages); ?>
                        </div>
                        <div class="flex space-x-2">
                            <?php if ($currentPage > 1): ?>
                                <a href="?route=admin.products&page=<?php echo $currentPage - 1; ?>" class="px-4 py-2 bg-white border rounded hover:bg-gray-50">Previous</a>
                            <?php endif; ?>
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?route=admin.products&page=<?php echo $currentPage + 1; ?>" class="px-4 py-2 bg-white border rounded hover:bg-gray-50">Next</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
    async function deleteProduct(id) {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
            try {
                const formData = new FormData();
                formData.append('id', id);

                const response = await fetch('?route=admin.products.delete', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.error || 'Failed to delete product', 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Something went wrong', 'error');
            }
        }
    }
    </script>
</body>
</html>
