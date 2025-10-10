const mysql = require('mysql2/promise');

async function testCategory() {
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
    const [rows] = await connection.execute('SELECT * FROM category LIMIT 5');
    console.log('✅ 分类数据查询成功:');
    console.log(rows);

    // 测试TypeORM查询
    const [testRows] = await connection.execute(`
      SELECT * FROM category 
      WHERE status = 1 
      ORDER BY sort ASC, id ASC
    `);
    console.log('✅ 状态筛选查询成功:');
    console.log(testRows);

    await connection.end();
    console.log('✅ 数据库连接已关闭');

  } catch (error) {
    console.error('❌ 错误:', error);
  }
}

testCategory();
