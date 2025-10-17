const { Client } = require('pg');

const connectionConfig = {
  host: 'dpg-d3kgpsd6ubrc73dvbjm0-a.oregon-postgres.render.com',
  port: 5432,
  user: 'tiktokshop_slkz_user',
  password: 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn',
  database: 'tiktokshop_slkz',
  ssl: { rejectUnauthorized: false },
  connectionTimeoutMillis: 15000
};

async function testUserRegistration() {
  const client = new Client(connectionConfig);
  
  try {
    console.log('🔌 连接到数据库...');
    await client.connect();
    console.log('✅ 数据库连接成功');

    // 检查用户表结构
    console.log('\n📋 检查用户表结构...');
    const tableInfo = await client.query(`
      SELECT column_name, data_type, is_nullable, column_default
      FROM information_schema.columns 
      WHERE table_name = 'user' 
      ORDER BY ordinal_position;
    `);
    
    console.log('用户表字段:');
    tableInfo.rows.forEach(col => {
      console.log(`  ${col.column_name}: ${col.data_type} (nullable: ${col.is_nullable})`);
    });

    // 检查现有用户
    console.log('\n👥 检查现有用户...');
    const existingUsers = await client.query('SELECT id, username, phone, nickname FROM "user" LIMIT 5');
    console.log('现有用户:');
    existingUsers.rows.forEach(user => {
      console.log(`  ID: ${user.id}, Username: ${user.username}, Phone: ${user.phone}, Nickname: ${user.nickname}`);
    });

    // 尝试插入测试用户
    console.log('\n🧪 尝试插入测试用户...');
    const testPhone = '13800138003';
    const testUsername = testPhone;
    const testPassword = '$2b$10$test.hash.for.password123';
    const testNickname = `用户${testPhone.slice(-4)}`;

    try {
      const insertResult = await client.query(`
        INSERT INTO "user" (username, phone, password, nickname, status, gender)
        VALUES ($1, $2, $3, $4, 1, 0)
        RETURNING id, username, phone, nickname;
      `, [testUsername, testPhone, testPassword, testNickname]);
      
      console.log('✅ 测试用户插入成功:');
      console.log(`  ID: ${insertResult.rows[0].id}`);
      console.log(`  Username: ${insertResult.rows[0].username}`);
      console.log(`  Phone: ${insertResult.rows[0].phone}`);
      console.log(`  Nickname: ${insertResult.rows[0].nickname}`);

      // 清理测试数据
      await client.query('DELETE FROM "user" WHERE phone = $1', [testPhone]);
      console.log('🧹 测试数据已清理');

    } catch (insertError) {
      console.error('❌ 插入测试用户失败:', insertError.message);
    }

  } catch (error) {
    console.error('❌ 测试失败:', error.message);
  } finally {
    await client.end();
    console.log('\n🔌 数据库连接已关闭');
  }
}

testUserRegistration();
