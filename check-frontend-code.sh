#!/bin/bash

echo "🔍 检查前端代码是否生效"
echo "======================"

# 1. 检查构建文件时间
echo "📁 1. 检查构建文件时间："
ls -la /root/TikShop/admin/dist/assets/invite-code-*.js

echo ""

# 2. 检查源代码修改时间
echo "🔧 2. 检查源代码修改时间："
ls -la /root/TikShop/admin/src/views/invite-code/index.vue

echo ""

# 3. 检查构建文件内容
echo "📄 3. 检查构建文件内容："
if grep -q "response.data.code === 200" /root/TikShop/admin/dist/assets/invite-code-*.js; then
  echo "✅ 构建文件包含修复的代码"
else
  echo "❌ 构建文件不包含修复的代码"
fi

echo ""

# 4. 检查源代码内容
echo "📄 4. 检查源代码内容："
if grep -q "response.data.code === 200" /root/TikShop/admin/src/views/invite-code/index.vue; then
  echo "✅ 源代码包含修复的代码"
else
  echo "❌ 源代码不包含修复的代码"
fi

echo ""

# 5. 检查服务状态
echo "📊 5. 检查服务状态："
pm2 status admin-frontend

echo ""

# 6. 测试API响应
echo "🔐 6. 测试API响应："
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

# 7. 检查浏览器缓存
echo "🌐 7. 浏览器缓存问题："
echo "   如果API正常但页面仍显示'No Data'，可能是浏览器缓存问题"
echo "   请尝试："
echo "   1. 强制刷新: Ctrl+Shift+R (Windows/Linux) 或 Cmd+Shift+R (Mac)"
echo "   2. 清除浏览器缓存"
echo "   3. 使用无痕模式"
echo "   4. 检查浏览器控制台是否有JavaScript错误"

echo ""

# 8. 检查Nginx缓存
echo "🔧 8. 检查Nginx缓存："
echo "   检查是否有Nginx缓存问题"
curl -s -I https://tiktokbusines.store/admin/ | grep -i cache

echo ""

echo "💡 建议解决方案："
echo "=================="
echo "1. 如果构建文件时间早于源代码修改时间，需要重新构建"
echo "2. 如果API正常但页面不显示，可能是浏览器缓存问题"
echo "3. 检查浏览器控制台是否有JavaScript错误"
echo "4. 尝试强制刷新页面"
echo "5. 检查网络请求是否成功"
