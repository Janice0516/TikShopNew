const mysql = require('mysql2/promise');
const bcrypt = require('bcrypt');

async function testMerchantLogin() {
  console.log('🔍 测试商家登录...');
  
  try {
    // 连接数据库
    const connection = await mysql.createConnection({
      host: 'localhost',
      user: 'root',
      password: '',
      database: 'ecommerce'
    });
    
    console.log('✅ 数据库连接成功');
    
    // 查找商家用户
    const [rows] = await connection.execute(
      'SELECT * FROM merchant WHERE username = ?',
      ['merchant001']
    );
    
    if (rows.length === 0) {
      console.log('❌ 未找到商家用户');
      return;
    }
    
    const merchant = rows[0];
    console.log('✅ 找到商家用户:', { 
      id: merchant.id, 
      username: merchant.username, 
      status: merchant.status 
    });
    
    // 测试密码
    const testPasswords = ['merchant123', '123456', 'merchant001'];
    
    for (const password of testPasswords) {
      const isValid = await bcrypt.compare(password, merchant.password);
      console.log(`🔐 密码 "${password}" 验证结果:`, isValid);
      if (isValid) {
        console.log('✅ 找到正确密码:', password);
        break;
      }
    }
    
    await connection.end();
    
  } catch (error) {
    console.error('❌ 错误:', error.message);
  }
}

testMerchantLogin();
