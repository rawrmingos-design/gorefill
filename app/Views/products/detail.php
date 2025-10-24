<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="?route=home" class="text-2xl font-bold text-blue-600">
                        🌊 GoRefill
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="?route=products" class="text-gray-700 hover:text-blue-600">Products</a>
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
        <!-- Breadcrumb -->
        <nav class="text-sm mb-6">
            <a href="?route=home" class="text-blue-600 hover:underline">Home</a>
            <span class="mx-2">/</span>
            <a href="?route=products" class="text-blue-600 hover:underline">Products</a>
            <span class="mx-2">/</span>
            <span class="text-gray-600"><?php echo e($product['name']); ?></span>
        </nav>

        <!-- Product Detail -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-8">
                <!-- Product Image -->
                <div>
                    <?php
                    $imageUrl = ImageHelper::getImageUrl($product['image']);
                    if ($imageUrl): ?>
                        <img src="<?php echo e($imageUrl); ?>" 
                             alt="<?php echo e($product['name']); ?>" 
                             class="w-full rounded-lg"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center\'><span class=\'text-9xl\'>📦</span></div>';">
                    <?php else: ?>
                        <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                            <span class="text-9xl">📦</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Product Info -->
                <div>
                    <div class="mb-4">
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                            <?php echo e($product['category']); ?>
                        </span>
                    </div>

                    <h1 class="text-4xl font-bold text-gray-800 mb-4"><?php echo e($product['name']); ?></h1>
                    
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-blue-600">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                    </div>

                    <?php if ($product['description']): ?>
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Description</h3>
                            <p class="text-gray-600 leading-relaxed"><?php echo nl2br(e($product['description'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Stock Status -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Availability</h3>
                        <?php if ($product['stock'] > 0): ?>
                            <p class="text-green-600 font-semibold">✓ In Stock (<?php echo e($product['stock']); ?> available)</p>
                        <?php else: ?>
                            <p class="text-red-600 font-semibold">✗ Out of Stock</p>
                        <?php endif; ?>
                    </div>

                    <!-- Quantity Selector -->
                    <?php if ($product['stock'] > 0): ?>
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Quantity</h3>
                            <div class="flex items-center space-x-4">
                                <button onclick="decrementQty()" class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-lg font-semibold">-</button>
                                <input type="number" id="quantity" value="1" min="1" max="<?php echo e($product['stock']); ?>" 
                                       class="w-20 text-center border rounded-lg py-2 font-semibold">
                                <button onclick="incrementQty()" class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-lg font-semibold">+</button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <button onclick="addToCart(<?php echo e($product['id']); ?>)" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-lg text-lg transition">
                                🛒 Add to Cart
                            </button>
                            <button onclick="buyNow(<?php echo e($product['id']); ?>)" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-lg text-lg transition">
                                ⚡ Buy Now
                            </button>
                        </div>
                    <?php else: ?>
                        <button disabled class="w-full bg-gray-400 text-white font-bold py-4 rounded-lg text-lg cursor-not-allowed">
                            Out of Stock
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Related Products</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach (array_slice($relatedProducts, 0, 4) as $related): ?>
                        <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                            <a href="?route=product.detail&id=<?php echo e($related['id']); ?>">
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
                                <a href="?route=product.detail&id=<?php echo e($related['id']); ?>">
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
    <script src="assets/js/cart.js"></script>
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
