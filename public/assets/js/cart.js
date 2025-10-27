/**
 * GoRefill Shopping Cart JavaScript
 * Handles AJAX operations for cart functionality
 */

/**
 * Add product to cart
 * @param {number} productId 
 * @param {number} quantity 
 */
async function addToCart(productId, quantity = 1) {
    try {
        const response = await fetch('?route=cart.add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        });

        const data = await response.json();

        if (data.success) {
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Added to Cart!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });

            // Update cart badge
            updateCartBadge(data.cart_count);
        } else {
            // Show error message
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    } catch (error) {
        console.error('Add to cart error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to add product to cart'
        });
    }
}

/**
 * Update cart item quantity
 * @param {number} productId 
 * @param {number} change - positive or negative change
 */
async function updateQuantity(productId, change) {
    try {
        // Get current quantity from input
        const qtyInput = document.querySelector(`tr[data-product-id="${productId}"] .qty-input`);
        const currentQty = parseInt(qtyInput.value);
        const newQty = currentQty + change;

        // Validate
        if (newQty < 1) {
            return;
        }

        const maxQty = parseInt(qtyInput.max);
        if (newQty > maxQty) {
            Swal.fire({
                icon: 'warning',
                title: 'Stock Limit',
                text: `Only ${maxQty} items available in stock`
            });
            return;
        }

        // Update via AJAX
        const response = await fetch('?route=cart.update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: newQty
            })
        });

        const data = await response.json();

        if (data.success) {
            // Update quantity input
            qtyInput.value = data.quantity;
            
            // Update cart badge
            updateCartBadge(data.cart_count);
            
            // Update item subtotal (real-time!)
            const subtotalElement = document.querySelector(`tr[data-product-id="${productId}"] .item-subtotal`);
            if (subtotalElement) {
                subtotalElement.textContent = 'Rp ' + formatNumber(data.item_subtotal);
            }
            
            // Update cart total (real-time!)
            updateCartTotals(data.cart_total);
            
            // Show success toast (smaller notification)
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1000,
                timerProgressBar: true
            });
            
            Toast.fire({
                icon: 'success',
                title: 'Cart updated'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    } catch (error) {
        console.error('Update quantity error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to update quantity'
        });
    }
}

/**
 * Set specific quantity
 * @param {number} productId 
 * @param {number} quantity 
 */
async function setQuantity(productId, quantity) {
    quantity = parseInt(quantity);
    
    if (quantity < 1) {
        quantity = 1;
    }

    try {
        const response = await fetch('?route=cart.update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        });

        const data = await response.json();

        if (data.success) {
            // Update cart badge
            updateCartBadge(data.cart_count);
            
            // Update item subtotal (real-time!)
            const subtotalElement = document.querySelector(`tr[data-product-id="${productId}"] .item-subtotal`);
            if (subtotalElement) {
                subtotalElement.textContent = 'Rp ' + formatNumber(data.item_subtotal);
            }
            
            // Update cart total (real-time!)
            updateCartTotals(data.cart_total);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    } catch (error) {
        console.error('Set quantity error:', error);
    }
}

/**
 * Remove item from cart
 * @param {number} productId 
 */
async function removeItem(productId) {
    // Confirm with SweetAlert
    const result = await Swal.fire({
        title: 'Remove Item?',
        text: 'Are you sure you want to remove this item from your cart?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, remove it',
        cancelButtonText: 'Cancel'
    });

    if (!result.isConfirmed) {
        return;
    }

    try {
        const response = await fetch('?route=cart.remove', {
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
            // Show success toast
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true
            });
            
            Toast.fire({
                icon: 'success',
                title: 'Item removed'
            });

            // Update cart badge
            updateCartBadge(data.cart_count);

            // Remove row with animation (real-time!)
            const row = document.querySelector(`tr[data-product-id="${productId}"]`);
            if (row) {
                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';
                
                setTimeout(() => {
                    row.remove();
                    
                    // Check if cart is empty
                    const cartItems = document.getElementById('cart-items');
                    if (cartItems && cartItems.children.length === 0) {
                        // Reload to show empty state
                        location.reload();
                    } else {
                        // Recalculate total via AJAX
                        recalculateCartTotal();
                    }
                }, 300);
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    } catch (error) {
        console.error('Remove item error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to remove item'
        });
    }
}

/**
 * Get cart count
 * @returns {Promise<number>}
 */
async function getCartCount() {
    try {
        const response = await fetch('?route=cart.count');
        const data = await response.json();
        return data.count;
    } catch (error) {
        console.error('Get cart count error:', error);
        return 0;
    }
}

/**
 * Update cart badge in navbar
 * @param {number} count 
 */
function updateCartBadge(count) {
    // Update desktop badge
    const badge = document.getElementById('cart-badge');
    if (badge) {
        badge.textContent = count;
        
        // Add animation
        badge.classList.add('animate-bounce');
        setTimeout(() => {
            badge.classList.remove('animate-bounce');
        }, 1000);
    }
    
    // Update mobile badge
    const mobileBadge = document.getElementById('cart-badge-mobile');
    if (mobileBadge) {
        mobileBadge.textContent = count;
        
        // Add animation
        mobileBadge.classList.add('animate-bounce');
        setTimeout(() => {
            mobileBadge.classList.remove('animate-bounce');
        }, 1000);
    }
}

/**
 * Update cart totals display (real-time)
 * @param {number} cartTotal 
 */
function updateCartTotals(cartTotal) {
    // Update subtotal
    const subtotalElement = document.getElementById('cart-subtotal');
    if (subtotalElement) {
        subtotalElement.textContent = 'Rp ' + formatNumber(cartTotal);
    }
    
    // Update total
    const totalElement = document.getElementById('cart-total');
    if (totalElement) {
        totalElement.textContent = 'Rp ' + formatNumber(cartTotal);
    }
}

/**
 * Recalculate cart total from current items
 */
async function recalculateCartTotal() {
    try {
        const response = await fetch('?route=cart.get');
        const data = await response.json();
        
        if (data.success) {
            updateCartTotals(data.total);
        }
    } catch (error) {
        console.error('Recalculate total error:', error);
    }
}

/**
 * Format number with thousands separator
 * @param {number} number 
 * @returns {string}
 */
function formatNumber(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

/**
 * Initialize cart on page load
 */
document.addEventListener('DOMContentLoaded', async function() {
    // Update cart badge with current count
    const count = await getCartCount();
    updateCartBadge(count);
});
