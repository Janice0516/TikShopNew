#!/bin/bash

# 生成用户前端环境变量文件

echo "🚀 生成用户前端环境变量文件..."

# 创建开发环境 .env 文件
cat > user-app/.env << 'EOF'
# 开发环境配置
VITE_API_BASE_URL=http://localhost:3000/api
VITE_APP_TITLE=TikTok Shop
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=development
VITE_DEBUG=true
EOF

echo "✅ 开发环境 .env 文件已创建"

# 创建生产环境 .env.production 文件
cat > user-app/.env.production << 'EOF'
# 生产环境配置
VITE_API_BASE_URL=http://localhost:3000/api
VITE_APP_TITLE=TikTok Shop
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=production
VITE_DEBUG=false
EOF

echo "✅ 生产环境 .env.production 文件已创建"

# 创建测试环境 .env.test 文件
cat > user-app/.env.test << 'EOF'
# 测试环境配置
VITE_API_BASE_URL=http://localhost:3000/api
VITE_APP_TITLE=TikTok Shop (Test)
VITE_APP_VERSION=1.0.0-test
VITE_NODE_ENV=test
VITE_DEBUG=true
EOF

echo "✅ 测试环境 .env.test 文件已创建"

# 创建本地开发环境 .env.local 文件
cat > user-app/.env.local << 'EOF'
# 本地开发环境配置
VITE_API_BASE_URL=http://localhost:3000/api
VITE_APP_TITLE=TikTok Shop (Local)
VITE_APP_VERSION=1.0.0-local
VITE_NODE_ENV=development
VITE_DEBUG=true
EOF

echo "✅ 本地环境 .env.local 文件已创建"

echo ""
echo "📋 环境变量文件创建完成！"
echo ""
echo "📁 文件列表："
echo "   - user-app/.env (开发环境)"
echo "   - user-app/.env.production (生产环境)"
echo "   - user-app/.env.test (测试环境)"
echo "   - user-app/.env.local (本地环境)"
echo ""
echo "🔧 环境变量说明："
echo "   - VITE_API_BASE_URL: API服务地址"
echo "   - VITE_APP_TITLE: 应用标题"
echo "   - VITE_APP_VERSION: 应用版本"
echo "   - VITE_NODE_ENV: 环境类型"
echo "   - VITE_DEBUG: 调试模式"
echo ""
echo "💡 使用提示："
echo "   - 开发时使用 .env 文件"
echo "   - 构建生产版本时使用 .env.production"
echo "   - 本地开发时可以使用 .env.local"
echo "   - 测试时使用 .env.test"
echo ""
echo "🚀 现在可以启动项目了！"
