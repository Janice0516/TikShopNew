const mysql = require('mysql2/promise');

async function testCategory() {
  console.log('🔍 测试分类功能...');
  
  try {
    // 连接数据库
    const connection = await mysql.createConnection({
      host: 'localhost',
      user: 'root',
      password: '',
      database: 'ecommerce'
    });
    
    console.log('✅ 数据库连接成功');
    
    // 查询分类数据
    const [rows] = await connection.execute(
      'SELECT * FROM category WHERE status = 1 ORDER BY sort ASC, id ASC'
    );
    
    console.log('✅ 查询到分类数据:', rows.length, '条');
    console.log('前3条数据:', rows.slice(0, 3));
    
    await connection.end();
    
  } catch (error) {
    console.error('❌ 错误:', error.message);
  }
}

testCategory();
