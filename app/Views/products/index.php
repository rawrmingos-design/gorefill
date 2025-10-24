<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
<?php require_once __DIR__ . '/../../Helpers/ImageHelper.php'; ?>
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="?route=home" class="text-2xl font-bold text-blue-600">
                        🌊 GoRefill
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="?route=products" class="text-blue-600 font-semibold">Products</a>
                    <a href="?route=cart" class="text-gray-700 hover:text-blue-600">
                        🛒 Cart <span id="cart-badge" class="bg-blue-600 text-white px-2 py-1 rounded-full text-xs">0</span>
                    </a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="?route=admin.dashboard" class="text-purple-600 hover:text-purple-800 font-semibold flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Admin Panel
                            </a>
                        <?php endif; ?>
                        <a href="?route=profile" class="text-gray-700 hover:text-blue-600 flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <?php echo e($_SESSION['name']); ?>
                        </a>
                        <a href="?route=auth.logout" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Logout</a>
                    <?php else: ?>
                        <a href="?route=auth.login" class="text-blue-600 hover:text-blue-800">Login</a>
                        <a href="?route=auth.register" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-lg shadow p-6 sticky top-8">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Filters</h2>
                    
                    <form method="GET" action="" class="space-y-6">
                        <input type="hidden" name="route" value="products">
                        
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" value="<?php echo e($filters['search'] ?? ''); ?>" 
                                   placeholder="Search products..." 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                            <select name="category" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo e($cat); ?>" <?php echo ($filters['category'] ?? '') === $cat ? 'selected' : ''; ?>>
                                        <?php echo e($cat); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Price Range</label>
                            <div class="space-y-2">
                                <input type="number" name="min" value="<?php echo e($filters['minPrice'] ?? ''); ?>" 
                                       placeholder="Min Price" 
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <input type="number" name="max" value="<?php echo e($filters['maxPrice'] ?? ''); ?>" 
                                       placeholder="Max Price" 
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <!-- Sort -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Sort By</label>
                            <select name="sort" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="created_at" <?php echo ($filters['sort'] ?? '') === 'created_at' ? 'selected' : ''; ?>>Newest</option>
                                <option value="name" <?php echo ($filters['sort'] ?? '') === 'name' ? 'selected' : ''; ?>>Name</option>
                                <option value="price" <?php echo ($filters['sort'] ?? '') === 'price' ? 'selected' : ''; ?>>Price</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Order</label>
                            <select name="order" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="asc" <?php echo ($filters['order'] ?? '') === 'asc' ? 'selected' : ''; ?>>Ascending</option>
                                <option value="desc" <?php echo ($filters['order'] ?? '') === 'desc' ? 'selected' : ''; ?>>Descending</option>
                            </select>
                        </div>
                        
                        <!-- Buttons -->
                        <div class="space-y-2">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                                Apply Filters
                            </button>
                            <a href="?route=products" class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 rounded-lg text-center transition">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- Products Grid -->
            <main class="flex-1">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800">Our Products</h1>
                    <p class="text-gray-600 mt-1"><?php echo e($totalProducts); ?> products found</p>
                </div>

                <?php if (empty($products)): ?>
                    <!-- Empty State -->
                    <div class="text-center py-16">
                        <svg class="mx-auto w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Products Found</h3>
                        <p class="text-gray-500">Try adjusting your filters or search terms</p>
                        <a href="?route=products" class="inline-block mt-4 text-blue-600 hover:underline">Reset Filters</a>
                    </div>
                <?php else: ?>
                    <!-- Product Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($products as $product): ?>
                            <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                                <!-- Product Image -->
                                <a href="?route=product.detail&id=<?php echo e($product['id']); ?>" class="block">
                                    <?php
                                    $imageUrl = ImageHelper::getImageUrl($product['image']);
                                    if ($imageUrl): ?>
                                        <img src="<?php echo e($imageUrl); ?>" 
                                             alt="<?php echo e($product['name']); ?>" 
                                             class="w-full h-48 object-cover"
                                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-48 bg-gray-200 flex items-center justify-center\'><span class=\'text-6xl\'>📦</span></div>';">
                                    <?php else: ?>
                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                            <span class="text-6xl">📦</span>
                                        </div>
                                    <?php endif; ?>
                                </a>
                                
                                <!-- Product Info -->
                                <div class="p-4">
                                    <div class="mb-2">
                                        <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded"><?php echo e($product['category']); ?></span>
                                    </div>
                                    
                                    <a href="?route=product.detail&id=<?php echo e($product['id']); ?>" class="block">
                                        <h3 class="font-bold text-gray-800 text-lg mb-2 hover:text-blue-600 transition"><?php echo e($product['name']); ?></h3>
                                    </a>
                                    
                                    <?php if ($product['description']): ?>
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2"><?php echo e(substr($product['description'], 0, 80)); ?>...</p>
                                    <?php endif; ?>
                                    
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="text-2xl font-bold text-blue-600">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                                        <span class="text-sm <?php echo $product['stock'] > 10 ? 'text-green-600' : ($product['stock'] > 0 ? 'text-orange-600' : 'text-red-600'); ?>">
                                            <?php if ($product['stock'] > 0): ?>
                                                <?php echo $product['stock']; ?> in stock
                                            <?php else: ?>
                                                Out of stock
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    
                                    <button onclick="addToCart(<?php echo e($product['id']); ?>)" 
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition <?php echo $product['stock'] <= 0 ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                                            <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                                        🛒 Add to Cart
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="mt-8 flex justify-center">
                            <nav class="flex space-x-2">
                                <?php if ($currentPage > 1): ?>
                                    <a href="?route=products&page=<?php echo $currentPage - 1; ?><?php echo isset($filters['category']) ? '&category=' . $filters['category'] : ''; ?><?php echo isset($filters['minPrice']) ? '&min=' . $filters['minPrice'] : ''; ?><?php echo isset($filters['maxPrice']) ? '&max=' . $filters['maxPrice'] : ''; ?>" 
                                       class="px-4 py-2 bg-white border rounded-lg hover:bg-gray-50">Previous</a>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <a href="?route=products&page=<?php echo $i; ?><?php echo isset($filters['category']) ? '&category=' . $filters['category'] : ''; ?><?php echo isset($filters['minPrice']) ? '&min=' . $filters['minPrice'] : ''; ?><?php echo isset($filters['maxPrice']) ? '&max=' . $filters['maxPrice'] : ''; ?>" 
                                       class="px-4 py-2 <?php echo $i === $currentPage ? 'bg-blue-600 text-white' : 'bg-white hover:bg-gray-50'; ?> border rounded-lg">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($currentPage < $totalPages): ?>
                                    <a href="?route=products&page=<?php echo $currentPage + 1; ?><?php echo isset($filters['category']) ? '&category=' . $filters['category'] : ''; ?><?php echo isset($filters['minPrice']) ? '&min=' . $filters['minPrice'] : ''; ?><?php echo isset($filters['maxPrice']) ? '&max=' . $filters['maxPrice'] : ''; ?>" 
                                       class="px-4 py-2 bg-white border rounded-lg hover:bg-gray-50">Next</a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="/public/assets/js/cart.js"></script>
</body>
</html>
