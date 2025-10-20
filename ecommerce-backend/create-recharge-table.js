const mysql = require('mysql2/promise');
const fs = require('fs');

async function createRechargeTable() {
  try {
    // 尝试不同的数据库连接配置
    const configs = [
      { host: 'localhost', user: 'root', password: '', database: 'tikshop' },
      { host: 'localhost', user: 'root', password: '123456', database: 'tikshop' },
      { host: 'localhost', user: 'root', password: 'root', database: 'tikshop' },
    ];

    let connection = null;
    for (const config of configs) {
      try {
        connection = await mysql.createConnection(config);
        console.log(`✅ 数据库连接成功: ${config.user}@${config.host}`);
        break;
      } catch (error) {
        console.log(`❌ 连接失败: ${config.user}@${config.host} - ${error.message}`);
      }
    }

    if (!connection) {
      console.error('❌ 所有数据库连接配置都失败了');
      return;
    }

    // 读取SQL文件
    const sql = fs.readFileSync('/root/TikShop/ecommerce-backend/create-recharge-table.sql', 'utf8');
    
    // 执行SQL
    console.log('执行SQL创建表和插入数据...');
    await connection.execute(sql);
    
    console.log('✅ merchant_recharge表创建成功，测试数据插入成功');
    
    // 验证表结构
    const [rows] = await connection.execute('DESCRIBE merchant_recharge');
    console.log('\n表结构:');
    rows.forEach(row => console.log(`  ${row.Field}: ${row.Type}`));
    
    // 验证数据
    const [data] = await connection.execute('SELECT COUNT(*) as count FROM merchant_recharge');
    console.log(`\n数据行数: ${data[0].count}`);
    
    await connection.end();
  } catch (error) {
    console.error('数据库操作错误:', error.message);
  }
}

createRechargeTable();
