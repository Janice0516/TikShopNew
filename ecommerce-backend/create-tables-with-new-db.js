const { Client } = require('pg');

async function createTables() {
  const client = new Client({
    connectionString: 'postgresql://tiktokshop_slkz_user:U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn@dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com/tiktokshop_slkz',
    ssl: { rejectUnauthorized: false },
    connectionTimeoutMillis: 10000
  });

  try {
    await client.connect();
    console.log('✅ 数据库连接成功\n');

    // 1. 检查现有表
    console.log('1. 检查现有表...');
    const tablesResult = await client.query(`
      SELECT table_name 
      FROM information_schema.tables 
      WHERE table_schema = 'public' 
      ORDER BY table_name
    `);
    
    console.log('   现有表:', tablesResult.rows.map(r => r.table_name).join(', '));

    // 2. 创建merchant表
    console.log('\n2. 创建merchant表...');
    await client.query(`
      CREATE TABLE IF NOT EXISTS merchant (
        id BIGSERIAL PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        merchant_name VARCHAR(100) NOT NULL,
        contact_name VARCHAR(50),
        contact_phone VARCHAR(20),
        shop_name VARCHAR(100),
        address TEXT,
        business_license VARCHAR(100),
        status SMALLINT DEFAULT 1,
        balance DECIMAL(10,2) DEFAULT 0.00,
        frozen_amount DECIMAL(10,2) DEFAULT 0.00,
        total_income DECIMAL(10,2) DEFAULT 0.00,
        total_withdraw DECIMAL(10,2) DEFAULT 0.00,
        create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      )
    `);
    console.log('   ✅ merchant表创建成功');

    // 3. 创建merchant_withdrawal表
    console.log('\n3. 创建merchant_withdrawal表...');
    await client.query(`
      CREATE TABLE IF NOT EXISTS merchant_withdrawal (
        id BIGSERIAL PRIMARY KEY,
        merchant_id BIGINT NOT NULL,
        withdrawal_amount DECIMAL(10,2) NOT NULL,
        bank_name VARCHAR(100) NOT NULL,
        bank_account VARCHAR(50) NOT NULL,
        account_holder VARCHAR(50) NOT NULL,
        status SMALLINT DEFAULT 0,
        remark VARCHAR(500),
        admin_remark VARCHAR(500),
        processed_by BIGINT,
        processed_at TIMESTAMP,
        create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      )
    `);
    console.log('   ✅ merchant_withdrawal表创建成功');

    // 4. 创建merchant_recharge表
    console.log('\n4. 创建merchant_recharge表...');
    await client.query(`
      CREATE TABLE IF NOT EXISTS merchant_recharge (
        id BIGSERIAL PRIMARY KEY,
        merchant_id BIGINT NOT NULL,
        recharge_amount DECIMAL(10,2) NOT NULL,
        payment_method VARCHAR(50),
        payment_reference VARCHAR(100),
        status SMALLINT DEFAULT 0,
        admin_id BIGINT,
        admin_name VARCHAR(50),
        audit_reason VARCHAR(500),
        audit_time TIMESTAMP,
        create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      )
    `);
    console.log('   ✅ merchant_recharge表创建成功');

    // 5. 插入测试商家数据
    console.log('\n5. 插入商家数据...');
    await client.query(`
      INSERT INTO merchant (username, password, merchant_name, contact_name, contact_phone, shop_name, balance) 
      VALUES 
        ('merchant001', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '测试商家001', '张三', '012-3456789', '测试店铺001', 10000.00),
        ('merchant002', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '测试商家002', '李四', '012-3456790', '测试店铺002', 5000.00),
        ('merchant003', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '测试商家003', '王五', '012-3456791', '测试店铺003', 8000.00)
      ON CONFLICT (username) DO NOTHING
    `);
    console.log('   ✅ 商家数据插入成功');

    // 6. 插入测试提现数据
    console.log('\n6. 插入提现数据...');
    await client.query(`
      INSERT INTO merchant_withdrawal (merchant_id, withdrawal_amount, bank_name, bank_account, account_holder, status, remark) 
      VALUES 
        (1, 1000.00, 'Maybank', '1234567890', '张三', 0, '测试提现申请1'),
        (1, 2000.00, 'CIMB Bank', '0987654321', '张三', 1, '测试提现申请2'),
        (2, 1500.00, 'Public Bank', '1122334455', '李四', 0, '测试提现申请3'),
        (2, 3000.00, 'RHB Bank', '5566778899', '李四', 2, '测试提现申请4'),
        (3, 2500.00, 'Hong Leong Bank', '9988776655', '王五', 0, '测试提现申请5')
      ON CONFLICT DO NOTHING
    `);
    console.log('   ✅ 提现数据插入成功');

    // 7. 插入测试充值数据
    console.log('\n7. 插入充值数据...');
    await client.query(`
      INSERT INTO merchant_recharge (merchant_id, recharge_amount, payment_method, payment_reference, status) 
      VALUES 
        (1, 5000.00, 'Bank Transfer', 'TXN001', 1),
        (2, 3000.00, 'Online Banking', 'TXN002', 1),
        (3, 4000.00, 'Credit Card', 'TXN003', 0)
      ON CONFLICT DO NOTHING
    `);
    console.log('   ✅ 充值数据插入成功');

    // 8. 验证数据
    console.log('\n8. 验证数据...');
    const merchantCount = await client.query('SELECT COUNT(*) FROM merchant');
    const withdrawalCount = await client.query('SELECT COUNT(*) FROM merchant_withdrawal');
    const rechargeCount = await client.query('SELECT COUNT(*) FROM merchant_recharge');
    
    console.log(`   📊 商家数量: ${merchantCount.rows[0].count}`);
    console.log(`   📊 提现记录数量: ${withdrawalCount.rows[0].count}`);
    console.log(`   📊 充值记录数量: ${rechargeCount.rows[0].count}`);

    console.log('\n🎉 数据库表创建和数据插入完成！');

  } catch (error) {
    console.error('❌ 错误:', error.message);
  } finally {
    try { await client.end(); } catch {}
  }
}

createTables();
