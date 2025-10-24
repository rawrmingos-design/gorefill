<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
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

    <!-- Content -->
    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden animate__animated animate__fadeIn">
            <!-- Profile Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-12 text-center">
                <div class="inline-block p-4 bg-white rounded-full mb-4">
                    <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2"><?php echo e($_SESSION['name'] ?? 'User'); ?></h1>
                <p class="text-blue-100"><?php echo e($_SESSION['email'] ?? ''); ?></p>
                <div class="mt-3">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold <?php 
                        $role = $_SESSION['role'] ?? 'user';
                        echo $role === 'admin' ? 'bg-purple-500 text-white' : 
                             ($role === 'kurir' ? 'bg-green-500 text-white' : 'bg-blue-200 text-blue-800');
                    ?>">
                        <?php echo strtoupper(e($_SESSION['role'] ?? 'USER')); ?>
                    </span>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="px-8 py-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Account Information</h2>
                
                <div class="space-y-4">
                    <!-- Full Name -->
                    <div class="flex items-center py-3 border-b">
                        <div class="flex items-center flex-1">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Full Name</p>
                                <p class="font-semibold text-gray-800"><?php echo e($_SESSION['name'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="flex items-center py-3 border-b">
                        <div class="flex items-center flex-1">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Email Address</p>
                                <p class="font-semibold text-gray-800"><?php echo e($_SESSION['email'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Phone -->
                    <?php if (isset($_SESSION['phone']) && $_SESSION['phone']): ?>
                    <div class="flex items-center py-3 border-b">
                        <div class="flex items-center flex-1">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Phone Number</p>
                                <p class="font-semibold text-gray-800"><?php echo e($_SESSION['phone']); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Member Since -->
                    <?php if (isset($_SESSION['created_at'])): ?>
                    <div class="flex items-center py-3">
                        <div class="flex items-center flex-1">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Member Since</p>
                                <p class="font-semibold text-gray-800"><?php echo date('F d, Y', strtotime($_SESSION['created_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="px-8 py-6 bg-gray-50 space-y-3">
                <button onclick="editProfile()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Profile
                </button>
                
                <button onclick="changePassword()" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 rounded-lg transition flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    Change Password
                </button>

                <button onclick="deleteAccount()" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-lg transition flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete Account
                </button>

                <a href="?route=home" class="block w-full bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold py-3 rounded-lg transition text-center">
                    ← Back to Home
                </a>
            </div>
        </div>
    </div>

    <script>
    function editProfile() {
        Swal.fire({
            title: 'Edit Profile',
            html: `
                <div class="text-left space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
                        <input type="text" id="edit_name" value="<?php echo e($_SESSION['name']); ?>" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" id="edit_phone" value="<?php echo e($_SESSION['phone'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg" placeholder="08123456789">
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Save Changes',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const name = document.getElementById('edit_name').value;
                const phone = document.getElementById('edit_phone').value;
                
                if (!name) {
                    Swal.showValidationMessage('Name is required');
                    return false;
                }
                
                return { name, phone };
            }
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const formData = new FormData();
                    formData.append('name', result.value.name);
                    formData.append('phone', result.value.phone);
                    
                    const response = await fetch('?route=profile.edit', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.error || 'Failed to update profile'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Please try again.'
                    });
                }
            }
        });
    }

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
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Please try again.'
                    });
                }
            }
        });
    }

    async function deleteAccount() {
        const result = await Swal.fire({
            title: 'Delete Account?',
            html: `
                <div class="text-left">
                    <p class="text-red-600 font-semibold mb-4">⚠️ Warning: This action cannot be undone!</p>
                    <p class="text-gray-600 mb-4">Deleting your account will:</p>
                    <ul class="list-disc list-inside text-gray-600 space-y-1 mb-4">
                        <li>Permanently delete your profile</li>
                        <li>Remove all your order history</li>
                        <li>Cancel any pending orders</li>
                    </ul>
                    <p class="text-gray-700 font-semibold">Type your email to confirm:</p>
                    <input type="email" id="confirm_email" class="w-full px-4 py-2 border rounded-lg mt-2" placeholder="<?php echo e($_SESSION['email']); ?>">
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Delete My Account',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const email = document.getElementById('confirm_email').value;
                const currentEmail = '<?php echo e($_SESSION['email']); ?>';
                
                if (email !== currentEmail) {
                    Swal.showValidationMessage('Email does not match');
                    return false;
                }
                
                return true;
            }
        });

        if (result.isConfirmed) {
            try {
                const formData = new FormData();
                formData.append('email', '<?php echo e($_SESSION['email']); ?>');
                
                const response = await fetch('?route=profile.delete', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Account Deleted',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || 'Failed to delete account'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong. Please try again.'
                });
            }
        }
    }
    </script>
</body>
</html>
