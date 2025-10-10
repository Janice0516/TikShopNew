-- 插入真实商品数据脚本

-- 电子产品
INSERT INTO product (name, description, price, original_price, stock, category_id, merchant_id, images, specifications, status, sales_count, created_at, updated_at) VALUES
-- TechStore Malaysia 商品
('iPhone 15 Pro Max 256GB', 'Latest iPhone with titanium design, A17 Pro chip, and advanced camera system', 4999.00, 5499.00, 25, 9, 1, '["/static/products/iphone15pro.jpg"]', '{"storage":"256GB","color":"Natural Titanium","screen":"6.7 inch","camera":"48MP"}', 1, 156, NOW(), NOW()),
('MacBook Pro M3 14-inch', 'Powerful laptop with M3 chip, perfect for professionals and creators', 7999.00, 8999.00, 15, 10, 1, '["/static/products/macbook-m3.jpg"]', '{"chip":"M3","ram":"16GB","storage":"512GB SSD","screen":"14 inch"}', 1, 89, NOW(), NOW()),
('AirPods Pro 2nd Gen', 'Wireless earbuds with active noise cancellation and spatial audio', 899.00, 1099.00, 50, 11, 1, '["/static/products/airpods-pro2.jpg"]', '{"battery":"6 hours","noise_cancellation":"Yes","water_resistant":"IPX4"}', 1, 445, NOW(), NOW()),
('Samsung Galaxy S24 Ultra', 'Premium Android smartphone with S Pen and advanced AI features', 4299.00, 4799.00, 20, 9, 1, '["/static/products/galaxy-s24.jpg"]', '{"storage":"256GB","color":"Titanium Black","screen":"6.8 inch","camera":"200MP"}', 1, 234, NOW(), NOW()),

-- Fashion Hub KL 商品
('Nike Air Max 270', 'Comfortable running shoes with Max Air cushioning', 399.00, 499.00, 100, 15, 2, '["/static/products/nike-airmax.jpg"]', '{"size":"US 7-12","color":"Black/White","material":"Mesh upper"}', 1, 1200, NOW(), NOW()),
('Adidas Ultraboost 22', 'High-performance running shoes with Boost midsole', 599.00, 699.00, 80, 15, 2, '["/static/products/adidas-ultraboost.jpg"]', '{"size":"US 7-12","color":"Core Black","material":"Primeknit upper"}', 1, 890, NOW(), NOW()),
('Uniqlo Heattech Long Sleeve', 'Thermal base layer for cold weather', 49.90, 69.90, 200, 13, 2, '["/static/products/uniqlo-heattech.jpg"]', '{"size":"XS-XXL","color":"Black/White/Gray","material":"Heattech fabric"}', 1, 2100, NOW(), NOW()),
('Zara Denim Jacket', 'Classic denim jacket with modern fit', 199.00, 249.00, 60, 13, 2, '["/static/products/zara-denim.jpg"]', '{"size":"XS-XL","color":"Blue","material":"100% Cotton"}', 1, 567, NOW(), NOW()),

-- Home Depot Malaysia 商品
('IKEA MALM Bed Frame', 'Minimalist bed frame with storage drawers', 899.00, 1099.00, 30, 17, 3, '["/static/products/ikea-malm.jpg"]', '{"size":"Queen","color":"White","material":"Particleboard","assembly":"Required"}', 1, 234, NOW(), NOW()),
('KitchenAid Stand Mixer', 'Professional stand mixer for baking enthusiasts', 1299.00, 1599.00, 15, 18, 3, '["/static/products/kitchenaid-mixer.jpg"]', '{"capacity":"5 Quart","color":"Empire Red","attachments":"3 included","warranty":"1 year"}', 1, 89, NOW(), NOW()),
('Philips Air Fryer XXL', 'Large capacity air fryer for healthy cooking', 399.00, 499.00, 40, 18, 3, '["/static/products/philips-airfryer.jpg"]', '{"capacity":"6.2L","power":"2225W","programs":"7 preset","timer":"60 minutes"}', 1, 456, NOW(), NOW()),
('Dyson V15 Detect Vacuum', 'Cordless vacuum with laser dust detection', 1999.00, 2299.00, 20, 17, 3, '["/static/products/dyson-v15.jpg"]', '{"runtime":"60 minutes","dustbin":"0.77L","filtration":"HEPA","weight":"3.0kg"}', 1, 123, NOW(), NOW()),

-- Sports Zone 商品
('Wilson Pro Staff Tennis Racket', 'Professional tennis racket for advanced players', 899.00, 1099.00, 25, 4, 4, '["/static/products/wilson-prostaff.jpg"]', '{"weight":"315g","head_size":"97 sq in","string_pattern":"16x19","grip":"4 3/8"}', 1, 78, NOW(), NOW()),
('Nike Dri-FIT Training Shorts', 'Moisture-wicking training shorts for workouts', 89.00, 119.00, 150, 2, 4, '["/static/products/nike-shorts.jpg"]', '{"size":"S-XXL","color":"Black","material":"Dri-FIT","pockets":"2 side pockets"}', 1, 890, NOW(), NOW()),
('Garmin Forerunner 255', 'GPS running watch with advanced training metrics', 1299.00, 1499.00, 35, 4, 4, '["/static/products/garmin-255.jpg"]', '{"battery":"14 days","gps":"Yes","heart_rate":"Yes","water_resistant":"5ATM"}', 1, 234, NOW(), NOW()),
('Yoga Mat Premium', 'Non-slip yoga mat with carrying strap', 79.00, 99.00, 80, 4, 4, '["/static/products/yoga-mat.jpg"]', '{"thickness":"6mm","material":"TPE","size":"183x61cm","weight":"1.2kg"}', 1, 567, NOW(), NOW()),

