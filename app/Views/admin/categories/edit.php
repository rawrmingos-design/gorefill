<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - GoRefill Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

    <?php $currentRoute = 'admin.categories'; ?>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="mb-6">
            <a href="index.php?route=admin.categories" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Kategori
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Edit Kategori</h1>
            <p class="text-gray-600 mt-1">Edit informasi kategori produk</p>
        </div>

        <!-- Error Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="index.php?route=admin.categories.update" method="POST">
                
                <!-- Hidden ID -->
                <input type="hidden" name="id" value="<?= htmlspecialchars($category['id']) ?>">

                <!-- Nama Kategori -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           required
                           maxlength="100"
                           value="<?= htmlspecialchars($category['name']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Contoh: Air Minum, Sabun & Detergen, dll">
                    <p class="text-xs text-gray-500 mt-1">Nama kategori harus unik dan maksimal 100 karakter</p>
                </div>

                <!-- Current Slug -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Slug Saat Ini
                    </label>
                    <div class="px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                        <code class="text-sm text-gray-700"><?= htmlspecialchars($category['slug']) ?></code>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Slug akan diupdate otomatis jika nama diubah</p>
                </div>

                <!-- Deskripsi -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Deskripsi kategori (opsional)"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                    <p class="text-xs text-gray-500 mt-1">Jelaskan jenis produk yang termasuk dalam kategori ini</p>
                </div>

                <!-- Info Box -->
                <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> Mengubah nama kategori akan mengubah slug, tetapi tidak mempengaruhi produk yang sudah menggunakan kategori ini.
                    </p>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end space-x-3">
                    <a href="index.php?route=admin.categories" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">
                        <i class="fas fa-save mr-2"></i> Update Kategori
                    </button>
                </div>

            </form>
        </div>

        <!-- Category Info -->
        <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h3 class="font-semibold text-gray-800 mb-2">Informasi Kategori</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">ID:</span>
                    <span class="font-medium ml-2"><?= htmlspecialchars($category['id']) ?></span>
                </div>
                <div>
                    <span class="text-gray-600">Dibuat:</span>
                    <span class="font-medium ml-2"><?= date('d M Y', strtotime($category['created_at'])) ?></span>
                </div>
                <?php if (isset($category['updated_at']) && $category['updated_at'] != $category['created_at']): ?>
                <div>
                    <span class="text-gray-600">Terakhir diupdate:</span>
                    <span class="font-medium ml-2"><?= date('d M Y H:i', strtotime($category['updated_at'])) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="bg-white shadow-lg mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-gray-600">
            <p>&copy; 2025 GoRefill Admin. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
