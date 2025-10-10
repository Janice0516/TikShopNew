const bcrypt = require('bcrypt');
const mysql = require('mysql2/promise');

async function testLogin() {
  try {
    // 连接数据库
    const connection = await mysql.createConnection({
      host: 'localhost',
      user: 'root',
      password: '',
      database: 'ecommerce'
    });

    console.log('✅ 数据库连接成功');

    // 测试管理员登录
    console.log('\n🔍 测试管理员登录...');
    const [adminRows] = await connection.execute(
      'SELECT id, username, password FROM admin WHERE username = ?',
      ['admin']
    );

    if (adminRows.length > 0) {
      const admin = adminRows[0];
      console.log('👤 管理员信息:', {
        id: admin.id,
        username: admin.username,
        password: admin.password.substring(0, 20) + '...'
      });

      const isValid = await bcrypt.compare('123456', admin.password);
      console.log('🔐 密码验证结果:', isValid);

      if (isValid) {
        console.log('✅ 管理员登录成功！');
      } else {
        console.log('❌ 管理员密码错误');
      }
    } else {
      console.log('❌ 管理员不存在');
    }

    // 测试用户登录
    console.log('\n🔍 测试用户登录...');
    const [userRows] = await connection.execute(
      'SELECT id, phone, password FROM user WHERE phone = ?',
      ['13800138000']
    );

    if (userRows.length > 0) {
      const user = userRows[0];
      console.log('👤 用户信息:', {
        id: user.id,
        phone: user.phone,
        password: user.password.substring(0, 20) + '...'
      });

      const isValid = await bcrypt.compare('123456', user.password);
      console.log('🔐 密码验证结果:', isValid);

      if (isValid) {
        console.log('✅ 用户登录成功！');
      } else {
        console.log('❌ 用户密码错误');
      }
    } else {
      console.log('❌ 用户不存在');
    }

    await connection.end();
  } catch (error) {
    console.error('❌ 错误:', error.message);
  }
}

testLogin();
