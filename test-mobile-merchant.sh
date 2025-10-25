#!/bin/bash

echo "🧪 测试商家后台移动端自动转换功能"
echo "=================================="

# 测试桌面端访问
echo "📱 测试1: 模拟桌面端访问"
curl -s -H "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36" \
  "http://localhost:5174/merchant/" | grep -o "title.*" | head -1

echo ""

# 测试移动端访问
echo "📱 测试2: 模拟iPhone访问"
curl -s -H "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15" \
  "http://localhost:5174/merchant/" | grep -o "title.*" | head -1

echo ""

# 测试Android访问
echo "📱 测试3: 模拟Android访问"
curl -s -H "User-Agent: Mozilla/5.0 (Linux; Android 10; SM-G975F) AppleWebKit/537.36" \
  "http://localhost:5174/merchant/" | grep -o "title.*" | head -1

echo ""

# 测试移动端路由
echo "📱 测试4: 直接访问移动端路由"
curl -s -H "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15" \
  "http://localhost:5174/merchant/mobile/dashboard" | grep -o "title.*" | head -1

echo ""

echo "✅ 测试完成！"
echo ""
echo "🌐 访问地址："
echo "   桌面端: http://localhost:5174/merchant/"
echo "   移动端: http://localhost:5174/merchant/mobile/dashboard"
echo ""
echo "📱 移动端功能："
echo "   - 自动设备检测"
echo "   - 移动端布局"
echo "   - 底部导航"
echo "   - 触摸优化"
echo "   - TikTok风格UI"
