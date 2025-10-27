-- ============================================
-- Create Test Admin User for GoRefill
-- ============================================
-- 
-- This script creates a test admin user for testing
-- the admin panel and product CRUD operations.
--
-- Admin Credentials:
-- Email: admin@gorefill.com
-- Password: password
--
-- Usage:
-- 1. Open phpMyAdmin
-- 2. Select 'gorefill' database
-- 3. Go to SQL tab
-- 4. Paste and execute this script
-- ============================================

-- Insert admin user
-- Password is hashed using bcrypt: password_hash('password', PASSWORD_DEFAULT)
INSERT INTO users (name, email, password, phone, role, created_at) 
VALUES (
    'Admin GoRefill',
    'admin@gorefill.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    '08123456789',
    'admin',
    NOW()
);

-- Insert test user (optional)
INSERT INTO users (name, email, password, phone, role, created_at) 
VALUES (
    'Test User',
    'user@gorefill.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    '08987654321',
    'user',
    NOW()
);

-- Insert sample products (optional)
INSERT INTO products (name, description, price, stock, category, created_at) VALUES
('Galon Air Minum 19L', 'Air minum berkualitas tinggi, higienis dan segar', 25000, 50, 'Air Minum', NOW()),
('Galon Air Mineral 19L', 'Air mineral asli dari pegunungan', 30000, 30, 'Air Minum', NOW()),
('Gas LPG 3kg', 'Gas LPG isi ulang 3kg', 20000, 40, 'Gas LPG', NOW()),
('Gas LPG 12kg', 'Gas LPG isi ulang 12kg', 130000, 25, 'Gas LPG', NOW()),
('Sabun Cuci Piring 1L', 'Sabun cuci piring isi ulang 1 liter', 15000, 60, 'Rumah Tangga', NOW()),
('Sabun Cuci Baju 1L', 'Sabun cuci baju konsentrat isi ulang', 18000, 45, 'Rumah Tangga', NOW());

SELECT 'Admin user created successfully!' as status;
SELECT 'Login credentials:' as info;
SELECT 'Email: admin@gorefill.com' as credential;
SELECT 'Password: password' as credential;
