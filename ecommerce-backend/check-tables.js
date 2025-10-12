const { Client } = require('pg');

async function checkTables() {
  const client = new Client({
    connectionString: 'postgresql://tikshop_user:xNye4k92dtzXqa9rPkLRW04Au74ZK6Yv@dpg-ctatjh5u0jms738shh30-a.oregon-postgres.render.com/tikshop',
    ssl: { rejectUnauthorized: false }
  });

  try {
    await client.connect();
    console.log('✅ 数据库连接成功\n');

    // 查询所有表
    const result = await client.query(`
      SELECT table_name 
      FROM information_schema.tables 
      WHERE table_schema = 'public' 
      ORDER BY table_name
    `);
    
    console.log('📋 数据库中的表:');
    result.rows.forEach(row => {
      console.log(`   - ${row.table_name}`);
    });
    
    console.log(`\n总计: ${result.rows.length} 个表`);

  } catch (error) {
    console.error('❌ 错误:', error.message);
  } finally {
    await client.end();
  }
}

checkTables();
