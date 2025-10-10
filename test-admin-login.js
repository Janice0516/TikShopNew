const bcrypt = require('bcrypt');
const mysql = require('mysql2/promise');

async function testAdminLogin() {
  try {
    // 连接数据库
    const connection = await mysql.createConnection({
      host: 'localhost',
      user: 'root',
      password: '',
      database: 'ecommerce'
    });

    console.log('✅ 数据库连接成功');

    // 查询admin用户
    const [rows] = await connection.execute(
      'SELECT id, username, password FROM admin WHERE username = ?',
      ['admin']
    );

    console.log('📊 查询结果:', rows);

    if (rows.length > 0) {
      const admin = rows[0];
      console.log('👤 管理员信息:', {
        id: admin.id,
        username: admin.username,
        password: admin.password
      });

      // 验证密码
      const isValid = await bcrypt.compare('123456', admin.password);
      console.log('🔐 密码验证结果:', isValid);

      if (isValid) {
        console.log('✅ 登录成功！');
      } else {
        console.log('❌ 密码错误');
      }
    } else {
      console.log('❌ 用户不存在');
    }

    await connection.end();
  } catch (error) {
    console.error('❌ 错误:', error.message);
  }
}

testAdminLogin();
