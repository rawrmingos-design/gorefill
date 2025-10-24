<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

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
                        🛒 Cart <span class="bg-blue-600 text-white px-2 py-1 rounded-full text-xs">0</span>
                    </a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="?route=admin.dashboard" class="text-purple-600 hover:text-purple-800 font-semibold flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Admin Panel
                        </a>
                    <?php endif; ?>
                    <a href="?route=profile" class="text-blue-600 font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <?php echo e($_SESSION['name']); ?>
                    </a>
                    <a href="?route=auth.logout" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Edit Profile Form -->
    <div class="max-w-2xl mx-auto px-4 py-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="?route=profile" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left"></i> Kembali ke Profile
            </a>
        </div>

        <!-- Error Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Edit Form -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-edit"></i> Edit Profile
            </h1>

            <form method="POST" action="?route=profile.edit" class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="<?= htmlspecialchars($user['name']) ?>"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?= htmlspecialchars($user['email']) ?>"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">
                        Email digunakan untuk login dan notifikasi
                    </p>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Telepon
                    </label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                           placeholder="08123456789"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">
                        Opsional - untuk keperluan pengiriman
                    </p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-4">
                    <button type="submit" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="?route=profile" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 rounded-lg text-center transition-colors">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>

            <!-- Change Password Section -->
            <div class="mt-8 pt-8 border-t">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-key"></i> Ganti Password
                </h2>
                <p class="text-gray-600 mb-4">
                    Untuk keamanan akun Anda, kami sarankan untuk mengganti password secara berkala.
                </p>
                <button onclick="changePassword()" 
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg">
                    <i class="fas fa-lock"></i> Ganti Password
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white shadow-lg mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-gray-600">
            <p>&copy; 2025 GoRefill. All rights reserved.</p>
        </div>
    </footer>
    <script>
         function changePassword() {
        Swal.fire({
            title: 'Change Password',
            html: `
                <div class="text-left space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Current Password</label>
                        <input type="password" id="current_password" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">New Password</label>
                        <input type="password" id="new_password" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" id="confirm_password" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Change Password',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const current = document.getElementById('current_password').value;
                const newPass = document.getElementById('new_password').value;
                const confirm = document.getElementById('confirm_password').value;
                
                if (!current || !newPass || !confirm) {
                    Swal.showValidationMessage('All fields are required');
                    return false;
                }
                
                if (newPass !== confirm) {
                    Swal.showValidationMessage('New passwords do not match');
                    return false;
                }
                
                if (newPass.length < 8) {
                    Swal.showValidationMessage('Password must be at least 8 characters');
                    return false;
                }
                
                return { current, newPass };
            }
        }).then(async (result) => {
            if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we change your password.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
                try {
                    const formData = new FormData();
                    formData.append('current_password', result.value.current);
                    formData.append('new_password', result.value.newPass);
                    formData.append('confirm_password', result.value.newPass);
                    
                    const response = await fetch('?route=profile.change-password', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();

                    Swal.close();
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            // Password changed, keep user logged in
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.error || 'Failed to change password'
                        });
                    }
                } catch (error) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Please try again.'
                    });
                }
            }
        });
    }

    </script>
</body>
</html>
