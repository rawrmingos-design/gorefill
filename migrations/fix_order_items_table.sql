-- ============================================
-- Fix Order Items Table - Add Product Snapshot
-- ============================================
-- Kenapa perlu product snapshot?
-- 1. Jika product dihapus/diubah, order history tetap valid
-- 2. Customer bisa lihat apa yang mereka beli (nama, gambar, harga saat beli)
-- 3. Admin bisa lihat detail order tanpa join ke products table
-- 4. Harga produk bisa berubah, tapi harga di order harus tetap
-- ============================================

USE gorefill;

-- Tambah kolom product snapshot
ALTER TABLE `order_items` 
ADD COLUMN `product_name` VARCHAR(255) NOT NULL AFTER `product_id`,
ADD COLUMN `product_image` VARCHAR(500) NOT NULL AFTER `product_name`,
ADD COLUMN `product_price` DECIMAL(12,2) NOT NULL COMMENT 'Harga asli produk saat dibeli' AFTER `product_image`;

-- Tambah kolom subtotal untuk efisiensi
ALTER TABLE `order_items`
ADD COLUMN `subtotal` DECIMAL(12,2) NOT NULL COMMENT 'qty * price' AFTER `price`;

-- Tambah kolom quantity (rename qty ke quantity untuk konsistensi)
ALTER TABLE `order_items`
CHANGE COLUMN `qty` `quantity` INT UNSIGNED NOT NULL DEFAULT 1;

-- Tambah created_at untuk tracking
ALTER TABLE `order_items`
ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `subtotal`;

-- Update constraint untuk quantity
ALTER TABLE `order_items`
MODIFY COLUMN `quantity` INT UNSIGNED NOT NULL DEFAULT 1;

-- Tambah index untuk performa
ALTER TABLE `order_items`
ADD INDEX `idx_order_items` (`order_id`),
ADD INDEX `idx_product_orders` (`product_id`);

SELECT '✅ Order Items table updated successfully!' AS status;
SELECT 'New structure: id, order_id, product_id, product_name, product_image, product_price, quantity, price, subtotal, created_at' AS columns;

-- Lihat struktur baru
DESCRIBE order_items;
