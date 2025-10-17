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

async function assignProductsToMoreMerchants() {
  const client = new Client(workingConfig);
  
  try {
    console.log('🔌 连接到Render PostgreSQL数据库...');
    await client.connect();
    console.log('✅ 数据库连接成功');

    // 1. 获取所有商家
    console.log('\n👥 获取商家列表...');
    const merchants = await client.query('SELECT id, username, merchant_name FROM merchant ORDER BY id');
    console.log(`📊 找到 ${merchants.rows.length} 个商家`);

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

    // 3. 为更多商家分配产品（按分类分配）
    console.log('\n🏪 开始为更多商家分配产品...');
    
    // 扩展商家分类分配策略
    const merchantCategoryAssignment = {
      1: [2, 3, 4], // merchant001: 电子产品、食品饮料、美妆个护
      2: [1, 5, 6], // merchant002: 服装鞋包、家居生活、运动户外
      3: [7, 8, 9, 10], // merchant003: 母婴用品、汽车用品、图书文具、宠物用品
      4: [11, 12, 13], // merchant004: 智能手机、笔记本电脑、音频设备
      5: [1, 2], // merchant005: 服装鞋包、电子产品
      6: [5, 6], // merchant006: 家居生活、运动户外
      8: [4, 5], // merchant008: 美妆个护、家居生活
      9: [9, 10], // merchant009: 图书文具、宠物用品
      10: [8, 11], // merchant010: 汽车用品、智能手机
      11: [10, 7], // merchant011: 宠物用品、母婴用品
      12: [3, 5], // merchant012: 食品饮料、家居生活
      13: [4, 6], // merchant013: 美妆个护、运动户外
      14: [7, 9], // merchant014: 母婴用品、图书文具
      15: [9, 13], // merchant015: 图书文具、相机设备
      16: [1, 2], // merchant016: 服装鞋包、电子产品
      18: [12, 13], // merchant018: 音频设备、相机设备
      19: [9, 5], // merchant019: 图书文具、家居生活
      20: [6, 8], // merchant020: 运动户外、汽车用品
      21: [7, 4], // merchant021: 母婴用品、美妆个护
      22: [4, 5], // merchant022: 美妆个护、家居生活
      23: [11, 12], // merchant023: 智能手机、音频设备
      24: [13, 6], // merchant024: 相机设备、运动户外
      25: [6, 4], // merchant025: 运动户外、美妆个护
      26: [5, 3], // merchant026: 家居生活、食品饮料
      28: [5, 4], // merchant028: 家居生活、美妆个护
      29: [5, 6], // merchant029: 家居生活、运动户外
      30: [5, 1], // merchant030: 家居生活、服装鞋包
      31: [11, 5], // merchant031: 智能手机、家居生活
      32: [11, 12], // merchant032: 智能手机、音频设备
      33: [11, 13] // merchant033: 智能手机、相机设备
    };

    let totalAssigned = 0;
    let totalFailed = 0;

    for (const merchant of merchants.rows) {
      const merchantId = merchant.id;
      const assignedCategories = merchantCategoryAssignment[merchantId] || [];
      
      if (assignedCategories.length === 0) {
        console.log(`⚠️  商家 ${merchant.username} (${merchant.merchant_name}) 没有分配分类，跳过`);
        continue;
      }
      
      console.log(`\n🏪 为商家 ${merchant.username} (${merchant.merchant_name}) 分配产品...`);
      console.log(`📂 分配分类: ${assignedCategories.map(catId => productsByCategory[catId]?.categoryName || `分类${catId}`).join(', ')}`);
      
      let merchantAssigned = 0;
      let merchantFailed = 0;

      for (const categoryId of assignedCategories) {
        const categoryData = productsByCategory[categoryId];
        if (!categoryData) {
          console.log(`  ⚠️  分类 ${categoryId} 不存在，跳过`);
          continue;
        }

        console.log(`  📂 处理分类: ${categoryData.categoryName} (${categoryData.products.length} 个产品)`);

        // 为每个分类随机选择3-8个产品
        const productsToAssign = categoryData.products.sort(() => 0.5 - Math.random()).slice(0, Math.min(5, categoryData.products.length));

        for (const product of productsToAssign) {
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

    // 4. 验证分配结果
    console.log('\n🔍 验证分配结果...');
    
    const merchantsWithProducts = await client.query(`
      SELECT m.username, m.merchant_name, COUNT(mp.id) as product_count, 
             AVG(mp.sale_price) as avg_price,
             AVG(mp.profit_margin) as avg_margin
      FROM merchant m
      LEFT JOIN merchant_product mp ON m.id = mp.merchant_id
      GROUP BY m.id, m.username, m.merchant_name
      HAVING COUNT(mp.id) > 0
      ORDER BY product_count DESC
    `);
    
    console.log(`\n📊 有产品的商家统计:`);
    merchantsWithProducts.rows.forEach(merchant => {
      console.log(`🏪 ${merchant.username}: ${merchant.product_count} 个产品，平均价格 RM${Math.round(merchant.avg_price * 100) / 100}，平均利润率 ${Math.round(merchant.avg_margin * 100) / 100}%`);
    });

    // 5. 显示每个商家的产品示例
    console.log('\n📋 商家产品示例:');
    for (const merchant of merchantsWithProducts.rows.slice(0, 10)) { // 只显示前10个商家
      const sampleProducts = await client.query(`
        SELECT mp.sale_price, p.name, p.brand, c.name as category_name
        FROM merchant_product mp
        JOIN platform_product p ON mp.product_id = p.id
        LEFT JOIN category c ON p.category_id = c.id
        WHERE mp.merchant_id = (SELECT id FROM merchant WHERE username = $1)
        ORDER BY mp.id DESC
        LIMIT 3
      `, [merchant.username]);
      
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

assignProductsToMoreMerchants();
