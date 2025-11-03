/**
 * Reviews.js
 * Handle product reviews and star rating UI
 */

let selectedRating = 0;

/**
 * Initialize star rating selector
 */
function initStarRating() {
    const stars = document.querySelectorAll('.star-rating .star');
    const ratingInput = document.getElementById('rating');
    
    if (!stars.length) return;
    
    stars.forEach((star, index) => {
        // Click to select rating
        star.addEventListener('click', function() {
            selectedRating = index + 1;
            ratingInput.value = selectedRating;
            updateStars(selectedRating);
        });
        
        // Hover effect
        star.addEventListener('mouseenter', function() {
            updateStars(index + 1);
        });
    });
    
    // Reset to selected rating on mouse leave
    const ratingContainer = document.querySelector('.star-rating');
    if (ratingContainer) {
        ratingContainer.addEventListener('mouseleave', function() {
            updateStars(selectedRating);
        });
    }
}

/**
 * Update star visual state
 * @param {number} rating - Rating value (1-5)
 */
function updateStars(rating) {
    const stars = document.querySelectorAll('.star-rating .star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('far');
            star.classList.add('fas', 'text-yellow-400');
        } else {
            star.classList.remove('fas', 'text-yellow-400');
            star.classList.add('far', 'text-gray-300');
        }
    });
}

/**
 * Display star rating (read-only)
 * @param {number} rating - Rating value (0-5, can be decimal)
 * @param {string} containerId - Container element ID
 */
function displayStarRating(rating, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    
    let html = '';
    
    // Full stars
    for (let i = 0; i < fullStars; i++) {
        html += '<i class="fas fa-star text-yellow-400"></i>';
    }
    
    // Half star
    if (hasHalfStar) {
        html += '<i class="fas fa-star-half-alt text-yellow-400"></i>';
    }
    
    // Empty stars
    for (let i = 0; i < emptyStars; i++) {
        html += '<i class="far fa-star text-gray-300"></i>';
    }
    
    container.innerHTML = html;
}

/**
 * Submit review via AJAX
 * @param {number} productId - Product ID
 */
async function submitReview(productId) {
    const rating = document.getElementById('rating').value;
    const review = document.getElementById('review').value;
    
    // Validation
    if (!rating || rating < 1 || rating > 5) {
        Swal.fire({
            icon: 'warning',
            title: 'Rating Diperlukan',
            text: 'Silakan pilih rating 1-5 bintang'
        });
        return;
    }
    
    if (!review.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Review Diperlukan',
            text: 'Silakan tulis review Anda'
        });
        return;
    }
    
    if (review.trim().length < 10) {
        Swal.fire({
            icon: 'warning',
            title: 'Review Terlalu Pendek',
            text: 'Review minimal 10 karakter'
        });
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Mengirim Review...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const response = await fetch('index.php?route=product.addReview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                rating: parseInt(rating),
                review: review.trim()
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success message
            await Swal.fire({
                icon: 'success',
                title: 'Review Berhasil!',
                text: data.message,
                confirmButtonText: 'OK'
            });
            
            // Reload page to show new review
            window.location.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message
            });
        }
    } catch (error) {
        console.error('Submit review error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan saat mengirim review'
        });
    }
}

/**
 * Format date to Indonesian format
 * @param {string} dateString - Date string
 * @return {string} Formatted date
 */
function formatReviewDate(dateString) {
    const date = new Date(dateString);
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    return date.toLocaleDateString('id-ID', options);
}

/**
 * Show rating distribution chart
 * @param {object} distribution - Rating distribution {5: count, 4: count, ...}
 * @param {number} totalReviews - Total review count
 */
function displayRatingDistribution(distribution, totalReviews) {
    const container = document.getElementById('ratingDistribution');
    if (!container || totalReviews === 0) return;
    
    let html = '';
    
    for (let star = 5; star >= 1; star--) {
        const count = distribution[star] || 0;
        const percentage = totalReviews > 0 ? (count / totalReviews * 100) : 0;
        
        html += `
            <div class="flex items-center space-x-2 mb-2">
                <span class="text-sm font-semibold w-8">${star} <i class="fas fa-star text-yellow-400 text-xs"></i></span>
                <div class="flex-1 bg-gray-200 rounded-full h-2">
                    <div class="bg-yellow-400 h-2 rounded-full" style="width: ${percentage}%"></div>
                </div>
                <span class="text-sm text-gray-600 w-12 text-right">${count}</span>
            </div>
        `;
    }
    
    container.innerHTML = html;
}

/**
 * Create star rating HTML (read-only)
 * @param {number} rating - Rating value
 * @return {string} HTML string
 */
function createStarHTML(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    
    let html = '';
    
    for (let i = 0; i < fullStars; i++) {
        html += '<i class="fas fa-star text-yellow-400"></i>';
    }
    
    if (hasHalfStar) {
        html += '<i class="fas fa-star-half-alt text-yellow-400"></i>';
    }
    
    for (let i = 0; i < emptyStars; i++) {
        html += '<i class="far fa-star text-gray-300"></i>';
    }
    
    return html;
}

/**
 * Scroll to reviews section
 */
function scrollToReviews() {
    const reviewsSection = document.getElementById('reviews-section');
    if (reviewsSection) {
        reviewsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

/**
 * Initialize on page load
 */
document.addEventListener('DOMContentLoaded', function() {
    initStarRating();
    console.log('✅ Reviews.js loaded');
});
