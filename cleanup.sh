#!/bin/bash

echo "🧹 清理TikTok Shop项目..."

# 停止所有PM2服务
echo "🔧 停止所有服务..."
pm2 delete all 2>/dev/null || echo "无运行中的服务"

# 清理日志文件
echo "📝 清理日志文件..."
rm -rf logs/* 2>/dev/null || echo "无日志文件"

# 清理临时文件
echo "🗑️ 清理临时文件..."
find . -name "*.log" -not -path "./node_modules/*" -delete 2>/dev/null || echo "无日志文件"
find . -name "*.tmp" -not -path "./node_modules/*" -delete 2>/dev/null || echo "无临时文件"
find . -name ".DS_Store" -delete 2>/dev/null || echo "无系统文件"

# 清理构建文件
echo "🏗️ 清理构建文件..."
rm -rf admin/dist 2>/dev/null || echo "无admin构建文件"
rm -rf merchant/dist 2>/dev/null || echo "无merchant构建文件"
rm -rf user-app/dist 2>/dev/null || echo "无user-app构建文件"
rm -rf ecommerce-backend/dist 2>/dev/null || echo "无backend构建文件"

# 清理node_modules（可选）
if [ "$1" = "--deep" ]; then
    echo "🗂️ 深度清理node_modules..."
    rm -rf node_modules 2>/dev/null || echo "无根目录node_modules"
    rm -rf admin/node_modules 2>/dev/null || echo "无admin node_modules"
    rm -rf merchant/node_modules 2>/dev/null || echo "无merchant node_modules"
    rm -rf user-app/node_modules 2>/dev/null || echo "无user-app node_modules"
    rm -rf ecommerce-backend/node_modules 2>/dev/null || echo "无backend node_modules"
fi

# 清理备份文件
echo "💾 清理备份文件..."
rm -rf user-app-backup-* 2>/dev/null || echo "无备份文件"

echo "✅ 项目清理完成！"
echo ""
echo "📋 清理内容:"
echo "   ✅ PM2服务已停止"
echo "   ✅ 日志文件已清理"
echo "   ✅ 临时文件已清理"
echo "   ✅ 构建文件已清理"
if [ "$1" = "--deep" ]; then
    echo "   ✅ node_modules已清理"
fi
echo ""
echo "🚀 重新启动项目:"
echo "   ./start-all.sh"
