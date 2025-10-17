const { Client } = require('pg');

// 使用可用的数据库连接配置
const workingConfig = {
  host: 'dpg-d3kgpsd6ubrc73dvbjm0-a.oregon-postgres.render.com',
  port: 5432,
  user: 'tiktokshop_slkz_user',
  password: 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn',
  database: 'tiktokshop_slkz',
  ssl: { rejectUnauthorized: false },
  connectionTimeoutMillis: 15000
};

async function assignProductsToMerchants() {
  const client = new Client(workingConfig);
  
  try {
    console.log('🔌 连接到Render PostgreSQL数据库...');
    await client.connect();
    console.log('✅ 数据库连接成功');

    // 1. 获取所有商家
    console.log('\n👥 获取商家列表...');
    const merchants = await client.query('SELECT id, username, merchant_name FROM merchant ORDER BY id');
    console.log(`📊 找到 ${merchants.rows.length} 个商家:`);
    merchants.rows.forEach(merchant => {
      console.log(`  ${merchant.id}. ${merchant.username} - ${merchant.merchant_name}`);
    });

    // 2. 获取所有产品并按分类分组
    console.log('\n📦 获取产品列表并按分类分组...');
    const products = await client.query(`
      SELECT p.id, p.name, p.brand, p.category_id, p.suggest_price, c.name as category_name
      FROM platform_product p
      LEFT JOIN category c ON p.category_id = c.id
      ORDER BY p.category_id, p.id
    `);
    
    console.log(`📊 找到 ${products.rows.length} 个产品`);

    // 按分类分组产品
    const productsByCategory = products.rows.reduce((acc, product) => {
      const categoryId = product.category_id;
      if (!acc[categoryId]) {
        acc[categoryId] = {
          categoryName: product.category_name || `分类${categoryId}`,
          products: []
        };
      }
      acc[categoryId].products.push(product);
      return acc;
    }, {});

    console.log('\n📂 产品分类统计:');
    Object.entries(productsByCategory).forEach(([categoryId, data]) => {
      console.log(`  📂 ${data.categoryName}: ${data.products.length} 个产品`);
    });

    // 3. 检查merchant_product表是否存在
    const tableCheck = await client.query(`
      SELECT EXISTS (
        SELECT FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_name = 'merchant_product'
      );
    `);

    if (!tableCheck.rows[0].exists) {
      console.log('\n📋 创建merchant_product表...');
      await client.query(`
        CREATE TABLE IF NOT EXISTS merchant_product (
          id BIGSERIAL PRIMARY KEY,
          merchant_id BIGINT NOT NULL,
          product_id BIGINT NOT NULL,
          sale_price DECIMAL(10,2) NOT NULL,
          profit_margin DECIMAL(5,2) DEFAULT 0,
          status SMALLINT DEFAULT 1,
          sales INTEGER DEFAULT 0,
          create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          UNIQUE(merchant_id, product_id)
        );
      `);
      console.log('✅ merchant_product表创建成功');
    }

    // 4. 为商家分配产品（按分类分配）
    console.log('\n🏪 开始为商家分配产品...');
    
    // 商家分类分配策略
    const merchantCategoryAssignment = {
      1: [2, 3, 4], // merchant001: 电子产品、食品饮料、美妆个护
      2: [1, 5, 6], // merchant002: 服装鞋包、家居生活、运动户外
      3: [7, 8, 9, 10] // merchant003: 母婴用品、汽车用品、图书文具、宠物用品
    };

    let totalAssigned = 0;
    let totalFailed = 0;

    for (const merchant of merchants.rows) {
      const merchantId = merchant.id;
      const assignedCategories = merchantCategoryAssignment[merchantId] || [];
      
      console.log(`\n🏪 为商家 ${merchant.username} (${merchant.merchant_name}) 分配产品...`);
      console.log(`📂 分配分类: ${assignedCategories.map(catId => productsByCategory[catId]?.categoryName || `分类${catId}`).join(', ')}`);
      
      let merchantAssigned = 0;
      let merchantFailed = 0;

      for (const categoryId of assignedCategories) {
        const categoryData = productsByCategory[categoryId];
        if (!categoryData) continue;

        console.log(`  📂 处理分类: ${categoryData.categoryName} (${categoryData.products.length} 个产品)`);

        for (const product of categoryData.products) {
          try {
            // 计算销售价格（建议售价的90%-110%之间）
            const basePrice = parseFloat(product.suggest_price);
            const salePrice = Math.round((basePrice * (0.9 + Math.random() * 0.2)) * 100) / 100;
            
            // 计算利润率
            const costPrice = basePrice * 0.7; // 假设成本价是建议售价的70%
            const profitMargin = ((salePrice - costPrice) / salePrice * 100);
            
            // 插入商家产品记录
            await client.query(`
              INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales)
              VALUES ($1, $2, $3, $4, 1, $5)
              ON CONFLICT (merchant_id, product_id) DO NOTHING
            `, [
              merchantId, 
              product.id, 
              salePrice, 
              Math.round(profitMargin * 100) / 100,
              Math.floor(Math.random() * 50) // 随机销量
            ]);
            
            merchantAssigned++;
            console.log(`    ✅ ${product.name} - RM${salePrice} (利润率: ${Math.round(profitMargin * 100) / 100}%)`);
            
          } catch (error) {
            merchantFailed++;
            console.log(`    ❌ ${product.name} - ${error.message}`);
          }
          
          // 避免请求过快
          await new Promise(resolve => setTimeout(resolve, 50));
        }
      }
      
      console.log(`  📊 商家 ${merchant.username} 分配完成: 成功 ${merchantAssigned} 个，失败 ${merchantFailed} 个`);
      totalAssigned += merchantAssigned;
      totalFailed += merchantFailed;
    }

    console.log(`\n📊 产品分配完成！`);
    console.log(`✅ 总成功: ${totalAssigned} 个`);
    console.log(`❌ 总失败: ${totalFailed} 个`);

    // 5. 验证分配结果
    console.log('\n🔍 验证分配结果...');
    
    for (const merchant of merchants.rows) {
      const merchantProducts = await client.query(`
        SELECT COUNT(*) as count, 
               AVG(sale_price) as avg_price,
               AVG(profit_margin) as avg_margin
        FROM merchant_product 
        WHERE merchant_id = $1
      `, [merchant.id]);
      
      const stats = merchantProducts.rows[0];
      console.log(`🏪 ${merchant.username}: ${stats.count} 个产品，平均价格 RM${Math.round(stats.avg_price * 100) / 100}，平均利润率 ${Math.round(stats.avg_margin * 100) / 100}%`);
    }

    // 6. 显示每个商家的产品示例
    console.log('\n📋 商家产品示例:');
    for (const merchant of merchants.rows) {
      const sampleProducts = await client.query(`
        SELECT mp.sale_price, p.name, p.brand, c.name as category_name
        FROM merchant_product mp
        JOIN platform_product p ON mp.product_id = p.id
        LEFT JOIN category c ON p.category_id = c.id
        WHERE mp.merchant_id = $1
        ORDER BY mp.id DESC
        LIMIT 5
      `, [merchant.id]);
      
      console.log(`\n🏪 ${merchant.username} (${merchant.merchant_name}):`);
      sampleProducts.rows.forEach(product => {
        console.log(`  ✅ ${product.name} - ${product.brand} - ${product.category_name} - RM${product.sale_price}`);
      });
    }

  } catch (error) {
    console.error('❌ 分配失败:', error.message);
    console.error('错误详情:', error);
  } finally {
    await client.end();
    console.log('\n🔌 数据库连接已关闭');
  }
}

assignProductsToMerchants();
