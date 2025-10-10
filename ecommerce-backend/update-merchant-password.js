const mysql = require('mysql2/promise');
const bcrypt = require('bcrypt');

async function updateMerchantPassword() {
  console.log('🔧 更新商家密码...');
  
  try {
    // 连接数据库
    const connection = await mysql.createConnection({
      host: 'localhost',
      user: 'root',
      password: '',
      database: 'ecommerce'
    });
    
    console.log('✅ 数据库连接成功');
    
    // 生成新密码哈希
    const newPassword = 'merchant123';
    const hashedPassword = await bcrypt.hash(newPassword, 10);
    
    // 更新商家密码
    await connection.execute(
      'UPDATE merchant SET password = ? WHERE username = ?',
      [hashedPassword, 'merchant001']
    );
    
    console.log('✅ 商家密码更新成功');
    
    // 验证新密码
    const [rows] = await connection.execute(
      'SELECT * FROM merchant WHERE username = ?',
      ['merchant001']
    );
    
    const merchant = rows[0];
    const isValid = await bcrypt.compare(newPassword, merchant.password);
    console.log('🔐 新密码验证结果:', isValid);
    
    await connection.end();
    
  } catch (error) {
    console.error('❌ 错误:', error.message);
  }
}

updateMerchantPassword();
