const mysql = require('mysql2/promise');

async function runMigration() {
  const connection = await mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '123456',
    database: 'ecommerce'
  });

  try {
    console.log('开始执行推荐商品控制字段迁移...');
    
    // 检查字段是否已存在
    const [columns] = await connection.execute(`
      SELECT COLUMN_NAME 
      FROM INFORMATION_SCHEMA.COLUMNS 
      WHERE TABLE_SCHEMA = 'ecommerce' 
      AND TABLE_NAME = 'merchant_product' 
      AND COLUMN_NAME = 'is_popular'
    `);

    if (columns.length > 0) {
      console.log('字段已存在，跳过迁移');
      return;
    }

    // 执行迁移
    await connection.execute(`
      ALTER TABLE merchant_product 
      ADD COLUMN is_popular tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否推荐为热门商品 0否 1是',
      ADD COLUMN is_top_deal tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否推荐为Top Deals 0否 1是',
      ADD COLUMN recommend_reason varchar(255) DEFAULT NULL COMMENT '推荐理由',
      ADD COLUMN recommend_priority int NOT NULL DEFAULT 0 COMMENT '推荐优先级 数字越大优先级越高',
      ADD COLUMN recommend_start_time datetime DEFAULT NULL COMMENT '推荐开始时间',
      ADD COLUMN recommend_end_time datetime DEFAULT NULL COMMENT '推荐结束时间'
    `);

    // 添加索引
    await connection.execute(`
      ALTER TABLE merchant_product 
      ADD INDEX idx_is_popular (is_popular),
      ADD INDEX idx_is_top_deal (is_top_deal),
      ADD INDEX idx_recommend_priority (recommend_priority),
      ADD INDEX idx_recommend_time (recommend_start_time, recommend_end_time)
    `);

    console.log('✅ 推荐商品控制字段迁移完成！');
    
    // 设置一些示例推荐商品
    console.log('设置示例推荐商品...');
    
    // 随机设置一些商品为推荐
    await connection.execute(`
      UPDATE merchant_product 
      SET is_popular = 1, 
          recommend_reason = '热销商品',
          recommend_priority = 80
      WHERE sales > 50 
      ORDER BY RAND() 
      LIMIT 5
    `);

    await connection.execute(`
      UPDATE merchant_product 
      SET is_top_deal = 1, 
          recommend_reason = '限时特价',
          recommend_priority = 90
      WHERE sales > 100 
      ORDER BY RAND() 
      LIMIT 3
    `);

    console.log('✅ 示例推荐商品设置完成！');

  } catch (error) {
    console.error('迁移失败:', error);
  } finally {
    await connection.end();
  }
}

runMigration();
