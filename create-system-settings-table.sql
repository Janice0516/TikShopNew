-- 创建系统设置表
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

-- 创建索引
CREATE INDEX IF NOT EXISTS idx_system_settings_key ON system_settings(setting_key);
CREATE INDEX IF NOT EXISTS idx_system_settings_category ON system_settings(category);

-- 插入默认设置数据
INSERT INTO system_settings (setting_key, setting_value, setting_type, category, description) VALUES
-- 基本设置
('site_name', 'TikShop 电商平台', 'string', 'basic', '网站名称'),
('site_logo', '', 'string', 'basic', '网站Logo'),
('site_description', '一个功能强大的电商平台', 'string', 'basic', '网站描述'),
('customer_service_phone', '+60 12-345 6789', 'string', 'basic', '客服电话'),
('customer_service_email', 'support@tikshop.com', 'string', 'basic', '客服邮箱'),
('default_currency', 'MYR', 'string', 'basic', '默认货币'),

-- 业务设置
('auto_approve_orders', 'true', 'boolean', 'business', '自动审核订单'),
('order_timeout', '30', 'number', 'business', '订单超时时间(分钟)'),
('min_withdrawal_amount', '10.00', 'number', 'business', '最低提现金额'),
('platform_fee_rate', '1.5', 'number', 'business', '平台手续费率(%)'),
('merchant_onboarding_audit', 'true', 'boolean', 'business', '商家入驻审核'),
('product_listing_audit', 'true', 'boolean', 'business', '商品上架审核'),

-- 安全设置
('login_failure_lock', 'true', 'boolean', 'security', '登录失败锁定'),
('max_login_attempts', '5', 'number', 'security', '最大失败次数'),
('lockout_duration', '30', 'number', 'security', '锁定时间(分钟)'),
('min_password_length', '8', 'number', 'security', '密码最小长度'),
('force_password_complexity', 'true', 'boolean', 'security', '强制密码复杂度'),
('session_timeout', '60', 'number', 'security', '会话超时时间(分钟)'),

-- 通知设置
('email_notifications', 'true', 'boolean', 'notification', '邮件通知'),
('sms_notifications', 'false', 'boolean', 'notification', '短信通知'),
('system_notifications', 'true', 'boolean', 'notification', '系统通知'),
('smtp_host', 'smtp.example.com', 'string', 'notification', 'SMTP服务器'),
('smtp_port', '587', 'number', 'notification', 'SMTP端口'),
('smtp_user', 'user@example.com', 'string', 'notification', 'SMTP用户名'),
('smtp_pass', 'password', 'string', 'notification', 'SMTP密码'),
('smtp_secure', 'true', 'boolean', 'notification', '使用SSL/TLS')

ON CONFLICT (setting_key) DO NOTHING;

-- 创建更新时间触发器
CREATE OR REPLACE FUNCTION update_system_settings_update_time()
RETURNS TRIGGER AS $$
BEGIN
    NEW.update_time = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_system_settings_update_time
    BEFORE UPDATE ON system_settings
    FOR EACH ROW
    EXECUTE FUNCTION update_system_settings_update_time();
