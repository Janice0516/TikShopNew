-- 插入真实数据脚本

-- 插入商品分类
INSERT INTO category (name, description, parent_id, sort_order, status, created_at, updated_at) VALUES
('Electronics', 'Electronic devices and gadgets', NULL, 1, 1, NOW(), NOW()),
('Fashion', 'Clothing and accessories', NULL, 2, 1, NOW(), NOW()),
('Home & Garden', 'Home improvement and garden supplies', NULL, 3, 1, NOW(), NOW()),
('Sports & Outdoors', 'Sports equipment and outdoor gear', NULL, 4, 1, NOW(), NOW()),
('Beauty & Health', 'Beauty products and health supplements', NULL, 5, 1, NOW(), NOW()),
('Books & Media', 'Books, movies, and music', NULL, 6, 1, NOW(), NOW()),
('Toys & Games', 'Toys and gaming products', NULL, 7, 1, NOW(), NOW()),
('Automotive', 'Car parts and accessories', NULL, 8, 1, NOW(), NOW());

-- 插入子分类
INSERT INTO category (name, description, parent_id, sort_order, status, created_at, updated_at) VALUES
-- Electronics subcategories
('Smartphones', 'Mobile phones and accessories', 1, 1, 1, NOW(), NOW()),
('Laptops', 'Laptop computers and accessories', 1, 2, 1, NOW(), NOW()),
('Audio', 'Headphones, speakers, and audio equipment', 1, 3, 1, NOW(), NOW()),
('Cameras', 'Digital cameras and photography equipment', 1, 4, 1, NOW(), NOW()),

-- Fashion subcategories
('Men\'s Clothing', 'Men\'s apparel and accessories', 2, 1, 1, NOW(), NOW()),
('Women\'s Clothing', 'Women\'s apparel and accessories', 2, 2, 1, NOW(), NOW()),
('Shoes', 'Footwear for men and women', 2, 3, 1, NOW(), NOW()),
('Accessories', 'Fashion accessories and jewelry', 2, 4, 1, NOW(), NOW()),

-- Home & Garden subcategories
('Furniture', 'Home and office furniture', 3, 1, 1, NOW(), NOW()),
('Kitchen & Dining', 'Kitchen appliances and dining accessories', 3, 2, 1, NOW(), NOW()),
('Garden Tools', 'Gardening equipment and tools', 3, 3, 1, NOW(), NOW()),
('Home Decor', 'Decorative items and home accessories', 3, 4, 1, NOW(), NOW());

-- 插入管理员账户
INSERT INTO admin (username, password, email, role, status, created_at, updated_at) VALUES
('admin', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@tiktokshop.com', 'super_admin', 1, NOW(), NOW()),
('manager', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager@tiktokshop.com', 'admin', 1, NOW(), NOW());

-- 插入真实商家数据
INSERT INTO merchant (username, password, email, phone, merchant_name, merchant_uid, contact_name, business_license, status, balance, frozen_amount, total_income, total_withdraw, created_at, updated_at) VALUES
('techstore_malaysia', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'contact@techstore.com.my', '+60123456789', 'TechStore Malaysia', 'TS001', 'Ahmad Rahman', 'BL2024001', 1, 5000.00, 0.00, 15000.00, 10000.00, NOW(), NOW()),
('fashion_hub_kl', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'info@fashionhub.com.my', '+60198765432', 'Fashion Hub KL', 'FH002', 'Siti Nurhaliza', 'BL2024002', 1, 3200.50, 500.00, 8500.00, 5300.00, NOW(), NOW()),
('home_depot_my', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sales@homedepot.com.my', '+60134567890', 'Home Depot Malaysia', 'HD003', 'Lim Wei Ming', 'BL2024003', 1, 7500.00, 0.00, 22000.00, 14500.00, NOW(), NOW()),
('sports_zone', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'orders@sportszone.com.my', '+60145678901', 'Sports Zone', 'SZ004', 'Raj Kumar', 'BL2024004', 1, 2800.00, 200.00, 6800.00, 4000.00, NOW(), NOW()),
('beauty_paradise', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hello@beautyparadise.com.my', '+60156789012', 'Beauty Paradise', 'BP005', 'Nurul Aisyah', 'BL2024005', 1, 4100.75, 0.00, 12000.00, 7900.00, NOW(), NOW()),
('book_world', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'support@bookworld.com.my', '+60167890123', 'Book World Malaysia', 'BW006', 'Tan Mei Ling', 'BL2024006', 1, 1500.00, 0.00, 4500.00, 3000.00, NOW(), NOW()),
('toy_kingdom', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'info@toykingdom.com.my', '+60178901234', 'Toy Kingdom', 'TK007', 'Muhammad Ali', 'BL2024007', 1, 2200.00, 100.00, 5500.00, 3300.00, NOW(), NOW()),
('auto_parts_pro', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sales@autopartspro.com.my', '+60189012345', 'Auto Parts Pro', 'AP008', 'David Chen', 'BL2024008', 1, 6800.00, 0.00, 18000.00, 11200.00, NOW(), NOW());

-- 插入用户数据
INSERT INTO user (username, password, email, phone, status, created_at, updated_at) VALUES
('john_doe', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'john.doe@email.com', '+60123456788', 1, NOW(), NOW()),
('sarah_wong', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sarah.wong@email.com', '+60123456787', 1, NOW(), NOW()),
('ahmad_ali', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ahmad.ali@email.com', '+60123456786', 1, NOW(), NOW()),
('priya_sharma', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'priya.sharma@email.com', '+60123456785', 1, NOW(), NOW()),
('lim_wei_ming', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lim.weiming@email.com', '+60123456784', 1, NOW(), NOW());
