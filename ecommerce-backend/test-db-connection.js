const { Client } = require('pg');

async function testConnection() {
  console.log('🔍 测试数据库连接配置\n');

  // 测试新的数据库连接
  const newConfig = {
    host: 'dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com',
    port: 5432,
    user: 'tiktokshop_slkz_user',
    password: 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn',
    database: 'tikshop_slkz',
    ssl: { rejectUnauthorized: false },
    connectionTimeoutMillis: 10000
  };

  console.log('1. 测试新数据库连接...');
  console.log('   配置:', {
    host: newConfig.host,
    port: newConfig.port,
    user: newConfig.user,
    database: newConfig.database
  });

  const client = new Client(newConfig);

  try {
    await client.connect();
    console.log('   ✅ 连接成功');

    // 测试查询
    const result = await client.query('SELECT COUNT(*) FROM merchant');
    console.log(`   📊 商家数量: ${result.rows[0].count}`);

    const withdrawalResult = await client.query('SELECT COUNT(*) FROM merchant_withdrawal');
    console.log(`   📊 提现记录数量: ${withdrawalResult.rows[0].count}`);

  } catch (error) {
    console.log('   ❌ 连接失败:', error.message);
  } finally {
    try { await client.end(); } catch {}
  }

  // 测试环境变量格式
  console.log('\n2. 生成环境变量配置...');
  console.log('请在Render Dashboard中设置以下环境变量:');
  console.log('');
  console.log('DB_TYPE=postgres');
  console.log('DB_HOST=dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com');
  console.log('DB_PORT=5432');
  console.log('DB_USERNAME=tiktokshop_slkz_user');
  console.log('DB_PASSWORD=U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn');
  console.log('DB_DATABASE=tiktokshop_slkz');
  console.log('NODE_ENV=production');
}

testConnection();
