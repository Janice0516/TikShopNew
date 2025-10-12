const { Client } = require('pg');

async function checkWithdrawalTables() {
  const client = new Client({
    connectionString: 'postgresql://tiktokshop_slkz_user:U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn@dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com/tiktokshop_slkz',
    ssl: { rejectUnauthorized: false }
  });

  try {
    await client.connect();
    console.log('✅ 数据库连接成功\n');

    // 检查merchant_withdrawal表
    console.log('1. 检查merchant_withdrawal表...');
    try {
      const withdrawalStructure = await client.query(`
        SELECT column_name, data_type, is_nullable
        FROM information_schema.columns 
        WHERE table_name = 'merchant_withdrawal' 
        ORDER BY ordinal_position
      `);
      
      console.log('   merchant_withdrawal表字段:');
      withdrawalStructure.rows.forEach(row => {
        console.log(`   - ${row.column_name}: ${row.data_type}`);
      });

      const withdrawalData = await client.query('SELECT COUNT(*) FROM merchant_withdrawal');
      console.log(`   数据行数: ${withdrawalData.rows[0].count}`);
    } catch (error) {
      console.log('   ❌ merchant_withdrawal表不存在或有问题:', error.message);
    }

    // 检查merchant_recharge表
    console.log('\n2. 检查merchant_recharge表...');
    try {
      const rechargeStructure = await client.query(`
        SELECT column_name, data_type, is_nullable
        FROM information_schema.columns 
        WHERE table_name = 'merchant_recharge' 
        ORDER BY ordinal_position
      `);
      
      console.log('   merchant_recharge表字段:');
      rechargeStructure.rows.forEach(row => {
        console.log(`   - ${row.column_name}: ${row.data_type}`);
      });

      const rechargeData = await client.query('SELECT COUNT(*) FROM merchant_recharge');
      console.log(`   数据行数: ${rechargeData.rows[0].count}`);
    } catch (error) {
      console.log('   ❌ merchant_recharge表不存在或有问题:', error.message);
    }

  } catch (error) {
    console.error('❌ 错误:', error.message);
  } finally {
    try { await client.end(); } catch {}
  }
}

checkWithdrawalTables();
