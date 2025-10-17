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

async function uploadDailyProducts() {
  const client = new Client(workingConfig);
  
  try {
    console.log('🔌 连接到Render PostgreSQL数据库...');
    await client.connect();
    console.log('✅ 数据库连接成功');

    // 检查当前产品数量
    const currentCount = await client.query('SELECT COUNT(*) FROM platform_product');
    console.log(`📊 当前数据库中有 ${currentCount.rows[0].count} 个产品`);

    // 读取SQL文件
    const fs = require('fs');
    
    console.log('\n📦 开始插入32个马来西亚生活用品...');
    const sqlContent = fs.readFileSync('daily-products.sql', 'utf8');
    const sqlStatements = sqlContent.split('\n').filter(line => 
      line.trim() && !line.trim().startsWith('--') && line.trim().startsWith('INSERT')
    );
    
    let successCount = 0;
    let failCount = 0;
    
    for (let i = 0; i < sqlStatements.length; i++) {
      const sql = sqlStatements[i];
      try {
        await client.query(sql);
        successCount++;
        
        // 提取产品名称
        const nameMatch = sql.match(/VALUES \('([^']+)'/);
        const productName = nameMatch ? nameMatch[1] : `产品 ${i + 1}`;
        console.log(`✅ 产品创建成功: ${productName}`);
        
      } catch (error) {
        failCount++;
        if (error.message.includes('duplicate key') || error.message.includes('already exists')) {
          console.log(`⚠️  产品已存在: ${error.message.split('"')[1] || `产品 ${i + 1}`}`);
        } else {
          console.log(`❌ 产品创建失败: ${error.message}`);
        }
      }
      
      // 避免请求过快
      await new Promise(resolve => setTimeout(resolve, 100));
    }

    console.log(`\n📊 生活用品插入完成！`);
    console.log(`✅ 成功: ${successCount} 个`);
    console.log(`❌ 失败: ${failCount} 个`);

    // 验证最终结果
    const finalCount = await client.query('SELECT COUNT(*) FROM platform_product');
    console.log(`\n🎉 上传完成！数据库中总共有 ${finalCount.rows[0].count} 个产品`);
    
    // 显示一些新添加的生活用品
    const newProducts = await client.query(`
      SELECT name, brand, suggest_price 
      FROM platform_product 
      WHERE name ILIKE '%milo%' OR name ILIKE '%nescafe%' OR name ILIKE '%maggi%' OR name ILIKE '%dettol%'
      ORDER BY id DESC 
      LIMIT 10
    `);
    
    console.log('\n📋 新添加的生活用品示例:');
    newProducts.rows.forEach(product => {
      console.log(`  ✅ ${product.name} - ${product.brand} - RM${product.suggest_price}`);
    });

    // 按分类统计
    const categoryStats = await client.query(`
      SELECT c.name as category_name, COUNT(p.id) as product_count 
      FROM platform_product p 
      LEFT JOIN category c ON p.category_id = c.id 
      GROUP BY c.name 
      ORDER BY product_count DESC
    `);
    
    console.log('\n📊 按分类统计:');
    categoryStats.rows.forEach(stat => {
      console.log(`  📂 ${stat.category_name || '未分类'}: ${stat.product_count} 个产品`);
    });

  } catch (error) {
    console.error('❌ 上传失败:', error.message);
    console.error('错误详情:', error);
  } finally {
    await client.end();
    console.log('\n🔌 数据库连接已关闭');
  }
}

uploadDailyProducts();
