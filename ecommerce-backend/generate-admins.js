const { Client } = require('pg');
const bcrypt = require('bcrypt');

// 数据库连接配置
const client = new Client({
  host: 'dpg-d0j8q8h2s78s73fq8hpg-a.oregon-postgres.render.com',
  port: 5432,
  user: 'tiktokshop_slkz_user',
  password: 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn',
  database: 'tiktokshop_slkz',
  ssl: { rejectUnauthorized: false }
});

// 生成随机密码
function generatePassword() {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  let result = '';
  for (let i = 0; i < 12; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return result;
}

// 生成随机昵称
function generateNickname() {
  const names = ['Admin', 'Manager', 'Supervisor', 'Director', 'Coordinator', 'Lead', 'Chief', 'Head', 'Senior', 'Principal'];
  const adjectives = ['Smart', 'Quick', 'Bright', 'Sharp', 'Swift', 'Bold', 'Cool', 'Wise', 'Strong', 'Fast'];
  const randomName = names[Math.floor(Math.random() * names.length)];
  const randomAdj = adjectives[Math.floor(Math.random() * adjectives.length)];
  const number = Math.floor(Math.random() * 999) + 1;
  return `${randomAdj}${randomName}${number}`;
}

async function generateAdmins() {
  try {
    console.log('🔐 连接数据库...');
    await client.connect();
    
    console.log('📊 创建管理员表...');
    await client.query(`
      CREATE TABLE IF NOT EXISTS admin (
        id BIGSERIAL PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(100) NOT NULL,
        nickname VARCHAR(50),
        avatar VARCHAR(255),
        role VARCHAR(20) DEFAULT 'admin',
        status INTEGER DEFAULT 1,
        create_time TIMESTAMP DEFAULT NOW(),
        update_time TIMESTAMP DEFAULT NOW()
      )
    `);
    
    console.log('👥 生成10个管理员账户...');
    const admins = [];
    
    for (let i = 1; i <= 10; i++) {
      const username = `admin${i.toString().padStart(3, '0')}`;
      const password = generatePassword();
      const hashedPassword = await bcrypt.hash(password, 10);
      const nickname = generateNickname();
      
      admins.push({ username, password, nickname, hashedPassword });
      
      console.log(`✅ 生成账户 ${i}: ${username} / ${password} / ${nickname}`);
    }
    
    console.log('💾 保存到数据库...');
    for (const admin of admins) {
      await client.query(
        'INSERT INTO admin (username, password, nickname, role, status) VALUES ($1, $2, $3, $4, $5) ON CONFLICT (username) DO NOTHING',
        [admin.username, admin.hashedPassword, admin.nickname, 'admin', 1]
      );
    }
    
    console.log('📋 查询结果...');
    const result = await client.query('SELECT username, nickname, role, status FROM admin ORDER BY id');
    
    console.log('\n🎉 管理员账户生成完成！');
    console.log('================================');
    console.log('📝 账户信息汇总：');
    console.log('================================');
    
    for (let i = 0; i < admins.length; i++) {
      const admin = admins[i];
      console.log(`${i + 1}. 用户名: ${admin.username}`);
      console.log(`   密码: ${admin.password}`);
      console.log(`   昵称: ${admin.nickname}`);
      console.log(`   角色: admin`);
      console.log('   ---');
    }
    
    console.log(`\n📊 数据库中共有 ${result.rows.length} 个管理员账户`);
    console.log('💡 请妥善保存这些账户信息');
    
  } catch (error) {
    console.error('❌ 错误:', error.message);
  } finally {
    await client.end();
  }
}

generateAdmins();
