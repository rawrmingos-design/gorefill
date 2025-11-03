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
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
<?php require_once __DIR__ . '/../../Helpers/ImageHelper.php'; ?>
    <!-- Navbar -->
    <?php include __DIR__ . '/../layouts/navbar.php'; ?>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-800 mb-2">
                        <i class="fas fa-heart text-red-500 mr-3"></i>Favorit Saya
                    </h1>
                    <p class="text-lg text-gray-600">
                        Anda memiliki <span class="font-bold text-blue-600"><?php echo $favoriteCount; ?></span> produk favorit
                    </p>
                </div>
                <a href="index.php?route=products" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md hover:shadow-lg">
                    <i class="fas fa-shopping-bag mr-2"></i>Lihat Semua Produk
                </a>
            </div>
        </div>

        <?php if (empty($favorites)): ?>
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <i class="far fa-heart text-gray-300 text-8xl mb-6"></i>
                <h2 class="text-3xl font-bold text-gray-700 mb-3">Belum Ada Favorit</h2>
                <p class="text-gray-500 text-lg mb-8">Anda belum menambahkan produk ke daftar favorit</p>
                <a href="index.php?route=products" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-semibold text-lg transition shadow-md hover:shadow-lg">
                    <i class="fas fa-shopping-bag mr-2"></i>Mulai Belanja
                </a>
            </div>
        <?php else: ?>
            <!-- Favorites Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 favorites-grid">
                <?php foreach ($favorites as $product): ?>
                    <div class="product-card bg-white rounded-xl shadow-md hover:shadow-2xl overflow-hidden" data-favorite-product="<?php echo $product['id']; ?>">
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
                            
                            <!-- Favorited Badge -->
                            <div class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg">
                                <i class="fas fa-heart mr-1"></i> Favorit
                            </div>
                            
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
                        </a>
                        
                        <!-- Product Info -->
                        <div class="p-5">
                            <!-- Category Badge -->
                            <div class="mb-3">
                                <span class="inline-block px-3 py-1 text-xs bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full font-semibold">
                                    <i class="fas fa-tag mr-1"></i> <?php echo e($product['category_name'] ?? 'Umum'); ?>
                                </span>
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
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <button 
                                    onclick="addToCart(<?php echo e($product['id']); ?>)" 
                                    class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 rounded-lg transition shadow-md hover:shadow-lg <?php echo $product['stock'] <= 0 ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                                    <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                                    <i class="fas fa-shopping-cart mr-2"></i> 
                                    <?php echo $product['stock'] <= 0 ? 'Stok Habis' : 'Tambah'; ?>
                                </button>
                                <button 
                                    onclick="removeFavorite(<?php echo e($product['id']); ?>)" 
                                    class="bg-red-100 hover:bg-red-200 text-red-600 font-semibold px-4 py-3 rounded-lg transition shadow-sm hover:shadow-md"
                                    title="Hapus dari favorit">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <script src="/public/assets/js/cart.js"></script>
    <script src="/public/assets/js/favorites.js"></script>
</body>
</html>
