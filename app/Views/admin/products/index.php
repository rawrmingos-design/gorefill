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
    <?php $currentRoute = 'admin.products'; ?>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <!-- Content -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Manage Products</h2>
                <p class="text-gray-600">Total: <?php echo e($totalProducts); ?> products</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Search Form -->
                <form method="GET" action="" class="flex items-center bg-white border rounded-lg overflow-hidden shadow-sm">
                    <input type="hidden" name="route" value="admin.products">
                    <input type="text" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Search product..." class="px-4 py-2 outline-none w-48 sm:w-64">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700"><i class="fas fa-search"></i></button>
                </form>
                <a href="?route=admin.products.create" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg text-center">
                    ➕ Add Product
                </a>
            </div>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
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
                                                <img src="<?php echo e($adminImageUrl); ?>" alt="<?php echo e($product['name']); ?>" class="w-12 h-12 rounded object-cover mr-3">
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
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded"><?php echo e($product['category_name']); ?></span>
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
                    <div class="px-6 py-4 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <!-- Page Info -->
                            <div class="text-sm text-gray-600">
                                Halaman <?php echo $currentPage; ?> dari <?php echo $totalPages; ?>
                            </div>
                            
                            <!-- Pagination Buttons -->
                            <div class="flex items-center space-x-2">
                                <!-- Previous Button -->
                                <?php if ($currentPage > 1): ?>
                                    <a href="?route=admin.products&page=<?php echo $currentPage - 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" 
                                       class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium text-gray-700">
                                        <i class="fas fa-chevron-left mr-1"></i> Previous
                                    </a>
                                <?php else: ?>
                                    <span class="px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed">
                                        <i class="fas fa-chevron-left mr-1"></i> Previous
                                    </span>
                                <?php endif; ?>
                                
                                <!-- Page Numbers -->
                                <?php
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $currentPage + 2);
                                
                                for ($i = $startPage; $i <= $endPage; $i++): 
                                ?>
                                    <?php if ($i == $currentPage): ?>
                                        <span class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold">
                                            <?php echo $i; ?>
                                        </span>
                                    <?php else: ?>
                                        <a href="?route=admin.products&page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" 
                                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium text-gray-700">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                
                                <!-- Next Button -->
                                <?php if ($currentPage < $totalPages): ?>
                                    <a href="?route=admin.products&page=<?php echo $currentPage + 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" 
                                       class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium text-gray-700">
                                        Next <i class="fas fa-chevron-right ml-1"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed">
                                        Next <i class="fas fa-chevron-right ml-1"></i>
                                    </span>
                                <?php endif; ?>
                            </div>
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
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.querySelector('input[name="search"]');
        const productContainer = document.querySelector('.bg-white.rounded-lg.shadow');
        let timeout = null;

        // Mencegah submit form saat tekan Enter
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') e.preventDefault();
        });

        function loadProducts(query = '', page = 1) {
            fetch(`index.php?route=admin.products&ajax=1&search=${encodeURIComponent(query)}&page=${page}&_=${Date.now()}`)
            .then(res => res.text())
            .then(html => {
                productContainer.innerHTML = html;
            })
            .catch(err => console.error('Search error:', err));
        }

        // Realtime search
        searchInput.addEventListener('keyup', () => {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
            loadProducts(searchInput.value.trim());
            }, 400);
        });

        // Delegasi event untuk pagination (agar tetap jalan setelah re-render)
        document.body.addEventListener('click', (e) => {
            if (e.target.classList.contains('pagination-btn')) {
            const page = e.target.getAttribute('data-page');
            const query = searchInput.value.trim();
            loadProducts(query, page);
            }
        });
    });
</script>

</body>
</html>
