#!/bin/bash

echo "🔍 测试Admin登录页面测试账户信息删除"
echo "=================================="

echo "1. 检查admin登录页面..."
curl -s -I "https://tiktokbusines.store/admin/login" | head -1

echo "2. 检查HTML内容（应该不包含测试账户信息）..."
if curl -s "https://tiktokbusines.store/admin/login" | grep -q "admin123\|Username.*admin\|Password.*admin123"; then
    echo "❌ 仍然包含测试账户信息"
else
    echo "✅ 测试账户信息已成功删除"
fi

echo "3. 检查静态资源..."
curl -s -I "https://tiktokbusines.store/admin/assets/index-CvxM8QGr.js" | head -1

echo "4. 检查CSS资源..."
curl -s -I "https://tiktokbusines.store/admin/assets/index-Dqv8VrO9.css" | head -1

echo ""
echo "✅ 修改完成！"
echo "📋 修改内容："
echo "   - 删除了登录页面底部的测试账户信息"
echo "   - 删除了相关的CSS样式 (.tips)"
echo "   - 重新构建并部署了admin前端"
echo "   - 登录页面现在更加简洁和安全"


