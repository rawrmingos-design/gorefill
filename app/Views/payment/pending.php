<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
<?php require_once __DIR__ . '/../../Helpers/ImageHelper.php'; ?>

    <!-- Navbar -->
    <?php include __DIR__ . '../../layouts/navbar.php'; ?>

    <!-- Pending Message -->
    <div class="max-w-3xl mx-auto px-4 py-12">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Pending Icon -->
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-yellow-100 rounded-full">
                    <i class="fas fa-clock text-6xl text-yellow-500"></i>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-3">Menunggu Pembayaran</h1>
            <p class="text-gray-600 mb-8">Order Anda telah dibuat. Silakan selesaikan pembayaran sesuai instruksi yang diberikan.</p>

            <!-- Order Info -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Detail Order</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Order Number:</span>
                        <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($order['order_number']); ?></span>
                    </div>
                    
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Tanggal:</span>
                        <span class="text-gray-800"><?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></span>
                    </div>
                    
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Metode Pembayaran:</span>
                        <span class="text-gray-800 uppercase"><?php echo htmlspecialchars($order['payment_method'] ?? 'Belum dipilih'); ?></span>
                    </div>
                    
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Status Pembayaran:</span>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                            MENUNGGU PEMBAYARAN
                        </span>
                    </div>
                    
                    <div class="flex justify-between pt-2">
                        <span class="text-lg font-semibold text-gray-800">Total yang Harus Dibayar:</span>
                        <span class="text-lg font-bold text-blue-600">
                            Rp <?php echo number_format($order['total'], 0, ',', '.'); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Payment Instructions -->
            <div class="bg-blue-50 rounded-lg p-6 mb-6 text-left">
                <h2 class="text-xl font-semibold text-blue-800 mb-3">
                    <i class="fas fa-info-circle mr-2"></i>
                    Instruksi Pembayaran
                </h2>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mt-1 mr-2"></i>
                        <span>Silakan lakukan pembayaran sesuai metode yang Anda pilih</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mt-1 mr-2"></i>
                        <span>Pembayaran akan diverifikasi secara otomatis oleh sistem</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mt-1 mr-2"></i>
                        <span>Anda akan menerima konfirmasi email setelah pembayaran berhasil</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mt-1 mr-2"></i>
                        <span>Jika belum membayar, Anda bisa cek status pembayaran di halaman profile</span>
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="checkPaymentStatus()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Cek Status Pembayaran
                </button>
                <a href="?route=profile" 
                   class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors">
                    <i class="fas fa-list mr-2"></i>
                    Lihat Pesanan Saya
                </a>
            </div>

            <!-- Warning -->
            <div class="mt-8 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Order akan otomatis dibatalkan jika pembayaran tidak diselesaikan dalam 24 jam.
                </p>
            </div>
        </div>
    </div>

    <script>
        function checkPaymentStatus() {
            Swal.fire({
                title: 'Mengecek Status...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('index.php?route=payment.checkStatus&order_id=<?php echo htmlspecialchars($order['order_number']); ?>')
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    
                    if (data.success) {
                        if (data.payment_status === 'paid') {
                            Swal.fire({
                                title: 'Pembayaran Berhasil!',
                                text: 'Pembayaran Anda telah dikonfirmasi',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = 'index.php?route=payment.success&order_id=<?php echo htmlspecialchars($order['order_number']); ?>';
                            });
                        } else if (data.payment_status === 'pending') {
                            Swal.fire({
                                title: 'Masih Menunggu',
                                text: 'Pembayaran Anda masih dalam proses verifikasi',
                                icon: 'info',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                title: 'Status: ' + data.payment_status,
                                text: 'Silakan cek kembali nanti',
                                icon: 'info',
                                confirmButtonText: 'OK'
                            });
                        }
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.close();
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal mengecek status pembayaran', 'error');
                });
        }

        // Auto check status every 30 seconds
        setInterval(checkPaymentStatus, 30000);
    </script>

    <!-- Footer -->
    <footer class="bg-white shadow-lg mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-gray-600">
            <p>&copy; 2025 GoRefill. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
