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

async function deleteAppleProducts() {
  const client = new Client(workingConfig);
  
  try {
    console.log('🔌 连接到Render PostgreSQL数据库...');
    await client.connect();
    console.log('✅ 数据库连接成功');

    // 1. 查找所有Apple产品
    console.log('\n🔍 查找所有Apple产品...');
    const appleProducts = await client.query(`
      SELECT id, name, brand, suggest_price 
      FROM platform_product 
      WHERE brand ILIKE '%apple%' OR name ILIKE '%apple%' OR name ILIKE '%iphone%' OR name ILIKE '%ipad%' OR name ILIKE '%macbook%' OR name ILIKE '%airpods%'
      ORDER BY id
    `);
    
    console.log(`📱 找到 ${appleProducts.rows.length} 个Apple产品:`);
    appleProducts.rows.forEach((product, index) => {
      console.log(`  ${index + 1}. ${product.name} - ${product.brand} - RM${product.suggest_price}`);
    });

    if (appleProducts.rows.length === 0) {
      console.log('ℹ️  没有找到Apple产品');
      return;
    }

    // 2. 确认删除
    console.log(`\n⚠️  准备删除 ${appleProducts.rows.length} 个Apple产品`);
    console.log('🗑️  开始删除...');

    // 3. 删除Apple产品
    let deletedCount = 0;
    let failedCount = 0;

    for (const product of appleProducts.rows) {
      try {
        await client.query('DELETE FROM platform_product WHERE id = $1', [product.id]);
        deletedCount++;
        console.log(`✅ 已删除: ${product.name}`);
      } catch (error) {
        failedCount++;
        console.log(`❌ 删除失败: ${product.name} - ${error.message}`);
      }
    }

    console.log(`\n📊 删除完成！`);
    console.log(`✅ 成功删除: ${deletedCount} 个产品`);
    console.log(`❌ 删除失败: ${failedCount} 个产品`);

    // 4. 验证删除结果
    console.log('\n🔍 验证删除结果...');
    const remainingAppleProducts = await client.query(`
      SELECT COUNT(*) 
      FROM platform_product 
      WHERE brand ILIKE '%apple%' OR name ILIKE '%apple%' OR name ILIKE '%iphone%' OR name ILIKE '%ipad%' OR name ILIKE '%macbook%' OR name ILIKE '%airpods%'
    `);
    
    console.log(`📱 剩余Apple产品数量: ${remainingAppleProducts.rows[0].count}`);

    // 5. 显示当前总产品数
    const totalProducts = await client.query('SELECT COUNT(*) FROM platform_product');
    console.log(`📦 数据库中总产品数量: ${totalProducts.rows[0].count}`);

    // 6. 显示一些剩余产品示例
    const remainingProducts = await client.query(`
      SELECT name, brand, suggest_price 
      FROM platform_product 
      ORDER BY id DESC 
      LIMIT 10
    `);
    
    console.log('\n📋 剩余产品示例:');
    remainingProducts.rows.forEach(product => {
      console.log(`  ✅ ${product.name} - ${product.brand} - RM${product.suggest_price}`);
    });

  } catch (error) {
    console.error('❌ 删除失败:', error.message);
    console.error('错误详情:', error);
  } finally {
    await client.end();
    console.log('\n🔌 数据库连接已关闭');
  }
}

deleteAppleProducts();
