#!/bin/bash

# TikTok Shop 前端服务部署脚本
# 用于在Render上快速部署所有前端服务

echo "🚀 TikTok Shop 前端服务部署指南"
echo "=================================="
echo ""

echo "📋 部署步骤："
echo "1. 商家后台 (Merchant Backend)"
echo "2. 管理后台 (Admin Backend)" 
echo "3. 用户前端 (User App - UniApp)"
echo ""

echo "🔧 商家后台配置："
echo "Name: tiktokshop-merchant"
echo "Root Directory: merchant"
echo "Build Command: npm install && npm run build"
echo "Publish Directory: dist"
echo "Environment Variables:"
echo "  VITE_API_BASE_URL: http://localhost:3000/api"
echo ""

echo "🔧 管理后台配置："
echo "Name: tiktokshop-admin"
echo "Root Directory: admin"
echo "Build Command: npm install && npm run build"
echo "Publish Directory: dist"
echo "Environment Variables:"
echo "  VITE_API_BASE_URL: http://localhost:3000/api"
echo ""

echo "🔧 用户前端配置 (UniApp)："
echo "Name: tiktokshop-user"
echo "Root Directory: user-app"
echo "Build Command: npm install && npm run build:h5"
echo "Publish Directory: dist/build/h5"
echo "Environment Variables:"
echo "  VITE_API_BASE_URL: http://localhost:3000/api"
echo ""

echo "🌐 预期访问地址："
echo "API服务: http://localhost:3000/api"
echo "商家后台: http://localhost:5176"
echo "管理后台: http://localhost:5175"
echo "用户前端: http://localhost:5177"
echo ""

echo "🔑 测试账号："
echo "商家后台: merchant001 / 123456"
echo "管理后台: admin / admin123"
echo ""

echo "✅ 部署完成后请测试："
echo "1. 访问各个前端页面"
echo "2. 测试登录功能"
echo "3. 验证API连接"
echo "4. 检查数据加载"
echo ""

echo "📝 注意事项："
echo "- 每个服务首次构建需要3-5分钟"
echo "- 确保所有环境变量都正确设置"
echo "- 如果构建失败，检查依赖和配置"
echo ""

echo "🎉 开始部署吧！"
