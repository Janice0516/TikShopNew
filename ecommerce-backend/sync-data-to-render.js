#!/usr/bin/env node

// 数据同步脚本 - 同步数据到Render PostgreSQL数据库
const { Client } = require('pg');
const fs = require('fs');
const path = require('path');

// Render PostgreSQL 连接配置
const renderDbConfig = {
  host: 'dpg-d0j8q8h2s78s73fq8hpg-a.oregon-postgres.render.com',
  port: 5432,
  user: 'tiktokshop_slkz_user',
  password: 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn',
  database: 'tiktokshop_slkz',
  ssl: { rejectUnauthorized: false },
  connectionTimeoutMillis: 60000,
};

async function syncDataToRender() {
  const client = new Client(renderDbConfig);
  
  try {
    console.log('🔌 连接到Render PostgreSQL数据库...');
    await client.connect();
    console.log('✅ 连接成功！');
    
    // 1. 清空现有数据（保留表结构）
    console.log('🧹 清空现有数据...');
    await clearExistingData(client);
    
    // 2. 插入基础数据
    console.log('📝 插入基础数据...');
    await insertBasicData(client);
    
    // 3. 插入商品数据
    console.log('🛍️ 插入商品数据...');
    await insertProductData(client);
    
    // 4. 插入商家商品关联
    console.log('🔗 插入商家商品关联...');
    await insertMerchantProducts(client);
    
    // 5. 插入订单数据
    console.log('📦 插入订单数据...');
    await insertOrderData(client);
    
    // 6. 插入资金操作记录
    console.log('💰 插入资金操作记录...');
    await insertFundOperations(client);
    
    console.log('✅ 数据同步完成！');
    
  } catch (error) {
    console.error('❌ 数据同步失败:');
    console.error('   错误类型:', error.code);
    console.error('   错误信息:', error.message);
    process.exit(1);
  } finally {
    await client.end();
  }
}

// 清空现有数据
async function clearExistingData(client) {
  const tables = [
    'merchant_recharge',
    'fund_operation', 
    'merchant_credit_rating',
    'merchant_withdrawal',
    'merchant_withdrawal_info',
    'order_item',
    'order',
    'merchant_product',
    'product',
    'merchant',
    'admin',
    'user',
    'category'
  ];
  
  for (const table of tables) {
    try {
      await client.query(`DELETE FROM ${table}`);
      console.log(`   ✅ 清空表 ${table}`);
    } catch (error) {
      console.log(`   ⚠️ 跳过表 ${table}: ${error.message}`);
    }
  }
}

