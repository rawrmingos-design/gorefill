-- ============================================
-- Update Orders Table for Midtrans Best Practices
-- Menambahkan kolom-kolom yang diperlukan sesuai dokumentasi Midtrans
-- ============================================

USE gorefill;

-- 1. Tambah kolom untuk Midtrans response data
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
ADD COLUMN `payment_code` VARCHAR(50) NULL COMMENT 'Payment code untuk convenience store' AFTER `store`;

-- 2. Tambah index untuk performa query
ALTER TABLE `orders`
ADD INDEX `idx_transaction_id` (`transaction_id`),
ADD INDEX `idx_transaction_status` (`transaction_status`),
ADD INDEX `idx_expiry_time` (`expiry_time`);

-- 3. Update payment_status enum untuk menambahkan status 'refund'
ALTER TABLE `orders`
MODIFY COLUMN `payment_status` ENUM('pending', 'paid', 'failed', 'expired', 'cancelled', 'refund') NOT NULL DEFAULT 'pending';

-- 4. Tambah kolom untuk customer info (opsional, untuk analytics)
ALTER TABLE `orders`
ADD COLUMN `customer_email` VARCHAR(255) NULL COMMENT 'Email customer dari session' AFTER `user_id`,
ADD COLUMN `customer_phone` VARCHAR(20) NULL COMMENT 'Phone customer dari session' AFTER `customer_email`;

-- 5. Tambah kolom untuk refund tracking
ALTER TABLE `orders`
ADD COLUMN `refund_amount` DECIMAL(12,2) NULL DEFAULT 0.00 COMMENT 'Jumlah yang di-refund' AFTER `total`,
ADD COLUMN `refund_reason` TEXT NULL COMMENT 'Alasan refund' AFTER `refund_amount`,
ADD COLUMN `refunded_at` TIMESTAMP NULL COMMENT 'Waktu refund' AFTER `refund_reason`;

-- ============================================
-- Penjelasan Kolom Baru:
-- ============================================

-- fraud_status: Status fraud detection dari Midtrans (accept/challenge/deny)
-- transaction_status: Status transaksi real-time dari Midtrans
-- transaction_time: Kapan transaksi dibuat di Midtrans
-- settlement_time: Kapan pembayaran di-settle
-- gross_amount: Total yang dikirim ke Midtrans (untuk validasi)
-- signature_key: Untuk validasi webhook dari Midtrans
-- bank: Nama bank untuk VA atau credit card
-- va_number: Nomor VA jika pakai Virtual Account
-- bill_key & biller_code: Untuk Mandiri Bill Payment
-- pdf_url: Link PDF invoice dari Midtrans
-- expiry_time: Kapan pembayaran akan expire
-- payment_type: Tipe pembayaran yang dipilih user
-- store: Nama store untuk Alfamart/Indomaret
-- payment_code: Kode pembayaran untuk convenience store
-- customer_email & customer_phone: Info customer untuk reference
-- refund_*: Tracking untuk refund

-- ============================================
-- Verifikasi Perubahan:
-- ============================================

-- Lihat struktur table yang sudah diupdate
DESCRIBE orders;

-- Lihat semua kolom
SHOW COLUMNS FROM orders;

-- ============================================
-- NOTES:
-- ============================================
-- 1. Kolom-kolom ini akan diisi otomatis dari Midtrans callback
-- 2. Tidak semua kolom akan terisi untuk setiap transaksi (tergantung payment method)
-- 3. Kolom signature_key penting untuk validasi webhook
-- 4. Kolom expiry_time berguna untuk auto-cancel expired orders
