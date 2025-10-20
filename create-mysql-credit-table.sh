#!/bin/bash

echo "🔧 创建MySQL信用评级表..."

# MySQL连接参数
DB_HOST="localhost"
DB_PORT="3306"
DB_NAME="ecommerce"
DB_USER="root"
DB_PASSWORD=""

# 创建表的SQL
SQL="
CREATE TABLE IF NOT EXISTS merchant_credit_rating (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  merchant_id BIGINT NOT NULL,
  rating SMALLINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
  score DECIMAL(5,2) NOT NULL CHECK (score >= 0 AND score <= 100),
  level VARCHAR(20) NOT NULL CHECK (level IN ('AAA', 'AA', 'A', 'BBB', 'BB', 'B', 'C')),
  evaluation_date DATE NOT NULL,
  valid_until DATE NOT NULL,
  evaluator_id BIGINT NOT NULL,
  evaluation_reason TEXT,
  status SMALLINT DEFAULT 1 CHECK (status IN (0, 1)),
  create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 创建索引
CREATE INDEX IF NOT EXISTS idx_merchant_credit_rating_merchant_id ON merchant_credit_rating(merchant_id);
CREATE INDEX IF NOT EXISTS idx_merchant_credit_rating_status ON merchant_credit_rating(status);
CREATE INDEX IF NOT EXISTS idx_merchant_credit_rating_level ON merchant_credit_rating(level);

-- 插入示例数据
INSERT INTO merchant_credit_rating (
  merchant_id, rating, score, level, evaluation_date, valid_until, 
  evaluator_id, evaluation_reason, status
) VALUES 
(1, 5, 95.5, 'AAA', '2024-01-01', '2024-12-31', 1, '商户经营状况良好，无违规记录', 1),
(2, 4, 88.0, 'AA', '2024-01-01', '2024-12-31', 1, '商户表现优秀，轻微违规', 1),
(3, 3, 75.5, 'BBB', '2024-01-01', '2024-12-31', 1, '商户表现一般，有违规记录', 1),
(4, 2, 65.0, 'BB', '2024-01-01', '2024-12-31', 1, '商户表现较差，多次违规', 1),
(5, 1, 45.0, 'C', '2024-01-01', '2024-12-31', 1, '商户表现很差，严重违规', 1);
"

# 使用mysql执行SQL
mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" -e "$SQL"

if [ $? -eq 0 ]; then
    echo "✅ MySQL信用评级表创建成功"
    
    # 检查数据
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" -e "SELECT COUNT(*) as count FROM merchant_credit_rating;"
else
    echo "❌ 创建表失败"
fi
