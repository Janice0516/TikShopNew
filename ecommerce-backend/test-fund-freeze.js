const { Client } = require('pg');

async function testFundFreeze() {
  const client = new Client({
    connectionString: 'postgresql://tiktokshop_slkz_user:U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn@dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com/tiktokshop_slkz',
    ssl: { rejectUnauthorized: false },
    connectionTimeoutMillis: 10000
  });

  try {
    await client.connect();
    console.log('✅ 数据库连接成功\n');

    // 1. 检查表是否存在
    console.log('1. 检查资金冻结相关表...');
    const tables = await client.query(`
      SELECT table_name 
      FROM information_schema.tables 
      WHERE table_name IN ('fund_freeze_record', 'fund_transaction')
    `);
    console.log('   表列表:', tables.rows.map(row => row.table_name));

    // 2. 检查merchant表是否有frozen_amount字段
    console.log('\n2. 检查merchant表结构...');
    const merchantColumns = await client.query(`
      SELECT column_name, data_type 
      FROM information_schema.columns 
      WHERE table_name = 'merchant' AND column_name IN ('balance', 'frozen_amount')
    `);
    console.log('   merchant字段:', merchantColumns.rows);

    // 3. 检查order表是否有成本价和佣金字段
    console.log('\n3. 检查order表结构...');
    const orderColumns = await client.query(`
      SELECT column_name, data_type 
      FROM information_schema.columns 
      WHERE table_name = 'order' AND column_name IN ('cost_amount', 'merchant_profit', 'platform_profit')
    `);
    console.log('   order字段:', orderColumns.rows);

    // 4. 查看现有商家数据
    console.log('\n4. 查看商家资金情况...');
    const merchants = await client.query(`
      SELECT id, merchant_name, balance, frozen_amount 
      FROM merchant 
      LIMIT 3
    `);
    console.log('   商家资金:', merchants.rows);

    // 5. 查看现有订单数据
    console.log('\n5. 查看订单数据...');
    const orders = await client.query(`
      SELECT id, order_no, total_amount, cost_amount, merchant_profit, platform_profit
      FROM "order" 
      LIMIT 3
    `);
    console.log('   订单数据:', orders.rows);

    console.log('\n🎉 资金冻结系统检查完成！');

  } catch (error) {
    console.error('❌ 检查失败:', error);
  } finally {
    await client.end();
  }
}

testFundFreeze();
