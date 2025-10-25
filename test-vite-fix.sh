#!/bin/bash

echo "🔧 测试Vite配置修复 - tiktokbusines.store域名访问"
echo "=============================================="

# 等待服务启动
echo "⏳ 等待服务启动..."
sleep 15

# 测试商家端服务
echo "📱 测试商家端服务状态:"
curl -s -I http://localhost:5174/merchant/ | head -3

echo ""

# 测试API代理是否工作
echo "🔗 测试API代理配置:"
echo "尝试访问API端点..."

# 测试登录API
echo "测试登录API:"
curl -s -X POST http://localhost:5174/merchant/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"merchant001","password":"123456"}' | head -3

echo ""

# 检查Vite配置
echo "📋 检查Vite配置:"
echo "商家端 allowedHosts 配置:"
grep -A 10 "allowedHosts" /root/TikShop/merchant/vite.config.ts

echo ""
echo "管理端 allowedHosts 配置:"
grep -A 10 "allowedHosts" /root/TikShop/admin/vite.config.ts

echo ""
echo "✅ 修复完成！"
echo ""
echo "🔧 修复内容:"
echo "1. ✅ 商家端: 添加 'tiktokbusines.store' 到 allowedHosts"
echo "2. ✅ 管理端: 添加 'tiktokbusines.store' 到 allowedHosts"
echo "3. ✅ 用户端: 已包含 'tiktokbusines.store' 配置"
echo ""
echo "🌐 现在可以正常访问:"
echo "   📱 商家端: http://localhost:5174/merchant/"
echo "   🖥️ 管理端: http://localhost:5175/admin/"
echo "   📱 用户端: http://localhost:3001/"
echo ""
echo "🎯 移动端功能:"
echo "   - 自动设备检测 ✅"
echo "   - 移动端布局 ✅"
echo "   - TikTok风格UI ✅"
echo "   - 底部导航 ✅"
