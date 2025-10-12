const { Client } = require('pg');

async function insertTestData() {
  const client = new Client({
    connectionString: 'postgresql://tiktokshop_slkz_user:U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn@dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com/tiktokshop_slkz',
    ssl: { rejectUnauthorized: false }
  });

  try {
    await client.connect();
    console.log('✅ 数据库连接成功\n');

    // 1. 插入提现数据
    console.log('1. 插入提现数据...');
    await client.query(`
      INSERT INTO merchant_withdrawal (merchant_id, withdrawal_amount, bank_name, bank_account, account_holder, status, remark) 
      VALUES 
        (1, 1000.00, 'Maybank', '1234567890', 'Apple Store Owner', 0, '测试提现申请1'),
        (1, 2000.00, 'CIMB Bank', '0987654321', 'Apple Store Owner', 1, '测试提现申请2'),
        (2, 1500.00, 'Public Bank', '1122334455', 'Samsung Store Owner', 0, '测试提现申请3'),
        (2, 3000.00, 'RHB Bank', '5566778899', 'Samsung Store Owner', 2, '测试提现申请4'),
        (3, 2500.00, 'Hong Leong Bank', '9988776655', 'Huawei Store Owner', 0, '测试提现申请5')
    `);
    console.log('   ✅ 提现数据插入成功');

    // 2. 插入充值数据
    console.log('\n2. 插入充值数据...');
    await client.query(`
      INSERT INTO merchant_recharge (merchant_id, recharge_amount, payment_method, payment_reference, status) 
      VALUES 
        (1, 5000.00, 'Bank Transfer', 'TXN001', 1),
        (2, 3000.00, 'Online Banking', 'TXN002', 1),
        (3, 4000.00, 'Credit Card', 'TXN003', 0)
    `);
    console.log('   ✅ 充值数据插入成功');

    // 3. 验证数据
    console.log('\n3. 验证数据...');
    const withdrawalCount = await client.query('SELECT COUNT(*) FROM merchant_withdrawal');
    const rechargeCount = await client.query('SELECT COUNT(*) FROM merchant_recharge');
    
    console.log(`   📊 提现记录数量: ${withdrawalCount.rows[0].count}`);
    console.log(`   📊 充值记录数量: ${rechargeCount.rows[0].count}`);

    // 4. 显示提现数据样本
    console.log('\n4. 提现数据样本:');
    const withdrawalSample = await client.query(`
      SELECT w.*, m.merchant_name 
      FROM merchant_withdrawal w 
      LEFT JOIN merchant m ON w.merchant_id = m.id 
      LIMIT 3
    `);
    
    withdrawalSample.rows.forEach((row, index) => {
      console.log(`   ${index + 1}. ${row.merchant_name}: ${row.withdrawal_amount} (状态: ${row.status})`);
    });

    console.log('\n🎉 测试数据插入完成！');

  } catch (error) {
    console.error('❌ 错误:', error.message);
  } finally {
    try { await client.end(); } catch {}
  }
}

insertTestData();