// 插入基础数据
async function insertBasicData(client) {
  // 插入商品分类
  const categories = [
    { name: 'Electronics', description: 'Electronic devices and gadgets', parent_id: null, sort_order: 1 },
    { name: 'Fashion', description: 'Clothing and accessories', parent_id: null, sort_order: 2 },
    { name: 'Home & Garden', description: 'Home improvement and garden supplies', parent_id: null, sort_order: 3 },
    { name: 'Sports & Outdoors', description: 'Sports equipment and outdoor gear', parent_id: null, sort_order: 4 },
    { name: 'Beauty & Health', description: 'Beauty products and health supplements', parent_id: null, sort_order: 5 },
    { name: 'Books & Media', description: 'Books, movies, and music', parent_id: null, sort_order: 6 },
    { name: 'Toys & Games', description: 'Toys and gaming products', parent_id: null, sort_order: 7 },
    { name: 'Automotive', description: 'Car parts and accessories', parent_id: null, sort_order: 8 }
  ];
  
  for (const category of categories) {
    await client.query(
      `INSERT INTO category (name, description, parent_id, sort_order, status, created_at, updated_at) 
       VALUES ($1, $2, $3, $4, 1, NOW(), NOW())`,
      [category.name, category.description, category.parent_id, category.sort_order]
    );
  }
  console.log('   ✅ 插入商品分类');
  
  // 插入子分类
  const subCategories = [
    { name: 'Smartphones', parent_id: 1, sort_order: 1 },
    { name: 'Laptops', parent_id: 1, sort_order: 2 },
    { name: 'Audio', parent_id: 1, sort_order: 3 },
    { name: 'Cameras', parent_id: 1, sort_order: 4 },
    { name: 'Men\'s Clothing', parent_id: 2, sort_order: 1 },
    { name: 'Women\'s Clothing', parent_id: 2, sort_order: 2 },
    { name: 'Shoes', parent_id: 2, sort_order: 3 },
    { name: 'Accessories', parent_id: 2, sort_order: 4 },
    { name: 'Furniture', parent_id: 3, sort_order: 1 },
    { name: 'Kitchen & Dining', parent_id: 3, sort_order: 2 },
    { name: 'Garden Tools', parent_id: 3, sort_order: 3 },
    { name: 'Home Decor', parent_id: 3, sort_order: 4 }
  ];
  
  for (const subCategory of subCategories) {
    await client.query(
      `INSERT INTO category (name, description, parent_id, sort_order, status, created_at, updated_at) 
       VALUES ($1, $2, $3, $4, 1, NOW(), NOW())`,
      [subCategory.name, 'Subcategory', subCategory.parent_id, subCategory.sort_order]
    );
  }
  console.log('   ✅ 插入子分类');
  
  // 插入管理员账户
  await client.query(
    `INSERT INTO admin (username, password, email, role, status, created_at, updated_at) 
     VALUES ($1, $2, $3, $4, 1, NOW(), NOW())`,
    ['admin', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@tiktokshop.com', 'super_admin']
  );
  console.log('   ✅ 插入管理员账户');
  
  // 插入商家数据
  const merchants = [
    { username: 'techstore_malaysia', email: 'contact@techstore.com.my', phone: '+60123456789', merchant_name: 'TechStore Malaysia', merchant_uid: 'TS001', contact_name: 'Ahmad Rahman', business_license: 'BL2024001', balance: 5000.00, total_income: 15000.00, total_withdraw: 10000.00 },
    { username: 'fashion_hub_kl', email: 'info@fashionhub.com.my', phone: '+60198765432', merchant_name: 'Fashion Hub KL', merchant_uid: 'FH002', contact_name: 'Siti Nurhaliza', business_license: 'BL2024002', balance: 3200.50, total_income: 8500.00, total_withdraw: 5300.00 },
    { username: 'home_depot_my', email: 'sales@homedepot.com.my', phone: '+60134567890', merchant_name: 'Home Depot Malaysia', merchant_uid: 'HD003', contact_name: 'Lim Wei Ming', business_license: 'BL2024003', balance: 7500.00, total_income: 22000.00, total_withdraw: 14500.00 },
    { username: 'sports_zone', email: 'orders@sportszone.com.my', phone: '+60145678901', merchant_name: 'Sports Zone', merchant_uid: 'SZ004', contact_name: 'Raj Kumar', business_license: 'BL2024004', balance: 2800.00, total_income: 6800.00, total_withdraw: 4000.00 },
    { username: 'beauty_paradise', email: 'hello@beautyparadise.com.my', phone: '+60156789012', merchant_name: 'Beauty Paradise', merchant_uid: 'BP005', contact_name: 'Nurul Aisyah', business_license: 'BL2024005', balance: 4100.75, total_income: 12000.00, total_withdraw: 7900.00 },
    { username: 'book_world', email: 'support@bookworld.com.my', phone: '+60167890123', merchant_name: 'Book World Malaysia', merchant_uid: 'BW006', contact_name: 'Tan Mei Ling', business_license: 'BL2024006', balance: 1500.00, total_income: 4500.00, total_withdraw: 3000.00 },
    { username: 'toy_kingdom', email: 'info@toykingdom.com.my', phone: '+60178901234', merchant_name: 'Toy Kingdom', merchant_uid: 'TK007', contact_name: 'Muhammad Ali', business_license: 'BL2024007', balance: 2200.00, total_income: 5500.00, total_withdraw: 3300.00 },
    { username: 'auto_parts_pro', email: 'sales@autopartspro.com.my', phone: '+60189012345', merchant_name: 'Auto Parts Pro', merchant_uid: 'AP008', contact_name: 'David Chen', business_license: 'BL2024008', balance: 6800.00, total_income: 18000.00, total_withdraw: 11200.00 }
  ];
  
  for (const merchant of merchants) {
    await client.query(
      `INSERT INTO merchant (username, password, email, phone, merchant_name, merchant_uid, contact_name, business_license, status, balance, frozen_amount, total_income, total_withdraw, created_at, updated_at) 
       VALUES ($1, $2, $3, $4, $5, $6, $7, $8, 1, $9, 0.00, $10, $11, NOW(), NOW())`,
      [merchant.username, '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', merchant.email, merchant.phone, merchant.merchant_name, merchant.merchant_uid, merchant.contact_name, merchant.business_license, merchant.balance, merchant.total_income, merchant.total_withdraw]
    );
  }
  console.log('   ✅ 插入商家数据');
  
  // 插入用户数据
  const users = [
    { username: 'john_doe', email: 'john.doe@email.com', phone: '+60123456788' },
    { username: 'sarah_wong', email: 'sarah.wong@email.com', phone: '+60123456787' },
    { username: 'ahmad_ali', email: 'ahmad.ali@email.com', phone: '+60123456786' },
    { username: 'priya_sharma', email: 'priya.sharma@email.com', phone: '+60123456785' },
    { username: 'lim_wei_ming', email: 'lim.weiming@email.com', phone: '+60123456784' }
  ];
  
  for (const user of users) {
    await client.query(
      `INSERT INTO user (username, password, email, phone, status, created_at, updated_at) 
       VALUES ($1, $2, $3, $4, 1, NOW(), NOW())`,
      [user.username, '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', user.email, user.phone]
    );
  }
  console.log('   ✅ 插入用户数据');
}

// 插入商品数据
async function insertProductData(client) {
  const products = [
    // TechStore Malaysia 商品
    { name: 'iPhone 15 Pro Max 256GB', description: 'Latest iPhone with titanium design, A17 Pro chip, and advanced camera system', price: 4999.00, original_price: 5499.00, stock: 25, category_id: 9, merchant_id: 1, sales_count: 156 },
    { name: 'MacBook Pro M3 14-inch', description: 'Powerful laptop with M3 chip, perfect for professionals and creators', price: 7999.00, original_price: 8999.00, stock: 15, category_id: 10, merchant_id: 1, sales_count: 89 },
    { name: 'AirPods Pro 2nd Gen', description: 'Wireless earbuds with active noise cancellation and spatial audio', price: 899.00, original_price: 1099.00, stock: 50, category_id: 11, merchant_id: 1, sales_count: 445 },
    { name: 'Samsung Galaxy S24 Ultra', description: 'Premium Android smartphone with S Pen and advanced AI features', price: 4299.00, original_price: 4799.00, stock: 20, category_id: 9, merchant_id: 1, sales_count: 234 },
    
    // Fashion Hub KL 商品
    { name: 'Nike Air Max 270', description: 'Comfortable running shoes with Max Air cushioning', price: 399.00, original_price: 499.00, stock: 100, category_id: 15, merchant_id: 2, sales_count: 1200 },
    { name: 'Adidas Ultraboost 22', description: 'High-performance running shoes with Boost midsole', price: 599.00, original_price: 699.00, stock: 80, category_id: 15, merchant_id: 2, sales_count: 890 },
    { name: 'Uniqlo Heattech Long Sleeve', description: 'Thermal base layer for cold weather', price: 49.90, original_price: 69.90, stock: 200, category_id: 13, merchant_id: 2, sales_count: 2100 },
    { name: 'Zara Denim Jacket', description: 'Classic denim jacket with modern fit', price: 199.00, original_price: 249.00, stock: 60, category_id: 13, merchant_id: 2, sales_count: 567 },
    
    // Home Depot Malaysia 商品
    { name: 'IKEA MALM Bed Frame', description: 'Minimalist bed frame with storage drawers', price: 899.00, original_price: 1099.00, stock: 30, category_id: 17, merchant_id: 3, sales_count: 234 },
    { name: 'KitchenAid Stand Mixer', description: 'Professional stand mixer for baking enthusiasts', price: 1299.00, original_price: 1599.00, stock: 15, category_id: 18, merchant_id: 3, sales_count: 89 },
    { name: 'Philips Air Fryer XXL', description: 'Large capacity air fryer for healthy cooking', price: 399.00, original_price: 499.00, stock: 40, category_id: 18, merchant_id: 3, sales_count: 456 },
    { name: 'Dyson V15 Detect Vacuum', description: 'Cordless vacuum with laser dust detection', price: 1999.00, original_price: 2299.00, stock: 20, category_id: 17, merchant_id: 3, sales_count: 123 },
    
    // Sports Zone 商品
    { name: 'Wilson Pro Staff Tennis Racket', description: 'Professional tennis racket for advanced players', price: 899.00, original_price: 1099.00, stock: 25, category_id: 4, merchant_id: 4, sales_count: 78 },
    { name: 'Nike Dri-FIT Training Shorts', description: 'Moisture-wicking training shorts for workouts', price: 89.00, original_price: 119.00, stock: 150, category_id: 2, merchant_id: 4, sales_count: 890 },
    { name: 'Garmin Forerunner 255', description: 'GPS running watch with advanced training metrics', price: 1299.00, original_price: 1499.00, stock: 35, category_id: 4, merchant_id: 4, sales_count: 234 },
    { name: 'Yoga Mat Premium', description: 'Non-slip yoga mat with carrying strap', price: 79.00, original_price: 99.00, stock: 80, category_id: 4, merchant_id: 4, sales_count: 567 },
    
    // Beauty Paradise 商品
    { name: 'SK-II Facial Treatment Essence', description: 'Premium skincare essence for radiant skin', price: 899.00, original_price: 1099.00, stock: 40, category_id: 5, merchant_id: 5, sales_count: 345 },
    { name: 'MAC Lipstick Ruby Woo', description: 'Classic red lipstick with matte finish', price: 89.00, original_price: 109.00, stock: 100, category_id: 5, merchant_id: 5, sales_count: 1200 },
    { name: 'La Mer The Moisturizing Cream', description: 'Luxury moisturizing cream for all skin types', price: 1299.00, original_price: 1499.00, stock: 20, category_id: 5, merchant_id: 5, sales_count: 89 },
    { name: 'Dyson Supersonic Hair Dryer', description: 'Professional hair dryer with intelligent heat control', price: 1299.00, original_price: 1599.00, stock: 25, category_id: 5, merchant_id: 5, sales_count: 156 },
    
    // Book World Malaysia 商品
    { name: 'Atomic Habits by James Clear', description: 'Bestselling book on building good habits and breaking bad ones', price: 49.90, original_price: 69.90, stock: 200, category_id: 6, merchant_id: 6, sales_count: 2100 },
    { name: 'The Psychology of Money', description: 'Timeless lessons on wealth, greed, and happiness', price: 59.90, original_price: 79.90, stock: 150, category_id: 6, merchant_id: 6, sales_count: 1456 },
    { name: 'Malaysian Cookbook', description: 'Authentic Malaysian recipes and cooking techniques', price: 89.90, original_price: 119.90, stock: 80, category_id: 6, merchant_id: 6, sales_count: 678 },
    { name: 'Harry Potter Complete Set', description: 'All 7 books in the Harry Potter series', price: 299.90, original_price: 399.90, stock: 50, category_id: 6, merchant_id: 6, sales_count: 234 },
    
    // Toy Kingdom 商品
    { name: 'LEGO Creator Expert Modular Building', description: 'Detailed modular building set for adults', price: 899.00, original_price: 1099.00, stock: 30, category_id: 7, merchant_id: 7, sales_count: 123 },
    { name: 'Barbie Dreamhouse', description: '3-story dollhouse with furniture and accessories', price: 299.00, original_price: 399.00, stock: 40, category_id: 7, merchant_id: 7, sales_count: 456 },
    { name: 'Nintendo Switch OLED', description: 'Gaming console with OLED screen and Joy-Con controllers', price: 1299.00, original_price: 1499.00, stock: 25, category_id: 7, merchant_id: 7, sales_count: 234 },
    { name: 'Hot Wheels Track Set', description: 'Racing track set with multiple cars and loops', price: 199.00, original_price: 249.00, stock: 60, category_id: 7, merchant_id: 7, sales_count: 567 },
    
    // Auto Parts Pro 商品
    { name: 'Michelin Pilot Sport 4', description: 'High-performance summer tires for sports cars', price: 899.00, original_price: 1099.00, stock: 20, category_id: 8, merchant_id: 8, sales_count: 89 },
    { name: 'Bosch Icon Wiper Blades', description: 'Premium windshield wiper blades for all weather', price: 89.00, original_price: 119.00, stock: 100, category_id: 8, merchant_id: 8, sales_count: 456 },
    { name: 'K&N Air Filter', description: 'High-flow air filter for improved engine performance', price: 199.00, original_price: 249.00, stock: 50, category_id: 8, merchant_id: 8, sales_count: 234 },
    { name: 'Mobil 1 Engine Oil 5W-30', description: 'Full synthetic engine oil for all vehicles', price: 89.00, original_price: 119.00, stock: 80, category_id: 8, merchant_id: 8, sales_count: 678 }
  ];
  
  for (const product of products) {
    await client.query(
      `INSERT INTO product (name, description, price, original_price, stock, category_id, merchant_id, images, specifications, status, sales_count, created_at, updated_at) 
       VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, 1, $10, NOW(), NOW())`,
      [product.name, product.description, product.price, product.original_price, product.stock, product.category_id, product.merchant_id, '["/static/products/default.jpg"]', '{}', product.sales_count]
    );
  }
  console.log('   ✅ 插入商品数据');
}

// 插入商家商品关联
async function insertMerchantProducts(client) {
  // 为每个商家关联他们的商品
  for (let merchantId = 1; merchantId <= 8; merchantId++) {
    const startProductId = (merchantId - 1) * 4 + 1;
    const endProductId = merchantId * 4;
    
    for (let productId = startProductId; productId <= endProductId; productId++) {
      await client.query(
        `INSERT INTO merchant_product (merchant_id, product_id, status, created_at, updated_at) 
         VALUES ($1, $2, 1, NOW(), NOW())`,
        [merchantId, productId]
      );
    }
  }
  console.log('   ✅ 插入商家商品关联');
}

// 插入订单数据
async function insertOrderData(client) {
  const orders = [
    { user_id: 1, merchant_id: 1, total_amount: 4999.00, status: 2, payment_status: 1, shipping_status: 1 },
    { user_id: 2, merchant_id: 2, total_amount: 798.00, status: 2, payment_status: 1, shipping_status: 1 },
    { user_id: 3, merchant_id: 3, total_amount: 2198.00, status: 2, payment_status: 1, shipping_status: 1 },
    { user_id: 4, merchant_id: 4, total_amount: 978.00, status: 2, payment_status: 1, shipping_status: 1 },
    { user_id: 5, merchant_id: 5, total_amount: 988.00, status: 2, payment_status: 1, shipping_status: 1 },
    { user_id: 1, merchant_id: 6, total_amount: 149.80, status: 2, payment_status: 1, shipping_status: 1 },
    { user_id: 2, merchant_id: 7, total_amount: 1198.00, status: 2, payment_status: 1, shipping_status: 1 },
    { user_id: 3, merchant_id: 8, total_amount: 988.00, status: 2, payment_status: 1, shipping_status: 1 }
  ];
  
  for (const order of orders) {
    const result = await client.query(
      `INSERT INTO "order" (user_id, merchant_id, total_amount, status, payment_status, shipping_status, created_at, updated_at) 
       VALUES ($1, $2, $3, $4, $5, $6, NOW(), NOW()) RETURNING id`,
      [order.user_id, order.merchant_id, order.total_amount, order.status, order.payment_status, order.shipping_status]
    );
    
    const orderId = result.rows[0].id;
    
    // 插入订单项
    await client.query(
      `INSERT INTO order_item (order_id, product_id, quantity, price, created_at, updated_at) 
       VALUES ($1, $2, $3, $4, NOW(), NOW())`,
      [orderId, 1, 1, order.total_amount]
    );
  }
  console.log('   ✅ 插入订单数据');
}

// 插入资金操作记录
async function insertFundOperations(client) {
  const fundOperations = [
    { merchant_id: 1, type: 'recharge', amount: 10000.00, description: 'Initial recharge' },
    { merchant_id: 2, type: 'recharge', amount: 5000.00, description: 'Initial recharge' },
    { merchant_id: 3, type: 'recharge', amount: 15000.00, description: 'Initial recharge' },
    { merchant_id: 4, type: 'recharge', amount: 3000.00, description: 'Initial recharge' },
    { merchant_id: 5, type: 'recharge', amount: 8000.00, description: 'Initial recharge' },
    { merchant_id: 6, type: 'recharge', amount: 2000.00, description: 'Initial recharge' },
    { merchant_id: 7, type: 'recharge', amount: 2500.00, description: 'Initial recharge' },
    { merchant_id: 8, type: 'recharge', amount: 12000.00, description: 'Initial recharge' }
  ];
  
  for (const operation of fundOperations) {
    await client.query(
      `INSERT INTO fund_operation (merchant_id, type, amount, description, status, created_at, updated_at) 
       VALUES ($1, $2, $3, $4, 1, NOW(), NOW())`,
      [operation.merchant_id, operation.type, operation.amount, operation.description]
    );
  }
  console.log('   ✅ 插入资金操作记录');
}

// 运行同步
syncDataToRender();
