const { Client } = require('pg');

// Render数据库连接配置
const client = new Client({
  host: 'dpg-d0j8b8k2o3jss73a8qkg-a.singapore-postgres.render.com',
  port: 5432,
  database: 'tiktokshop_slkz',
  user: 'tiktokshop_slkz_user',
  password: 'V8QZqJQZqJQZqJQZqJQZqJQZqJQZqJQZ',
  ssl: { rejectUnauthorized: false },
  connectionTimeoutMillis: 30000
});

async function executeProductsSQL() {
  try {
    await client.connect();
    console.log('✅ 数据库连接成功');

    // 读取SQL文件
    const fs = require('fs');
    const sqlContent = fs.readFileSync('create-40-products.sql', 'utf8');
    
    // 分割SQL语句
    const sqlStatements = sqlContent.split('\n').filter(line => 
      line.trim() && !line.trim().startsWith('--') && line.trim().startsWith('INSERT')
    );
    
    console.log(`📋 找到 ${sqlStatements.length} 条产品插入语句`);

    let successCount = 0;
    let failCount = 0;

    // 执行每个SQL语句
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
        console.log(`❌ 产品创建失败: ${error.message}`);
      }
    }

    console.log(`\n📊 产品创建完成！`);
    console.log(`✅ 成功: ${successCount} 个`);
    console.log(`❌ 失败: ${failCount} 个`);

    // 验证结果
    const result = await client.query('SELECT COUNT(*) FROM platform_product');
    console.log(`\n🎉 数据库中总共有 ${result.rows[0].count} 个产品`);

  } catch (error) {
    console.error('❌ 执行失败:', error.message);
  } finally {
    await client.end();
  }
}

executeProductsSQL();
