const { Client } = require('pg');

async function checkDatabaseName() {
  console.log('🔍 检查正确的数据库名称\n');

  // 尝试不同的数据库名称
  const possibleNames = [
    'tikshop_slkz',
    'tiktokshop_slkz', 
    'tikshop',
    'tiktokshop'
  ];

  for (const dbName of possibleNames) {
    console.log(`测试数据库名称: ${dbName}`);
    
    const client = new Client({
      host: 'dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com',
      port: 5432,
      user: 'tiktokshop_slkz_user',
      password: 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn',
      database: dbName,
      ssl: { rejectUnauthorized: false },
      connectionTimeoutMillis: 5000
    });

    try {
      await client.connect();
      console.log(`   ✅ ${dbName} 连接成功`);
      
      // 检查表
      const tablesResult = await client.query(`
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        ORDER BY table_name
      `);
      
      console.log(`   📋 表数量: ${tablesResult.rows.length}`);
      console.log(`   📋 表列表: ${tablesResult.rows.map(r => r.table_name).join(', ')}`);
      
      await client.end();
      break; // 找到正确的数据库就停止
      
    } catch (error) {
      console.log(`   ❌ ${dbName} 连接失败: ${error.message}`);
      try { await client.end(); } catch {}
    }
  }
}

checkDatabaseName();
