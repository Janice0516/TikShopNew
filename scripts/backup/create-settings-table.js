const { Client } = require('pg');

const client = new Client({
  host: 'dpg-d0j8b8k2o3jss73a8qkg-a.singapore-postgres.render.com',
  port: 5432,
  database: 'tiktokshop_slkz',
  user: 'tiktokshop_slkz_user',
  password: 'V8QZqJQZqJQZqJQZqJQZqJQZqJQZqJQZ',
  ssl: true
});

async function createSettingsTable() {
  try {
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

    // 创建索引
    await client.query('CREATE INDEX IF NOT EXISTS idx_system_settings_key ON system_settings(setting_key);');
    await client.query('CREATE INDEX IF NOT EXISTS idx_system_settings_category ON system_settings(category);');
    console.log('✅ 索引创建成功');

    // 插入默认设置数据
    const insertSQL = `
      INSERT INTO system_settings (setting_key, setting_value, setting_type, category, description) VALUES
      ('site_name', 'TikShop 电商平台', 'string', 'basic', '网站名称'),
      ('site_logo', '', 'string', 'basic', '网站Logo'),
      ('site_description', '一个功能强大的电商平台', 'string', 'basic', '网站描述'),
      ('customer_service_phone', '+60 12-345 6789', 'string', 'basic', '客服电话'),
      ('customer_service_email', 'support@tikshop.com', 'string', 'basic', '客服邮箱'),
      ('default_currency', 'MYR', 'string', 'basic', '默认货币'),
      ('auto_approve_orders', 'true', 'boolean', 'business', '自动审核订单'),
      ('order_timeout', '30', 'number', 'business', '订单超时时间(分钟)'),
      ('min_withdrawal_amount', '10.00', 'number', 'business', '最低提现金额'),
      ('platform_fee_rate', '1.5', 'number', 'business', '平台手续费率(%)'),
      ('merchant_onboarding_audit', 'true', 'boolean', 'business', '商家入驻审核'),
      ('product_listing_audit', 'true', 'boolean', 'business', '商品上架审核'),
      ('login_failure_lock', 'true', 'boolean', 'security', '登录失败锁定'),
      ('max_login_attempts', '5', 'number', 'security', '最大失败次数'),
      ('lockout_duration', '30', 'number', 'security', '锁定时间(分钟)'),
      ('min_password_length', '8', 'number', 'security', '密码最小长度'),
      ('force_password_complexity', 'true', 'boolean', 'security', '强制密码复杂度'),
      ('session_timeout', '60', 'number', 'security', '会话超时时间(分钟)'),
      ('email_notifications', 'true', 'boolean', 'notification', '邮件通知'),
      ('sms_notifications', 'false', 'boolean', 'notification', '短信通知'),
      ('system_notifications', 'true', 'boolean', 'notification', '系统通知'),
      ('smtp_host', 'smtp.example.com', 'string', 'notification', 'SMTP服务器'),
      ('smtp_port', '587', 'number', 'notification', 'SMTP端口'),
      ('smtp_user', 'user@example.com', 'string', 'notification', 'SMTP用户名'),
      ('smtp_pass', 'password', 'string', 'notification', 'SMTP密码'),
      ('smtp_secure', 'true', 'boolean', 'notification', '使用SSL/TLS')
      ON CONFLICT (setting_key) DO NOTHING;
    `;

    await client.query(insertSQL);
    console.log('✅ 默认设置数据插入成功');

    // 创建更新时间触发器
    const triggerSQL = `
      CREATE OR REPLACE FUNCTION update_system_settings_update_time()
      RETURNS TRIGGER AS $$
      BEGIN
          NEW.update_time = CURRENT_TIMESTAMP;
          RETURN NEW;
      END;
      $$ language 'plpgsql';

      DROP TRIGGER IF EXISTS update_system_settings_update_time ON system_settings;
      CREATE TRIGGER update_system_settings_update_time
          BEFORE UPDATE ON system_settings
          FOR EACH ROW
          EXECUTE FUNCTION update_system_settings_update_time();
    `;

    await client.query(triggerSQL);
    console.log('✅ 更新时间触发器创建成功');

    // 验证数据
    const result = await client.query('SELECT COUNT(*) FROM system_settings');
    console.log(`✅ 设置表中共有 ${result.rows[0].count} 条记录`);

  } catch (error) {
    console.error('❌ 创建失败:', error.message);
  } finally {
    await client.end();
  }
}

createSettingsTable();
