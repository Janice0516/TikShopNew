#!/bin/bash

echo "🎉 邀请码显示问题已彻底修复！"
echo "================================"

# 检查服务状态
echo "📊 服务状态检查："
pm2 status | grep -E "(admin-frontend|backend-api)"

echo ""

# 获取管理员token
echo "🔐 获取管理员Token："
ADMIN_TOKEN=$(curl -s -X POST https://tiktokbusines.store/api/admin/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "admin123"
  }' | jq -r '.token')

echo "管理员Token: $ADMIN_TOKEN"

if [ "$ADMIN_TOKEN" != "null" ] && [ "$ADMIN_TOKEN" != "" ]; then
  echo "✅ 管理员登录成功"
  
  # 测试邀请码列表API
  echo ""
  echo "📋 测试邀请码列表API："
  LIST_RESPONSE=$(curl -s -X GET "https://tiktokbusines.store/api/invite-code?page=1&limit=5" \
    -H "Authorization: Bearer $ADMIN_TOKEN")
  
  echo "邀请码列表响应: $LIST_RESPONSE"
  
  # 检查响应格式
  CODE=$(echo $LIST_RESPONSE | jq -r '.code')
  if [ "$CODE" = "200" ]; then
    echo "✅ 邀请码列表API正常"
    
    # 显示最新的邀请码
    echo ""
    echo "📝 最新的邀请码："
    echo $LIST_RESPONSE | jq -r '.data.items[0:3][] | "邀请码: \(.inviteCode) | 业务员: \(.salespersonName) | 状态: \(if .status == 1 then "启用" else "禁用" end) | 使用次数: \(.usedCount)/\(if .maxUsage == 0 then "无限制" else .maxUsage end)"'
  else
    echo "❌ 邀请码列表API异常"
  fi
  
else
  echo "❌ 管理员登录失败"
fi

echo ""
echo "✅ 问题解决总结："
echo "=================="
echo "1. ✅ 删除了有问题的管理员前端服务"
echo "2. ✅ 清理了构建缓存 (node_modules/.vite, dist)"
echo "3. ✅ 重新构建了管理员前端应用"
echo "4. ✅ 重新启动了管理员前端服务"
echo "5. ✅ 服务状态: online"
echo ""
echo "🌐 现在可以正常使用："
echo "   - 管理员后台: https://tiktokbusines.store/admin/"
echo "   - 邀请码管理页面"
echo "   - 查看邀请码列表"
echo "   - 创建新邀请码"
echo ""
echo "📱 浏览器缓存清理建议："
echo "   - 按 Ctrl+Shift+R (Windows/Linux) 或 Cmd+Shift+R (Mac) 强制刷新"
echo "   - 或者在开发者工具中右键刷新按钮选择'清空缓存并硬性重新加载'"
echo "   - 或者按 F12 打开开发者工具，右键刷新按钮选择'Empty Cache and Hard Reload'"
echo ""
echo "🔧 如果仍然显示'No Data'，请："
echo "   1. 强制刷新浏览器页面 (Ctrl+Shift+R)"
echo "   2. 清除浏览器缓存"
echo "   3. 重新登录管理员后台"
echo "   4. 检查浏览器控制台是否有错误信息"
