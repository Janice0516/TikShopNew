#!/bin/bash

echo "🔧 检查管理后台缺失的数据库表..."
echo "================================"

# 数据库连接信息
DB_HOST="127.0.0.1"
DB_USER="tikshop"
DB_PASS="TikShop_MySQL_#2025!9pQwXz"
DB_NAME="ecommerce"

# 检查提现表是否存在
echo "1. 检查 merchant_withdrawal 表..."
mysql -h$DB_HOST -u$DB_USER -p$DB_PASS $DB_NAME -e "SHOW TABLES LIKE 'merchant_withdrawal';" 2>/dev/null | grep -q "merchant_withdrawal"

if [ $? -eq 0 ]; then
    echo "✅ merchant_withdrawal 表已存在"
else
    echo "❌ merchant_withdrawal 表不存在，正在创建..."
    
    mysql -h$DB_HOST -u$DB_USER -p$DB_PASS $DB_NAME << 'EOF'
CREATE TABLE IF NOT EXISTS merchant_withdrawal (
    id BIGINT AUTO_INCREMENT PRIMARY KEY COMMENT '主键ID',
    merchant_id BIGINT NOT NULL COMMENT '商户ID',
    withdrawal_amount DECIMAL(10, 2) NOT NULL COMMENT '提现金额',
    bank_name VARCHAR(100) NOT NULL COMMENT '银行名称',
    bank_account VARCHAR(50) NOT NULL COMMENT '银行账号',
    account_holder VARCHAR(50) NOT NULL COMMENT '账户持有人',
    status SMALLINT DEFAULT 0 COMMENT '状态 0待审核 1已通过 2已拒绝 3已打款',
    remark VARCHAR(500) NULL COMMENT '备注',
    admin_remark VARCHAR(500) NULL COMMENT '管理员备注',
    processed_by BIGINT NULL COMMENT '处理人ID',
    processed_at TIMESTAMP NULL COMMENT '处理时间',
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    INDEX idx_merchant_id (merchant_id),
    INDEX idx_status (status),
    INDEX idx_create_time (create_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商户提现申请表';
EOF
    
    if [ $? -eq 0 ]; then
        echo "✅ merchant_withdrawal 表创建成功"
    else
        echo "❌ merchant_withdrawal 表创建失败"
    fi
fi

# 检查资金操作表是否存在
echo "2. 检查 fund_operation 表..."
mysql -h$DB_HOST -u$DB_USER -p$DB_PASS $DB_NAME -e "SHOW TABLES LIKE 'fund_operation';" 2>/dev/null | grep -q "fund_operation"

if [ $? -eq 0 ]; then
    echo "✅ fund_operation 表已存在"
else
    echo "❌ fund_operation 表不存在，正在创建..."
    
    mysql -h$DB_HOST -u$DB_USER -p$DB_PASS $DB_NAME << 'EOF'
CREATE TABLE IF NOT EXISTS fund_operation (
    id BIGINT AUTO_INCREMENT PRIMARY KEY COMMENT '主键ID',
    merchant_id BIGINT NOT NULL COMMENT '商户ID',
    operation_type SMALLINT NOT NULL COMMENT '操作类型 1充值 2提现 3冻结 4解冻 5扣款 6退款',
    amount DECIMAL(10, 2) NOT NULL COMMENT '操作金额',
    balance_before DECIMAL(10, 2) NOT NULL COMMENT '操作前余额',
    balance_after DECIMAL(10, 2) NOT NULL COMMENT '操作后余额',
    order_id VARCHAR(100) NULL COMMENT '关联订单ID',
    description VARCHAR(500) NULL COMMENT '操作描述',
    operator_id BIGINT NULL COMMENT '操作人ID',
    operator_type VARCHAR(20) NULL COMMENT '操作人类型 admin/merchant/system',
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    INDEX idx_merchant_id (merchant_id),
    INDEX idx_operation_type (operation_type),
    INDEX idx_create_time (create_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='资金操作记录表';
EOF
    
    if [ $? -eq 0 ]; then
        echo "✅ fund_operation 表创建成功"
    else
        echo "❌ fund_operation 表创建失败"
    fi
fi

# 检查充值审核表是否存在
echo "3. 检查 merchant_recharge 表..."
mysql -h$DB_HOST -u$DB_USER -p$DB_PASS $DB_NAME -e "SHOW TABLES LIKE 'merchant_recharge';" 2>/dev/null | grep -q "merchant_recharge"

if [ $? -eq 0 ]; then
    echo "✅ merchant_recharge 表已存在"
else
    echo "❌ merchant_recharge 表不存在，正在创建..."
    
    mysql -h$DB_HOST -u$DB_USER -p$DB_PASS $DB_NAME << 'EOF'
CREATE TABLE IF NOT EXISTS merchant_recharge (
    id BIGINT AUTO_INCREMENT PRIMARY KEY COMMENT '主键ID',
    merchant_id BIGINT NOT NULL COMMENT '商户ID',
    recharge_amount DECIMAL(10, 2) NOT NULL COMMENT '充值金额',
    payment_method VARCHAR(50) NOT NULL COMMENT '支付方式',
    payment_reference VARCHAR(100) NULL COMMENT '支付参考号',
    status SMALLINT DEFAULT 0 COMMENT '状态 0待审核 1已通过 2已拒绝',
    admin_remark VARCHAR(500) NULL COMMENT '管理员备注',
    processed_by BIGINT NULL COMMENT '处理人ID',
    processed_at TIMESTAMP NULL COMMENT '处理时间',
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    INDEX idx_merchant_id (merchant_id),
    INDEX idx_status (status),
    INDEX idx_create_time (create_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商户充值审核表';
EOF
    
    if [ $? -eq 0 ]; then
        echo "✅ merchant_recharge 表创建成功"
    else
        echo "❌ merchant_recharge 表创建失败"
    fi
fi

echo "================================"
echo "✅ 数据库表检查完成"
