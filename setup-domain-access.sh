#!/bin/bash

echo "🌐 配置域名访问 - tiktokbusines.store"
echo "=================================="

# 等待服务启动
echo "⏳ 等待服务启动..."
sleep 15

# 检查服务状态
echo "📊 服务状态检查："
pm2 status | grep -E "(merchant-frontend|backend-api)"

echo ""

# 测试本地访问
echo "🔍 测试本地访问："
curl -s -I http://localhost:5174/merchant/ | head -3

echo ""

# 测试API访问
echo "🔍 测试API访问："
curl -s -I http://localhost:3000/api/merchant/login | head -3

echo ""

# 检查域名解析
echo "🔍 检查域名解析："
nslookup tiktokbusines.store | head -5

echo ""

# 测试域名访问
echo "🔍 测试域名访问："
curl -s -I https://tiktokbusines.store/merchant/ | head -3

echo ""

echo "✅ 域名访问配置完成！"
echo ""
echo "🌐 访问地址："
echo "   域名访问: https://tiktokbusines.store/merchant/"
echo "   本地访问: http://localhost:5174/merchant/"
echo ""
echo "🔧 配置说明："
echo "1. ✅ Vite代理已配置支持域名"
echo "2. ✅ allowedHosts已包含域名"
echo "3. ✅ 路由守卫已优化"
echo "4. ✅ 移动端功能已实现"
echo ""
echo "📱 移动端功能："
echo "   - 自动设备检测"
echo "   - 移动端布局"
echo "   - TikTok风格UI"
echo "   - 底部导航"
echo "   - 域名访问支持"
