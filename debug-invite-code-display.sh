#!/bin/bash

echo "🔍 检查邀请码显示问题的可能原因："
echo "=================================="
echo ""
echo "📊 1. 检查服务状态："
pm2 status | grep admin-frontend
echo ""
echo "📁 2. 检查构建文件："
ls -la /root/TikShop/admin/dist/assets/invite-code-*.js
echo ""
echo "📄 3. 检查构建文件内容："
if grep -q "e.data.code===200" /root/TikShop/admin/dist/assets/invite-code-*.js; then
  echo "✅ 构建文件包含修复的代码"
else
  echo "❌ 构建文件不包含修复的代码"
fi
echo ""
echo "🌐 4. 检查API响应："
LOGIN_RESPONSE=$(curl -s -X POST https://tiktokbusines.store/api/admin/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "admin123"
  }')

TOKEN=$(echo $LOGIN_RESPONSE | jq -r '.token')
if [ "$TOKEN" != "null" ] && [ "$TOKEN" != "" ]; then
  echo "✅ 管理员登录成功"
  
  LIST_RESPONSE=$(curl -s -X GET "https://tiktokbusines.store/api/invite-code?page=1&limit=3" \
    -H "Authorization: Bearer $TOKEN")
  
  CODE=$(echo $LIST_RESPONSE | jq -r '.code')
  TOTAL=$(echo $LIST_RESPONSE | jq -r '.data.total')
  echo "📝 API响应: code=$CODE, total=$TOTAL"
  
  if [ "$CODE" = "200" ] && [ "$TOTAL" -gt 0 ]; then
    echo "✅ API数据正常"
  else
    echo "❌ API数据异常"
  fi
else
  echo "❌ 管理员登录失败"
fi

echo ""
echo "🔧 5. 可能的问题和解决方案："
echo "============================="
echo ""
echo "问题1: 浏览器缓存"
echo "解决方案:"
echo "  1. 强制刷新: Ctrl+Shift+R (Windows/Linux) 或 Cmd+Shift+R (Mac)"
echo "  2. 清除浏览器缓存"
echo "  3. 使用无痕模式"
echo "  4. 检查浏览器开发者工具的网络面板"
echo ""
echo "问题2: JavaScript错误"
echo "解决方案:"
echo "  1. 打开浏览器开发者工具 (F12)"
echo "  2. 查看控制台 (Console) 是否有错误"
echo "  3. 查看网络面板 (Network) 是否成功加载JS文件"
echo ""
echo "问题3: 服务未更新"
echo "解决方案:"
echo "  1. 检查PM2服务状态"
echo "  2. 重启管理员前端服务"
echo ""
echo "问题4: 构建文件问题"
echo "解决方案:"
echo "  1. 重新构建前端"
echo "  2. 清除构建缓存"
echo ""
echo "💡 建议的调试步骤："
echo "=================="
echo "1. 打开浏览器开发者工具 (F12)"
echo "2. 查看控制台是否有JavaScript错误"
echo "3. 查看网络面板，确认是否加载了最新的JS文件"
echo "4. 强制刷新页面 (Ctrl+Shift+R)"
echo "5. 如果还有问题，使用无痕模式测试"
echo ""
echo "🌐 测试页面："
echo "============"
echo "可以访问: https://tiktokbusines.store/admin-test.html"
echo "这个页面可以测试API响应和解析逻辑"
