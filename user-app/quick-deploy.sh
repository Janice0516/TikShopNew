#!/bin/bash

# TikTok Shop Vue.js 项目快速部署脚本
echo "🚀 TikTok Shop Vue.js 项目快速部署脚本"
echo "=================================="

# 检查Node.js版本
echo "📋 检查Node.js版本..."
node --version
npm --version

# 安装依赖
echo "📦 安装项目依赖..."
npm install

# 跳过类型检查，直接构建
echo "🔨 构建项目 (跳过类型检查)..."
npm run build-only

# 检查构建结果
if [ -d "dist" ]; then
    echo "✅ 构建成功！"
    echo "📁 构建文件列表:"
    ls -la dist/
    echo ""
    echo "📊 构建统计:"
    du -sh dist/
    echo ""
    echo "🎉 项目已准备好部署！"
    echo ""
    echo "📤 推荐部署平台:"
    echo "1. Vercel: https://vercel.com (最简单)"
    echo "2. Netlify: https://netlify.com"
    echo "3. Render: https://render.com"
    echo ""
    echo "🌐 GitHub仓库: https://github.com/Janice0516/TikShop"
    echo "📖 详细部署指南: DEPLOYMENT_GUIDE.md"
else
    echo "❌ 构建失败！请检查错误信息"
    exit 1
fi
