#!/bin/bash

echo "🔍 检查管理员后台邀请码页面显示问题"
echo "=================================="

# 1. 检查服务状态
echo "📊 1. 检查服务状态："
pm2 status | grep -E "(admin-frontend|backend-api)"

echo ""

# 2. 测试管理员登录
echo "🔐 2. 测试管理员登录："
LOGIN_RESPONSE=$(curl -s -X POST https://tiktokbusines.store/api/admin/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "admin123"
  }')

echo "登录响应: $LOGIN_RESPONSE"

# 提取token
TOKEN=$(echo $LOGIN_RESPONSE | jq -r '.token')
if [ "$TOKEN" != "null" ] && [ "$TOKEN" != "" ]; then
  echo "✅ 管理员登录成功"
  
  # 3. 测试邀请码列表API
  echo ""
  echo "📋 3. 测试邀请码列表API："
  LIST_RESPONSE=$(curl -s -X GET "https://tiktokbusines.store/api/invite-code?page=1&limit=5" \
    -H "Authorization: Bearer $TOKEN")
  
  echo "邀请码列表响应: $LIST_RESPONSE"
  
  CODE=$(echo $LIST_RESPONSE | jq -r '.code')
  if [ "$CODE" = "200" ]; then
    echo "✅ 邀请码列表API正常"
    TOTAL=$(echo $LIST_RESPONSE | jq -r '.data.total')
    echo "📝 邀请码总数: $TOTAL"
    
    if [ "$TOTAL" -gt 0 ]; then
      echo "📝 前5个邀请码:"
      echo $LIST_RESPONSE | jq -r '.data.items[] | "  - \(.inviteCode) (\(.salespersonName))"'
    fi
  else
    echo "❌ 邀请码列表API异常"
  fi
  
  # 4. 测试邀请码统计API
  echo ""
  echo "📊 4. 测试邀请码统计API："
  STATS_RESPONSE=$(curl -s -X GET "https://tiktokbusines.store/api/invite-code/stats" \
    -H "Authorization: Bearer $TOKEN")
  
  echo "统计响应: $STATS_RESPONSE"
  
  STATS_CODE=$(echo $STATS_RESPONSE | jq -r '.code')
  if [ "$STATS_CODE" = "200" ]; then
    echo "✅ 邀请码统计API正常"
  else
    echo "❌ 邀请码统计API异常"
  fi
  
else
  echo "❌ 管理员登录失败"
fi

echo ""

# 5. 检查管理员前端页面
echo "🌐 5. 检查管理员前端页面："
ADMIN_PAGE_RESPONSE=$(curl -s -I https://tiktokbusines.store/admin/)
echo "管理员页面状态: $(echo $ADMIN_PAGE_RESPONSE | head -1)"

echo ""

# 6. 检查邀请码页面路由
echo "🛣️ 6. 检查邀请码页面路由："
INVITE_PAGE_RESPONSE=$(curl -s -I https://tiktokbusines.store/admin/#/invite-code)
echo "邀请码页面状态: $(echo $INVITE_PAGE_RESPONSE | head -1)"

echo ""

# 7. 检查前端构建文件
echo "📁 7. 检查前端构建文件："
if [ -d "/root/TikShop/admin/dist" ]; then
  echo "✅ 管理员前端构建目录存在"
  echo "📄 构建文件列表:"
  ls -la /root/TikShop/admin/dist/ | head -10
else
  echo "❌ 管理员前端构建目录不存在"
fi

echo ""

# 8. 检查邀请码页面组件
echo "📄 8. 检查邀请码页面组件："
if [ -f "/root/TikShop/admin/src/views/invite-code/index.vue" ]; then
  echo "✅ 邀请码页面组件存在"
  echo "📄 组件文件大小: $(wc -c < /root/TikShop/admin/src/views/invite-code/index.vue) 字节"
else
  echo "❌ 邀请码页面组件不存在"
fi

echo ""

# 9. 检查国际化文件
echo "🌍 9. 检查国际化文件："
if [ -f "/root/TikShop/admin/src/i18n/locales/zh-CN.json" ]; then
  echo "✅ 中文国际化文件存在"
  INVITE_CODE_TRANSLATION=$(grep -o '"inviteCode": "[^"]*"' /root/TikShop/admin/src/i18n/locales/zh-CN.json)
  echo "📝 邀请码翻译: $INVITE_CODE_TRANSLATION"
else
  echo "❌ 中文国际化文件不存在"
fi

echo ""

# 10. 检查API接口文件
echo "🔌 10. 检查API接口文件："
if [ -f "/root/TikShop/admin/src/api/invite-code.ts" ]; then
  echo "✅ 邀请码API接口文件存在"
  echo "📄 API接口文件大小: $(wc -c < /root/TikShop/admin/src/api/invite-code.ts) 字节"
else
  echo "❌ 邀请码API接口文件不存在"
fi

echo ""
echo "🔍 问题诊断总结："
echo "=================="
echo "如果API都正常但页面不显示邀请码，可能的原因："
echo "1. 浏览器缓存问题 - 请强制刷新页面 (Ctrl+Shift+R)"
echo "2. JavaScript错误 - 请检查浏览器控制台"
echo "3. 前端构建问题 - 需要重新构建"
echo "4. 路由配置问题 - 检查路由是否正确"
echo "5. 组件加载问题 - 检查组件是否正确导入"
echo ""
echo "💡 建议解决方案："
echo "1. 清除浏览器缓存并重新登录"
echo "2. 检查浏览器开发者工具的控制台错误"
echo "3. 重新构建管理员前端"
echo "4. 检查网络请求是否成功"
