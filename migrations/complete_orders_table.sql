-- ============================================
-- Complete Orders Table untuk Midtrans Integration
-- Menyesuaikan dengan struktur yang sudah ada
-- ============================================

USE gorefill;

-- 1. Tambah kolom yang masih kurang (skip jika error = sudah ada)
ALTER TABLE `orders` ADD COLUMN `order_number` VARCHAR(50) UNIQUE NULL COMMENT 'Format: ORD-YYYYMMDD-XXXX';
ALTER TABLE `orders` ADD COLUMN `address_id` INT UNSIGNED NULL;
ALTER TABLE `orders` ADD COLUMN `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00;
ALTER TABLE `orders` ADD COLUMN `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00;
ALTER TABLE `orders` ADD COLUMN `snap_token` VARCHAR(255) NULL COMMENT 'Midtrans Snap Token';
ALTER TABLE `orders` ADD COLUMN `paid_at` TIMESTAMP NULL;

-- 2. Tambah kolom address snapshot
ALTER TABLE `orders` ADD COLUMN `shipping_name` VARCHAR(255) NULL;
ALTER TABLE `orders` ADD COLUMN `shipping_phone` VARCHAR(20) NULL;
ALTER TABLE `orders` ADD COLUMN `shipping_address` TEXT NULL;
ALTER TABLE `orders` ADD COLUMN `shipping_city` VARCHAR(100) NULL;
ALTER TABLE `orders` ADD COLUMN `shipping_postal_code` VARCHAR(10) NULL;

-- 3. Tambah kolom tracking
ALTER TABLE `orders` ADD COLUMN `courier` VARCHAR(50) NULL COMMENT 'JNE, TIKI, POS, etc';
ALTER TABLE `orders` ADD COLUMN `tracking_number` VARCHAR(100) NULL;

-- 4. Tambah kolom Midtrans detail
ALTER TABLE `orders` ADD COLUMN `transaction_status` VARCHAR(50) NULL COMMENT 'capture, settlement, pending, deny, cancel, expire';
ALTER TABLE `orders` ADD COLUMN `transaction_time` TIMESTAMP NULL;
ALTER TABLE `orders` ADD COLUMN `settlement_time` TIMESTAMP NULL;
ALTER TABLE `orders` ADD COLUMN `gross_amount` DECIMAL(12,2) NULL;
ALTER TABLE `orders` ADD COLUMN `currency` VARCHAR(3) DEFAULT 'IDR';
ALTER TABLE `orders` ADD COLUMN `signature_key` VARCHAR(255) NULL;
ALTER TABLE `orders` ADD COLUMN `bank` VARCHAR(50) NULL;
ALTER TABLE `orders` ADD COLUMN `va_number` VARCHAR(50) NULL;
ALTER TABLE `orders` ADD COLUMN `bill_key` VARCHAR(50) NULL;
ALTER TABLE `orders` ADD COLUMN `biller_code` VARCHAR(50) NULL;
ALTER TABLE `orders` ADD COLUMN `pdf_url` VARCHAR(500) NULL;
ALTER TABLE `orders` ADD COLUMN `finish_redirect_url` VARCHAR(500) NULL;
ALTER TABLE `orders` ADD COLUMN `expiry_time` TIMESTAMP NULL;
ALTER TABLE `orders` ADD COLUMN `store` VARCHAR(50) NULL;
ALTER TABLE `orders` ADD COLUMN `payment_code` VARCHAR(50) NULL;

-- 5. Tambah kolom customer info
ALTER TABLE `orders` ADD COLUMN `customer_email` VARCHAR(255) NULL;
ALTER TABLE `orders` ADD COLUMN `customer_phone` VARCHAR(20) NULL;

-- 6. Tambah kolom refund
ALTER TABLE `orders` ADD COLUMN `refund_amount` DECIMAL(12,2) NULL DEFAULT 0.00;
ALTER TABLE `orders` ADD COLUMN `refund_reason` TEXT NULL;
ALTER TABLE `orders` ADD COLUMN `refunded_at` TIMESTAMP NULL;

-- 7. Tambah updated_at jika belum ada
ALTER TABLE `orders` ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 8. Update payment_status enum untuk include semua status
ALTER TABLE `orders` MODIFY COLUMN `payment_status` ENUM('pending', 'unpaid', 'paid', 'failed', 'expired', 'cancelled', 'refund') DEFAULT 'pending';

-- 9. Update order status enum
ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('pending', 'confirmed', 'packing', 'shipped', 'delivered', 'cancelled', 'expired') DEFAULT 'pending';

-- 10. Tambah foreign key jika belum ada (akan error jika sudah ada, tapi gpp)
ALTER TABLE `orders` ADD CONSTRAINT `fk_orders_address` FOREIGN KEY (`address_id`) REFERENCES `addresses`(`id`) ON DELETE RESTRICT;
ALTER TABLE `orders` ADD CONSTRAINT `fk_orders_voucher` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers`(`id`) ON DELETE SET NULL;

-- 11. Tambah index untuk performa
ALTER TABLE `orders` ADD INDEX `idx_order_number` (`order_number`);
ALTER TABLE `orders` ADD INDEX `idx_user_orders` (`user_id`, `created_at`);
ALTER TABLE `orders` ADD INDEX `idx_order_status` (`status`);
ALTER TABLE `orders` ADD INDEX `idx_transaction_status` (`transaction_status`);
ALTER TABLE `orders` ADD INDEX `idx_expiry_time` (`expiry_time`);

SELECT 'Orders table structure completed!' AS status;
SELECT COUNT(*) as total_columns FROM information_schema.columns WHERE table_schema = 'gorefill' AND table_name = 'orders';
