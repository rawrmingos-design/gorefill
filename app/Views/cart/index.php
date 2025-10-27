<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
<?php require_once __DIR__ . '/../../Helpers/ImageHelper.php'; ?>
    <!-- Navbar -->
    <?php include __DIR__ . '../../layouts/navbar.php'; ?>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-8 flex items-center">
            <i class="fas fa-shopping-cart text-blue-600 mr-3"></i> Keranjang Belanja
        </h1>

        <?php if (empty($cartItems)): ?>
            <!-- Empty Cart -->
            <div class="text-center py-20 bg-white rounded-xl shadow-lg">
                <div class="mb-6">
                    <i class="fas fa-shopping-cart text-gray-300 text-9xl"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-700 mb-3">Keranjang Belanja Kosong</h3>
                <p class="text-gray-500 mb-8 text-lg">Mulai belanja dan tambahkan produk ke keranjang!</p>
                <a href="?route=products" class="inline-block bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-10 py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition transform hover:scale-105">
                    <i class="fas fa-store mr-2"></i> Belanja Sekarang
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-bold uppercase"><i class="fas fa-box mr-2"></i>Produk</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold uppercase"><i class="fas fa-tag mr-2"></i>Harga</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold uppercase"><i class="fas fa-calculator mr-2"></i>Jumlah</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold uppercase"><i class="fas fa-money-bill mr-2"></i>Subtotal</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold uppercase"><i class="fas fa-cog mr-2"></i>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items" class="divide-y divide-gray-200">
                                <?php foreach ($cartItems as $item): ?>
                                    <tr data-product-id="<?php echo e($item['id']); ?>">
                                        <!-- Product -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <?php
                                                $itemImageUrl = ImageHelper::getImageUrl($item['image']);
                                                if ($itemImageUrl): ?>
                                                    <img src="<?php echo e($itemImageUrl); ?>" 
                                                         alt="<?php echo e($item['name']); ?>" 
                                                         class="w-16 h-16 object-cover rounded mr-4"
                                                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-16 h-16 bg-gray-200 rounded mr-4 flex items-center justify-center\'><span class=\'text-2xl\'>📦</span></div><div><a href=\'?route=product.detail&id=<?php echo e($item['id']); ?>\' class=\'font-semibold text-gray-800 hover:text-blue-600\'><?php echo e($item['name']); ?></a><p class=\'text-sm text-gray-500\'>Stock: <?php echo e($item['stock']); ?></p></div>';">
                                                <?php else: ?>
                                                    <div class="w-16 h-16 bg-gray-200 rounded mr-4 flex items-center justify-center">
                                                        <span class="text-2xl">📦</span>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <a href="?route=product.detail&id=<?php echo e($item['id']); ?>" class="font-semibold text-gray-800 hover:text-blue-600">
                                                        <?php echo e($item['name']); ?>
                                                    </a>
                                                    <p class="text-sm text-gray-500">Stock: <?php echo e($item['stock']); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Price -->
                                        <td class="px-6 py-4 text-gray-800">
                                            Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                                        </td>
                                        
                                        <!-- Quantity -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-2">
                                                <button onclick="updateQuantity(<?php echo e($item['id']); ?>, -1)" 
                                                        class="w-9 h-9 bg-red-500 hover:bg-red-600 text-white rounded-lg font-bold transition shadow-sm">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" 
                                                       value="<?php echo e($item['qty']); ?>" 
                                                       min="1" 
                                                       max="<?php echo e($item['stock']); ?>"
                                                       class="w-16 text-center border-2 border-blue-300 rounded-lg py-2 font-bold qty-input focus:ring-2 focus:ring-blue-500"
                                                       onchange="setQuantity(<?php echo e($item['id']); ?>, this.value)"
                                                       readonly>
                                                <button onclick="updateQuantity(<?php echo e($item['id']); ?>, 1)" 
                                                        class="w-9 h-9 bg-green-500 hover:bg-green-600 text-white rounded-lg font-bold transition shadow-sm">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        
                                        <!-- Subtotal -->
                                        <td class="px-6 py-4 font-semibold text-gray-800 item-subtotal">
                                            Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?>
                                        </td>
                                        
                                        <!-- Action -->
                                        <td class="px-6 py-4">
                                            <button onclick="removeItem(<?php echo e($item['id']); ?>)" 
                                                    class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg font-semibold transition">
                                                <i class="fas fa-trash mr-1"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6 sticky top-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-receipt text-blue-600 mr-2"></i> Ringkasan Belanja
                        </h2>
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between text-gray-700 text-lg">
                                <span class="font-medium">Subtotal</span>
                                <span id="cart-subtotal" class="font-bold">Rp <?php echo number_format($cartTotal, 0, ',', '.'); ?></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span class="font-medium">Ongkir</span>
                                <span class="text-green-600 font-semibold">Dihitung di checkout</span>
                            </div>
                            <div class="border-t-2 border-gray-200 pt-4 flex justify-between text-2xl font-bold text-gray-800">
                                <span>Total</span>
                                <span id="cart-total" class="text-blue-600">Rp <?php echo number_format($cartTotal, 0, ',', '.'); ?></span>
                            </div>
                        </div>
                        
                        <a href="?route=checkout" class="block w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-center font-bold py-4 rounded-xl mb-3 transition shadow-lg hover:shadow-xl transform hover:scale-105">
                            <i class="fas fa-credit-card mr-2"></i> Lanjut ke Pembayaran
                        </a>
                        
                        <a href="?route=products" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-center font-semibold py-4 rounded-xl transition">
                            <i class="fas fa-arrow-left mr-2"></i> Lanjut Belanja
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="/public/assets/js/cart.js"></script>
</body>
</html>
