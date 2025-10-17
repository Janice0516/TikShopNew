const { Client } = require('pg');

// 使用环境变量中的数据库连接信息
const client = new Client({
  host: process.env.DB_HOST || 'dpg-d0j8b8k2o3jss73a8qkg-a.singapore-postgres.render.com',
  port: process.env.DB_PORT || 5432,
  database: process.env.DB_DATABASE || 'tiktokshop_slkz',
  user: process.env.DB_USERNAME || 'tiktokshop_slkz_user',
  password: process.env.DB_PASSWORD || 'V8QZqJQZqJQZqJQZqJQZqJQZqJQZqJQZ',
  ssl: { rejectUnauthorized: false }
});

async function createSettingsTable() {
  try {
    console.log('🔗 尝试连接数据库...');
    console.log('Host:', client.host);
    console.log('Database:', client.database);
    console.log('User:', client.user);
    
    await client.connect();
    console.log('✅ 数据库连接成功');

    // 创建系统设置表
    const createTableSQL = `
      CREATE TABLE IF NOT EXISTS system_settings (
        id BIGSERIAL PRIMARY KEY,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT NOT NULL,
        setting_type VARCHAR(50) DEFAULT 'string',
        category VARCHAR(50) DEFAULT 'general',
        description VARCHAR(255),
        create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
    `;

    await client.query(createTableSQL);
    console.log('✅ system_settings表创建成功');

    // 插入默认设置数据
    const insertSQL = `
      INSERT INTO system_settings (setting_key, setting_value, setting_type, category, description) VALUES
      ('site_name', 'TikShop 电商平台', 'string', 'basic', '网站名称'),
      ('default_currency', 'MYR', 'string', 'basic', '默认货币')
      ON CONFLICT (setting_key) DO NOTHING;
    `;

    await client.query(insertSQL);
    console.log('✅ 默认设置数据插入成功');

    // 验证数据
    const result = await client.query('SELECT COUNT(*) FROM system_settings');
    console.log(`✅ 设置表中共有 ${result.rows[0].count} 条记录`);

  } catch (error) {
    console.error('❌ 创建失败:', error.message);
    console.error('错误详情:', error);
  } finally {
    await client.end();
  }
}

createSettingsTable();
