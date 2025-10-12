-- 创建缺失的数据库表和数据
-- 请在Render数据库管理界面中执行此脚本

-- 1. 创建merchant表
CREATE TABLE IF NOT EXISTS merchant (
    id BIGSERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    merchant_name VARCHAR(100) NOT NULL,
    contact_name VARCHAR(50),
    contact_phone VARCHAR(20),
    shop_name VARCHAR(100),
    address TEXT,
    business_license VARCHAR(100),
    status SMALLINT DEFAULT 1,
    balance DECIMAL(10,2) DEFAULT 0.00,
    frozen_amount DECIMAL(10,2) DEFAULT 0.00,
    total_income DECIMAL(10,2) DEFAULT 0.00,
    total_withdraw DECIMAL(10,2) DEFAULT 0.00,
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. 创建merchant_withdrawal表
CREATE TABLE IF NOT EXISTS merchant_withdrawal (
    id BIGSERIAL PRIMARY KEY,
    merchant_id BIGINT NOT NULL,
    withdrawal_amount DECIMAL(10,2) NOT NULL,
    bank_name VARCHAR(100) NOT NULL,
    bank_account VARCHAR(50) NOT NULL,
    account_holder VARCHAR(50) NOT NULL,
    status SMALLINT DEFAULT 0,
    remark VARCHAR(500),
    admin_remark VARCHAR(500),
    processed_by BIGINT,
    processed_at TIMESTAMP,
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. 创建merchant_recharge表
CREATE TABLE IF NOT EXISTS merchant_recharge (
    id BIGSERIAL PRIMARY KEY,
    merchant_id BIGINT NOT NULL,
    recharge_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    payment_reference VARCHAR(100),
    status SMALLINT DEFAULT 0,
    admin_id BIGINT,
    admin_name VARCHAR(50),
    audit_reason VARCHAR(500),
    audit_time TIMESTAMP,
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. 插入测试商家数据
INSERT INTO merchant (username, password, merchant_name, contact_name, contact_phone, shop_name, balance) 
VALUES 
    ('merchant001', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '测试商家001', '张三', '012-3456789', '测试店铺001', 10000.00),
    ('merchant002', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '测试商家002', '李四', '012-3456790', '测试店铺002', 5000.00),
    ('merchant003', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '测试商家003', '王五', '012-3456791', '测试店铺003', 8000.00)
ON CONFLICT (username) DO NOTHING;

-- 5. 插入测试提现数据
INSERT INTO merchant_withdrawal (merchant_id, withdrawal_amount, bank_name, bank_account, account_holder, status, remark) 
VALUES 
    (1, 1000.00, 'Maybank', '1234567890', '张三', 0, '测试提现申请1'),
    (1, 2000.00, 'CIMB Bank', '0987654321', '张三', 1, '测试提现申请2'),
    (2, 1500.00, 'Public Bank', '1122334455', '李四', 0, '测试提现申请3'),
    (2, 3000.00, 'RHB Bank', '5566778899', '李四', 2, '测试提现申请4'),
    (3, 2500.00, 'Hong Leong Bank', '9988776655', '王五', 0, '测试提现申请5')
ON CONFLICT DO NOTHING;

-- 6. 插入测试充值数据
INSERT INTO merchant_recharge (merchant_id, recharge_amount, payment_method, payment_reference, status) 
VALUES 
    (1, 5000.00, 'Bank Transfer', 'TXN001', 1),
    (2, 3000.00, 'Online Banking', 'TXN002', 1),
    (3, 4000.00, 'Credit Card', 'TXN003', 0)
ON CONFLICT DO NOTHING;

-- 7. 验证数据
SELECT 'merchant' as table_name, COUNT(*) as count FROM merchant
UNION ALL
SELECT 'merchant_withdrawal' as table_name, COUNT(*) as count FROM merchant_withdrawal
UNION ALL
SELECT 'merchant_recharge' as table_name, COUNT(*) as count FROM merchant_recharge;
