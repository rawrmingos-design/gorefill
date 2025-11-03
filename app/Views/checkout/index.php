<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Checkout') ?> - GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Leaflet.js for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
            crossorigin=""></script>
    
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
                                                <p class="text-sm text-gray-600">
                                                    <?= htmlspecialchars($address['street']) ?>
                                                </p>
                                                <?php if ($address['village'] || $address['district']): ?>
                                                    <p class="text-sm text-gray-600">
                                                        <?= htmlspecialchars($address['village']) ?><?= $address['district'] ? ', ' . htmlspecialchars($address['district']) : '' ?>
                                                    </p>
                                                <?php endif; ?>
                                                <p class="text-sm text-gray-600">
                                                    <?= htmlspecialchars($address['regency'] ?? $address['city']) ?>
                                                    <?php if ($address['province']): ?>
                                                        , <?= htmlspecialchars($address['province']) ?>
                                                    <?php endif; ?>
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
                    
                    <!-- Available Vouchers (Week 4 Day 17) -->
                    <?php if (!empty($availableVouchers)): ?>
                        <div class="mb-4 bg-gradient-to-r from-blue-50 to-green-50 border border-green-200 rounded-lg p-4">
                            <p class="font-semibold text-green-800 mb-3 flex items-center">
                                <i class="fas fa-gift mr-2"></i>
                                Voucher Tersedia untuk Anda!
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <?php foreach ($availableVouchers as $voucher): ?>
                                    <div class="bg-white border border-green-300 rounded-lg p-3 cursor-pointer hover:shadow-md transition" 
                                         onclick="document.getElementById('voucherCode').value='<?= e($voucher['code']) ?>'; applyVoucher();">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="font-bold text-blue-600 text-sm"><?= e($voucher['code']) ?></p>
                                                <p class="text-xs text-gray-600">Diskon <?= $voucher['discount_percent'] ?>%</p>
                                                <?php if ($voucher['min_purchase'] > 0): ?>
                                                    <p class="text-xs text-gray-500">Min: Rp <?= number_format($voucher['min_purchase'], 0, ',', '.') ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-bold text-green-600">-Rp<?= number_format($voucher['calculated_discount'], 0, ',', '.') ?></p>
                                                <button class="text-xs text-blue-600 hover:underline">Gunakan</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
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

                    <button id="checkoutButton" onclick="proceedToPayment()" 
                            class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors font-semibold disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <i class="fas fa-lock mr-2"></i>
                        <span id="checkoutButtonText">Lanjutkan ke Pembayaran</span>
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
    <div id="addAddressModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center overflow-y-auto">
        <div class="bg-white rounded-lg w-full max-w-2xl mx-4 my-8 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4 p-6 pb-0 sticky top-0 bg-white z-10">
                <h3 class="text-xl font-semibold flex items-center">
                    <i class="fas fa-map-marker-alt text-green-600 mr-2"></i>
                    Tambah Alamat Baru
                </h3>
                <button onclick="closeAddAddressModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="addAddressForm" class="space-y-4 p-6 pt-2">
                <!-- Map Container -->
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-map-marked-alt text-green-600 mr-1"></i>
                            Pilih Lokasi di Peta
                        </label>
                        <button type="button" 
                                onclick="getCurrentLocation()" 
                                class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center">
                            <i class="fas fa-crosshairs mr-1"></i>
                            Gunakan Lokasi Saya
                        </button>
                    </div>
                    <div id="map" class="w-full h-64 rounded-lg border-2 border-gray-300 shadow-sm"></div>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Klik pada peta untuk menempatkan pin di lokasi pengiriman Anda
                    </p>
                </div>

                <!-- Hidden inputs for coordinates -->
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Label Alamat *</label>
                    <input type="text" name="label" required
                           placeholder="Contoh: Rumah, Kantor"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap *</label>
                    <textarea name="street" required rows="3"
                              placeholder="Jalan, nomor rumah, RT/RW"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>
                
                <!-- Location Data from Reverse Geocoding -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-map text-green-600 mr-1 text-xs"></i>
                            Provinsi *
                        </label>
                        <input type="text" name="province" id="province" required readonly
                               placeholder="Akan terisi otomatis"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-building text-green-600 mr-1 text-xs"></i>
                            Kabupaten/Kota *
                        </label>
                        <input type="text" name="regency" id="regency" required readonly
                               placeholder="Akan terisi otomatis"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-map-signs text-green-600 mr-1 text-xs"></i>
                            Kecamatan *
                        </label>
                        <input type="text" name="district" id="district" required readonly
                               placeholder="Akan terisi otomatis"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-home text-green-600 mr-1 text-xs"></i>
                            Kelurahan/Desa *
                        </label>
                        <input type="text" name="village" id="village" required readonly
                               placeholder="Akan terisi otomatis"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-city text-green-600 mr-1 text-xs"></i>
                            Kota (Detail)
                        </label>
                        <input type="text" name="city" id="city"
                               placeholder="Detail kota/area"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-mail-bulk text-green-600 mr-1 text-xs"></i>
                            Kode Pos
                        </label>
                        <input type="text" name="postal_code" id="postal_code" readonly
                               placeholder="Akan terisi otomatis"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="isDefault" class="mr-2">
                    <label for="isDefault" class="text-sm text-gray-700">Jadikan alamat utama</label>
                </div>
                
                <div class="flex gap-3 pt-4 sticky bottom-0 bg-white pb-6 border-t mt-4 pt-4">
                    <button type="button" onclick="closeAddAddressModal()"
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                        <i class="fas fa-save mr-1"></i> Simpan Alamat
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <script src="public/assets/js/cart.js"></script>
    <script src="/public/assets/js/maps.js"></script>
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

        // Add address modal functions are now in maps.js

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

        // Prevent multiple simultaneous checkout requests
        let isProcessingCheckout = false;
        
        // Proceed to payment
        function proceedToPayment() {
            <?php if (empty($addresses)): ?>
                Swal.fire('Perhatian', 'Silakan tambahkan alamat pengiriman terlebih dahulu', 'warning');
                return;
            <?php endif; ?>
            
            // ✅ FIX: Prevent race condition - Check if already processing
            if (isProcessingCheckout) {
                console.warn('⚠️ Checkout already in progress, ignoring duplicate request');
                return;
            }
            
            // ✅ FIX: Set processing flag
            isProcessingCheckout = true;
            
            // ✅ FIX: Disable button immediately
            const checkoutButton = document.getElementById('checkoutButton');
            const checkoutButtonText = document.getElementById('checkoutButtonText');
            checkoutButton.disabled = true;
            checkoutButtonText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

            // Show loading
            Swal.fire({
                title: 'Memproses Checkout...',
                text: 'Mohon tunggu, jangan refresh halaman',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
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
                
                // ✅ FIX: Re-enable button on error
                isProcessingCheckout = false;
                checkoutButton.disabled = false;
                checkoutButtonText.innerHTML = 'Lanjutkan ke Pembayaran';
            });
        }

        // Format number helper
        function formatNumber(num) {
            return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    </script>
</body>
</html>
