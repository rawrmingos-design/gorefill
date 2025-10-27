-- DEBUG SCRIPT: Check Order Data for Tracking
-- Run this to verify your order has all required data

-- Replace 'ORD-2025XXXX-XXXX' with your actual order number
SET @order_num = 'ORD-20251026-0003';

-- Check Order Data
SELECT 
    'ORDER DATA' as check_type,
    o.id,
    o.order_number,
    o.status,
    o.courier_id,
    o.shipping_latitude,
    o.shipping_longitude,
    o.shipping_name,
    o.shipping_address,
    CASE 
        WHEN o.status = 'shipped' THEN '✅'
        ELSE '❌ Status must be "shipped"'
    END as status_check,
    CASE 
        WHEN o.courier_id IS NOT NULL THEN '✅'
        ELSE '❌ No courier assigned'
    END as courier_check,
    CASE 
        WHEN o.shipping_latitude IS NOT NULL THEN '✅'
        ELSE '❌ No shipping latitude'
    END as lat_check,
    CASE 
        WHEN o.shipping_longitude IS NOT NULL THEN '✅'
        ELSE '❌ No shipping longitude'
    END as lng_check
FROM orders o
WHERE o.order_number = @order_num;

-- Check Courier Data
SELECT 
    'COURIER DATA' as check_type,
    u.id,
    u.name,
    u.email,
    u.phone,
    u.role,
    CASE 
        WHEN u.role = 'kurir' THEN '✅'
        ELSE '❌ Role must be "kurir"'
    END as role_check
FROM orders o
LEFT JOIN users u ON o.courier_id = u.id
WHERE o.order_number = @order_num;

-- Check Courier Location
SELECT 
    'COURIER LOCATION' as check_type,
    cl.id,
    cl.courier_id,
    cl.lat,
    cl.lng,
    cl.updated_at,
    CASE 
        WHEN cl.lat IS NOT NULL AND cl.lng IS NOT NULL THEN '✅'
        ELSE '❌ No courier location data'
    END as location_check,
    TIMESTAMPDIFF(MINUTE, cl.updated_at, NOW()) as minutes_ago
FROM orders o
LEFT JOIN courier_locations cl ON o.courier_id = cl.courier_id
WHERE o.order_number = @order_num;

-- If data is missing, here's how to fix it:
-- 
-- 1. Assign courier to order:
-- UPDATE orders SET courier_id = 5 WHERE order_number = @order_num;
--
-- 2. Set order status to shipped:
-- UPDATE orders SET status = 'shipped' WHERE order_number = @order_num;
--
-- 3. Add shipping coordinates (example: Semarang):
-- UPDATE orders 
-- SET shipping_latitude = -6.9667, 
--     shipping_longitude = 110.4167 
-- WHERE order_number = @order_num;
--
-- 4. Add courier location:
-- INSERT INTO courier_locations (courier_id, lat, lng, updated_at)
-- VALUES (5, -6.9700, 110.4200, NOW())
-- ON DUPLICATE KEY UPDATE 
--     lat = -6.9700, 
--     lng = 110.4200, 
--     updated_at = NOW();
