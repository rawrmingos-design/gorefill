<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Contact') ?> - GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/png" href="/public/assets/images/logo.png">
</head>
<body class="bg-gray-50">
    <?php include __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="container mx-auto px-4 py-12 max-w-6xl">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                <i class="fas fa-envelope text-green-600 mr-3"></i>
                Hubungi Kami
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Punya pertanyaan atau butuh bantuan? Kami siap membantu Anda!
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Contact Info Cards -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Email Card -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow border-l-4 border-blue-500">
                    <div class="flex items-center mb-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-envelope text-blue-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Email</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-2">Kirim email ke kami:</p>
                    <a href="mailto:support@gorefill.com" class="text-blue-600 hover:text-blue-700 font-medium">
                        support@gorefill.com
                    </a>
                </div>

                <!-- Phone Card -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow border-l-4 border-green-500">
                    <div class="flex items-center mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fab fa-whatsapp text-green-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">WhatsApp</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-2">Hubungi via WhatsApp:</p>
                    <a href="https://wa.me/6281234567890" target="_blank" class="text-green-600 hover:text-green-700 font-medium">
                        +62 812-3456-7890
                    </a>
                </div>

                <!-- Hours Card -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow border-l-4 border-purple-500">
                    <div class="flex items-center mb-3">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-clock text-purple-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Jam Operasional</h3>
                    </div>
                    <div class="text-gray-600 text-sm space-y-1">
                        <p><strong>Senin - Jumat:</strong><br>09:00 - 17:00 WIB</p>
                        <p><strong>Sabtu:</strong><br>09:00 - 14:00 WIB</p>
                        <p><strong>Minggu:</strong><br>Libur</p>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-xl p-6 border-2 border-green-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">
                        <i class="fas fa-share-alt text-green-600 mr-2"></i>
                        Ikuti Kami
                    </h3>
                    <div class="flex space-x-3">
                        <a href="#" class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white hover:bg-blue-700 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-pink-600 rounded-full flex items-center justify-center text-white hover:bg-pink-700 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center text-white hover:bg-blue-500 transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center text-white hover:bg-red-700 transition">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-paper-plane text-green-600 mr-2"></i>
                        Kirim Pesan
                    </h2>

                    <form id="contactForm" class="space-y-5">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" required
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-green-500 focus:outline-none transition"
                                   placeholder="Masukkan nama Anda">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" required
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-green-500 focus:outline-none transition"
                                   placeholder="nama@email.com">
                        </div>

                        <!-- Subject -->
                        <div>
                            <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">
                                Subjek <span class="text-red-500">*</span>
                            </label>
                            <select id="subject" name="subject" required
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-green-500 focus:outline-none transition">
                                <option value="">Pilih subjek...</option>
                                <option value="Pertanyaan Produk">Pertanyaan Produk</option>
                                <option value="Masalah Pembayaran">Masalah Pembayaran</option>
                                <option value="Status Pengiriman">Status Pengiriman</option>
                                <option value="Keluhan Produk">Keluhan Produk</option>
                                <option value="Kerjasama/Partnership">Kerjasama/Partnership</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <!-- Message -->
                        <div>
                            <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">
                                Pesan <span class="text-red-500">*</span>
                            </label>
                            <textarea id="message" name="message" rows="6" required
                                      class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-green-500 focus:outline-none transition resize-none"
                                      placeholder="Tulis pesan Anda di sini... (minimal 10 karakter)"></textarea>
                            <p class="text-xs text-gray-500 mt-1">
                                <span id="charCount">0</span> / 1000 karakter
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="submitBtn"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-lg transition shadow-md hover:shadow-lg disabled:bg-gray-400 disabled:cursor-not-allowed">
                            <i class="fas fa-paper-plane mr-2"></i>
                            <span id="submitText">Kirim Pesan</span>
                        </button>
                    </form>

                    <!-- Info Box -->
                    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Respon Cepat:</strong> Kami akan merespon pesan Anda dalam 1x24 jam pada hari kerja.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <script>
        // Character counter
        const messageInput = document.getElementById('message');
        const charCount = document.getElementById('charCount');
        
        messageInput.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            if (length > 1000) {
                charCount.classList.add('text-red-500');
                this.value = this.value.substring(0, 1000);
            } else {
                charCount.classList.remove('text-red-500');
            }
        });

        // Form submission
        document.getElementById('contactForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const originalText = submitText.textContent;
            
            // Disable button and show loading
            submitBtn.disabled = true;
            submitText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
            
            // Get form data
            const formData = new FormData(this);
            
            try {
                const response = await fetch('?route=contact.submit', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Success
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        html: result.message,
                        confirmButtonColor: '#10b981',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Reset form
                        document.getElementById('contactForm').reset();
                        charCount.textContent = '0';
                    });
                } else {
                    // Error
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: result.message,
                        confirmButtonColor: '#ef4444'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.',
                    confirmButtonColor: '#ef4444'
                });
            } finally {
                // Re-enable button
                submitBtn.disabled = false;
                submitText.textContent = originalText;
            }
        });
    </script>
</body>
</html>
