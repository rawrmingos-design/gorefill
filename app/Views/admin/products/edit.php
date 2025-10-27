<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'Edit Product'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <?php $currentRoute = 'admin.products'; ?>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <!-- Content -->
    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Edit Product</h2>
            <p class="text-gray-600">Update product details below</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form id="productForm" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo e($product['id']); ?>">
                
                <div class="space-y-4">
                    <!-- Product Name -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Product Name *</label>
                        <input type="text" name="name" required value="<?php echo e($product['name']); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Kategori *</label>
                        <select name="category_id" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['id']) ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Price & Stock -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Price (Rp) *</label>
                            <input type="number" name="price" required min="0" step="100" value="<?php echo e($product['price']); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Stock *</label>
                            <input type="number" name="stock" required min="0" value="<?php echo e($product['stock']); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Description</label>
                        <textarea name="description" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo e($product['description'] ?? ''); ?></textarea>
                    </div>

                    <!-- Current Image -->
                    <?php if ($product['image']): ?>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Current Image</label>
                            <?php
                            require_once __DIR__ . '/../../../Helpers/ImageHelper.php';
                            $imageUrl = ImageHelper::getImageUrl($product['image']);
                            ?>
                            <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($product['name']); ?>" class="w-32 h-32 object-cover rounded border">
                            <?php if (ImageHelper::isExternalUrl($product['image'])): ?>
                                <p class="text-sm text-blue-600 mt-1">From Unsplash</p>
                            <?php else: ?>
                                <p class="text-sm text-gray-600 mt-1">Local file</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- New Image -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Change Image (optional)</label>
                        
                        <!-- Image Type Selection -->
                        <div class="mb-3">
                            <label class="inline-flex items-center mr-6">
                                <input type="radio" name="image_type" value="file" checked class="mr-2" onchange="toggleImageInput()">
                                <span class="text-gray-700">Upload File</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="image_type" value="url" class="mr-2" onchange="toggleImageInput()">
                                <span class="text-gray-700">Unsplash URL</span>
                            </label>
                            <label class="inline-flex items-center ml-6">
                                <input type="radio" name="image_type" value="keep" class="mr-2" onchange="toggleImageInput()">
                                <span class="text-gray-700">Keep Current</span>
                            </label>
                        </div>
                        
                        <!-- File Upload Input -->
                        <div id="fileInput">
                            <input type="file" name="image_file" accept="image/*" class="w-full px-4 py-2 border rounded-lg">
                            <p class="text-sm text-gray-500 mt-1">Supported formats: JPG, PNG, WebP (max 5MB)</p>
                        </div>
                        
                        <!-- URL Input -->
                        <div id="urlInput" style="display: none;">
                            <input type="url" name="image_url" placeholder="https://images.unsplash.com/photo-..." class="w-full px-4 py-2 border rounded-lg">
                            <p class="text-sm text-gray-500 mt-1">Must be from Unsplash (unsplash.com or images.unsplash.com)</p>
                            <p class="text-sm text-blue-600 mt-1">
                                <a href="https://unsplash.com" target="_blank" class="hover:underline">Browse Unsplash →</a>
                            </p>
                        </div>
                        
                        <p class="text-sm text-gray-500 mt-2">Leave as "Keep Current" to maintain existing image</p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit" id="submitBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg">
                            <span id="btnText">Update Product</span>
                        </button>
                        <a href="?route=admin.products" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 rounded-lg text-center">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.getElementById('productForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        
        submitBtn.disabled = true;
        btnText.textContent = 'Updating...';
        
        const formData = new FormData(e.target);
        
        try {
            const response = await fetch('?route=admin.products.edit', {
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
                    window.location.href = data.redirect;
                });
            } else {
                let errorMsg = data.error || 'Failed to update product';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).join('<br>');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: errorMsg
                });
                submitBtn.disabled = false;
                btnText.textContent = 'Update Product';
            }
        } catch (error) {
            Swal.fire('Error', 'Something went wrong', 'error');
            submitBtn.disabled = false;
            btnText.textContent = 'Update Product';
        }
    });
    
    // Toggle between file upload, URL input, and keep current
    function toggleImageInput() {
        const imageType = document.querySelector('input[name="image_type"]:checked').value;
        const fileInput = document.getElementById('fileInput');
        const urlInput = document.getElementById('urlInput');
        
        if (imageType === 'file') {
            fileInput.style.display = 'block';
            urlInput.style.display = 'none';
            // Clear URL input
            document.querySelector('input[name="image_url"]').value = '';
        } else if (imageType === 'url') {
            fileInput.style.display = 'none';
            urlInput.style.display = 'block';
            // Clear file input
            document.querySelector('input[name="image_file"]').value = '';
        } else {
            // Keep current - hide both
            fileInput.style.display = 'none';
            urlInput.style.display = 'none';
            // Clear both inputs
            document.querySelector('input[name="image_file"]').value = '';
            document.querySelector('input[name="image_url"]').value = '';
        }
    }
    </script>
</body>
</html>
