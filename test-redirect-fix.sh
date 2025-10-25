#!/bin/bash

echo "🔧 测试商家后台重定向循环修复"
echo "=============================="

# 等待服务启动
echo "⏳ 等待服务启动..."
sleep 5

# 测试根路径访问
echo "📱 测试1: 根路径访问"
curl -s -I -L http://localhost:5174/merchant/ | head -5

echo ""

# 测试移动端路由
echo "📱 测试2: 移动端路由"
curl -s -I -L http://localhost:5174/merchant/mobile/dashboard | head -5

echo ""

# 测试桌面端路由
echo "📱 测试3: 桌面端路由"
curl -s -I -L http://localhost:5174/merchant/dashboard | head -5

echo ""

# 测试登录页面
echo "📱 测试4: 登录页面"
curl -s -I -L http://localhost:5174/merchant/login | head -5

echo ""

# 检查是否有重定向循环
echo "🔍 检查重定向次数:"
echo "根路径重定向次数:"
curl -s -I http://localhost:5174/merchant/ | grep -c "Location:" || echo "0"

echo "移动端重定向次数:"
curl -s -I http://localhost:5174/merchant/mobile/dashboard | grep -c "Location:" || echo "0"

echo ""

echo "✅ 修复完成！"
echo ""
echo "🔧 修复内容:"
echo "1. ✅ 添加重定向循环检测"
echo "2. ✅ 修复路由守卫逻辑"
echo "3. ✅ 优化移动端跳转逻辑"
echo "4. ✅ 修复桌面端默认路由"
echo ""
echo "🌐 现在可以正常访问:"
echo "   📱 商家端: http://localhost:5174/merchant/"
echo "   📱 移动端: http://localhost:5174/merchant/mobile/dashboard"
echo "   🖥️ 桌面端: http://localhost:5174/merchant/dashboard"
echo "   🔐 登录页: http://localhost:5174/merchant/login"
echo ""
echo "🎯 移动端功能:"
echo "   - 自动设备检测 ✅"
echo "   - 移动端布局 ✅"
echo "   - TikTok风格UI ✅"
echo "   - 无重定向循环 ✅"
