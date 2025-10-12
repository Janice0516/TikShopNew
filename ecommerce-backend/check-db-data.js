const { Client } = require('pg');

const client = new Client({
  host: 'dpg-ctatjh5u0jms738shh30-a.oregon-postgres.render.com',
  port: 5432,
  user: 'tikshop_user',
  password: 'xNye4k92dtzXqa9rPkLRW04Au74ZK6Yv',
  database: 'tikshop',
  ssl: { rejectUnauthorized: false }
});

async function checkData() {
  try {
    await client.connect();
    console.log('✅ 数据库连接成功\n');

    const tables = [
      { name: 'user', label: '用户' },
      { name: 'merchant', label: '商家' },
      { name: 'platform_product', label: '平台商品' },
      { name: 'category', label: '分类' },
      { name: '"order"', label: '订单' },
      { name: 'order_item', label: '订单明细' },
      { name: 'merchant_withdrawal', label: '商户提现' },
      { name: 'merchant_recharge', label: '商户充值' }
    ];

    for (const table of tables) {
      const result = await client.query(`SELECT COUNT(*) FROM ${table.name}`);
      console.log(`${table.label}表 (${table.name}): ${result.rows[0].count} 条数据`);
    }

    console.log('\n正在检查部分数据样本...\n');
    
    // 检查用户数据
    const users = await client.query('SELECT id, username, phone FROM "user" LIMIT 3');
    console.log('用户样本:', users.rows);

    // 检查商家数据
    const merchants = await client.query('SELECT id, merchant_name, username FROM merchant LIMIT 3');
    console.log('\n商家样本:', merchants.rows);

    // 检查商品数据
    const products = await client.query('SELECT id, name, price FROM platform_product LIMIT 3');
    console.log('\n商品样本:', products.rows);

  } catch (error) {
    console.error('❌ 错误:', error.message);
  } finally {
    await client.end();
  }
}

checkData();
