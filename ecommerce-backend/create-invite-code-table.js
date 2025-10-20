const mysql = require('mysql2/promise');

async function createInviteCodeTable() {
  let connection;
  try {
    // 尝试不同的连接配置
    const configs = [
      { host: 'localhost', user: 'root', password: '', database: 'tikshop' },
      { host: 'localhost', user: 'root', password: '123456', database: 'tikshop' },
      { host: 'localhost', user: 'root', password: '', database: 'ecommerce' },
      { host: 'localhost', user: 'root', password: '123456', database: 'ecommerce' }
    ];

    for (const config of configs) {
      try {
        console.log(`Trying to connect with config:`, config);
        connection = await mysql.createConnection(config);
        console.log('Connected successfully!');
        break;
      } catch (error) {
        console.log(`Failed with config:`, error.message);
        continue;
      }
    }

    if (!connection) {
      throw new Error('Could not connect to database with any configuration');
    }

    // 创建邀请码表
    const createTableSQL = `
      CREATE TABLE IF NOT EXISTS \`invite_code\` (
        \`id\` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
        \`invite_code\` varchar(20) NOT NULL COMMENT '邀请码',
        \`salesperson_name\` varchar(100) NOT NULL COMMENT '业务员姓名',
        \`salesperson_phone\` varchar(20) DEFAULT NULL COMMENT '业务员电话',
        \`salesperson_id\` varchar(50) DEFAULT NULL COMMENT '业务员ID',
        \`used_count\` int(11) NOT NULL DEFAULT '0' COMMENT '已使用次数',
        \`max_usage\` int(11) NOT NULL DEFAULT '0' COMMENT '最大使用次数，0表示无限制',
        \`status\` smallint(6) NOT NULL DEFAULT '1' COMMENT '状态 0禁用 1启用',
        \`expire_time\` timestamp NULL DEFAULT NULL COMMENT '过期时间',
        \`remark\` varchar(255) DEFAULT NULL COMMENT '备注',
        \`create_time\` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
        \`update_time\` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
        PRIMARY KEY (\`id\`),
        UNIQUE KEY \`idx_invite_code\` (\`invite_code\`),
        KEY \`idx_salesperson_name\` (\`salesperson_name\`),
        KEY \`idx_status\` (\`status\`),
        KEY \`idx_create_time\` (\`create_time\`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='邀请码表';
    `;

    console.log('Creating invite_code table...');
    await connection.execute(createTableSQL);
    console.log('invite_code table created successfully!');

    // 检查merchant表是否存在invite_code字段
    const checkMerchantSQL = `
      SELECT COLUMN_NAME 
      FROM INFORMATION_SCHEMA.COLUMNS 
      WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'merchant' 
      AND COLUMN_NAME IN ('invite_code', 'salesperson_name', 'salesperson_phone', 'salesperson_id');
    `;

    const [rows] = await connection.execute(checkMerchantSQL);
    const existingColumns = rows.map(row => row.COLUMN_NAME);
    console.log('Existing columns in merchant table:', existingColumns);

    // 添加缺失的字段
    const addColumnsSQL = [];
    
    if (!existingColumns.includes('invite_code')) {
      addColumnsSQL.push('ADD COLUMN `invite_code` varchar(20) DEFAULT NULL COMMENT \'注册时使用的邀请码\' AFTER `total_withdraw`');
    }
    
    if (!existingColumns.includes('salesperson_name')) {
      addColumnsSQL.push('ADD COLUMN `salesperson_name` varchar(100) DEFAULT NULL COMMENT \'业务员姓名\' AFTER `invite_code`');
    }
    
    if (!existingColumns.includes('salesperson_phone')) {
      addColumnsSQL.push('ADD COLUMN `salesperson_phone` varchar(20) DEFAULT NULL COMMENT \'业务员电话\' AFTER `salesperson_name`');
    }
    
    if (!existingColumns.includes('salesperson_id')) {
      addColumnsSQL.push('ADD COLUMN `salesperson_id` varchar(50) DEFAULT NULL COMMENT \'业务员ID\' AFTER `salesperson_phone`');
    }

    if (addColumnsSQL.length > 0) {
      const alterSQL = `ALTER TABLE \`merchant\` ${addColumnsSQL.join(', ')}`;
      console.log('Adding columns to merchant table...');
      console.log('SQL:', alterSQL);
      await connection.execute(alterSQL);
      console.log('Columns added to merchant table successfully!');
    } else {
      console.log('All required columns already exist in merchant table');
    }

    // 插入示例数据
    const insertSQL = `
      INSERT INTO \`invite_code\` (\`invite_code\`, \`salesperson_name\`, \`salesperson_phone\`, \`salesperson_id\`, \`max_usage\`, \`status\`, \`remark\`) VALUES
      ('WELCOME2024', '张三', '012-3456789', 'SALES001', 100, 1, '欢迎新商家入驻'),
      ('VIP2024', '李四', '012-9876543', 'SALES002', 50, 1, 'VIP客户专用邀请码'),
      ('PREMIUM2024', '王五', '012-1111111', 'SALES003', 0, 1, '高级客户无限制邀请码')
      ON DUPLICATE KEY UPDATE \`remark\` = VALUES(\`remark\`);
    `;

    console.log('Inserting sample data...');
    await connection.execute(insertSQL);
    console.log('Sample data inserted successfully!');

    console.log('Database migration completed successfully!');

  } catch (error) {
    console.error('Migration failed:', error.message);
  } finally {
    if (connection) {
      await connection.end();
    }
  }
}

createInviteCodeTable();
