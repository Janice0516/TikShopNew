const { Client } = require('pg');

async function check() {
  const client = new Client({
    connectionString: 'postgresql://tikshop_user:xNye4k92dtzXqa9rPkLRW04Au74ZK6Yv@dpg-ctatjh5u0jms738shh30-a.oregon-postgres.render.com/tikshop',
    ssl: { rejectUnauthorized: false },
    connectionTimeoutMillis: 10000
  });

  try {
    await client.connect();
    console.log('数据库连接成功\n');

    const tables = ['"user"', 'merchant', 'platform_product', 'category', '"order"'];
    
    for (const table of tables) {
      const r = await client.query(`SELECT COUNT(*) FROM ${table}`);
      console.log(`${table}: ${r.rows[0].count} 条`);
    }
  } catch (e) {
    console.error('错误:', e.message);
  } finally {
    try { await client.end(); } catch {}
  }
}

check();
