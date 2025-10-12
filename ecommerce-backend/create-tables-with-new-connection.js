#!/usr/bin/env node

// 使用新的Render数据库连接创建表和数据
const { Client } = require('pg');

// 新的连接配置
const connectionConfig = {
  host: 'dpg-d3kgpsd6ubrc73dvbjm0-a.oregon-postgres.render.com',
  port: 5432,
  user: 'tiktokshop_slkz_user',
  password: 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn',
  database: 'tiktokshop_slkz',
  ssl: { rejectUnauthorized: false },
  connectionTimeoutMillis: 15000,
};

async function createTablesAndData() {
  console.log('🚀 开始创建数据库表和数据...');
  
  const client = new Client(connectionConfig);
  
  try {
    await client.connect();
    console.log('✅ 数据库连接成功');
    
    // 1. 创建分类表
    console.log('\n📂 创建分类表...');
    await client.query(`
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
      )
    `);
    console.log('✅ 分类表创建成功');
    
    // 2. 创建商品表
    console.log('\n🛍️ 创建商品表...');
    await client.query(`
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
      )
    `);
    console.log('✅ 商品表创建成功');
    
    // 3. 创建用户表
    console.log('\n👤 创建用户表...');
    await client.query(`
      CREATE TABLE IF NOT EXISTS "user" (
        id BIGSERIAL PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        nickname VARCHAR(50),
        email VARCHAR(100),
        phone VARCHAR(20),
        avatar VARCHAR(255),
        gender SMALLINT DEFAULT 0,
        status SMALLINT DEFAULT 1,
        last_login_time TIMESTAMP,
        create_time TIMESTAMP DEFAULT NOW(),
        update_time TIMESTAMP DEFAULT NOW()
      )
    `);
    console.log('✅ 用户表创建成功');
    
    // 4. 创建商家表
    console.log('\n🏪 创建商家表...');
    await client.query(`
      CREATE TABLE IF NOT EXISTS merchant (
        id BIGSERIAL PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        merchant_name VARCHAR(100) NOT NULL,
        contact_phone VARCHAR(20),
        contact_email VARCHAR(100),
        business_license VARCHAR(255),
        status SMALLINT DEFAULT 1,
        credit_rating DECIMAL(3,2) DEFAULT 5.00,
        create_time TIMESTAMP DEFAULT NOW(),
        update_time TIMESTAMP DEFAULT NOW()
      )
    `);
    console.log('✅ 商家表创建成功');
    
    // 5. 创建订单表
    console.log('\n📦 创建订单表...');
    await client.query(`
      CREATE TABLE IF NOT EXISTS "order" (
        id BIGSERIAL PRIMARY KEY,
        order_no VARCHAR(50) UNIQUE NOT NULL,
        user_id BIGINT NOT NULL,
        merchant_id BIGINT NOT NULL,
        product_id BIGINT NOT NULL,
        quantity INTEGER NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status SMALLINT DEFAULT 1,
        shipping_address TEXT,
        create_time TIMESTAMP DEFAULT NOW(),
        update_time TIMESTAMP DEFAULT NOW()
      )
    `);
    console.log('✅ 订单表创建成功');
    
    // 6. 插入分类数据
    console.log('\n📂 插入分类数据...');
    const categories = [
      { name: 'Electronics', parentId: 0, level: 1, sort: 1, status: 1 },
      { name: 'Fashion', parentId: 0, level: 1, sort: 2, status: 1 },
      { name: 'Home & Garden', parentId: 0, level: 1, sort: 3, status: 1 },
      { name: 'Sports & Outdoors', parentId: 0, level: 1, sort: 4, status: 1 },
      { name: 'Beauty & Health', parentId: 0, level: 1, sort: 5, status: 1 },
      { name: 'Books & Media', parentId: 0, level: 1, sort: 6, status: 1 },
      { name: 'Toys & Games', parentId: 0, level: 1, sort: 7, status: 1 },
      { name: 'Automotive', parentId: 0, level: 1, sort: 8, status: 1 },
      { name: 'Smartphones', parentId: 1, level: 2, sort: 1, status: 1 },
      { name: 'Laptops', parentId: 1, level: 2, sort: 2, status: 1 },
      { name: 'Audio', parentId: 1, level: 2, sort: 3, status: 1 },
      { name: 'Cameras', parentId: 1, level: 2, sort: 4, status: 1 },
      { name: 'Men\'s Clothing', parentId: 2, level: 2, sort: 1, status: 1 },
      { name: 'Women\'s Clothing', parentId: 2, level: 2, sort: 2, status: 1 },
      { name: 'Shoes', parentId: 2, level: 2, sort: 3, status: 1 },
      { name: 'Accessories', parentId: 2, level: 2, sort: 4, status: 1 },
      { name: 'Furniture', parentId: 3, level: 2, sort: 1, status: 1 },
      { name: 'Kitchen & Dining', parentId: 3, level: 2, sort: 2, status: 1 },
      { name: 'Garden Tools', parentId: 3, level: 2, sort: 3, status: 1 },
      { name: 'Home Decor', parentId: 3, level: 2, sort: 4, status: 1 }
    ];
    
    let categoryMap = {};
    for (const category of categories) {
      const result = await client.query(
        'INSERT INTO category (name, parent_id, level, sort, status) VALUES ($1, $2, $3, $4, $5) RETURNING id',
        [category.name, category.parentId, category.level, category.sort, category.status]
      );
      categoryMap[category.name] = result.rows[0].id;
      console.log(`   ✅ 创建分类: ${category.name} (ID: ${result.rows[0].id})`);
    }
    
    // 7. 插入商品数据
    console.log('\n🛍️ 插入商品数据...');
    const products = [
      { name: 'iPhone 15 Pro Max 256GB', categoryId: categoryMap['Smartphones'], brand: 'Apple', mainImage: '/static/products/iphone15pro.jpg', images: JSON.stringify(['/static/products/iphone15pro.jpg']), costPrice: 4500.00, suggestPrice: 4999.00, stock: 25, sales: 156, description: 'Latest iPhone with titanium design, A17 Pro chip, and advanced camera system' },
      { name: 'MacBook Pro M3 14-inch', categoryId: categoryMap['Laptops'], brand: 'Apple', mainImage: '/static/products/macbook-m3.jpg', images: JSON.stringify(['/static/products/macbook-m3.jpg']), costPrice: 7500.00, suggestPrice: 7999.00, stock: 15, sales: 89, description: 'Powerful laptop with M3 chip, perfect for professionals and creators' },
      { name: 'AirPods Pro 2nd Gen', categoryId: categoryMap['Audio'], brand: 'Apple', mainImage: '/static/products/airpods-pro2.jpg', images: JSON.stringify(['/static/products/airpods-pro2.jpg']), costPrice: 800.00, suggestPrice: 899.00, stock: 50, sales: 445, description: 'Wireless earbuds with active noise cancellation and spatial audio' },
      { name: 'Samsung Galaxy S24 Ultra', categoryId: categoryMap['Smartphones'], brand: 'Samsung', mainImage: '/static/products/galaxy-s24.jpg', images: JSON.stringify(['/static/products/galaxy-s24.jpg']), costPrice: 4000.00, suggestPrice: 4299.00, stock: 20, sales: 234, description: 'Premium Android smartphone with S Pen and advanced AI features' },
      { name: 'Nike Air Max 270', categoryId: categoryMap['Shoes'], brand: 'Nike', mainImage: '/static/products/nike-airmax.jpg', images: JSON.stringify(['/static/products/nike-airmax.jpg']), costPrice: 350.00, suggestPrice: 399.00, stock: 100, sales: 1200, description: 'Comfortable running shoes with Max Air cushioning' },
      { name: 'Sony WH-1000XM5', categoryId: categoryMap['Audio'], brand: 'Sony', mainImage: '/static/products/sony-wh1000xm5.jpg', images: JSON.stringify(['/static/products/sony-wh1000xm5.jpg']), costPrice: 1200.00, suggestPrice: 1299.00, stock: 30, sales: 78, description: 'Industry-leading noise canceling headphones with 30-hour battery life' },
      { name: 'Canon EOS R5', categoryId: categoryMap['Cameras'], brand: 'Canon', mainImage: '/static/products/canon-eos-r5.jpg', images: JSON.stringify(['/static/products/canon-eos-r5.jpg']), costPrice: 8500.00, suggestPrice: 8999.00, stock: 8, sales: 23, description: 'Professional mirrorless camera with 45MP full-frame sensor' },
      { name: 'Adidas Ultraboost 22', categoryId: categoryMap['Shoes'], brand: 'Adidas', mainImage: '/static/products/adidas-ultraboost.jpg', images: JSON.stringify(['/static/products/adidas-ultraboost.jpg']), costPrice: 280.00, suggestPrice: 320.00, stock: 75, sales: 890, description: 'Responsive running shoes with Boost midsole technology' }
    ];
    
    let successCount = 0;
    for (const product of products) {
      try {
        await client.query(
          `INSERT INTO platform_product (name, category_id, brand, main_image, images, cost_price, suggest_price, stock, sales, description, status) 
           VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, 1)`,
          [product.name, product.categoryId, product.brand, product.mainImage, product.images, product.costPrice, product.suggestPrice, product.stock, product.sales, product.description]
        );
        console.log(`   ✅ 创建商品: ${product.name} - ${product.brand}`);
        successCount++;
      } catch (error) {
        console.log(`   ⚠️ 商品 ${product.name} 创建失败: ${error.message}`);
      }
    }
    
    // 8. 插入测试用户
    console.log('\n👤 插入测试用户...');
    const bcrypt = require('bcrypt');
    const hashedPassword = await bcrypt.hash('123456', 10);
    
    const users = [
      { username: 'user001', password: hashedPassword, nickname: 'Test User 1', email: 'user001@test.com', phone: '13800138001' },
      { username: 'user002', password: hashedPassword, nickname: 'Test User 2', email: 'user002@test.com', phone: '13800138002' },
      { username: 'user003', password: hashedPassword, nickname: 'Test User 3', email: 'user003@test.com', phone: '13800138003' }
    ];
    
    for (const user of users) {
      try {
        await client.query(
          'INSERT INTO "user" (username, password, nickname, email, phone, status) VALUES ($1, $2, $3, $4, $5, 1)',
          [user.username, user.password, user.nickname, user.email, user.phone]
        );
        console.log(`   ✅ 创建用户: ${user.username} - ${user.nickname}`);
      } catch (error) {
        console.log(`   ⚠️ 用户 ${user.username} 创建失败: ${error.message}`);
      }
    }
    
    // 9. 插入测试商家
    console.log('\n🏪 插入测试商家...');
    const merchants = [
      { username: 'merchant001', password: hashedPassword, merchantName: 'Apple Store', contactPhone: '13800138011', contactEmail: 'merchant001@test.com' },
      { username: 'merchant002', password: hashedPassword, merchantName: 'Nike Official', contactPhone: '13800138012', contactEmail: 'merchant002@test.com' },
      { username: 'merchant003', password: hashedPassword, merchantName: 'Sony Store', contactPhone: '13800138013', contactEmail: 'merchant003@test.com' }
    ];
    
    for (const merchant of merchants) {
      try {
        await client.query(
          'INSERT INTO merchant (username, password, merchant_name, contact_phone, contact_email, status) VALUES ($1, $2, $3, $4, $5, 1)',
          [merchant.username, merchant.password, merchant.merchantName, merchant.contactPhone, merchant.contactEmail]
        );
        console.log(`   ✅ 创建商家: ${merchant.username} - ${merchant.merchantName}`);
      } catch (error) {
        console.log(`   ⚠️ 商家 ${merchant.username} 创建失败: ${error.message}`);
      }
    }
    
    // 10. 检查最终结果
    console.log('\n📊 检查最终结果...');
    const categoryCount = await client.query('SELECT COUNT(*) as count FROM category');
    const productCount = await client.query('SELECT COUNT(*) as count FROM platform_product');
    const userCount = await client.query('SELECT COUNT(*) as count FROM "user"');
    const merchantCount = await client.query('SELECT COUNT(*) as count FROM merchant');
    
    console.log(`✅ 分类数量: ${categoryCount.rows[0].count}`);
    console.log(`✅ 商品数量: ${productCount.rows[0].count}`);
    console.log(`✅ 用户数量: ${userCount.rows[0].count}`);
    console.log(`✅ 商家数量: ${merchantCount.rows[0].count}`);
    console.log(`✅ 成功创建商品: ${successCount} 个`);
    
    console.log('\n🎉 数据库设置完成！');
    console.log('💡 现在可以测试管理后台的分类和商品管理功能了');
    
  } catch (error) {
    console.error('❌ 创建表和数据失败:', error.message);
  } finally {
    await client.end();
  }
}

createTablesAndData();
