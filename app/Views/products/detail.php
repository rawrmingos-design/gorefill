<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php 
    // Load Midtrans config for client key
    $midtransConfig = require __DIR__ . '/../../../config/midtrans.php';
    $snapUrl = $midtransConfig['is_production'] ? $midtransConfig['snap_url']['production'] : $midtransConfig['snap_url']['sandbox'];
    ?>
    <!-- Midtrans Snap.js -->
    <script src="<?= $snapUrl ?>" data-client-key="<?= $midtransConfig['client_key'] ?>"></script>
</head>
<body class="bg-gray-50">
<?php require_once __DIR__ . '/../../Helpers/ImageHelper.php'; ?>
    <!-- Navbar -->
    <?php include __DIR__ . '../../layouts/navbar.php'; ?>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex items-center text-sm mb-6 bg-white px-4 py-3 rounded-lg shadow-sm">
            <a href="?route=home" class="text-blue-600 hover:text-blue-800 font-medium"><i class="fas fa-home mr-2"></i>Home</a>
            <i class="fas fa-chevron-right mx-3 text-gray-400 text-xs"></i>
            <a href="?route=products" class="text-blue-600 hover:text-blue-800 font-medium">Produk</a>
            <i class="fas fa-chevron-right mx-3 text-gray-400 text-xs"></i>
            <span class="text-gray-600 font-medium"><?php echo e($product['name']); ?></span>
        </nav>

        <!-- Product Detail -->
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-8">
                <!-- Product Image -->
                <div class="sticky top-8">
                    <?php
                    $imageUrl = ImageHelper::getImageUrl($product['image']);
                    if ($imageUrl): ?>
                        <img src="<?php echo e($imageUrl); ?>" 
                             alt="<?php echo e($product['name']); ?>" 
                             class="w-full rounded-xl shadow-lg hover:shadow-2xl transition"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-96 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center\'><i class=\'fas fa-box text-9xl text-blue-400\'></i></div>';">
                    <?php else: ?>
                        <div class="w-full h-96 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                            <i class="fas fa-box text-9xl text-blue-400"></i>
                        </div>
                    <?php endif; ?>
                </div>
                                
                <!-- Product Info -->
                <div>
                    <!-- Category & Eco Badge -->
                    <div class="flex items-center gap-3 mb-4 flex-wrap">
                        <span class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full text-sm font-semibold shadow-lg">
                            <i class="fas fa-tag mr-2"></i><?php echo e($product['category_name'] ?? 'Umum'); ?>
                        </span>
                        <?php if ($product['badge_env'] == 1): ?>
                            <span class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-full text-sm font-semibold shadow-lg animate-pulse">
                                <i class="fa-solid fa-recycle mr-2"></i>Ramah Lingkungan
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Product Name & Favorite Button -->
                    <div class="flex items-start justify-between mb-6">
                        <h1 class="text-4xl md:text-5xl font-bold text-gray-800 leading-tight flex-1"><?php echo e($product['name']); ?></h1>
                        <button 
                            onclick="toggleFavorite(<?php echo $product['id']; ?>)"
                            data-product-id="<?php echo $product['id']; ?>"
                            class="ml-4 bg-white hover:bg-gray-50 border-2 border-gray-200 px-4 py-3 rounded-xl flex items-center space-x-2 transition shadow-sm hover:shadow-md flex-shrink-0"
                            title="<?php echo $isFavorite ? 'Hapus dari favorit' : 'Tambah ke favorit'; ?>">
                            <i class="<?php echo $isFavorite ? 'fas fa-heart text-red-500' : 'far fa-heart'; ?> text-xl"></i>
                            <span class="font-semibold text-sm hidden md:inline">Favorit</span>
                        </button>
                    </div>
                    
                    <div class="mb-6 bg-blue-50 p-6 rounded-xl border-2 border-blue-200">
                        <p class="text-sm text-gray-600 mb-2">Harga</p>
                        <span class="text-5xl font-bold text-blue-600">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                    </div>

                    <?php if ($product['description']): ?>
                        <div class="mb-6 bg-gray-50 p-6 rounded-xl">
                            <h3 class="text-xl font-bold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i> Deskripsi Produk
                            </h3>
                            <p class="text-gray-700 leading-relaxed"><?php echo nl2br(e($product['description'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Stock Status -->
                    <div class="mb-6 p-4 rounded-xl <?php echo $product['stock'] > 0 ? 'bg-green-50 border-2 border-green-200' : 'bg-red-50 border-2 border-red-200'; ?>">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center">
                            <i class="fas fa-box mr-2"></i> Ketersediaan Stok
                        </h3>
                        <?php if ($product['stock'] > 0): ?>
                            <p class="text-green-700 font-bold text-lg">
                                <i class="fas fa-check-circle mr-2"></i> Tersedia (<?php echo e($product['stock']); ?> unit)
                            </p>
                        <?php else: ?>
                            <p class="text-red-700 font-bold text-lg">
                                <i class="fas fa-times-circle mr-2"></i> Stok Habis
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Quantity Selector -->
                    <?php if ($product['stock'] > 0): ?>
                        <div class="mb-6 bg-gray-50 p-6 rounded-xl">
                            <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-calculator mr-2"></i> Jumlah Pembelian
                            </h3>
                            <div class="flex items-center space-x-4">
                                <button onclick="decrementQty()" class="w-12 h-12 bg-red-500 hover:bg-red-600 text-white rounded-lg font-bold text-xl transition shadow-md">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="quantity" value="1" min="1" max="<?php echo e($product['stock']); ?>" 
                                       class="w-24 text-center border-2 border-blue-300 rounded-lg py-3 font-bold text-xl focus:ring-2 focus:ring-blue-500">
                                <button onclick="incrementQty()" class="w-12 h-12 bg-green-500 hover:bg-green-600 text-white rounded-lg font-bold text-xl transition shadow-md">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-4">
                            <button onclick="addToCart(<?php echo e($product['id']); ?>)" 
                                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-5 rounded-xl text-lg transition shadow-lg hover:shadow-2xl transform hover:scale-105">
                                <i class="fas fa-shopping-cart mr-2"></i> Tambah ke Keranjang
                            </button>
                            <button onclick="buyNow(<?php echo e($product['id']); ?>)" 
                                    class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-5 rounded-xl text-lg transition shadow-lg hover:shadow-2xl transform hover:scale-105">
                                <i class="fas fa-bolt mr-2"></i> Beli Sekarang
                            </button>
                        </div>
                    <?php else: ?>
                        <button disabled class="w-full bg-gray-400 text-white font-bold py-5 rounded-xl text-lg cursor-not-allowed opacity-60">
                            <i class="fas fa-ban mr-2"></i> Stok Habis
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="bg-white rounded-xl shadow-2xl p-8 mb-12" id="reviews-section">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-star text-yellow-400 mr-3"></i> Reviews & Rating
            </h2>
            
            <!-- Rating Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8 pb-8 border-b">
                <!-- Average Rating -->
                <div class="text-center">
                    <div class="text-6xl font-bold text-gray-800 mb-2"><?php echo number_format($averageRating, 1); ?></div>
                    <div class="flex justify-center space-x-1 mb-2">
                        <?php
                        $fullStars = floor($averageRating);
                        $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
                        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                        
                        for ($i = 0; $i < $fullStars; $i++): ?>
                            <i class="fas fa-star text-yellow-400 text-2xl"></i>
                        <?php endfor;
                        
                        if ($hasHalfStar): ?>
                            <i class="fas fa-star-half-alt text-yellow-400 text-2xl"></i>
                        <?php endif;
                        
                        for ($i = 0; $i < $emptyStars; $i++): ?>
                            <i class="far fa-star text-gray-300 text-2xl"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="text-gray-600"><?php echo $reviewCount; ?> Review<?php echo $reviewCount != 1 ? 's' : ''; ?></p>
                </div>
                
                <!-- Rating Distribution -->
                <div class="md:col-span-2">
                    <h3 class="font-bold text-gray-800 mb-4">Rating Distribution</h3>
                    <?php for ($star = 5; $star >= 1; $star--): 
                        $count = $ratingDistribution[$star] ?? 0;
                        $percentage = $reviewCount > 0 ? ($count / $reviewCount * 100) : 0;
                    ?>
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="text-sm font-semibold w-8"><?php echo $star; ?> <i class="fas fa-star text-yellow-400 text-xs"></i></span>
                            <div class="flex-1 bg-gray-200 rounded-full h-3">
                                <div class="bg-yellow-400 h-3 rounded-full transition-all duration-300" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                            <span class="text-sm text-gray-600 w-12 text-right"><?php echo $count; ?></span>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
            
            <!-- Review Form -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($canReview && !$hasReviewed): ?>
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-edit text-blue-600 mr-2"></i> Tulis Review Anda
                        </h3>
                        
                        <!-- Star Rating Selector -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Rating:</label>
                            <div class="star-rating flex space-x-2 text-4xl cursor-pointer">
                                <i class="far fa-star star text-gray-300 hover:text-yellow-400 transition" data-rating="1"></i>
                                <i class="far fa-star star text-gray-300 hover:text-yellow-400 transition" data-rating="2"></i>
                                <i class="far fa-star star text-gray-300 hover:text-yellow-400 transition" data-rating="3"></i>
                                <i class="far fa-star star text-gray-300 hover:text-yellow-400 transition" data-rating="4"></i>
                                <i class="far fa-star star text-gray-300 hover:text-yellow-400 transition" data-rating="5"></i>
                            </div>
                            <input type="hidden" id="rating" value="0">
                        </div>
                        
                        <!-- Review Text -->
                        <div class="mb-4">
                            <label for="review" class="block text-sm font-semibold text-gray-700 mb-2">Review Anda:</label>
                            <textarea 
                                id="review" 
                                rows="5" 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none resize-none"
                                placeholder="Ceritakan pengalaman Anda dengan produk ini... (minimal 10 karakter)"></textarea>
                        </div>
                        
                        <!-- Submit Button -->
                        <button 
                            onclick="submitReview(<?php echo $product['id']; ?>)"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition shadow-md hover:shadow-lg">
                            <i class="fas fa-paper-plane mr-2"></i> Kirim Review
                        </button>
                    </div>
                <?php elseif ($hasReviewed): ?>
                    <div class="bg-green-50 border-2 border-green-200 rounded-xl p-6 mb-8 text-center">
                        <i class="fas fa-check-circle text-green-600 text-4xl mb-3"></i>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Terima Kasih!</h3>
                        <p class="text-gray-600">Anda sudah mereview produk ini</p>
                    </div>
                <?php else: ?>
                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-6 mb-8 text-center">
                        <i class="fas fa-info-circle text-yellow-600 text-4xl mb-3"></i>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Bisa Review</h3>
                        <p class="text-gray-600">Anda hanya bisa mereview produk yang sudah Anda beli</p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-6 mb-8 text-center">
                    <i class="fas fa-user-lock text-gray-400 text-4xl mb-3"></i>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Login Untuk Review</h3>
                    <p class="text-gray-600 mb-4">Silakan login untuk memberikan review</p>
                    <a href="?route=auth.login" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                        Login
                    </a>
                </div>
            <?php endif; ?>
            
            <!-- Reviews List -->
            <?php if (!empty($reviews)): ?>
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Customer Reviews (<?php echo $reviewCount; ?>)</h3>
                <div class="space-y-6">
                    <?php foreach ($reviews as $review): ?>
                        <div class="bg-gray-50 rounded-xl p-6 hover:shadow-md transition">
                            <!-- Review Header -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                        <?php echo strtoupper(substr($review['user_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800"><?php echo e($review['user_name']); ?></h4>
                                        <div class="flex items-center space-x-2">
                                            <div class="flex space-x-1">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="text-sm text-gray-500">• <?php echo date('d M Y', strtotime($review['created_at'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Verified Purchase Badge -->
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i> Verified Purchase
                                </span>
                            </div>
                            
                            <!-- Review Text -->
                            <p class="text-gray-700 leading-relaxed"><?php echo nl2br(e($review['comment'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-comments text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Belum Ada Review</h3>
                    <p class="text-gray-500">Jadilah yang pertama memberikan review untuk produk ini!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Related Products</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach (array_slice($relatedProducts, 0, 4) as $related): ?>
                        <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                            <a href="?route=product.detail&slug=<?php echo e($related['slug']); ?>">
                                <?php
                                $relatedImageUrl = ImageHelper::getImageUrl($related['image']);
                                if ($relatedImageUrl): ?>
                                    <img src="<?php echo e($relatedImageUrl); ?>" 
                                         alt="<?php echo e($related['name']); ?>" 
                                         class="w-full h-48 object-cover"
                                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-48 bg-gray-200 flex items-center justify-center\'><span class=\'text-6xl\'>📦</span></div>';">
                                <?php else: ?>
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <span class="text-6xl">📦</span>
                                    </div>
                                <?php endif; ?>
                            </a>
                            <div class="p-4">
                                <a href="?route=product.detail&slug=<?php echo e($related['slug']); ?>">
                                    <h3 class="font-bold text-gray-800 mb-2 hover:text-blue-600"><?php echo e($related['name']); ?></h3>
                                </a>
                                <span class="text-xl font-bold text-blue-600">Rp <?php echo number_format($related['price'], 0, ',', '.'); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
    const maxStock = <?php echo e($product['stock']); ?>;
    
    function incrementQty() {
        const qtyInput = document.getElementById('quantity');
        const currentQty = parseInt(qtyInput.value);
        if (currentQty < maxStock) {
            qtyInput.value = currentQty + 1;
        }
    }
    
    function decrementQty() {
        const qtyInput = document.getElementById('quantity');
        const currentQty = parseInt(qtyInput.value);
        if (currentQty > 1) {
            qtyInput.value = currentQty - 1;
        }
    }
    
    function buyNow(productId) {
        const qty = document.getElementById('quantity').value;
        
        // Show loading
        Swal.fire({
            title: 'Processing...',
            text: 'Memproses pembelian Anda',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Create form data
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', qty);
        
        // Submit to buy now endpoint
        fetch('index.php?route=checkout.buyNow', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.redirected) {
                // Follow redirect
                window.location.href = response.url;
            } else {
                return response.text();
            }
        })
        .then(text => {
            if (text) {
                // Check if it's an error page
                if (text.includes('error') || text.includes('Error')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat memproses pembelian'
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat memproses pembelian'
            });
        });
    }
    </script>
    <script src="/public/assets/js/cart.js"></script>
    <script src="/public/assets/js/favorites.js"></script>
    <script src="/public/assets/js/reviews.js"></script>
    <script>
    // Override addToCart to include quantity from selector
    const originalAddToCart = addToCart;
    addToCart = function(productId) {
        const qty = parseInt(document.getElementById('quantity').value);
        originalAddToCart(productId, qty);
    };
    </script>
</body>
</html>
