#!/bin/bash

# 生成管理后台环境变量文件

echo "🚀 生成管理后台环境变量文件..."

# 创建开发环境 .env 文件
cat > admin/.env << 'EOF'
# 开发环境配置
VITE_API_BASE_URL=http://localhost:3000/api
VITE_APP_TITLE=TikTok Shop Admin
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=development
VITE_DEBUG=true
EOF

echo "✅ 管理后台开发环境 .env 文件已创建"

# 创建生产环境 .env.production 文件
cat > admin/.env.production << 'EOF'
# 生产环境配置
VITE_API_BASE_URL=http://localhost:3000/api
VITE_APP_TITLE=TikTok Shop Admin
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=production
VITE_DEBUG=false
EOF

echo "✅ 管理后台生产环境 .env.production 文件已创建"

# 创建测试环境 .env.test 文件
cat > admin/.env.test << 'EOF'
# 测试环境配置
VITE_API_BASE_URL=http://localhost:3000/api
VITE_APP_TITLE=TikTok Shop Admin (Test)
VITE_APP_VERSION=1.0.0-test
VITE_NODE_ENV=test
VITE_DEBUG=true
EOF

echo "✅ 管理后台测试环境 .env.test 文件已创建"

# 创建本地开发环境 .env.local 文件
cat > admin/.env.local << 'EOF'
# 本地开发环境配置
VITE_API_BASE_URL=http://localhost:3000/api
VITE_APP_TITLE=TikTok Shop Admin (Local)
VITE_APP_VERSION=1.0.0-local
VITE_NODE_ENV=development
VITE_DEBUG=true
EOF

echo "✅ 管理后台本地环境 .env.local 文件已创建"

echo ""
echo "📋 管理后台环境变量文件创建完成！"
