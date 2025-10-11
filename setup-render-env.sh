#!/bin/bash

# Render CLI 环境变量设置脚本

echo "🚀 使用Render CLI设置环境变量..."

# 检查是否安装了Render CLI
if ! command -v render &> /dev/null; then
    echo "❌ Render CLI未安装"
    echo "📦 请先安装: npm install -g @render/cli"
    echo "🔑 然后登录: render auth login"
    exit 1
fi

# 设置用户前端环境变量
echo "📱 设置用户前端环境变量..."

# 假设服务名为 "tiktokshop-user-frontend"
SERVICE_NAME="tiktokshop-user-frontend"

# 设置环境变量
render env set VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api --service $SERVICE_NAME
render env set VITE_APP_TITLE="TikTok Shop" --service $SERVICE_NAME
render env set VITE_APP_VERSION=1.0.0 --service $SERVICE_NAME
render env set VITE_NODE_ENV=production --service $SERVICE_NAME

echo "✅ 环境变量设置完成！"
echo ""
echo "📋 已设置的环境变量："
echo "   - VITE_API_BASE_URL: https://tiktokshop-api.onrender.com/api"
echo "   - VITE_APP_TITLE: TikTok Shop"
echo "   - VITE_APP_VERSION: 1.0.0"
echo "   - VITE_NODE_ENV: production"
echo ""
echo "🔄 服务将自动重新部署以应用新的环境变量"
