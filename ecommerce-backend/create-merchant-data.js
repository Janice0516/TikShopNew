const { Client } = require('pg');

async function createMerchantData() {
  const client = new Client({
    connectionString: 'postgresql://tikshop_user:xNye4k92dtzXqa9rPkLRW04Au74ZK6Yv@dpg-ctatjh5u0jms738shh30-a.oregon-postgres.render.com/tikshop',
    ssl: { rejectUnauthorized: false },
    connectionTimeoutMillis: 5000
  });

  try {
    await client.connect();
    console.log('✅ 数据库连接成功\n');

    // 创建merchant表
    console.log('1. 创建merchant表...');
    await client.query(`
      CREATE TABLE IF NOT EXISTS merchant (
        id BIGSERIAL PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        merchant_name VARCHAR(100) NOT NULL,
        contact_person VARCHAR(50),
        phone VARCHAR(20),
        email VARCHAR(100),
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

    // 插入测试商家数据
    console.log('\n2. 插入商家数据...');
    await client.query(`
      INSERT INTO merchant (username, password, merchant_name, contact_person, phone, email, balance) 
      VALUES 
        ('merchant001', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '测试商家001', '张三', '13800138001', 'merchant001@test.com', 10000.00),
        ('merchant002', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '测试商家002', '李四', '13800138002', 'merchant002@test.com', 5000.00)
      ON CONFLICT (username) DO NOTHING
    `);
    console.log('   ✅ 商家数据插入成功');

    // 创建merchant_withdrawal表
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

    // 插入测试提现数据
    console.log('\n4. 插入提现数据...');
    await client.query(`
      INSERT INTO merchant_withdrawal (merchant_id, withdrawal_amount, bank_name, bank_account, account_holder, status, remark) 
      VALUES 
        (1, 1000.00, 'Maybank', '1234567890', '张三', 0, '测试提现申请1'),
        (1, 2000.00, 'CIMB Bank', '0987654321', '张三', 1, '测试提现申请2'),
        (2, 1500.00, 'Public Bank', '1122334455', '李四', 0, '测试提现申请3')
      ON CONFLICT DO NOTHING
    `);
    console.log('   ✅ 提现数据插入成功');

    // 验证数据
    console.log('\n5. 验证数据...');
    const merchantCount = await client.query('SELECT COUNT(*) FROM merchant');
    const withdrawalCount = await client.query('SELECT COUNT(*) FROM merchant_withdrawal');
    
    console.log(`   📊 商家数量: ${merchantCount.rows[0].count}`);
    console.log(`   📊 提现记录数量: ${withdrawalCount.rows[0].count}`);

  } catch (error) {
    console.error('❌ 错误:', error.message);
  } finally {
    try { await client.end(); } catch {}
  }
}

createMerchantData();
