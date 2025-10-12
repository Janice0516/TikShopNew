#!/usr/bin/env node

// 尝试不同的数据库连接方式
const { Client } = require('pg');

// 不同的连接配置
const connectionConfigs = [
  {
    name: '配置1: 标准SSL',
    config: {
      host: 'dpg-d0j8q8h2s78s73fq8hpg-a.oregon-postgres.render.com',
      port: 5432,
      user: 'tiktokshop_slkz_user',
      password: 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn',
      database: 'tiktokshop_slkz',
      ssl: { rejectUnauthorized: false },
      connectionTimeoutMillis: 10000,
    }
  },
  {
    name: '配置2: 强制SSL',
    config: {
      host: 'dpg-d0j8q8h2s78s73fq8hpg-a.oregon-postgres.render.com',
      port: 5432,
      user: 'tiktokshop_slkz_user',
      password: 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn',
      database: 'tiktokshop_slkz',
      ssl: true,
      connectionTimeoutMillis: 10000,
    }
  },
  {
    name: '配置3: 无SSL',
    config: {
      host: 'dpg-d0j8q8h2s78s73fq8hpg-a.oregon-postgres.render.com',
      port: 5432,
      user: 'tiktokshop_slkz_user',
      password: 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn',
      database: 'tiktokshop_slkz',
      ssl: false,
      connectionTimeoutMillis: 10000,
    }
  },
  {
    name: '配置4: 连接字符串',
    config: {
      connectionString: 'postgresql://tiktokshop_slkz_user:U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn@dpg-d0j8q8h2s78s73fq8hpg-a.oregon-postgres.render.com:5432/tiktokshop_slkz?sslmode=require',
      connectionTimeoutMillis: 10000,
    }
  }
];

async function testConnections() {
  console.log('🔍 测试不同的数据库连接配置...');
  
  for (const { name, config } of connectionConfigs) {
    console.log(`\n📡 测试 ${name}...`);
    
    const client = new Client(config);
    
    try {
      await client.connect();
      console.log(`✅ ${name} 连接成功！`);
      
      // 测试查询
      const result = await client.query('SELECT version()');
      console.log(`   📊 数据库版本: ${result.rows[0].version.split(' ')[0]}`);
      
      // 检查表是否存在
      const tablesResult = await client.query(`
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        ORDER BY table_name
      `);
      
      console.log(`   📋 现有表: ${tablesResult.rows.map(r => r.table_name).join(', ') || '无'}`);
      
      await client.end();
      
      // 如果连接成功，使用这个配置创建表
      console.log(`\n🎉 使用 ${name} 创建表和数据...`);
      await createTablesAndData(config);
      break;
      
    } catch (error) {
      console.log(`❌ ${name} 连接失败: ${error.message}`);
      try {
        await client.end();
      } catch (e) {
        // 忽略结束连接的错误
      }
    }
  }
}

async function createTablesAndData(config) {
  const client = new Client(config);
  
  try {
    await client.connect();
    
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
    
    // 插入商品数据
    console.log('🛍️ 插入商品数据...');
    const products = [
      { name: 'iPhone 15 Pro Max 256GB', categoryId: categoryMap['Smartphones'], brand: 'Apple', mainImage: '/static/products/iphone15pro.jpg', images: JSON.stringify(['/static/products/iphone15pro.jpg']), costPrice: 4500.00, suggestPrice: 4999.00, stock: 25, sales: 156, description: 'Latest iPhone with titanium design, A17 Pro chip, and advanced camera system' },
      { name: 'MacBook Pro M3 14-inch', categoryId: categoryMap['Laptops'], brand: 'Apple', mainImage: '/static/products/macbook-m3.jpg', images: JSON.stringify(['/static/products/macbook-m3.jpg']), costPrice: 7500.00, suggestPrice: 7999.00, stock: 15, sales: 89, description: 'Powerful laptop with M3 chip, perfect for professionals and creators' },
      { name: 'AirPods Pro 2nd Gen', categoryId: categoryMap['Audio'], brand: 'Apple', mainImage: '/static/products/airpods-pro2.jpg', images: JSON.stringify(['/static/products/airpods-pro2.jpg']), costPrice: 800.00, suggestPrice: 899.00, stock: 50, sales: 445, description: 'Wireless earbuds with active noise cancellation and spatial audio' },
      { name: 'Samsung Galaxy S24 Ultra', categoryId: categoryMap['Smartphones'], brand: 'Samsung', mainImage: '/static/products/galaxy-s24.jpg', images: JSON.stringify(['/static/products/galaxy-s24.jpg']), costPrice: 4000.00, suggestPrice: 4299.00, stock: 20, sales: 234, description: 'Premium Android smartphone with S Pen and advanced AI features' },
      { name: 'Nike Air Max 270', categoryId: categoryMap['Shoes'], brand: 'Nike', mainImage: '/static/products/nike-airmax.jpg', images: JSON.stringify(['/static/products/nike-airmax.jpg']), costPrice: 350.00, suggestPrice: 399.00, stock: 100, sales: 1200, description: 'Comfortable running shoes with Max Air cushioning' }
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
    
    console.log('🎉 数据库设置完成！');
    
  } catch (error) {
    console.error('❌ 创建表和数据失败:', error.message);
  } finally {
    await client.end();
  }
}

testConnections();
