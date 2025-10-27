-- Add min_purchase column to vouchers table
ALTER TABLE `vouchers` 
ADD COLUMN `min_purchase` DECIMAL(12,2) DEFAULT 0 AFTER `discount_percent`;

-- Update existing vouchers with default min_purchase
UPDATE `vouchers` SET `min_purchase` = 0 WHERE `min_purchase` IS NULL;
