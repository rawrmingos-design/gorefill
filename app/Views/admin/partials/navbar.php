<!-- Admin Navbar -->
<nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="index.php?route=admin.dashboard" class="text-2xl font-bold text-green-600 hover:text-green-700 transition-colors">
                    🌿 GoRefill Admin
                </a>
            </div>
            <div class="flex items-center space-x-1">
                <a href="index.php?route=admin.dashboard" 
                   class="<?= ($currentRoute ?? '') === 'admin.dashboard' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' ?> px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-chart-line mr-2"></i>Dashboard
                </a>
                <a href="index.php?route=admin.products" 
                   class="<?= ($currentRoute ?? '') === 'admin.products' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' ?> px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-box mr-2"></i>Produk
                </a>
                <a href="index.php?route=admin.categories" 
                   class="<?= ($currentRoute ?? '') === 'admin.categories' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' ?> px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-tags mr-2"></i>Kategori
                </a>
                <a href="index.php?route=admin.orders" 
                   class="<?= ($currentRoute ?? '') === 'admin.orders' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' ?> px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-box-open mr-2"></i>Orders
                </a>
                <a href="index.php?route=admin.vouchers" 
                   class="<?= ($currentRoute ?? '') === 'admin.vouchers' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50' ?> px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-ticket-alt mr-2"></i>Voucher
                </a>
                
                <!-- User Dropdown -->
                <div class="relative ml-3" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center text-gray-700 hover:text-blue-600 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-user-circle text-xl mr-2"></i>
                        <span class="font-medium"><?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?></span>
                        <i class="fas fa-chevron-down ml-2 text-xs"></i>
                    </button>
                    
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 border border-gray-200"
                         style="display: none;">
                        <a href="index.php?route=profile" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                            <i class="fas fa-user mr-2"></i>Profile
                        </a>
                        <div class="border-t border-gray-200 my-1"></div>
                        <a href="index.php?route=auth.logout" class="block px-4 py-2 text-red-600 hover:bg-red-50">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Alpine.js for dropdown -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
