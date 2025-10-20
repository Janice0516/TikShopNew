#!/bin/bash

# 生成10个管理员账户脚本
echo "🔐 生成10个管理员账户..."

# 数据库连接信息
DB_HOST="dpg-d0j8q8h2s78s73fq8hpg-a.oregon-postgres.render.com"
DB_PORT="5432"
DB_USER="tiktokshop_slkz_user"
DB_NAME="tiktokshop_slkz"
DB_PASSWORD="U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn"

# 设置环境变量
export PGPASSWORD="$DB_PASSWORD"

# 生成随机密码的函数
generate_password() {
    openssl rand -base64 12 | tr -d "=+/" | cut -c1-12
}

# 生成随机昵称的函数
generate_nickname() {
    local names=("Admin" "Manager" "Supervisor" "Director" "Coordinator" "Lead" "Chief" "Head" "Senior" "Principal")
    local adjectives=("Smart" "Quick" "Bright" "Sharp" "Swift" "Bold" "Cool" "Wise" "Strong" "Fast")
    local random_name=${names[$RANDOM % ${#names[@]}]}
    local random_adj=${adjectives[$RANDOM % ${#adjectives[@]}]}
    local number=$((RANDOM % 999 + 1))
    echo "${random_adj}${random_name}${number}"
}

echo "📊 创建管理员账户..."

# 创建SQL脚本
cat > temp_admin_setup.sql << 'EOF'
-- 创建管理员表（如果不存在）
CREATE TABLE IF NOT EXISTS admin (
    id BIGSERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,
    nickname VARCHAR(50),
    avatar VARCHAR(255),
    role VARCHAR(20) DEFAULT 'admin',
    status INTEGER DEFAULT 1,
    create_time TIMESTAMP DEFAULT NOW(),
    update_time TIMESTAMP DEFAULT NOW()
);

-- 插入10个管理员账户
EOF

# 生成10个管理员账户
for i in {1..10}; do
    username="admin$(printf "%03d" $i)"
    password=$(generate_password)
    nickname=$(generate_nickname)
    
    echo "INSERT INTO admin (username, password, nickname, role, status) VALUES ('$username', '$password', '$nickname', 'admin', 1);" >> temp_admin_setup.sql
    
    echo "✅ 生成账户 $i: $username / $password / $nickname"
done

# 添加查询语句
cat >> temp_admin_setup.sql << 'EOF'

-- 检查结果
SELECT 'Total admins:' as info, COUNT(*) as count FROM admin;
SELECT 'Admin accounts:' as info, username, nickname, role, status FROM admin ORDER BY id;
EOF

# 执行SQL脚本
echo "📊 执行数据库操作..."
psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -f temp_admin_setup.sql

# 清理临时文件
rm temp_admin_setup.sql

echo "🎉 管理员账户生成完成！"
echo "💡 所有账户密码都是随机生成的12位字符串"
echo "🔑 请妥善保存这些账户信息"
