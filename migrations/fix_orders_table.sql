-- ============================================
-- Fix Orders Table - Safe Update
-- ============================================

USE gorefill;

-- Drop kolom yang mungkin sudah ada (safe)
ALTER TABLE `orders` DROP COLUMN IF EXISTS `fraud_status`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `transaction_status`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `transaction_time`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `settlement_time`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `gross_amount`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `currency`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `signature_key`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `bank`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `va_number`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `bill_key`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `biller_code`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `pdf_url`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `finish_redirect_url`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `expiry_time`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `payment_type`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `store`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `payment_code`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `customer_email`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `customer_phone`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `refund_amount`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `refund_reason`;
ALTER TABLE `orders` DROP COLUMN IF EXISTS `refunded_at`;

-- Tambah semua kolom baru
ALTER TABLE `orders`
ADD COLUMN `fraud_status` VARCHAR(20) NULL COMMENT 'accept, challenge, deny' AFTER `transaction_id`,
ADD COLUMN `transaction_status` VARCHAR(50) NULL COMMENT 'capture, settlement, pending, deny, cancel, expire, refund' AFTER `fraud_status`,
ADD COLUMN `transaction_time` TIMESTAMP NULL COMMENT 'Waktu transaksi dari Midtrans' AFTER `transaction_status`,
ADD COLUMN `settlement_time` TIMESTAMP NULL COMMENT 'Waktu settlement dari Midtrans' AFTER `transaction_time`,
ADD COLUMN `gross_amount` DECIMAL(12,2) NULL COMMENT 'Total amount yang dikirim ke Midtrans' AFTER `settlement_time`,
ADD COLUMN `currency` VARCHAR(3) DEFAULT 'IDR' COMMENT 'Currency code' AFTER `gross_amount`,
ADD COLUMN `signature_key` VARCHAR(255) NULL COMMENT 'Signature key dari Midtrans untuk validasi' AFTER `currency`,
ADD COLUMN `bank` VARCHAR(50) NULL COMMENT 'Bank name untuk VA/credit card' AFTER `signature_key`,
ADD COLUMN `va_number` VARCHAR(50) NULL COMMENT 'Virtual Account number jika pakai VA' AFTER `bank`,
ADD COLUMN `bill_key` VARCHAR(50) NULL COMMENT 'Bill key untuk Mandiri Bill' AFTER `va_number`,
ADD COLUMN `biller_code` VARCHAR(50) NULL COMMENT 'Biller code untuk Mandiri Bill' AFTER `bill_key`,
ADD COLUMN `pdf_url` VARCHAR(500) NULL COMMENT 'PDF URL untuk invoice' AFTER `biller_code`,
ADD COLUMN `finish_redirect_url` VARCHAR(500) NULL COMMENT 'URL redirect setelah pembayaran selesai' AFTER `pdf_url`,
ADD COLUMN `expiry_time` TIMESTAMP NULL COMMENT 'Waktu kadaluarsa pembayaran' AFTER `finish_redirect_url`,
ADD COLUMN `payment_type` VARCHAR(50) NULL COMMENT 'Tipe pembayaran: credit_card, bank_transfer, echannel, gopay, dll' AFTER `expiry_time`,
ADD COLUMN `store` VARCHAR(50) NULL COMMENT 'Store name untuk convenience store (alfamart/indomaret)' AFTER `payment_type`,
ADD COLUMN `payment_code` VARCHAR(50) NULL COMMENT 'Payment code untuk convenience store' AFTER `store`,
ADD COLUMN `customer_email` VARCHAR(255) NULL COMMENT 'Email customer dari session' AFTER `user_id`,
ADD COLUMN `customer_phone` VARCHAR(20) NULL COMMENT 'Phone customer dari session' AFTER `customer_email`,
ADD COLUMN `refund_amount` DECIMAL(12,2) NULL DEFAULT 0.00 COMMENT 'Jumlah yang di-refund' AFTER `total`,
ADD COLUMN `refund_reason` TEXT NULL COMMENT 'Alasan refund' AFTER `refund_amount`,
ADD COLUMN `refunded_at` TIMESTAMP NULL COMMENT 'Waktu refund' AFTER `refund_reason`;

-- Tambah index
ALTER TABLE `orders` ADD INDEX IF NOT EXISTS `idx_transaction_id` (`transaction_id`);
ALTER TABLE `orders` ADD INDEX IF NOT EXISTS `idx_transaction_status` (`transaction_status`);
ALTER TABLE `orders` ADD INDEX IF NOT EXISTS `idx_expiry_time` (`expiry_time`);

-- Update payment_status enum
ALTER TABLE `orders` MODIFY COLUMN `payment_status` ENUM('pending', 'paid', 'failed', 'expired', 'cancelled', 'refund') NOT NULL DEFAULT 'pending';

SELECT 'Orders table updated successfully!' AS status;
