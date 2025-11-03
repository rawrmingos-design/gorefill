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
            <h1 class="text-3xl font-bold text-gray-800">Tambah Kategori Baru</h1>
            <p class="text-gray-600 mt-1">Isi form di bawah untuk menambah kategori produk</p>
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
            <form action="index.php?route=admin.categories.store" method="POST">
                
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
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Contoh: Air Minum, Sabun & Detergen, dll">
                    <p class="text-xs text-gray-500 mt-1">Nama kategori harus unik dan maksimal 100 karakter</p>
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
                              placeholder="Deskripsi kategori (opsional)"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Jelaskan jenis produk yang termasuk dalam kategori ini</p>
                </div>

                <!-- Info Box -->
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Catatan:</strong> Slug akan dibuat otomatis dari nama kategori. 
                        Contoh: "Sabun & Detergen" → "sabun-detergen"
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
                        <i class="fas fa-save mr-2"></i> Simpan Kategori
                    </button>
                </div>

            </form>
        </div>

    </div>

    <?php require_once __DIR__ . "/../partials/footer.php"; ?>

</body>
</html>
