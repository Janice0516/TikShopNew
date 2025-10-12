const { Client } = require('pg');
const bcrypt = require('bcrypt');

async function fixMerchantPassword() {
  const client = new Client({
    connectionString: 'postgresql://tiktokshop_slkz_user:U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn@dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com/tiktokshop_slkz',
    ssl: { rejectUnauthorized: false }
  });

  try {
    await client.connect();
    console.log('✅ 数据库连接成功\n');

    // 生成password123的哈希
    const hashedPassword = await bcrypt.hash('password123', 10);
    console.log('1. 更新商家密码...');
    
    // 更新所有商家的密码
    await client.query(
      'UPDATE merchant SET password = $1',
      [hashedPassword]
    );
    
    console.log('   ✅ 商家密码更新完成');
    console.log('   新密码: password123');
    console.log('   哈希值:', hashedPassword.substring(0, 20) + '...');

    // 验证更新
    console.log('\n2. 验证密码更新...');
    const merchants = await client.query('SELECT username, password FROM merchant LIMIT 3');
    merchants.rows.forEach(merchant => {
      console.log(`   - ${merchant.username}: ${merchant.password.substring(0, 20)}...`);
    });

  } catch (error) {
    console.error('❌ 错误:', error.message);
  } finally {
    try { await client.end(); } catch {}
  }
}

fixMerchantPassword();
