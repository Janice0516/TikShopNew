const { Client } = require('pg');

async function createFundFreezeTables() {
  const client = new Client({
    connectionString: 'postgresql://tiktokshop_slkz_user:U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn@dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com/tiktokshop_slkz',
    ssl: { rejectUnauthorized: false },
    connectionTimeoutMillis: 10000
  });

  try {
    await client.connect();
    console.log('✅ 数据库连接成功\n');

    // 1. 创建资金冻结记录表
    console.log('1. 创建资金冻结记录表...');
    await client.query(`
      CREATE TABLE IF NOT EXISTS fund_freeze_record (
        id BIGSERIAL PRIMARY KEY,
        merchant_id BIGINT NOT NULL,
        order_id BIGINT NOT NULL,
        freeze_amount DECIMAL(10,2) NOT NULL,
        freeze_type SMALLINT DEFAULT 1,
        freeze_status SMALLINT DEFAULT 1,
        freeze_reason VARCHAR(255) DEFAULT '订单资金冻结',
        unfreeze_time TIMESTAMP NULL,
        unfreeze_reason VARCHAR(255) NULL,
        create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      )
    `);
    console.log('   ✅ fund_freeze_record表创建成功');

    // 2. 创建资金流水表
    console.log('\n2. 创建资金流水表...');
    await client.query(`
      CREATE TABLE IF NOT EXISTS fund_transaction (
        id BIGSERIAL PRIMARY KEY,
        merchant_id BIGINT NOT NULL,
        order_id BIGINT NULL,
        transaction_type SMALLINT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        balance_before DECIMAL(10,2) NOT NULL,
        balance_after DECIMAL(10,2) NOT NULL,
        frozen_before DECIMAL(10,2) NOT NULL,
        frozen_after DECIMAL(10,2) NOT NULL,
        description VARCHAR(255) NOT NULL,
        remark VARCHAR(500) NULL,
        create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      )
    `);
    console.log('   ✅ fund_transaction表创建成功');

    // 3. 创建索引
    console.log('\n3. 创建索引...');
    await client.query(`CREATE INDEX IF NOT EXISTS idx_fund_freeze_merchant_id ON fund_freeze_record(merchant_id);`);
    await client.query(`CREATE INDEX IF NOT EXISTS idx_fund_freeze_order_id ON fund_freeze_record(order_id);`);
    await client.query(`CREATE INDEX IF NOT EXISTS idx_fund_transaction_merchant_id ON fund_transaction(merchant_id);`);
    console.log('   ✅ 索引创建成功');

    // 4. 更新merchant表，添加冻结金额字段
    console.log('\n4. 检查merchant表结构...');
    const merchantColumns = await client.query(`
      SELECT column_name 
      FROM information_schema.columns 
      WHERE table_name = 'merchant' AND column_name = 'frozen_amount'
    `);
    
    if (merchantColumns.rows.length === 0) {
      console.log('   添加frozen_amount字段...');
      await client.query(`ALTER TABLE merchant ADD COLUMN frozen_amount DECIMAL(10,2) DEFAULT 0.00;`);
      console.log('   ✅ frozen_amount字段添加成功');
    } else {
      console.log('   ✅ frozen_amount字段已存在');
    }

    // 5. 更新order表，添加成本价和佣金字段
    console.log('\n5. 检查order表结构...');
    const orderColumns = await client.query(`
      SELECT column_name 
      FROM information_schema.columns 
      WHERE table_name = 'order' AND column_name IN ('cost_amount', 'merchant_profit', 'platform_profit')
    `);
    
    const existingColumns = orderColumns.rows.map(row => row.column_name);
    
    if (!existingColumns.includes('cost_amount')) {
      await client.query(`ALTER TABLE "order" ADD COLUMN cost_amount DECIMAL(10,2) DEFAULT 0.00;`);
      console.log('   ✅ cost_amount字段添加成功');
    }
    
    if (!existingColumns.includes('merchant_profit')) {
      await client.query(`ALTER TABLE "order" ADD COLUMN merchant_profit DECIMAL(10,2) DEFAULT 0.00;`);
      console.log('   ✅ merchant_profit字段添加成功');
    }
    
    if (!existingColumns.includes('platform_profit')) {
      await client.query(`ALTER TABLE "order" ADD COLUMN platform_profit DECIMAL(10,2) DEFAULT 0.00;`);
      console.log('   ✅ platform_profit字段添加成功');
    }

    console.log('\n🎉 资金冻结相关表创建完成！');

  } catch (error) {
    console.error('❌ 创建表失败:', error);
  } finally {
    await client.end();
  }
}

createFundFreezeTables();
