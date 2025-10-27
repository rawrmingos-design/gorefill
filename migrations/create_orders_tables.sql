-- ============================================
-- GoRefill Orders & Payment Tables
-- Day 9: Midtrans Payment Integration
-- ============================================

-- Orders Table
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(50) UNIQUE NOT NULL COMMENT 'Format: ORD-YYYYMMDD-XXXX',
  `user_id` INT UNSIGNED NOT NULL,
  `address_id` INT UNSIGNED NOT NULL,
  `voucher_id` INT UNSIGNED NULL,
  
  -- Pricing
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `total` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  
  -- Payment
  `payment_method` VARCHAR(50) NULL COMMENT 'gopay, bca_va, credit_card, etc',
  `payment_status` ENUM('pending', 'paid', 'failed', 'expired', 'cancelled') NOT NULL DEFAULT 'pending',
  `snap_token` VARCHAR(255) NULL COMMENT 'Midtrans Snap Token',
  `transaction_id` VARCHAR(100) NULL COMMENT 'Midtrans Transaction ID',
  `paid_at` TIMESTAMP NULL,
  
  -- Order Status
  `status` ENUM('pending', 'confirmed', 'packing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
  
  -- Address Snapshot (in case user deletes address later)
  `shipping_name` VARCHAR(255) NOT NULL,
  `shipping_phone` VARCHAR(20) NOT NULL,
  `shipping_address` TEXT NOT NULL,
  `shipping_city` VARCHAR(100) NOT NULL,
  `shipping_postal_code` VARCHAR(10) NOT NULL,
  
  -- Tracking
  `courier` VARCHAR(50) NULL COMMENT 'JNE, TIKI, POS, etc - for future use',
  `tracking_number` VARCHAR(100) NULL,
  `notes` TEXT NULL,
  
  -- Timestamps
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  -- Foreign Keys
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`address_id`) REFERENCES `addresses`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`voucher_id`) REFERENCES `vouchers`(`id`) ON DELETE SET NULL,
  
  -- Indexes
  INDEX `idx_user_orders` (`user_id`, `created_at`),
  INDEX `idx_order_status` (`status`),
  INDEX `idx_payment_status` (`payment_status`),
  INDEX `idx_order_number` (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order Items Table
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  
  -- Product Snapshot (in case product changes/deleted)
  `product_name` VARCHAR(255) NOT NULL,
  `product_image` VARCHAR(500) NOT NULL,
  `product_price` DECIMAL(12,2) NOT NULL,
  
  -- Order Details
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  `price` DECIMAL(12,2) NOT NULL COMMENT 'Price at time of order',
  `subtotal` DECIMAL(12,2) NOT NULL,
  
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  -- Foreign Keys
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT,
  
  -- Indexes
  INDEX `idx_order_items` (`order_id`),
  INDEX `idx_product_orders` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Sample Data for Testing
-- ============================================

-- Note: Run this after you have created some test orders
-- Orders will be created through the application flow

-- ============================================
-- Useful Queries
-- ============================================

-- Get user order history with items
-- SELECT o.*, oi.* FROM orders o
-- JOIN order_items oi ON o.id = oi.order_id
-- WHERE o.user_id = 1
-- ORDER BY o.created_at DESC;

-- Get pending payments
-- SELECT * FROM orders 
-- WHERE payment_status = 'pending' 
-- AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR);

-- Get orders by status
-- SELECT * FROM orders WHERE status = 'packing';

-- Revenue report
-- SELECT DATE(created_at) as date, 
--        COUNT(*) as orders, 
--        SUM(total) as revenue
-- FROM orders 
-- WHERE payment_status = 'paid'
-- GROUP BY DATE(created_at)
-- ORDER BY date DESC;
