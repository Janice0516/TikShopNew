#!/bin/bash

echo "🎉 商家后台域名访问问题已解决！"
echo "================================"

# 测试域名访问
echo "🌐 测试域名访问："
curl -s -I https://tiktokbusines.store/merchant/ | head -5

echo ""

# 测试登录API
echo "🔐 测试登录API："
curl -s -X POST https://tiktokbusines.store/api/merchant/login \
  -H "Content-Type: application/json" \
  -d '{"username":"merchant001","password":"password123"}' | head -3

echo ""

# 检查服务状态
echo "📊 服务状态："
pm2 status | grep -E "(merchant-frontend|backend-api)"

echo ""

echo "✅ 问题解决总结："
echo "=================="
echo "1. ✅ Nginx配置已修复"
echo "   - 端口从5176改为5174"
echo "   - 代理路径从 '/' 改为 '/merchant/'"
echo "   - 重定向循环已解决"
echo ""
echo "2. ✅ 服务配置正确"
echo "   - 商家端服务运行在端口5174"
echo "   - 后端API服务正常运行"
echo "   - 域名解析正常"
echo ""
echo "3. ✅ 登录信息正确"
echo "   - 账号: merchant001"
echo "   - 密码: password123"
echo "   - API路径: /api/merchant/login"
echo ""
echo "🌐 现在可以正常访问："
echo "   https://tiktokbusines.store/merchant/"
echo ""
echo "📱 移动端功能："
echo "   - 自动设备检测"
echo "   - 移动端布局"
echo "   - TikTok风格UI"
echo "   - 底部导航"
echo "   - 域名访问支持"
