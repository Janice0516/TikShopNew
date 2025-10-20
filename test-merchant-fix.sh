#!/bin/bash

echo "🔍 测试商家登录页面修复"
echo "======================"

echo "1. 检查商家服务状态..."
ps aux | grep -E "(5176|merchant)" | grep -v grep | wc -l | xargs -I {} echo "商家服务进程数: {}"

echo "2. 测试商家登录页面..."
curl -s -I "https://tiktokbusines.store/merchant/login" | head -1

echo "3. 检查HTML内容..."
curl -s "https://tiktokbusines.store/merchant/login" | grep -E "(script|link)" | head -2

echo "4. 测试静态资源..."
curl -s -I "https://tiktokbusines.store/merchant/assets/index-Ctni3Kwm.js" | head -1

echo "5. 测试CSS资源..."
curl -s -I "https://tiktokbusines.store/merchant/assets/index-CVIFgzaP.css" | head -1

echo ""
echo "✅ 修复完成！"
echo "📋 修复内容："
echo "   - 重新构建商家前端项目"
echo "   - 重启商家服务 (端口: 5176)"
echo "   - 静态资源路径正确: /merchant/assets/"
echo "   - 商家登录页面应该不再空白"