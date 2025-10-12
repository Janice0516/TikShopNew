#!/bin/bash

# 快速连接Render数据库并创建表和数据
echo "🚀 快速设置Render数据库..."

# 数据库连接信息
DB_HOST="dpg-d0j8q8h2s78s73fq8hpg-a.oregon-postgres.render.com"
DB_PORT="5432"
DB_USER="tiktokshop_slkz_user"
DB_NAME="tiktokshop_slkz"
DB_PASSWORD="U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn"

# 设置环境变量
export PGPASSWORD="$DB_PASSWORD"

echo "🔌 连接数据库..."

# 创建SQL脚本
cat > temp_setup.sql << 'EOF'
-- 创建分类表
CREATE TABLE IF NOT EXISTS category (
    id BIGSERIAL PRIMARY KEY,
    parent_id BIGINT DEFAULT 0,
    name VARCHAR(50) NOT NULL,
    level SMALLINT DEFAULT 1,
    sort INTEGER DEFAULT 0,
    icon VARCHAR(255),
    status SMALLINT DEFAULT 1,
    create_time TIMESTAMP DEFAULT NOW(),
    update_time TIMESTAMP DEFAULT NOW()
);

-- 创建商品表
CREATE TABLE IF NOT EXISTS platform_product (
    id BIGSERIAL PRIMARY KEY,
    product_no VARCHAR(50) UNIQUE,
    name VARCHAR(200) NOT NULL,
    category_id BIGINT NOT NULL,
    brand VARCHAR(100),
    main_image VARCHAR(255) NOT NULL,
    images TEXT,
    video VARCHAR(255),
    cost_price DECIMAL(10,2) NOT NULL,
    suggest_price DECIMAL(10,2),
    stock INTEGER DEFAULT 0,
    sales INTEGER DEFAULT 0,
    description TEXT,
    status SMALLINT DEFAULT 1,
    sort INTEGER DEFAULT 0,
    create_time TIMESTAMP DEFAULT NOW(),
    update_time TIMESTAMP DEFAULT NOW()
);

-- 插入分类数据
INSERT INTO category (name, parent_id, level, sort, status) VALUES
('Electronics', 0, 1, 1, 1),
('Fashion', 0, 1, 2, 1),
('Home & Garden', 0, 1, 3, 1),
('Sports & Outdoors', 0, 1, 4, 1),
('Beauty & Health', 0, 1, 5, 1),
('Books & Media', 0, 1, 6, 1),
('Toys & Games', 0, 1, 7, 1),
('Automotive', 0, 1, 8, 1),
('Smartphones', 1, 2, 1, 1),
('Laptops', 1, 2, 2, 1),
('Audio', 1, 2, 3, 1),
('Cameras', 1, 2, 4, 1),
('Men''s Clothing', 2, 2, 1, 1),
('Women''s Clothing', 2, 2, 2, 1),
('Shoes', 2, 2, 3, 1),
('Accessories', 2, 2, 4, 1),
('Furniture', 3, 2, 1, 1),
('Kitchen & Dining', 3, 2, 2, 1),
('Garden Tools', 3, 2, 3, 1),
('Home Decor', 3, 2, 4, 1)
ON CONFLICT DO NOTHING;

