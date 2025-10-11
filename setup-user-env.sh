#!/bin/bash

# 用户前端环境变量设置脚本

echo "🚀 设置用户前端环境变量..."

# 检查是否已存在.env文件
if [ -f "user-app/.env" ]; then
    echo "⚠️  .env文件已存在，是否覆盖？(y/n)"
    read -r response
    if [[ "$response" != "y" ]]; then
        echo "❌ 取消设置"
        exit 1
    fi
fi

# 创建.env文件
cat > user-app/.env << EOF
# API基础URL
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api

# 应用配置
VITE_APP_TITLE=TikTok Shop
VITE_APP_VERSION=1.0.0

# 开发环境配置
VITE_NODE_ENV=development
EOF

echo "✅ 环境变量文件已创建: user-app/.env"

# 创建生产环境.env文件
cat > user-app/.env.production << EOF
# 生产环境API基础URL
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api

# 应用配置
VITE_APP_TITLE=TikTok Shop
VITE_APP_VERSION=1.0.0

# 生产环境配置
VITE_NODE_ENV=production
EOF

echo "✅ 生产环境变量文件已创建: user-app/.env.production"

echo ""
echo "📋 环境变量配置完成！"
echo "🔧 API Base URL: https://tiktokshop-api.onrender.com/api"
echo "📱 应用标题: TikTok Shop"
echo "🏷️  版本: 1.0.0"
echo ""
echo "💡 提示:"
echo "   - 开发环境使用 .env 文件"
echo "   - 生产环境使用 .env.production 文件"
echo "   - 如需修改API地址，请编辑对应的环境变量文件"
echo ""
echo "🚀 现在可以启动用户前端项目了！"
