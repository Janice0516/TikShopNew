#!/bin/bash

echo "🔧 创建资金管理相关数据库表..."
echo "================================"

# 数据库连接信息
DB_HOST="127.0.0.1"
DB_USER="tikshop"
DB_PASS="TikShop_MySQL_#2025!9pQwXz"
DB_NAME="ecommerce"

# 创建 fund_transaction 表
echo "1. 创建 fund_transaction 表..."
mysql -h$DB_HOST -u$DB_USER -p$DB_PASS $DB_NAME << 'EOF'
CREATE TABLE IF NOT EXISTS fund_transaction (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '主键ID',
    merchant_id BIGINT NOT NULL COMMENT '商户ID',
    order_id BIGINT NULL COMMENT '订单ID',
    transaction_type SMALLINT NOT NULL COMMENT '交易类型 1冻结 2解冻 3佣金结算 4提现 5充值',
    amount DECIMAL(10, 2) NOT NULL COMMENT '交易金额',
    balance_before DECIMAL(10, 2) NOT NULL COMMENT '交易前余额',
    balance_after DECIMAL(10, 2) NOT NULL COMMENT '交易后余额',
    frozen_before DECIMAL(10, 2) NOT NULL COMMENT '交易前冻结金额',
    frozen_after DECIMAL(10, 2) NOT NULL COMMENT '交易后冻结金额',
    description VARCHAR(255) NOT NULL COMMENT '交易描述',
    remark VARCHAR(500) NULL COMMENT '备注',
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    INDEX idx_merchant_id (merchant_id),
    INDEX idx_transaction_type (transaction_type),
    INDEX idx_create_time (create_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='资金交易记录表';
EOF

if [ $? -eq 0 ]; then
    echo "✅ fund_transaction 表创建成功"
else
    echo "❌ fund_transaction 表创建失败"
fi

# 创建 fund_freeze_record 表
echo "2. 创建 fund_freeze_record 表..."
mysql -h$DB_HOST -u$DB_USER -p$DB_PASS $DB_NAME << 'EOF'
CREATE TABLE IF NOT EXISTS fund_freeze_record (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '主键ID',
    merchant_id BIGINT NOT NULL COMMENT '商户ID',
    order_id BIGINT NOT NULL COMMENT '订单ID',
    freeze_amount DECIMAL(10, 2) NOT NULL COMMENT '冻结金额',
    freeze_type SMALLINT DEFAULT 1 COMMENT '冻结类型 1订单冻结 2其他冻结',
    freeze_status SMALLINT DEFAULT 1 COMMENT '冻结状态 1已冻结 2已解冻 3已取消',
    freeze_reason VARCHAR(255) DEFAULT '订单资金冻结' COMMENT '冻结原因',
    unfreeze_time TIMESTAMP NULL COMMENT '解冻时间',
    unfreeze_reason VARCHAR(255) NULL COMMENT '解冻原因',
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    INDEX idx_merchant_id (merchant_id),
    INDEX idx_order_id (order_id),
    INDEX idx_freeze_status (freeze_status),
    INDEX idx_create_time (create_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='资金冻结记录表';
EOF

if [ $? -eq 0 ]; then
    echo "✅ fund_freeze_record 表创建成功"
else
    echo "❌ fund_freeze_record 表创建失败"
fi

echo "================================"
echo "✅ 资金管理表创建完成"
