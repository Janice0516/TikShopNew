const { Client } = require('pg');

// Render数据库连接配置
const renderDbConfig = {
  host: 'dpg-d0j8b8k2o3jss73a8qkg-a.singapore-postgres.render.com',
  port: 5432,
  database: 'tiktokshop_slkz',
  user: 'tiktokshop_slkz_user',
  password: 'V8QZqJQZqJQZqJQZqJQZqJQZqJQZqJQZ',
  ssl: { rejectUnauthorized: false },
  connectionTimeoutMillis: 30000,
  idleTimeoutMillis: 30000,
  query_timeout: 30000
};

async function uploadProductsToRender() {
  const client = new Client(renderDbConfig);
  
  try {
    console.log('🔌 连接到Render PostgreSQL数据库...');
    await client.connect();
    console.log('✅ 数据库连接成功');

    // 检查当前产品数量
    const currentCount = await client.query('SELECT COUNT(*) FROM platform_product');
    console.log(`📊 当前数据库中有 ${currentCount.rows[0].count} 个产品`);

    // 读取SQL文件
    const fs = require('fs');
    
    // 1. 插入40个原始产品
    console.log('\n📦 开始插入40个原始产品...');
    const originalSql = fs.readFileSync('create-40-products.sql', 'utf8');
    const originalStatements = originalSql.split('\n').filter(line => 
      line.trim() && !line.trim().startsWith('--') && line.trim().startsWith('INSERT')
    );
    
    let successCount = 0;
    let failCount = 0;
    
    for (let i = 0; i < originalStatements.length; i++) {
      const sql = originalStatements[i];
      try {
        await client.query(sql);
        successCount++;
        
        // 提取产品名称
        const nameMatch = sql.match(/VALUES \('([^']+)'/);
        const productName = nameMatch ? nameMatch[1] : `产品 ${i + 1}`;
        console.log(`✅ 产品创建成功: ${productName}`);
        
      } catch (error) {
        failCount++;
        if (error.message.includes('duplicate key')) {
          console.log(`⚠️  产品已存在: ${error.message.split('"')[1] || `产品 ${i + 1}`}`);
        } else {
          console.log(`❌ 产品创建失败: ${error.message}`);
        }
      }
    }

    console.log(`\n📊 原始产品插入完成！`);
    console.log(`✅ 成功: ${successCount} 个`);
    console.log(`❌ 失败: ${failCount} 个`);

    // 2. 插入32个产品变体
    console.log('\n🔄 开始插入32个产品变体...');
    const variantsSql = fs.readFileSync('duplicated-products.sql', 'utf8');
    const variantStatements = variantsSql.split('\n').filter(line => 
      line.trim() && !line.trim().startsWith('--') && line.trim().startsWith('INSERT')
    );
    
    let variantSuccessCount = 0;
    let variantFailCount = 0;
    
    for (let i = 0; i < variantStatements.length; i++) {
      const sql = variantStatements[i];
      try {
        await client.query(sql);
        variantSuccessCount++;
        
        // 提取产品名称
        const nameMatch = sql.match(/VALUES \('([^']+)'/);
        const productName = nameMatch ? nameMatch[1] : `变体 ${i + 1}`;
        console.log(`✅ 变体创建成功: ${productName}`);
        
      } catch (error) {
        variantFailCount++;
        if (error.message.includes('duplicate key')) {
          console.log(`⚠️  变体已存在: ${error.message.split('"')[1] || `变体 ${i + 1}`}`);
        } else {
          console.log(`❌ 变体创建失败: ${error.message}`);
        }
      }
    }

    console.log(`\n📊 产品变体插入完成！`);
    console.log(`✅ 成功: ${variantSuccessCount} 个`);
    console.log(`❌ 失败: ${variantFailCount} 个`);

    // 验证最终结果
    const finalCount = await client.query('SELECT COUNT(*) FROM platform_product');
    console.log(`\n🎉 上传完成！数据库中总共有 ${finalCount.rows[0].count} 个产品`);
    
    // 显示一些示例产品
    const sampleProducts = await client.query('SELECT name, brand, suggest_price FROM platform_product ORDER BY id DESC LIMIT 10');
    console.log('\n📋 最新添加的产品示例:');
    sampleProducts.rows.forEach(product => {
      console.log(`  ✅ ${product.name} - ${product.brand} - RM${product.suggest_price}`);
    });

  } catch (error) {
    console.error('❌ 上传失败:', error.message);
    console.error('错误详情:', error);
  } finally {
    await client.end();
    console.log('\n🔌 数据库连接已关闭');
  }
}

uploadProductsToRender();
