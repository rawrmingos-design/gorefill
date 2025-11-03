/**
 * Favorites.js
 * Handle wishlist/favorites toggle functionality
 */

/**
 * Toggle favorite status for a product
 * @param {number} productId - Product ID to toggle
 */
async function toggleFavorite(productId) {
    try {
        const response = await fetch('index.php?route=favorite.toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId
            })
        });

        const data = await response.json();

        if (data.success) {
            // Update heart icon
            updateHeartIcon(productId, data.is_favorite);

            // Update favorite count in navbar if exists
            updateFavoriteCount(data.favorite_count);

            // Show success notification
            Swal.fire({
                icon: 'success',
                title: data.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        } else {
            // Check if login required
            if (data.require_login) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Login Diperlukan',
                    text: data.message,
                    showCancelButton: true,
                    confirmButtonText: 'Login',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'index.php?route=auth.login';
                    }
                });
            } else {
                // Show error notification
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        }
    } catch (error) {
        console.error('Toggle favorite error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan saat memproses favorit',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    }
}

/**
 * Update heart icon visual state
 * @param {number} productId - Product ID
 * @param {boolean} isFavorite - Is favorited
 */
function updateHeartIcon(productId, isFavorite) {
    // Find all heart buttons for this product (could be multiple on same page)
    const heartButtons = document.querySelectorAll(`[data-product-id="${productId}"]`);

    heartButtons.forEach(button => {
        const icon = button.querySelector('i');
        
        if (isFavorite) {
            // Filled heart (favorited)
            icon.classList.remove('far', 'fa-heart');
            icon.classList.add('fas', 'fa-heart', 'text-red-500');
            button.setAttribute('title', 'Hapus dari favorit');
        } else {
            // Outline heart (not favorited)
            icon.classList.remove('fas', 'fa-heart', 'text-red-500');
            icon.classList.add('far', 'fa-heart');
            button.setAttribute('title', 'Tambah ke favorit');
        }
    });
}

/**
 * Update favorite count in navbar badge
 * @param {number} count - New favorite count
 */
function updateFavoriteCount(count) {
    const countBadge = document.getElementById('favoriteCount');
    
    if (countBadge) {
        countBadge.textContent = count;
        
        // Hide badge if count is 0
        if (count > 0) {
            countBadge.classList.remove('hidden');
        } else {
            countBadge.classList.add('hidden');
        }
    }
}

/**
 * Remove product from favorites page
 * @param {number} productId - Product ID to remove
 */
async function removeFavorite(productId) {
    // Show confirmation
    const result = await Swal.fire({
        icon: 'question',
        title: 'Hapus dari Favorit?',
        text: 'Produk ini akan dihapus dari daftar favorit Anda',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280'
    });

    if (!result.isConfirmed) {
        return;
    }

    try {
        const response = await fetch('index.php?route=favorite.remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId
            })
        });

        const data = await response.json();

        if (data.success) {
            // Remove product card from DOM
            const productCard = document.querySelector(`[data-favorite-product="${productId}"]`);
            if (productCard) {
                productCard.remove();
            }

            // Update favorite count
            updateFavoriteCount(data.favorite_count);

            // Check if no more favorites
            const remainingProducts = document.querySelectorAll('[data-favorite-product]');
            if (remainingProducts.length === 0) {
                // Show empty state
                showEmptyFavoritesState();
            }

            // Show success notification
            Swal.fire({
                icon: 'success',
                title: data.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message
            });
        }
    } catch (error) {
        console.error('Remove favorite error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan saat menghapus favorit'
        });
    }
}

/**
 * Show empty favorites state
 */
function showEmptyFavoritesState() {
    const container = document.querySelector('.favorites-grid');
    if (container) {
        container.innerHTML = `
            <div class="col-span-full text-center py-16">
                <i class="far fa-heart text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-700 mb-2">Belum Ada Favorit</h3>
                <p class="text-gray-500 mb-6">Anda belum menambahkan produk ke favorit</p>
                <a href="index.php?route=products" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                    <i class="fas fa-shopping-bag mr-2"></i>Lihat Produk
                </a>
            </div>
        `;
    }
}

// Log when favorites.js is loaded
console.log('✅ Favorites.js loaded');
