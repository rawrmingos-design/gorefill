<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .product-card {
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .category-pill {
            transition: all 0.2s ease;
        }
        .category-pill:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
<?php require_once __DIR__ . '/../../Helpers/ImageHelper.php'; ?>
    <!-- Navbar -->
    <?php include __DIR__ . '/../layouts/navbar.php'; ?>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">🌊 Katalog Produk</h1>
            <p class="text-lg text-gray-600">Pilih produk kebutuhan rumah tangga Anda</p>
        </div>

        <!-- Category Pills -->
        <div class="mb-8">
            <div class="bg-white rounded-xl shadow-md p-4">
                <div class="flex items-center gap-3 flex-wrap justify-center">
                    <span class="text-sm font-semibold text-gray-600"><i class="fas fa-filter mr-1"></i> Kategori:</span>
                    <a href="?route=products" 
                       class="category-pill <?php echo empty($filters['category']) ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?> px-6 py-2 rounded-full font-medium text-sm">
                        <i class="fas fa-th mr-1"></i> Semua
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="?route=products&category=<?php echo $cat['id']; ?>" 
                           class="category-pill <?php echo ($filters['category'] ?? '') == $cat['id'] ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?> px-6 py-2 rounded-full font-medium text-sm">
                            <?php echo e($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="lg:w-72 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-md p-6 sticky top-8">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-sliders-h mr-2 text-blue-600"></i> Filter Lanjutan
                    </h2>
                    
                    <form method="GET" action="" class="space-y-6" id="filterForm">
                        <input type="hidden" name="route" value="products">
                        <?php if(!empty($filters['category'])): ?>
                            <input type="hidden" name="category" value="<?php echo e($filters['category']); ?>">
                        <?php endif; ?>
                        
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-search mr-1"></i> Cari Produk
                            </label>
                            <input type="text" name="search" id="searchInput" value="<?php echo e($filters['search'] ?? ''); ?>" 
                                   placeholder="Cari nama produk..." 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle"></i> Kosongkan untuk melihat semua produk</p>
                        </div>
                        
                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-money-bill-wave mr-1"></i> Rentang Harga
                            </label>
                            <div class="space-y-3">
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-500 text-sm">Rp</span>
                                    <input type="number" name="min" id="minPrice" value="<?php echo e($filters['minPrice'] ?? ''); ?>" 
                                           placeholder="Harga Minimum" min="0" step="1000"
                                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-500 text-sm">Rp</span>
                                    <input type="number" name="max" id="maxPrice" value="<?php echo e($filters['maxPrice'] ?? ''); ?>" 
                                           placeholder="Harga Maksimum" min="0" step="1000"
                                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <p class="text-xs text-gray-500"><i class="fas fa-info-circle"></i> Kosongkan untuk tidak membatasi harga</p>
                            </div>
                        </div>
                        
                        <!-- Sort -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-sort mr-1"></i> Urutkan
                            </label>
                            <select name="sort" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="created_at" <?php echo ($filters['sort'] ?? '') === 'created_at' ? 'selected' : ''; ?>>Terbaru</option>
                                <option value="name" <?php echo ($filters['sort'] ?? '') === 'name' ? 'selected' : ''; ?>>Nama A-Z</option>
                                <option value="price" <?php echo ($filters['sort'] ?? '') === 'price' ? 'selected' : ''; ?>>Harga</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-arrow-up mr-1"></i> Arah
                            </label>
                            <select name="order" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="asc" <?php echo ($filters['order'] ?? '') === 'asc' ? 'selected' : ''; ?>>Naik (A→Z, 0→9)</option>
                                <option value="desc" <?php echo ($filters['order'] ?? '') === 'desc' ? 'selected' : ''; ?>>Turun (Z→A, 9→0)</option>
                            </select>
                        </div>
                        
                        <!-- Buttons -->
                        <div class="space-y-3">
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 rounded-lg transition shadow-md hover:shadow-lg">
                                <i class="fas fa-check mr-2"></i> Terapkan Filter
                            </button>
                            <a href="?route=products" class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 rounded-lg text-center transition">
                                <i class="fas fa-undo mr-2"></i> Reset Filter
                            </a>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- Products Grid -->
            <main class="flex-1">
                <!-- Result Info -->
                <div class="mb-6 flex justify-between items-center bg-white rounded-lg shadow-sm p-4">
                    <div>
                        <p class="text-gray-600">
                            <span class="font-semibold text-blue-600 text-lg"><?php echo e($totalProducts); ?></span> produk ditemukan
                            <?php if(!empty($filters['category'])): ?>
                                <?php 
                                $selectedCat = array_filter($categories, fn($c) => $c['id'] == $filters['category']);
                                $selectedCat = reset($selectedCat);
                                ?>
                                <span class="text-sm">di kategori <strong><?php echo e($selectedCat['name'] ?? ''); ?></strong></span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php if(!empty($filters['search']) || !empty($filters['minPrice']) || !empty($filters['maxPrice'])): ?>
                        <a href="?route=products<?php echo !empty($filters['category']) ? '&category=' . $filters['category'] : ''; ?>" 
                           class="text-sm text-red-600 hover:text-red-700 font-medium">
                            <i class="fas fa-times-circle mr-1"></i> Hapus Filter
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (empty($products)): ?>
                    <!-- Empty State -->
                    <div class="bg-white rounded-xl shadow-md p-16 text-center">
                        <div class="mb-4">
                            <i class="fas fa-box-open text-gray-300 text-6xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-700 mb-2">Produk Tidak Ditemukan</h3>
                        <p class="text-gray-500 mb-6">Coba ubah filter atau kata kunci pencarian Anda</p>
                        <a href="?route=products" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                            <i class="fas fa-undo mr-2"></i> Reset & Lihat Semua Produk
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Product Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card bg-white rounded-xl shadow-md hover:shadow-2xl overflow-hidden">
                            <!-- Product Image -->
                                <a href="?route=product.detail&slug=<?php echo e($product['slug']); ?>" class="block relative group">
                                    <?php
                                    $imageUrl = ImageHelper::getImageUrl($product['image']);
                                    if ($imageUrl): ?>
                                        <img src="<?php echo e($imageUrl); ?>" 
                                             alt="<?php echo e($product['name']); ?>" 
                                             class="w-full h-64 object-cover group-hover:scale-105 transition duration-300"
                                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-64 bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center\'><span class=\'text-7xl\'>📦</span></div>';">
                                        <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition"></div>
                                    <?php else: ?>
                                        <div class="w-full h-64 bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                            <span class="text-7xl">📦</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Stock Badge -->
                                    <?php if ($product['stock'] <= 0): ?>
                                        <div class="absolute top-3 right-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                            <i class="fas fa-times-circle mr-1"></i> Habis
                                        </div>
                                    <?php elseif ($product['stock'] <= 10): ?>
                                        <div class="absolute top-3 right-3 bg-orange-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                            <i class="fas fa-exclamation-circle mr-1"></i> Stok Terbatas
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Favorite Button -->
                                    <?php
                                    $isFavorited = in_array($product['id'], $favoritedIds);
                                    ?>
                                    <button 
                                        onclick="toggleFavorite(<?php echo $product['id']; ?>)"
                                        data-product-id="<?php echo $product['id']; ?>"
                                        class="absolute top-3 left-3 bg-white hover:bg-gray-50 text-gray-700 w-10 h-10 rounded-full shadow-lg flex items-center justify-center transition z-10"
                                        title="<?php echo $isFavorited ? 'Hapus dari favorit' : 'Tambah ke favorit'; ?>">
                                        <i class="<?php echo $isFavorited ? 'fas fa-heart text-red-500' : 'far fa-heart'; ?> text-lg"></i>
                                    </button>
                                </a>
                                
                                <!-- Product Info -->
                                <div class="p-5">
                                    <!-- Category & Eco Badge -->
                                    <div class="mb-3 flex items-center gap-2 flex-wrap">
                                        <span class="inline-block px-3 py-1 text-xs bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full font-semibold">
                                            <i class="fas fa-tag mr-1"></i> <?php echo e($product['category_name'] ?? 'Umum'); ?>
                                        </span>
                                        <?php if ($product['badge_env'] == 1): ?>
                                            <span class="inline-block px-3 py-1 text-xs bg-gradient-to-r from-green-500 to-green-600 text-white rounded-full font-semibold animate-pulse">
                                                <i class="fa-solid fa-recycle mr-1"></i> Ramah Lingkungan
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Product Name -->
                                    <a href="?route=product.detail&slug=<?php echo e($product['slug']); ?>" class="block">
                                        <h3 class="font-bold text-gray-800 text-xl mb-2 hover:text-blue-600 transition line-clamp-2 min-h-[3.5rem]">
                                            <?php echo e($product['name']); ?>
                                        </h3>
                                    </a>
                                    
                                    <!-- Description -->
                                    <?php if ($product['description']): ?>
                                        <p class="text-sm text-gray-500 mb-4 line-clamp-2 min-h-[2.5rem]">
                                            <?php echo e(substr($product['description'], 0, 90)); ?><?php echo strlen($product['description']) > 90 ? '...' : ''; ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="text-sm text-gray-400 mb-4 italic min-h-[2.5rem]">Tidak ada deskripsi</p>
                                    <?php endif; ?>
                                    
                                    <!-- Star Rating -->
                                    <?php 
                                    $rating = $productRatings[$product['id']] ?? ['average_rating' => 0, 'review_count' => 0];
                                    $avgRating = $rating['average_rating'];
                                    $reviewCount = $rating['review_count'];
                                    ?>
                                    <div class="flex items-center space-x-2 mb-3">
                                        <div class="flex space-x-1">
                                            <?php
                                            $fullStars = floor($avgRating);
                                            $hasHalfStar = ($avgRating - $fullStars) >= 0.5;
                                            $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                            
                                            for ($i = 0; $i < $fullStars; $i++): ?>
                                                <i class="fas fa-star text-yellow-400 text-sm"></i>
                                            <?php endfor;
                                            
                                            if ($hasHalfStar): ?>
                                                <i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>
                                            <?php endif;
                                            
                                            for ($i = 0; $i < $emptyStars; $i++): ?>
                                                <i class="far fa-star text-gray-300 text-sm"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="text-xs text-gray-600">
                                            <?php if ($reviewCount > 0): ?>
                                                (<?php echo number_format($avgRating, 1); ?> • <?php echo $reviewCount; ?> reviews)
                                            <?php else: ?>
                                                (Belum ada review)
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Price & Stock Info -->
                                    <div class="border-t pt-4 mb-4">
                                        <div class="flex justify-between items-center mb-2">
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Harga</p>
                                                <span class="text-2xl font-bold text-blue-600">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500 mb-1">Stok</p>
                                                <span class="text-lg font-bold <?php echo $product['stock'] > 10 ? 'text-green-600' : ($product['stock'] > 0 ? 'text-orange-600' : 'text-red-600'); ?>">
                                                    <i class="fas fa-box mr-1"></i><?php echo $product['stock']; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Add to Cart Button -->
                                    <button onclick="addToCart(<?php echo e($product['id']); ?>)" 
                                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 rounded-lg transition shadow-md hover:shadow-lg <?php echo $product['stock'] <= 0 ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                                            <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                                        <i class="fas fa-shopping-cart mr-2"></i> 
                                        <?php echo $product['stock'] <= 0 ? 'Stok Habis' : 'Tambah ke Keranjang'; ?>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="mt-8 flex justify-center">
                            <nav class="flex items-center gap-2 bg-white rounded-lg shadow-md p-3">
                                <?php if ($currentPage > 1): ?>
                                    <a href="?route=products&page=<?php echo $currentPage - 1; ?><?php echo isset($filters['category']) ? '&category=' . $filters['category'] : ''; ?><?php echo isset($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?><?php echo isset($filters['minPrice']) ? '&min=' . $filters['minPrice'] : ''; ?><?php echo isset($filters['maxPrice']) ? '&max=' . $filters['maxPrice'] : ''; ?><?php echo isset($filters['sort']) ? '&sort=' . $filters['sort'] : ''; ?><?php echo isset($filters['order']) ? '&order=' . $filters['order'] : ''; ?>" 
                                       class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">
                                        <i class="fas fa-chevron-left mr-1"></i> Sebelumnya
                                    </a>
                                <?php endif; ?>
                                
                                <?php 
                                $start = max(1, $currentPage - 2);
                                $end = min($totalPages, $currentPage + 2);
                                ?>
                                
                                <?php if ($start > 1): ?>
                                    <a href="?route=products&page=1<?php echo isset($filters['category']) ? '&category=' . $filters['category'] : ''; ?><?php echo isset($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?><?php echo isset($filters['minPrice']) ? '&min=' . $filters['minPrice'] : ''; ?><?php echo isset($filters['maxPrice']) ? '&max=' . $filters['maxPrice'] : ''; ?><?php echo isset($filters['sort']) ? '&sort=' . $filters['sort'] : ''; ?><?php echo isset($filters['order']) ? '&order=' . $filters['order'] : ''; ?>" 
                                       class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">1</a>
                                    <?php if ($start > 2): ?>
                                        <span class="px-2 text-gray-400">...</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php for ($i = $start; $i <= $end; $i++): ?>
                                    <a href="?route=products&page=<?php echo $i; ?><?php echo isset($filters['category']) ? '&category=' . $filters['category'] : ''; ?><?php echo isset($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?><?php echo isset($filters['minPrice']) ? '&min=' . $filters['minPrice'] : ''; ?><?php echo isset($filters['maxPrice']) ? '&max=' . $filters['maxPrice'] : ''; ?><?php echo isset($filters['sort']) ? '&sort=' . $filters['sort'] : ''; ?><?php echo isset($filters['order']) ? '&order=' . $filters['order'] : ''; ?>" 
                                       class="px-4 py-2 <?php echo $i === $currentPage ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200'; ?> rounded-lg font-medium transition">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($end < $totalPages): ?>
                                    <?php if ($end < $totalPages - 1): ?>
                                        <span class="px-2 text-gray-400">...</span>
                                    <?php endif; ?>
                                    <a href="?route=products&page=<?php echo $totalPages; ?><?php echo isset($filters['category']) ? '&category=' . $filters['category'] : ''; ?><?php echo isset($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?><?php echo isset($filters['minPrice']) ? '&min=' . $filters['minPrice'] : ''; ?><?php echo isset($filters['maxPrice']) ? '&max=' . $filters['maxPrice'] : ''; ?><?php echo isset($filters['sort']) ? '&sort=' . $filters['sort'] : ''; ?><?php echo isset($filters['order']) ? '&order=' . $filters['order'] : ''; ?>" 
                                       class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition"><?php echo $totalPages; ?></a>
                                <?php endif; ?>
                                
                                <?php if ($currentPage < $totalPages): ?>
                                    <a href="?route=products&page=<?php echo $currentPage + 1; ?><?php echo isset($filters['category']) ? '&category=' . $filters['category'] : ''; ?><?php echo isset($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?><?php echo isset($filters['minPrice']) ? '&min=' . $filters['minPrice'] : ''; ?><?php echo isset($filters['maxPrice']) ? '&max=' . $filters['maxPrice'] : ''; ?><?php echo isset($filters['sort']) ? '&sort=' . $filters['sort'] : ''; ?><?php echo isset($filters['order']) ? '&order=' . $filters['order'] : ''; ?>" 
                                       class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">
                                        Selanjutnya <i class="fas fa-chevron-right ml-1"></i>
                                    </a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </main>
        </div>
        </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../layouts/footer.php'; ?>
    
    <!-- Client-side filter validation -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('filterForm');
        const searchInput = document.getElementById('searchInput');
        const minPriceInput = document.getElementById('minPrice');
        const maxPriceInput = document.getElementById('maxPrice');
        
        // Validate form before submit
        filterForm.addEventListener('submit', function(e) {
            let hasValidFilter = false;
            
            // Check if search has value
            if (searchInput && searchInput.value.trim() !== '') {
                hasValidFilter = true;
            }
            
            // Check if min price has valid value
            if (minPriceInput && minPriceInput.value.trim() !== '' && parseFloat(minPriceInput.value) > 0) {
                hasValidFilter = true;
            }
            
            // Check if max price has valid value
            if (maxPriceInput && maxPriceInput.value.trim() !== '' && parseFloat(maxPriceInput.value) > 0) {
                hasValidFilter = true;
            }
            
            // Check if category is selected (from hidden input or URL)
            const categoryInput = document.querySelector('input[name="category"]');
            if (categoryInput && categoryInput.value) {
                hasValidFilter = true;
            }
            
            // If no valid filters and trying to search with empty values, prevent submission
            if (!hasValidFilter && (searchInput.value.trim() === '' || minPriceInput.value === '' || maxPriceInput.value === '')) {
                // Just submit normally - will show all products
                hasValidFilter = true;
            }
            
            // Validate price range
            const minVal = parseFloat(minPriceInput.value) || 0;
            const maxVal = parseFloat(maxPriceInput.value) || 0;
            
            if (minVal > 0 && maxVal > 0 && minVal > maxVal) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Harga Tidak Valid',
                    text: 'Harga minimum tidak boleh lebih besar dari harga maksimum!',
                    confirmButtonColor: '#3b82f6'
                });
                return false;
            }
        });
        
        // Clear empty inputs before submit to prevent "?search=&min=&max=" URLs
        filterForm.addEventListener('submit', function() {
            // Remove empty search parameter
            if (searchInput && searchInput.value.trim() === '') {
                searchInput.removeAttribute('name');
            }
            
            // Remove empty/zero price parameters
            if (minPriceInput && (minPriceInput.value.trim() === '' || parseFloat(minPriceInput.value) <= 0)) {
                minPriceInput.removeAttribute('name');
            }
            
            if (maxPriceInput && (maxPriceInput.value.trim() === '' || parseFloat(maxPriceInput.value) <= 0)) {
                maxPriceInput.removeAttribute('name');
            }
        });
        
        // Real-time validation feedback
        if (minPriceInput && maxPriceInput) {
            function validatePriceRange() {
                const minVal = parseFloat(minPriceInput.value) || 0;
                const maxVal = parseFloat(maxPriceInput.value) || 0;
                
                if (minVal > 0 && maxVal > 0 && minVal > maxVal) {
                    minPriceInput.classList.add('border-red-500');
                    maxPriceInput.classList.add('border-red-500');
                } else {
                    minPriceInput.classList.remove('border-red-500');
                    maxPriceInput.classList.remove('border-red-500');
                }
            }
            
            minPriceInput.addEventListener('input', validatePriceRange);
            maxPriceInput.addEventListener('input', validatePriceRange);
        }
    });
    </script>
    <script src="public/assets/js/cart.js"></script>
    <script src="public/assets/js/favorites.js"></script>
</body>
</html>
