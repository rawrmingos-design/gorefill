-- ============================================
-- Fix Orders Table - Compatible Version
-- ============================================

USE gorefill;

-- Tambah kolom baru (akan skip jika sudah ada)
ALTER TABLE `orders` ADD COLUMN `fraud_status` VARCHAR(20) NULL COMMENT 'accept, challenge, deny' AFTER `transaction_id`;
ALTER TABLE `orders` ADD COLUMN `transaction_status` VARCHAR(50) NULL COMMENT 'capture, settlement, pending, deny, cancel, expire, refund' AFTER `fraud_status`;
ALTER TABLE `orders` ADD COLUMN `transaction_time` TIMESTAMP NULL COMMENT 'Waktu transaksi dari Midtrans' AFTER `transaction_status`;
ALTER TABLE `orders` ADD COLUMN `settlement_time` TIMESTAMP NULL COMMENT 'Waktu settlement dari Midtrans' AFTER `transaction_time`;
ALTER TABLE `orders` ADD COLUMN `gross_amount` DECIMAL(12,2) NULL COMMENT 'Total amount yang dikirim ke Midtrans' AFTER `settlement_time`;
ALTER TABLE `orders` ADD COLUMN `currency` VARCHAR(3) DEFAULT 'IDR' COMMENT 'Currency code' AFTER `gross_amount`;
ALTER TABLE `orders` ADD COLUMN `signature_key` VARCHAR(255) NULL COMMENT 'Signature key dari Midtrans untuk validasi' AFTER `currency`;
ALTER TABLE `orders` ADD COLUMN `bank` VARCHAR(50) NULL COMMENT 'Bank name untuk VA/credit card' AFTER `signature_key`;
ALTER TABLE `orders` ADD COLUMN `va_number` VARCHAR(50) NULL COMMENT 'Virtual Account number jika pakai VA' AFTER `bank`;
ALTER TABLE `orders` ADD COLUMN `bill_key` VARCHAR(50) NULL COMMENT 'Bill key untuk Mandiri Bill' AFTER `va_number`;
ALTER TABLE `orders` ADD COLUMN `biller_code` VARCHAR(50) NULL COMMENT 'Biller code untuk Mandiri Bill' AFTER `bill_key`;
ALTER TABLE `orders` ADD COLUMN `pdf_url` VARCHAR(500) NULL COMMENT 'PDF URL untuk invoice' AFTER `biller_code`;
ALTER TABLE `orders` ADD COLUMN `finish_redirect_url` VARCHAR(500) NULL COMMENT 'URL redirect setelah pembayaran selesai' AFTER `pdf_url`;
ALTER TABLE `orders` ADD COLUMN `expiry_time` TIMESTAMP NULL COMMENT 'Waktu kadaluarsa pembayaran' AFTER `finish_redirect_url`;
ALTER TABLE `orders` ADD COLUMN `payment_type` VARCHAR(50) NULL COMMENT 'Tipe pembayaran: credit_card, bank_transfer, echannel, gopay, dll' AFTER `expiry_time`;
ALTER TABLE `orders` ADD COLUMN `store` VARCHAR(50) NULL COMMENT 'Store name untuk convenience store (alfamart/indomaret)' AFTER `payment_type`;
ALTER TABLE `orders` ADD COLUMN `payment_code` VARCHAR(50) NULL COMMENT 'Payment code untuk convenience store' AFTER `store`;
ALTER TABLE `orders` ADD COLUMN `customer_email` VARCHAR(255) NULL COMMENT 'Email customer dari session' AFTER `user_id`;
ALTER TABLE `orders` ADD COLUMN `customer_phone` VARCHAR(20) NULL COMMENT 'Phone customer dari session' AFTER `customer_email`;
ALTER TABLE `orders` ADD COLUMN `refund_amount` DECIMAL(12,2) NULL DEFAULT 0.00 COMMENT 'Jumlah yang di-refund' AFTER `total`;
ALTER TABLE `orders` ADD COLUMN `refund_reason` TEXT NULL COMMENT 'Alasan refund' AFTER `refund_amount`;
ALTER TABLE `orders` ADD COLUMN `refunded_at` TIMESTAMP NULL COMMENT 'Waktu refund' AFTER `refund_reason`;

SELECT 'Orders table updated successfully!' AS status;
