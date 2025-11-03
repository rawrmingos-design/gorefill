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
    <?php include __DIR__ . '../../layouts/navbar.php'; ?>

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

    <?php include __DIR__ . '/../layouts/footer.php'; ?>
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
