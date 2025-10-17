const { Client } = require('pg');

// 尝试不同的连接配置
const connectionConfigs = [
  {
    name: 'Render DB 1',
    config: {
      host: 'dpg-d0j8b8k2o3jss73a8qkg-a.singapore-postgres.render.com',
      port: 5432,
      database: 'tiktokshop_slkz',
      user: 'tiktokshop_slkz_user',
      password: 'V8QZqJQZqJQZqJQZqJQZqJQZqJQZqJQZ',
      ssl: { rejectUnauthorized: false },
      connectionTimeoutMillis: 10000
    }
  },
  {
    name: 'Render DB 2',
    config: {
      host: 'dpg-d3kgpsd6ubrc73dvbjm0-a.oregon-postgres.render.com',
      port: 5432,
      user: 'tiktokshop_slkz_user',
      password: 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn',
      database: 'tiktokshop_slkz',
      ssl: { rejectUnauthorized: false },
      connectionTimeoutMillis: 10000
    }
  },
  {
    name: 'Connection String',
    config: {
      connectionString: 'postgresql://tiktokshop_slkz_user:V8QZqJQZqJQZqJQZqJQZqJQZqJQZqJQZ@dpg-d0j8b8k2o3jss73a8qkg-a.singapore-postgres.render.com:5432/tiktokshop_slkz',
      ssl: { rejectUnauthorized: false },
      connectionTimeoutMillis: 10000
    }
  }
];

async function testConnections() {
  for (const { name, config } of connectionConfigs) {
    console.log(`\n🔌 测试连接: ${name}`);
    const client = new Client(config);
    
    try {
      await client.connect();
      console.log(`✅ ${name} 连接成功`);
      
      // 测试查询
      const result = await client.query('SELECT COUNT(*) FROM platform_product');
      console.log(`📊 产品数量: ${result.rows[0].count}`);
      
      await client.end();
      console.log(`🔌 ${name} 连接已关闭`);
      
      // 如果连接成功，使用这个配置
      console.log(`\n🎉 找到可用连接: ${name}`);
      return config;
      
    } catch (error) {
      console.log(`❌ ${name} 连接失败: ${error.message}`);
      try {
        await client.end();
      } catch (e) {
        // 忽略关闭错误
      }
    }
  }
  
  console.log('\n❌ 所有连接都失败了');
  return null;
}

testConnections();
