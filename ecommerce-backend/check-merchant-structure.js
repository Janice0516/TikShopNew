const { Client } = require('pg');

async function checkStructure() {
  const client = new Client({
    connectionString: 'postgresql://tiktokshop_slkz_user:U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn@dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com/tiktokshop_slkz',
    ssl: { rejectUnauthorized: false }
  });

  try {
    await client.connect();
    console.log('✅ 数据库连接成功\n');

    // 检查merchant表结构
    console.log('1. 检查merchant表结构...');
    const structure = await client.query(`
      SELECT column_name, data_type, is_nullable, column_default
      FROM information_schema.columns 
      WHERE table_name = 'merchant' 
      ORDER BY ordinal_position
    `);
    
    console.log('   merchant表字段:');
    structure.rows.forEach(row => {
      console.log(`   - ${row.column_name}: ${row.data_type} (nullable: ${row.is_nullable})`);
    });

    // 检查merchant表数据
    console.log('\n2. 检查merchant表数据...');
    const data = await client.query('SELECT * FROM merchant LIMIT 3');
    console.log(`   数据行数: ${data.rows.length}`);
    if (data.rows.length > 0) {
      console.log('   示例数据:', data.rows[0]);
    }

  } catch (error) {
    console.error('❌ 错误:', error.message);
  } finally {
    try { await client.end(); } catch {}
  }
}

checkStructure();
