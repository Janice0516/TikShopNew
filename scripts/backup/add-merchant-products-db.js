const { Client } = require('pg');

const client = new Client({
  host: 'dpg-d0j8b8k2o3jss73a8qkg-a.singapore-postgres.render.com',
  port: 5432,
  database: 'tiktokshop_slkz',
  user: 'tiktokshop_slkz_user',
  password: 'V8QZqJQZqJQZqJQZqJQZqJQZqJQZqJQZ',
  ssl: { rejectUnauthorized: false }
});

async function addMerchantProducts() {
  try {
    await client.connect();
    console.log('✅ 数据库连接成功');

    // 检查merchant_product表是否存在
    const tableCheck = await client.query(`
      SELECT EXISTS (
        SELECT FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_name = 'merchant_product'
      );
    `);

    if (!tableCheck.rows[0].exists) {
      console.log('📋 创建merchant_product表...');
      await client.query(`
        CREATE TABLE merchant_product (
          id BIGSERIAL PRIMARY KEY,
          merchant_id BIGINT NOT NULL,
          product_id BIGINT NOT NULL,
          sale_price DECIMAL(10,2) NOT NULL,
          profit_margin DECIMAL(5,2) DEFAULT 0,
          status SMALLINT DEFAULT 1,
          create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          UNIQUE(merchant_id, product_id)
        );
      `);
      console.log('✅ merchant_product表创建成功');
    }

    // 获取商家列表
    const merchants = await client.query('SELECT id, username FROM merchant ORDER BY id LIMIT 3');
    console.log(`📊 找到 ${merchants.rows.length} 个商家`);

    // 获取产品列表
    const products = await client.query('SELECT id, name, cost_price, suggest_price FROM product ORDER BY id LIMIT 10');
    console.log(`📦 找到 ${products.rows.length} 个产品`);

    // 为每个商家上架产品
    for (const merchant of merchants.rows) {
      console.log(`\n🏪 为商家 ${merchant.username} (ID: ${merchant.id}) 上架产品...`);
      
      let successCount = 0;
      
      for (const product of products.rows) {
        try {
          // 计算销售价格（建议售价的90%-110%之间）
          const basePrice = parseFloat(product.suggest_price) || parseFloat(product.cost_price);
          const salePrice = Math.round((basePrice * (0.9 + Math.random() * 0.2)) * 100) / 100;
          
          // 计算利润率
          const profitMargin = ((salePrice - parseFloat(product.cost_price)) / salePrice * 100);
          
          // 插入商家产品记录
          await client.query(`
            INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status)
            VALUES ($1, $2, $3, $4, 1)
            ON CONFLICT (merchant_id, product_id) DO NOTHING
          `, [merchant.id, product.id, salePrice, profitMargin]);
          
          successCount++;
          console.log(`  ✅ 上架产品: ${product.name} - RM${salePrice} (利润率: ${profitMargin.toFixed(1)}%)`);
          
        } catch (error) {
          console.log(`  ⚠️  跳过产品: ${product.name} - ${error.message}`);
        }
      }
      
      console.log(`📊 商家 ${merchant.username} 成功上架 ${successCount} 个产品`);
    }

    // 验证结果
    const totalMerchantProducts = await client.query('SELECT COUNT(*) FROM merchant_product');
    console.log(`\n🎉 总共上架了 ${totalMerchantProducts.rows[0].count} 个商家产品`);

  } catch (error) {
    console.error('❌ 执行失败:', error.message);
  } finally {
    await client.end();
  }
}

addMerchantProducts();
