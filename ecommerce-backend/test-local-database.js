#!/usr/bin/env node

// 临时解决方案：使用本地数据库进行测试
const { Client } = require('pg');

// 本地数据库配置（如果用户有本地PostgreSQL）
const localDbConfig = {
  host: 'localhost',
  port: 5432,
  user: 'postgres',
  password: 'password', // 用户需要修改为实际密码
  database: 'tiktokshop_local',
  ssl: false,
  connectionTimeoutMillis: 5000,
};

async function testLocalDatabase() {
  console.log('🔍 测试本地数据库连接...');
  
  const client = new Client(localDbConfig);
  
  try {
    await client.connect();
    console.log('✅ 本地数据库连接成功！');
    
    // 创建数据库（如果不存在）
    await client.query('CREATE DATABASE tiktokshop_local');
    console.log('✅ 数据库创建成功');
    
    await client.end();
    
    // 重新连接到新数据库
    const newClient = new Client({
      ...localDbConfig,
      database: 'tiktokshop_local'
    });
    
    await newClient.connect();
    
    // 创建表和数据
    await createTablesAndData(newClient);
    
    await newClient.end();
    
  } catch (error) {
    console.log('❌ 本地数据库连接失败:', error.message);
    console.log('💡 请确保：');
    console.log('   1. PostgreSQL已安装并运行');
    console.log('   2. 用户名和密码正确');
    console.log('   3. 端口5432可用');
  }
}

async function createTablesAndData(client) {
  try {
    // 创建分类表
    console.log('📂 创建分类表...');
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
    
    // 创建商品表
    console.log('🛍️ 创建商品表...');
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
    
    // 插入分类数据
    console.log('📂 插入分类数据...');
    const categories = [
      { name: 'Electronics', parentId: 0, level: 1, sort: 1, status: 1 },
      { name: 'Fashion', parentId: 0, level: 1, sort: 2, status: 1 },
      { name: 'Home & Garden', parentId: 0, level: 1, sort: 3, status: 1 },
      { name: 'Sports & Outdoors', parentId: 0, level: 1, sort: 4, status: 1 },
      { name: 'Beauty & Health', parentId: 0, level: 1, sort: 5, status: 1 },
      { name: 'Books & Media', parentId: 0, level: 1, sort: 6, status: 1 },
      { name: 'Toys & Games', parentId: 0, level: 1, sort: 7, status: 1 },
      { name: 'Automotive', parentId: 0, level: 1, sort: 8, status: 1 }
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
    
    // 插入商品数据
    console.log('🛍️ 插入商品数据...');
    const products = [
      { name: 'iPhone 15 Pro Max 256GB', categoryId: categoryMap['Electronics'], brand: 'Apple', mainImage: '/static/products/iphone15pro.jpg', images: JSON.stringify(['/static/products/iphone15pro.jpg']), costPrice: 4500.00, suggestPrice: 4999.00, stock: 25, sales: 156, description: 'Latest iPhone with titanium design, A17 Pro chip, and advanced camera system' },
      { name: 'MacBook Pro M3 14-inch', categoryId: categoryMap['Electronics'], brand: 'Apple', mainImage: '/static/products/macbook-m3.jpg', images: JSON.stringify(['/static/products/macbook-m3.jpg']), costPrice: 7500.00, suggestPrice: 7999.00, stock: 15, sales: 89, description: 'Powerful laptop with M3 chip, perfect for professionals and creators' },
      { name: 'Nike Air Max 270', categoryId: categoryMap['Fashion'], brand: 'Nike', mainImage: '/static/products/nike-airmax.jpg', images: JSON.stringify(['/static/products/nike-airmax.jpg']), costPrice: 350.00, suggestPrice: 399.00, stock: 100, sales: 1200, description: 'Comfortable running shoes with Max Air cushioning' }
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
    
    // 检查最终结果
    console.log('📊 检查最终结果...');
    const categoryCount = await client.query('SELECT COUNT(*) as count FROM category');
    const productCount = await client.query('SELECT COUNT(*) as count FROM platform_product');
    
    console.log(`✅ 分类数量: ${categoryCount.rows[0].count}`);
    console.log(`✅ 商品数量: ${productCount.rows[0].count}`);
    console.log(`✅ 成功创建商品: ${successCount} 个`);
    
    console.log('🎉 本地数据库设置完成！');
    console.log('💡 现在可以修改后端配置使用本地数据库进行测试');
    
  } catch (error) {
    console.error('❌ 创建表和数据失败:', error.message);
  }
}

testLocalDatabase();
