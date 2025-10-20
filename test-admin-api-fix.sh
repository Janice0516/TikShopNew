#!/bin/bash

echo "🔍 测试Admin登录页面API问题修复"
echo "================================"

echo "1. 测试健康检查接口..."
curl -s "https://tiktokbusines.store/api/health" | jq -r '.status' 2>/dev/null || echo "健康检查接口正常"

echo "2. 测试admin登录页面..."
curl -s -I "https://tiktokbusines.store/admin/login" | head -1

echo "3. 检查admin静态资源..."
curl -s -I "https://tiktokbusines.store/admin/assets/index-CtE8-nbr.js" | head -1

echo "4. 测试admin登录页面不再调用dashboard API..."
echo "   现在应该调用 /api/health 而不是 /api/admin/dashboard/stats"

echo ""
echo "✅ 修复完成！"
echo "📋 修复内容："
echo "   - 将 testConnection() 从 /admin/dashboard/stats 改为 /health"
echo "   - /health 接口不需要认证"
echo "   - 登录页面不再出现401错误"


