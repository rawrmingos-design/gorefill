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
                    <a href="?route=products" class="text-gray-700 hover:text-blue-600">Products</a>
                    <a href="?route=cart" class="text-blue-600 font-semibold">
                        🛒 Cart <span id="cart-badge" class="bg-blue-600 text-white px-2 py-1 rounded-full text-xs"><?php echo e($cartCount); ?></span>
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
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Shopping Cart</h1>

        <?php if (empty($cartItems)): ?>
            <!-- Empty Cart -->
            <div class="text-center py-16 bg-white rounded-lg shadow">
                <svg class="mx-auto w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Your Cart is Empty</h3>
                <p class="text-gray-500 mb-6">Start shopping to add items to your cart!</p>
                <a href="?route=products" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold">
                    Browse Products
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
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
                                                        class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded font-semibold">-</button>
                                                <input type="number" 
                                                       value="<?php echo e($item['qty']); ?>" 
                                                       min="1" 
                                                       max="<?php echo e($item['stock']); ?>"
                                                       class="w-16 text-center border rounded py-1 qty-input"
                                                       onchange="setQuantity(<?php echo e($item['id']); ?>, this.value)"
                                                       readonly>
                                                <button onclick="updateQuantity(<?php echo e($item['id']); ?>, 1)" 
                                                        class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded font-semibold">+</button>
                                            </div>
                                        </td>
                                        
                                        <!-- Subtotal -->
                                        <td class="px-6 py-4 font-semibold text-gray-800 item-subtotal">
                                            Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?>
                                        </td>
                                        
                                        <!-- Action -->
                                        <td class="px-6 py-4">
                                            <button onclick="removeItem(<?php echo e($item['id']); ?>)" 
                                                    class="text-red-600 hover:text-red-800 font-semibold">
                                                Remove
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
                    <div class="bg-white rounded-lg shadow p-6 sticky top-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Cart Summary</h2>
                        
                        <div class="space-y-3 mb-4">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span id="cart-subtotal">Rp <?php echo number_format($cartTotal, 0, ',', '.'); ?></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping</span>
                                <span class="text-green-600">Calculated at checkout</span>
                            </div>
                            <div class="border-t pt-3 flex justify-between text-xl font-bold text-gray-800">
                                <span>Total</span>
                                <span id="cart-total">Rp <?php echo number_format($cartTotal, 0, ',', '.'); ?></span>
                            </div>
                        </div>
                        
                        <a href="?route=checkout" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center font-bold py-3 rounded-lg mb-3 transition">
                            Proceed to Checkout
                        </a>
                        
                        <a href="?route=products" class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 text-center font-semibold py-3 rounded-lg transition">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="/public/assets/js/cart.js"></script>
</body>
</html>
