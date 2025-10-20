const mysql = require('mysql2/promise');

async function createCreditRatingTable() {
  let connection;
  
  try {
    // 尝试不同的连接配置
    const configs = [
      { host: '127.0.0.1', user: 'tikshop', password: 'TikShop_MySQL_#2025!9pQwXz', database: 'ecommerce' },
      { host: 'localhost', user: 'root', password: '', database: 'ecommerce' },
      { host: 'localhost', user: 'root', password: 'root', database: 'ecommerce' },
      { host: 'localhost', user: 'root', password: '123456', database: 'ecommerce' },
      { host: 'localhost', user: 'tikshop_user', password: 'tikshop_password', database: 'ecommerce' }
    ];
    
    for (const config of configs) {
      try {
        console.log(`尝试连接: ${config.user}@${config.host}/${config.database}`);
        connection = await mysql.createConnection(config);
        console.log('✅ 数据库连接成功');
        break;
      } catch (error) {
        console.log(`❌ 连接失败: ${error.message}`);
        continue;
      }
    }
    
    if (!connection) {
      throw new Error('无法连接到数据库');
    }
    
    // 创建表
    const createTableSQL = `
      CREATE TABLE IF NOT EXISTS merchant_credit_rating (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        merchant_id BIGINT NOT NULL,
        rating SMALLINT NOT NULL,
        score DECIMAL(5,2) NOT NULL,
        level VARCHAR(20) NOT NULL,
        evaluation_date DATE NOT NULL,
        valid_until DATE NOT NULL,
        evaluator_id BIGINT NOT NULL,
        evaluation_reason TEXT,
        status SMALLINT DEFAULT 1,
        create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
      )
    `;
    
    await connection.execute(createTableSQL);
    console.log('✅ merchant_credit_rating表创建成功');
    
    // 创建索引
    const indexes = [
      'CREATE INDEX IF NOT EXISTS idx_merchant_credit_rating_merchant_id ON merchant_credit_rating(merchant_id)',
      'CREATE INDEX IF NOT EXISTS idx_merchant_credit_rating_status ON merchant_credit_rating(status)',
      'CREATE INDEX IF NOT EXISTS idx_merchant_credit_rating_level ON merchant_credit_rating(level)'
    ];
    
    for (const indexSQL of indexes) {
      await connection.execute(indexSQL);
    }
    console.log('✅ 索引创建成功');
    
    // 插入示例数据
    const insertSQL = `
      INSERT INTO merchant_credit_rating (
        merchant_id, rating, score, level, evaluation_date, valid_until, 
        evaluator_id, evaluation_reason, status
      ) VALUES 
      (1, 5, 95.5, 'AAA', '2024-01-01', '2024-12-31', 1, '商户经营状况良好，无违规记录', 1),
      (2, 4, 88.0, 'AA', '2024-01-01', '2024-12-31', 1, '商户表现优秀，轻微违规', 1),
      (3, 3, 75.5, 'BBB', '2024-01-01', '2024-12-31', 1, '商户表现一般，有违规记录', 1),
      (4, 2, 65.0, 'BB', '2024-01-01', '2024-12-31', 1, '商户表现较差，多次违规', 1),
      (5, 1, 45.0, 'C', '2024-01-01', '2024-12-31', 1, '商户表现很差，严重违规', 1)
    `;
    
    await connection.execute(insertSQL);
    console.log('✅ 示例数据插入成功');
    
    // 检查数据
    const [rows] = await connection.execute('SELECT COUNT(*) as count FROM merchant_credit_rating');
    console.log(`📊 表中记录数: ${rows[0].count}`);
    
  } catch (error) {
    console.error('❌ 创建表失败:', error.message);
  } finally {
    if (connection) {
      await connection.end();
    }
  }
}

createCreditRatingTable();
