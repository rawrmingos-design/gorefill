<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($config['app']['name']); ?> - Layanan Isi Ulang Terpercaya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header/Navbar -->
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

    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-blue-600 to-indigo-700 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center animate__animated animate__fadeIn">
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">
                    Selamat Datang di GoRefill
                </h1>
                <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
                    Layanan Isi Ulang Air, LPG & Kebutuhan Rumah Tangga Terpercaya
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="?route=products" class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-lg font-bold text-lg transition">
                        🛒 Shop Now
                    </a>
                    <a href="#features" class="bg-blue-500 hover:bg-blue-400 text-white px-8 py-4 rounded-lg font-bold text-lg transition">
                        Learn More
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
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Why Choose GoRefill?</h2>
            <p class="text-xl text-gray-600">Kemudahan dan kualitas terbaik untuk kebutuhan rumah tangga Anda</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6 bg-white rounded-lg shadow-lg hover:shadow-xl transition">
                <div class="text-5xl mb-4">💧</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Air Berkualitas</h3>
                <p class="text-gray-600">Air minum higienis dan terjamin kualitasnya</p>
            </div>
            
            <div class="text-center p-6 bg-white rounded-lg shadow-lg hover:shadow-xl transition">
                <div class="text-5xl mb-4">🚚</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Pengiriman Cepat</h3>
                <p class="text-gray-600">Layanan antar langsung ke rumah Anda</p>
            </div>
            
            <div class="text-center p-6 bg-white rounded-lg shadow-lg hover:shadow-xl transition">
                <div class="text-5xl mb-4">💰</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Harga Terjangkau</h3>
                <p class="text-gray-600">Harga kompetitif dengan kualitas terbaik</p>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Product Categories</h2>
                <p class="text-xl text-gray-600">Temukan berbagai produk kebutuhan rumah tangga</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <a href="?route=products&category=Air%20Minum" class="group">
                    <div class="bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg p-8 text-center hover:shadow-lg transition">
                        <div class="text-6xl mb-4">💧</div>
                        <h3 class="font-bold text-gray-800 group-hover:text-blue-600">Air Minum</h3>
                    </div>
                </a>
                
                <a href="?route=products&category=Gas%20LPG" class="group">
                    <div class="bg-gradient-to-br from-orange-100 to-orange-200 rounded-lg p-8 text-center hover:shadow-lg transition">
                        <div class="text-6xl mb-4">🔥</div>
                        <h3 class="font-bold text-gray-800 group-hover:text-orange-600">Gas LPG</h3>
                    </div>
                </a>
                
                <a href="?route=products&category=Rumah%20Tangga" class="group">
                    <div class="bg-gradient-to-br from-green-100 to-green-200 rounded-lg p-8 text-center hover:shadow-lg transition">
                        <div class="text-6xl mb-4">🧼</div>
                        <h3 class="font-bold text-gray-800 group-hover:text-green-600">Rumah Tangga</h3>
                    </div>
                </a>
                
                <a href="?route=products" class="group">
                    <div class="bg-gradient-to-br from-purple-100 to-purple-200 rounded-lg p-8 text-center hover:shadow-lg transition">
                        <div class="text-6xl mb-4">🛒</div>
                        <h3 class="font-bold text-gray-800 group-hover:text-purple-600">Lihat Semua</h3>
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
