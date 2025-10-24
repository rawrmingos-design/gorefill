<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Checkout') ?> - GoRefill</title>
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
                            <a href="?route=admin.dashboard" class="text-purple-600 hover:text-purple-800 font-semibold">
                                <i class="fas fa-cog"></i> Admin
                            </a>
                        <?php endif; ?>
                        <a href="?route=profile" class="text-gray-700 hover:text-blue-600">
                            <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['name']) ?>
                        </a>
                        <a href="?route=auth.logout" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Logout</a>
                    <?php else: ?>
                        <a href="?route=auth.login" class="text-green-600 hover:text-blue-800">Login</a>
                        <a href="?route=auth.register" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Checkout</h1>
            <p class="text-gray-600 mt-1">Lengkapi informasi pemesanan Anda</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Buy Now Mode Info -->
        <?php if (isset($isBuyNow) && $isBuyNow): ?>
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded mb-4 flex items-center">
                <i class="fas fa-bolt text-blue-600 mr-2"></i>
                <span><strong>Quick Checkout:</strong> Anda sedang melakukan pembelian cepat (Buy Now)</span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Checkout Form -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Section 1: Cart Summary -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <?php if (isset($isBuyNow) && $isBuyNow): ?>
                            <i class="fas fa-bolt mr-2 text-blue-600"></i>
                            Produk yang Dibeli
                        <?php else: ?>
                            <i class="fas fa-shopping-cart mr-2 text-green-600"></i>
                            Ringkasan Keranjang
                        <?php endif; ?>
                    </h2>
                    <div class="space-y-3">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="flex items-center gap-4 pb-3 border-b last:border-b-0">
                                <?php
                                $itemImageUrl = ImageHelper::getImageUrl($item['image']);
                                if ($itemImageUrl): ?>
                                    <img src="<?= htmlspecialchars($itemImageUrl) ?>" 
                                         alt="<?= htmlspecialchars($item['name']) ?>"
                                         class="w-16 h-16 object-cover rounded"
                                         onerror="this.onerror=null; this.src='/public/assets/images/placeholder.jpg'">
                                <?php else: ?>
                                    <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                        <span class="text-2xl">📦</span>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-800"><?= htmlspecialchars($item['name']) ?></h3>
                                    <p class="text-sm text-gray-600">
                                        Rp <?= number_format($item['price'], 0, ',', '.') ?> × <?= $item['qty'] ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">
                                        Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Section 2: Address Selection -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-green-600"></i>
                            Alamat Pengiriman
                        </h2>
                        <button onclick="openAddAddressModal()" 
                                class="text-green-600 hover:text-green-700 text-sm font-medium">
                            <i class="fas fa-plus mr-1"></i> Tambah Alamat
                        </button>
                    </div>

                    <?php if (empty($addresses)): ?>
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-map-marker-alt text-4xl mb-3"></i>
                            <p>Belum ada alamat tersimpan</p>
                            <button onclick="openAddAddressModal()" 
                                    class="mt-3 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                                Tambah Alamat Baru
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($addresses as $address): ?>
                                <label class="block cursor-pointer">
                                    <div class="border-2 rounded-lg p-4 transition-all <?= $address['id'] == $selectedAddressId ? 'border-green-600 bg-green-50' : 'border-gray-200 hover:border-green-300' ?>">
                                        <div class="flex items-start gap-3">
                                            <input type="radio" 
                                                   name="address" 
                                                   value="<?= $address['id'] ?>"
                                                   <?= $address['id'] == $selectedAddressId ? 'checked' : '' ?>
                                                   onchange="selectAddress(<?= $address['id'] ?>)"
                                                   onclick="selectAddress(<?= $address['id'] ?>)"
                                                   class="mt-1">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="font-semibold text-gray-800">
                                                        <?= htmlspecialchars($address['label']) ?>
                                                    </span>
                                                    <?php if ($address['is_default']): ?>
                                                        <span class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded">
                                                            Default
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if ($address['place_name']): ?>
                                                    <p class="text-sm text-gray-700 font-medium">
                                                        <?= htmlspecialchars($address['place_name']) ?>
                                                    </p>
                                                <?php endif; ?>
                                                <p class="text-sm text-gray-600">
                                                    <?= htmlspecialchars($address['street']) ?>
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <?= htmlspecialchars($address['city']) ?>
                                                    <?php if ($address['postal_code']): ?>
                                                        - <?= htmlspecialchars($address['postal_code']) ?>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Section 3: Voucher -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="fas fa-ticket-alt mr-2 text-green-600"></i>
                        Kode Voucher
                    </h2>
                    
                    <div id="voucherInputSection" class="<?= $voucherInfo ? 'hidden' : '' ?>">
                        <div class="flex gap-2">
                            <input type="text" 
                                   id="voucherCode" 
                                   placeholder="Masukkan kode voucher"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <button onclick="applyVoucher()" 
                                    class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                Terapkan
                            </button>
                        </div>
                    </div>

                    <div id="voucherAppliedSection" class="<?= !$voucherInfo ? 'hidden' : '' ?>">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Voucher Diterapkan
                                </p>
                                <p class="text-sm text-green-700">
                                    Kode: <span id="appliedVoucherCode"><?= $voucherInfo['code'] ?? '' ?></span>
                                    - Diskon <?= $voucherInfo['discount_percent'] ?? 0 ?>%
                                </p>
                            </div>
                            <button onclick="removeVoucher()" 
                                    class="text-red-600 hover:text-red-700">
                                <i class="fas fa-times-circle text-xl"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Payment Method -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="fas fa-credit-card mr-2 text-green-600"></i>
                        Metode Pembayaran
                    </h2>
                    <div class="bg-gradient-to-r from-blue-50 to-green-50 border border-green-200 rounded-lg p-6">
                        <div class="flex items-center justify-center mb-4">
                            <img src="https://midtrans.com/assets/images/logo-midtrans.svg" alt="Midtrans" class="h-8">
                        </div>
                        <p class="text-center text-gray-700 font-medium mb-3">
                            <i class="fas fa-shield-alt text-green-600 mr-2"></i>
                            Pembayaran Aman dengan Midtrans
                        </p>
                        <div class="grid grid-cols-3 gap-2 text-center text-xs text-gray-600">
                            <div class="bg-white rounded p-2">
                                <i class="fas fa-credit-card text-blue-600 mb-1"></i>
                                <p>Kartu Kredit</p>
                            </div>
                            <div class="bg-white rounded p-2">
                                <i class="fas fa-mobile-alt text-green-600 mb-1"></i>
                                <p>GoPay</p>
                            </div>
                            <div class="bg-white rounded p-2">
                                <i class="fas fa-university text-purple-600 mb-1"></i>
                                <p>Transfer Bank</p>
                            </div>
                            <div class="bg-white rounded p-2">
                                <i class="fas fa-wallet text-orange-600 mb-1"></i>
                                <p>ShopeePay</p>
                            </div>
                            <div class="bg-white rounded p-2">
                                <i class="fas fa-store text-red-600 mb-1"></i>
                                <p>Alfamart</p>
                            </div>
                            <div class="bg-white rounded p-2">
                                <i class="fas fa-store text-yellow-600 mb-1"></i>
                                <p>Indomaret</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-3">
                            Pilih metode pembayaran setelah klik tombol "Lanjutkan ke Pembayaran"
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-semibold mb-4">Ringkasan Pesanan</h2>
                    
                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal</span>
                            <span id="subtotalAmount">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                        </div>
                        
                        <div class="flex justify-between text-green-600" id="discountRow" 
                             style="<?= $voucherDiscount > 0 ? '' : 'display: none;' ?>">
                            <span>Diskon Voucher</span>
                            <span id="discountAmount">- Rp <?= number_format($voucherDiscount, 0, ',', '.') ?></span>
                        </div>
                        
                        <div class="border-t pt-3 flex justify-between text-lg font-bold text-gray-800">
                            <span>Total</span>
                            <span id="totalAmount">Rp <?= number_format($total, 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <button onclick="proceedToPayment()" 
                            class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors font-semibold">
                        <i class="fas fa-lock mr-2"></i>
                        Lanjutkan ke Pembayaran
                    </button>

                    <p class="text-xs text-gray-500 text-center mt-3">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Transaksi Anda aman dan terenkripsi
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Address Modal -->
    <div id="addAddressModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Tambah Alamat Baru</h3>
                <button onclick="closeAddAddressModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="addAddressForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Label Alamat *</label>
                    <input type="text" name="label" required
                           placeholder="Contoh: Rumah, Kantor"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tempat</label>
                    <input type="text" name="place_name"
                           placeholder="Contoh: Gedung ABC"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap *</label>
                    <textarea name="street" required rows="3"
                              placeholder="Jalan, nomor rumah, RT/RW"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kota *</label>
                        <input type="text" name="city" required
                               placeholder="Kota"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                        <input type="text" name="postal_code"
                               placeholder="12345"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="isDefault" class="mr-2">
                    <label for="isDefault" class="text-sm text-gray-700">Jadikan alamat utama</label>
                </div>
                
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeAddAddressModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-white shadow-lg mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-gray-600">
            <p>&copy; 2025 GoRefill. All rights reserved.</p>
        </div>
    </footer>

    <script src="public/assets/js/cart.js"></script>
    <script>
        // Select address
        function selectAddress(addressId) {
            console.log(addressId);
            fetch('index.php?route=checkout.selectAddress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'address_id=' + addressId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Address selected');
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Apply voucher
        function applyVoucher() {
            const voucherCode = document.getElementById('voucherCode').value.trim();
            
            if (!voucherCode) {
                Swal.fire('Error', 'Masukkan kode voucher', 'error');
                return;
            }

            fetch('index.php?route=checkout.applyVoucher', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'voucher_code=' + encodeURIComponent(voucherCode)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI
                    document.getElementById('voucherInputSection').classList.add('hidden');
                    document.getElementById('voucherAppliedSection').classList.remove('hidden');
                    document.getElementById('appliedVoucherCode').textContent = voucherCode.toUpperCase();
                    
                    // Update amounts
                    document.getElementById('discountRow').style.display = 'flex';
                    document.getElementById('discountAmount').textContent = '- Rp ' + formatNumber(data.discount);
                    document.getElementById('totalAmount').textContent = 'Rp ' + formatNumber(data.total);
                    
                    Swal.fire('Berhasil', data.message, 'success');
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            });
        }

        // Remove voucher
        function removeVoucher() {
            fetch('index.php?route=checkout.removeVoucher', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI
                    document.getElementById('voucherInputSection').classList.remove('hidden');
                    document.getElementById('voucherAppliedSection').classList.add('hidden');
                    document.getElementById('voucherCode').value = '';
                    
                    // Update amounts
                    document.getElementById('discountRow').style.display = 'none';
                    document.getElementById('totalAmount').textContent = 'Rp ' + formatNumber(data.total);
                    
                    Swal.fire('Berhasil', data.message, 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Add address modal
        function openAddAddressModal() {
            document.getElementById('addAddressModal').classList.remove('hidden');
        }

        function closeAddAddressModal() {
            document.getElementById('addAddressModal').classList.add('hidden');
            document.getElementById('addAddressForm').reset();
        }

        // Handle add address form submission
        document.getElementById('addAddressForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('index.php?route=checkout.createAddress', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            });
        });

        // Proceed to payment
        function proceedToPayment() {
            <?php if (empty($addresses)): ?>
                Swal.fire('Perhatian', 'Silakan tambahkan alamat pengiriman terlebih dahulu', 'warning');
                return;
            <?php endif; ?>

            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Call checkout API to create order and get Snap token
            fetch('index.php?route=checkout.create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success && data.snap_token) {
                    // Open Snap payment popup
                    snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            console.log('Payment success:', result);
                            window.location.href = 'index.php?route=payment.success&order_id=' + data.order_number;
                        },
                        onPending: function(result) {
                            console.log('Payment pending:', result);
                            window.location.href = 'index.php?route=payment.pending&order_id=' + data.order_number;
                        },
                        onError: function(result) {
                            console.log('Payment error:', result);
                            window.location.href = 'index.php?route=payment.failed&order_id=' + data.order_number;
                        },
                        onClose: function() {
                            console.log('Payment popup closed');
                            Swal.fire({
                                title: 'Pembayaran Dibatalkan',
                                text: 'Anda menutup halaman pembayaran. Order tetap tersimpan, Anda bisa melanjutkan pembayaran dari halaman order.',
                                icon: 'info',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = 'index.php?route=profile.orders';
                            });
                        }
                    });
                } else {
                    Swal.fire('Error', data.message || 'Gagal memproses checkout', 'error');
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Checkout error:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat memproses checkout', 'error');
            });
        }

        // Format number helper
        function formatNumber(num) {
            return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    </script>
</body>
</html>
