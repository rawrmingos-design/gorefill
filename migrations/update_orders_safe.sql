-- ============================================
-- Update Orders Table - Safe Version (No FK)
-- ============================================

USE gorefill;

-- Tambah kolom yang masih kurang
-- Akan skip dengan error jika sudah ada (itu normal)

-- Core order fields
ALTER TABLE `orders` ADD COLUMN `order_number` VARCHAR(50) UNIQUE NULL COMMENT 'ORD-YYYYMMDD-XXXX';
ALTER TABLE `orders` ADD COLUMN `address_id` INT UNSIGNED NULL;
ALTER TABLE `orders` ADD COLUMN `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00;
ALTER TABLE `orders` ADD COLUMN `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00;
ALTER TABLE `orders` ADD COLUMN `snap_token` VARCHAR(255) NULL;
ALTER TABLE `orders` ADD COLUMN `paid_at` TIMESTAMP NULL;

-- Address snapshot
ALTER TABLE `orders` ADD COLUMN `shipping_name` VARCHAR(255) NULL;
ALTER TABLE `orders` ADD COLUMN `shipping_phone` VARCHAR(20) NULL;
ALTER TABLE `orders` ADD COLUMN `shipping_address` TEXT NULL;
ALTER TABLE `orders` ADD COLUMN `shipping_city` VARCHAR(100) NULL;
ALTER TABLE `orders` ADD COLUMN `shipping_postal_code` VARCHAR(10) NULL;

-- Tracking
ALTER TABLE `orders` ADD COLUMN `courier` VARCHAR(50) NULL;
ALTER TABLE `orders` ADD COLUMN `tracking_number` VARCHAR(100) NULL;

-- Midtrans details
ALTER TABLE `orders` ADD COLUMN `transaction_status` VARCHAR(50) NULL;
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

-- Customer info
ALTER TABLE `orders` ADD COLUMN `customer_email` VARCHAR(255) NULL;
ALTER TABLE `orders` ADD COLUMN `customer_phone` VARCHAR(20) NULL;

-- Refund
ALTER TABLE `orders` ADD COLUMN `refund_amount` DECIMAL(12,2) NULL DEFAULT 0.00;
ALTER TABLE `orders` ADD COLUMN `refund_reason` TEXT NULL;
ALTER TABLE `orders` ADD COLUMN `refunded_at` TIMESTAMP NULL;

-- Timestamps
ALTER TABLE `orders` ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Rename note to notes jika ada
ALTER TABLE `orders` CHANGE COLUMN `note` `notes` TEXT NULL;

SELECT '✅ Orders table update completed!' AS status;