-- 插入商品数据（32个真实商品）
INSERT INTO platform_product (name, category_id, brand, main_image, images, cost_price, suggest_price, stock, sales, description, status) VALUES
('iPhone 15 Pro Max 256GB', 9, 'Apple', '/static/products/iphone15pro.jpg', '["/static/products/iphone15pro.jpg"]', 4500.00, 4999.00, 25, 156, 'Latest iPhone with titanium design, A17 Pro chip, and advanced camera system', 1),
('MacBook Pro M3 14-inch', 10, 'Apple', '/static/products/macbook-m3.jpg', '["/static/products/macbook-m3.jpg"]', 7500.00, 7999.00, 15, 89, 'Powerful laptop with M3 chip, perfect for professionals and creators', 1),
('AirPods Pro 2nd Gen', 11, 'Apple', '/static/products/airpods-pro2.jpg', '["/static/products/airpods-pro2.jpg"]', 800.00, 899.00, 50, 445, 'Wireless earbuds with active noise cancellation and spatial audio', 1),
('Samsung Galaxy S24 Ultra', 9, 'Samsung', '/static/products/galaxy-s24.jpg', '["/static/products/galaxy-s24.jpg"]', 4000.00, 4299.00, 20, 234, 'Premium Android smartphone with S Pen and advanced AI features', 1),
('Nike Air Max 270', 15, 'Nike', '/static/products/nike-airmax.jpg', '["/static/products/nike-airmax.jpg"]', 350.00, 399.00, 100, 1200, 'Comfortable running shoes with Max Air cushioning', 1),
('Adidas Ultraboost 22', 15, 'Adidas', '/static/products/adidas-ultraboost.jpg', '["/static/products/adidas-ultraboost.jpg"]', 550.00, 599.00, 80, 890, 'High-performance running shoes with Boost midsole', 1),
('Uniqlo Heattech Long Sleeve', 13, 'Uniqlo', '/static/products/uniqlo-heattech.jpg', '["/static/products/uniqlo-heattech.jpg"]', 40.00, 49.90, 200, 2100, 'Thermal base layer for cold weather', 1),
('Zara Denim Jacket', 13, 'Zara', '/static/products/zara-denim.jpg', '["/static/products/zara-denim.jpg"]', 180.00, 199.00, 60, 567, 'Classic denim jacket with modern fit', 1),
('IKEA MALM Bed Frame', 17, 'IKEA', '/static/products/ikea-malm.jpg', '["/static/products/ikea-malm.jpg"]', 800.00, 899.00, 30, 234, 'Minimalist bed frame with storage drawers', 1),
('KitchenAid Stand Mixer', 18, 'KitchenAid', '/static/products/kitchenaid-mixer.jpg', '["/static/products/kitchenaid-mixer.jpg"]', 1200.00, 1299.00, 15, 89, 'Professional stand mixer for baking enthusiasts', 1),
('Philips Air Fryer XXL', 18, 'Philips', '/static/products/philips-airfryer.jpg', '["/static/products/philips-airfryer.jpg"]', 350.00, 399.00, 40, 456, 'Large capacity air fryer for healthy cooking', 1),
('Dyson V15 Detect Vacuum', 17, 'Dyson', '/static/products/dyson-v15.jpg', '["/static/products/dyson-v15.jpg"]', 1800.00, 1999.00, 20, 123, 'Cordless vacuum with laser dust detection', 1),
('Wilson Pro Staff Tennis Racket', 4, 'Wilson', '/static/products/wilson-prostaff.jpg', '["/static/products/wilson-prostaff.jpg"]', 800.00, 899.00, 25, 78, 'Professional tennis racket for advanced players', 1),
('Nike Dri-FIT Training Shorts', 4, 'Nike', '/static/products/nike-shorts.jpg', '["/static/products/nike-shorts.jpg"]', 80.00, 89.00, 150, 890, 'Moisture-wicking training shorts for workouts', 1),
('Garmin Forerunner 255', 4, 'Garmin', '/static/products/garmin-255.jpg', '["/static/products/garmin-255.jpg"]', 1200.00, 1299.00, 35, 234, 'GPS running watch with advanced training metrics', 1),
('Yoga Mat Premium', 4, 'Generic', '/static/products/yoga-mat.jpg', '["/static/products/yoga-mat.jpg"]', 70.00, 79.00, 80, 567, 'Non-slip yoga mat with carrying strap', 1),
('SK-II Facial Treatment Essence', 5, 'SK-II', '/static/products/sk2-essence.jpg', '["/static/products/sk2-essence.jpg"]', 800.00, 899.00, 40, 345, 'Premium skincare essence for radiant skin', 1),
('MAC Lipstick Ruby Woo', 5, 'MAC', '/static/products/mac-rubywoo.jpg', '["/static/products/mac-rubywoo.jpg"]', 80.00, 89.00, 100, 1200, 'Classic red lipstick with matte finish', 1),
('La Mer The Moisturizing Cream', 5, 'La Mer', '/static/products/lamer-cream.jpg', '["/static/products/lamer-cream.jpg"]', 1200.00, 1299.00, 20, 89, 'Luxury moisturizing cream for all skin types', 1),
('Dyson Supersonic Hair Dryer', 5, 'Dyson', '/static/products/dyson-hairdryer.jpg', '["/static/products/dyson-hairdryer.jpg"]', 1200.00, 1299.00, 25, 156, 'Professional hair dryer with intelligent heat control', 1),
('Atomic Habits by James Clear', 6, 'Random House', '/static/products/atomic-habits.jpg', '["/static/products/atomic-habits.jpg"]', 40.00, 49.90, 200, 2100, 'Bestselling book on building good habits and breaking bad ones', 1),
('The Psychology of Money', 6, 'Harriman House', '/static/products/psychology-money.jpg', '["/static/products/psychology-money.jpg"]', 50.00, 59.90, 150, 1456, 'Timeless lessons on wealth, greed, and happiness', 1),
('Malaysian Cookbook', 6, 'Local Publisher', '/static/products/malaysian-cookbook.jpg', '["/static/products/malaysian-cookbook.jpg"]', 80.00, 89.90, 80, 678, 'Authentic Malaysian recipes and cooking techniques', 1),
('Harry Potter Complete Set', 6, 'Bloomsbury', '/static/products/harry-potter-set.jpg', '["/static/products/harry-potter-set.jpg"]', 280.00, 299.90, 50, 234, 'All 7 books in the Harry Potter series', 1),
('LEGO Creator Expert Modular Building', 7, 'LEGO', '/static/products/lego-modular.jpg', '["/static/products/lego-modular.jpg"]', 800.00, 899.00, 30, 123, 'Detailed modular building set for adults', 1),
('Barbie Dreamhouse', 7, 'Mattel', '/static/products/barbie-dreamhouse.jpg', '["/static/products/barbie-dreamhouse.jpg"]', 280.00, 299.00, 40, 456, '3-story dollhouse with furniture and accessories', 1),
('Nintendo Switch OLED', 7, 'Nintendo', '/static/products/nintendo-switch.jpg', '["/static/products/nintendo-switch.jpg"]', 1200.00, 1299.00, 25, 234, 'Gaming console with OLED screen and Joy-Con controllers', 1),
('Hot Wheels Track Set', 7, 'Mattel', '/static/products/hotwheels-track.jpg', '["/static/products/hotwheels-track.jpg"]', 180.00, 199.00, 60, 567, 'Racing track set with multiple cars and loops', 1),
('Michelin Pilot Sport 4', 8, 'Michelin', '/static/products/michelin-pilot.jpg', '["/static/products/michelin-pilot.jpg"]', 800.00, 899.00, 20, 89, 'High-performance summer tires for sports cars', 1),
('Bosch Icon Wiper Blades', 8, 'Bosch', '/static/products/bosch-wipers.jpg', '["/static/products/bosch-wipers.jpg"]', 80.00, 89.00, 100, 456, 'Premium windshield wiper blades for all weather', 1),
('K&N Air Filter', 8, 'K&N', '/static/products/kn-airfilter.jpg', '["/static/products/kn-airfilter.jpg"]', 180.00, 199.00, 50, 234, 'High-flow air filter for improved engine performance', 1),
('Mobil 1 Engine Oil 5W-30', 8, 'Mobil', '/static/products/mobil1-oil.jpg', '["/static/products/mobil1-oil.jpg"]', 80.00, 89.00, 80, 678, 'Full synthetic engine oil for all vehicles', 1)
ON CONFLICT DO NOTHING;

-- 检查结果
SELECT 'Categories created:' as info, COUNT(*) as count FROM category;
SELECT 'Products created:' as info, COUNT(*) as count FROM platform_product;
EOF

# 执行SQL脚本
echo "📊 执行数据库设置..."
psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -f temp_setup.sql

# 检查结果
echo "✅ 检查结果..."
psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -c "
SELECT 'Categories:' as info, COUNT(*) as count FROM category;
SELECT 'Products:' as info, COUNT(*) as count FROM platform_product;
"

# 清理临时文件
rm temp_setup.sql

echo "🎉 数据库设置完成！"
echo "💡 现在可以测试API是否正常工作了"