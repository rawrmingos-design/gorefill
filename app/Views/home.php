<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($config['app']['name']); ?> - Layanan Isi Ulang Terpercaya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header/Navbar -->
    <?php include __DIR__ . '/layouts/navbar.php'; ?>
    

    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-blue-600 to-indigo-700 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center animate__animated animate__fadeIn">
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">
                    <i class="fas fa-recycle mr-4"></i>Selamat Datang di GoRefill
                </h1>
                <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
                    Layanan Isi Ulang <strong>Air Minum</strong>, <strong>Gas LPG</strong> & <strong>Kebutuhan Rumah Tangga</strong> Terpercaya
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="?route=products" class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-xl font-bold text-lg transition shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-shopping-cart mr-2"></i> Belanja Sekarang
                    </a>
                    <a href="#products" class="bg-blue-500 hover:bg-blue-400 text-white px-8 py-4 rounded-xl font-bold text-lg transition shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-box mr-2"></i> Lihat Produk
                    </a>
                </div>
            </div>
        </div>
        <!-- Wave -->
        <div class="absolute bottom-0 w-full">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="rgb(249, 250, 251)"/>
            </svg>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4"><i class="fas fa-star text-yellow-500 mr-2"></i>Mengapa Pilih GoRefill?</h2>
            <p class="text-xl text-gray-600">Kemudahan dan kualitas terbaik untuk kebutuhan rumah tangga Anda</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center p-6 bg-white rounded-xl shadow-md hover:shadow-2xl transition transform hover:-translate-y-2">
                <div class="w-20 h-20 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-droplet text-4xl text-blue-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Air Berkualitas</h3>
                <p class="text-sm text-gray-600">Air minum higienis dan terjamin kualitasnya</p>
            </div>
            
            <div class="text-center p-6 bg-white rounded-xl shadow-md hover:shadow-2xl transition transform hover:-translate-y-2">
                <div class="w-20 h-20 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-truck-fast text-4xl text-green-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Pengiriman Cepat</h3>
                <p class="text-sm text-gray-600">Layanan antar langsung ke rumah Anda</p>
            </div>
            
            <div class="text-center p-6 bg-white rounded-xl shadow-md hover:shadow-2xl transition transform hover:-translate-y-2">
                <div class="w-20 h-20 mx-auto mb-4 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-hand-holding-dollar text-4xl text-yellow-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Harga Terjangkau</h3>
                <p class="text-sm text-gray-600">Harga kompetitif dengan kualitas terbaik</p>
            </div>
            
            <div class="text-center p-6 bg-white rounded-xl shadow-md hover:shadow-2xl transition transform hover:-translate-y-2">
                <div class="w-20 h-20 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shield-halved text-4xl text-purple-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Aman & Terpercaya</h3>
                <p class="text-sm text-gray-600">Produk resmi dengan jaminan kualitas</p>
            </div>
        </div>
    </div>

    <!-- Featured Products Section -->
    <div id="products" class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4"><i class="fas fa-fire text-orange-500 mr-2"></i>Produk Terlaris</h2>
                <p class="text-xl text-gray-600">Produk pilihan yang paling diminati pelanggan</p>
            </div>
            
            <?php if (!empty($featuredProducts)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <?php foreach (array_slice($featuredProducts, 0, 4) as $product): ?>
                    <div class="bg-white rounded-xl shadow-md hover:shadow-2xl transition transform hover:-translate-y-2 overflow-hidden">
                        <a href="?route=product.detail&slug=<?php echo e($product['slug']); ?>" class="block relative group">
                            <?php
                            require_once __DIR__ . '/../Helpers/ImageHelper.php';
                            $imageUrl = ImageHelper::getImageUrl($product['image']);
                            if ($imageUrl): ?>
                                <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($product['name']); ?>" class="w-full h-56 object-cover group-hover:scale-105 transition duration-300">
                            <?php else: ?>
                                <div class="w-full h-56 bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                    <i class="fas fa-box text-6xl text-blue-400"></i>
                                </div>
                            <?php endif; ?>
                        </a>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-800 mb-2 line-clamp-2 min-h-[3rem]"><?php echo e($product['name']); ?></h3>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-xl font-bold text-blue-600">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                                <span class="text-sm text-gray-500"><i class="fas fa-box mr-1"></i><?php echo e($product['stock']); ?></span>
                            </div>
                            <button onclick="addToCart(<?php echo e($product['id']); ?>)" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-2 rounded-lg font-semibold transition">
                                <i class="fas fa-shopping-cart mr-2"></i>Tambah
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4"><i class="fas fa-th-large mr-2"></i>Kategori Produk</h2>
                <p class="text-lg text-gray-600">Temukan berbagai produk kebutuhan rumah tangga</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <a href="?route=products&category=<?php echo e($cat['id']); ?>" class="group">
                            <div class="bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl p-8 text-center hover:shadow-xl transition transform hover:-translate-y-2">
                                <div class="text-5xl mb-4">
                                    <?php 
                                    $icons = ['droplet', 'fire-burner', 'spray-can-sparkles', 'bottle-droplet', 'oil-can'];
                                    $icon = $icons[($cat['id'] - 1) % count($icons)];
                                    ?>
                                    <i class="fas fa-<?php echo $icon; ?> text-blue-600"></i>
                                </div>
                                <h3 class="font-bold text-gray-800 group-hover:text-blue-700 text-lg"><?php echo e($cat['name']); ?></h3>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <a href="?route=products" class="group">
                    <div class="bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl p-8 text-center hover:shadow-xl transition transform hover:-translate-y-2">
                        <div class="text-5xl mb-4"><i class="fas fa-grip text-purple-600"></i></div>
                        <h3 class="font-bold text-gray-800 group-hover:text-purple-700 text-lg">Lihat Semua</h3>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-4">Siap Berbelanja?</h2>
            <p class="text-xl text-blue-100 mb-8">Daftar sekarang dan dapatkan penawaran terbaik!</p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="?route=auth.register" class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-lg font-bold text-lg transition">
                        Daftar Sekarang
                    </a>
                    <a href="?route=auth.login" class="bg-blue-500 hover:bg-blue-400 text-white px-8 py-4 rounded-lg font-bold text-lg transition">
                        Login
                    </a>
                <?php else: ?>
                    <a href="?route=products" class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-lg font-bold text-lg transition">
                        Mulai Belanja
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-lg mb-2">© 2025 GoRefill - Layanan Isi Ulang Terpercaya</p>
                <p class="text-gray-400">Developed by Fahmi Aksan Nugroho</p>
            </div>
        </div>
    </footer>
    <script src="public/assets/js/cart.js"></script>
</body>
</html>