-- Beauty Paradise 商品
('SK-II Facial Treatment Essence', 'Premium skincare essence for radiant skin', 899.00, 1099.00, 40, 5, 5, '["/static/products/sk2-essence.jpg"]', '{"volume":"230ml","skin_type":"All","key_ingredient":"Pitera","made_in":"Japan"}', 1, 345, NOW(), NOW()),
('MAC Lipstick Ruby Woo', 'Classic red lipstick with matte finish', 89.00, 109.00, 100, 5, 5, '["/static/products/mac-rubywoo.jpg"]', '{"finish":"Matte","color":"Ruby Woo","weight":"3g","longevity":"8 hours"}', 1, 1200, NOW(), NOW()),
('La Mer The Moisturizing Cream', 'Luxury moisturizing cream for all skin types', 1299.00, 1499.00, 20, 5, 5, '["/static/products/lamer-cream.jpg"]', '{"size":"30ml","skin_type":"All","key_ingredient":"Miracle Broth","texture":"Rich cream"}', 1, 89, NOW(), NOW()),
('Dyson Supersonic Hair Dryer', 'Professional hair dryer with intelligent heat control', 1299.00, 1599.00, 25, 5, 5, '["/static/products/dyson-hairdryer.jpg"]', '{"power":"1600W","attachments":"4 included","weight":"560g","noise":"Reduced"}', 1, 156, NOW(), NOW()),

-- Book World Malaysia 商品
('Atomic Habits by James Clear', 'Bestselling book on building good habits and breaking bad ones', 49.90, 69.90, 200, 6, 6, '["/static/products/atomic-habits.jpg"]', '{"pages":"320","language":"English","format":"Paperback","publisher":"Random House"}', 1, 2100, NOW(), NOW()),
('The Psychology of Money', 'Timeless lessons on wealth, greed, and happiness', 59.90, 79.90, 150, 6, 6, '["/static/products/psychology-money.jpg"]', '{"pages":"256","language":"English","format":"Hardcover","publisher":"Harriman House"}', 1, 1456, NOW(), NOW()),
('Malaysian Cookbook', 'Authentic Malaysian recipes and cooking techniques', 89.90, 119.90, 80, 6, 6, '["/static/products/malaysian-cookbook.jpg"]', '{"pages":"288","language":"English","format":"Hardcover","recipes":"150+"}', 1, 678, NOW(), NOW()),
('Harry Potter Complete Set', 'All 7 books in the Harry Potter series', 299.90, 399.90, 50, 6, 6, '["/static/products/harry-potter-set.jpg"]', '{"books":"7","format":"Paperback","language":"English","box_set":"Yes"}', 1, 234, NOW(), NOW()),

-- Toy Kingdom 商品
('LEGO Creator Expert Modular Building', 'Detailed modular building set for adults', 899.00, 1099.00, 30, 7, 7, '["/static/products/lego-modular.jpg"]', '{"pieces":"2568","age":"16+","theme":"Creator Expert","difficulty":"Advanced"}', 1, 123, NOW(), NOW()),
('Barbie Dreamhouse', '3-story dollhouse with furniture and accessories', 299.00, 399.00, 40, 7, 7, '["/static/products/barbie-dreamhouse.jpg"]', '{"rooms":"8","accessories":"25+","age":"3+","height":"109cm"}', 1, 456, NOW(), NOW()),
('Nintendo Switch OLED', 'Gaming console with OLED screen and Joy-Con controllers', 1299.00, 1499.00, 25, 7, 7, '["/static/products/nintendo-switch.jpg"]', '{"screen":"7 inch OLED","storage":"64GB","controllers":"Joy-Con","battery":"4.5-9 hours"}', 1, 234, NOW(), NOW()),
('Hot Wheels Track Set', 'Racing track set with multiple cars and loops', 199.00, 249.00, 60, 7, 7, '["/static/products/hotwheels-track.jpg"]', '{"cars":"4","track_length":"6 meters","age":"3+","batteries":"Not included"}', 1, 567, NOW(), NOW()),

-- Auto Parts Pro 商品
('Michelin Pilot Sport 4', 'High-performance summer tires for sports cars', 899.00, 1099.00, 20, 8, 8, '["/static/products/michelin-pilot.jpg"]', '{"size":"225/45R17","speed_rating":"Y","load_index":"91","season":"Summer"}', 1, 89, NOW(), NOW()),
('Bosch Icon Wiper Blades', 'Premium windshield wiper blades for all weather', 89.00, 119.00, 100, 8, 8, '["/static/products/bosch-wipers.jpg"]', '{"length":"26 inch","material":"Rubber","fit":"Universal","warranty":"1 year"}', 1, 456, NOW(), NOW()),
('K&N Air Filter', 'High-flow air filter for improved engine performance', 199.00, 249.00, 50, 8, 8, '["/static/products/kn-airfilter.jpg"]', '{"material":"Cotton gauze","cleaning":"Reusable","flow":"Increased","warranty":"1M miles"}', 1, 234, NOW(), NOW()),
('Mobil 1 Engine Oil 5W-30', 'Full synthetic engine oil for all vehicles', 89.00, 119.00, 80, 8, 8, '["/static/products/mobil1-oil.jpg"]', '{"viscosity":"5W-30","volume":"5L","type":"Full Synthetic","api":"SN Plus"}', 1, 678, NOW(), NOW());
