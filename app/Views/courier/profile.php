<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Courier Profile') ?> - GoRefill</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <header class="bg-green-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="fas fa-truck text-3xl"></i>
                    <div>
                        <h1 class="text-2xl font-bold">Courier Profile</h1>
                        <p class="text-sm text-green-100">
                            <?= htmlspecialchars($_SESSION['name'] ?? ($user['name'] ?? 'Courier')) ?>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="index.php?route=courier.dashboard" class="bg-white text-green-700 hover:bg-green-50 px-4 py-2 rounded-lg text-sm font-medium border border-green-600">
                        <i class="fas fa-arrow-left mr-1"></i> Dashboard
                    </a>
                    <a href="index.php?route=auth.logout" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg text-sm font-medium">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-8">
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="mb-4 rounded-lg bg-green-100 border border-green-200 px-4 py-3 text-sm text-green-800 flex items-center justify-between">
                <div>
                    <i class="fas fa-check-circle mr-2"></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="mb-4 rounded-lg bg-red-100 border border-red-200 px-4 py-3 text-sm text-red-800 flex items-center justify-between">
                <div>
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user mr-2 text-green-600"></i> Profil Kurir
                </h2>
                <form method="POST" action="index.php?route=courier.updateProfile" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <input type="text" name="name" required
                               value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" required
                               value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="phone"
                               value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-lock mr-2 text-blue-600"></i> Ubah Password
                </h2>
                <form method="POST" action="index.php?route=courier.changePassword" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                        <input type="password" name="current_password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input type="password" name="new_password" required minlength="8"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" required minlength="8"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center justify-center">
                            <i class="fas fa-key mr-2"></i> Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
