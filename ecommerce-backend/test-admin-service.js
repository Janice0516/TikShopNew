const mysql = require('mysql2/promise');
const bcrypt = require('bcrypt');

async function testAdminService() {
  console.log('🔍 测试AdminService...');
  
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
      'SELECT id, username, password, status FROM admin WHERE username = ?',
      ['admin']
    );
    
    if (rows.length === 0) {
      console.log('❌ 未找到admin用户');
      return;
    }
    
    const admin = rows[0];
    console.log('✅ 找到admin用户:', {
      id: admin.id,
      username: admin.username,
      status: admin.status
    });
    
    // 测试密码验证
    const testPassword = 'admin123';
    const isValid = await bcrypt.compare(testPassword, admin.password);
    console.log('🔐 密码验证结果:', isValid);
    
    if (isValid) {
      console.log('✅ 密码验证成功');
    } else {
      console.log('❌ 密码验证失败');
      
      // 生成新的密码哈希
      const newHash = await bcrypt.hash(testPassword, 10);
      console.log('🔧 生成新密码哈希:', newHash);
      
      // 更新数据库中的密码
      await connection.execute(
        'UPDATE admin SET password = ? WHERE id = ?',
        [newHash, admin.id]
      );
      console.log('✅ 已更新admin密码');
    }
    
    await connection.end();
    
  } catch (error) {
    console.error('❌ 测试失败:', error.message);
  }
}

testAdminService();
