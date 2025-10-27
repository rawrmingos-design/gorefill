<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

    <!-- Navbar -->
    <?php include __DIR__ . '../../layouts/navbar.php'; ?>

    <!-- Failed Message -->
    <div class="max-w-3xl mx-auto px-4 py-12">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Failed Icon -->
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-red-100 rounded-full">
                    <i class="fas fa-times-circle text-6xl text-red-500"></i>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-3">Pembayaran Gagal</h1>
            <p class="text-gray-600 mb-8">Maaf, pembayaran Anda tidak dapat diproses. Silakan coba lagi.</p>

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
                        <span class="text-gray-600">Status Pembayaran:</span>
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                            GAGAL
                        </span>
                    </div>
                    
                    <div class="flex justify-between pt-2">
                        <span class="text-lg font-semibold text-gray-800">Total:</span>
                        <span class="text-lg font-bold text-gray-600">
                            Rp <?php echo number_format($order['total'], 0, ',', '.'); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Possible Reasons -->
            <div class="bg-red-50 rounded-lg p-6 mb-6 text-left">
                <h2 class="text-xl font-semibold text-red-800 mb-3">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Kemungkinan Penyebab
                </h2>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <i class="fas fa-circle text-red-500 text-xs mt-2 mr-2"></i>
                        <span>Saldo atau limit kartu kredit tidak mencukupi</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-circle text-red-500 text-xs mt-2 mr-2"></i>
                        <span>Informasi kartu yang dimasukkan tidak valid</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-circle text-red-500 text-xs mt-2 mr-2"></i>
                        <span>Transaksi ditolak oleh bank penerbit kartu</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-circle text-red-500 text-xs mt-2 mr-2"></i>
                        <span>Koneksi internet terputus saat proses pembayaran</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-circle text-red-500 text-xs mt-2 mr-2"></i>
                        <span>Waktu pembayaran telah habis (timeout)</span>
                    </li>
                </ul>
            </div>

            <!-- What to Do -->
            <div class="bg-blue-50 rounded-lg p-6 mb-6 text-left">
                <h2 class="text-xl font-semibold text-blue-800 mb-3">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Apa yang Harus Dilakukan?
                </h2>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mt-1 mr-2"></i>
                        <span>Pastikan saldo atau limit kartu Anda mencukupi</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mt-1 mr-2"></i>
                        <span>Periksa kembali informasi kartu yang Anda masukkan</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mt-1 mr-2"></i>
                        <span>Gunakan metode pembayaran alternatif (GoPay, Transfer Bank, dll)</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mt-1 mr-2"></i>
                        <span>Hubungi bank Anda jika masalah berlanjut</span>
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="?route=products" 
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Belanja Lagi
                </a>
                <a href="?route=profile" 
                   class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors">
                    <i class="fas fa-list mr-2"></i>
                    Lihat Pesanan Saya
                </a>
                <a href="?route=home" 
                   class="inline-block border-2 border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold px-8 py-3 rounded-lg transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    Kembali ke Beranda
                </a>
            </div>

            <!-- Help -->
            <div class="mt-8 p-4 bg-gray-100 rounded-lg">
                <p class="text-sm text-gray-700">
                    <i class="fas fa-question-circle mr-2"></i>
                    Butuh bantuan? Hubungi customer service kami di <strong>support@gorefill.com</strong> atau WhatsApp <strong>0812-3456-7890</strong>
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white shadow-lg mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-gray-600">
            <p>&copy; 2025 GoRefill. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
