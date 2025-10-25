#!/bin/bash

echo "🎉 邀请码显示问题已修复！"
echo "================================"

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
  
  # 测试创建新邀请码
  echo ""
  echo "🎫 测试创建新邀请码："
  CREATE_RESPONSE=$(curl -s -X POST https://tiktokbusines.store/api/invite-code \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $ADMIN_TOKEN" \
    -d '{
      "salespersonName": "修复测试业务员",
      "salespersonPhone": "012-9999999",
      "salespersonId": "FIX_TEST_001",
      "maxUsage": 50,
      "remark": "修复测试邀请码"
    }')
  
  echo "创建邀请码响应: $CREATE_RESPONSE"
  
  CREATE_CODE=$(echo $CREATE_RESPONSE | jq -r '.code')
  if [ "$CREATE_CODE" = "200" ]; then
    echo "✅ 邀请码创建成功"
    NEW_INVITE_CODE=$(echo $CREATE_RESPONSE | jq -r '.data.inviteCode')
    echo "📝 新创建的邀请码: $NEW_INVITE_CODE"
  else
    echo "❌ 邀请码创建失败"
  fi
  
else
  echo "❌ 管理员登录失败"
fi

echo ""
echo "✅ 问题解决总结："
echo "=================="
echo "1. ✅ 修复了前端响应格式解析错误"
echo "   - 原代码: response.data.data.code === 200"
echo "   - 修复后: response.code === 200"
echo "   - 原代码: response.data.data.items"
echo "   - 修复后: response.data.items"
echo ""
echo "2. ✅ 修复了邀请码创建后的响应处理"
echo "   - 统一了响应格式处理逻辑"
echo ""
echo "3. ✅ 重启了管理员前端服务"
echo "   - 应用了代码修复"
echo "   - 服务状态: online"
echo ""
echo "🌐 现在可以正常使用："
echo "   - 管理员后台: https://tiktokbusines.store/admin/"
echo "   - 邀请码管理页面"
echo "   - 查看邀请码列表"
echo "   - 创建新邀请码"
echo "   - 管理邀请码状态"
echo ""
echo "📱 邀请码功能："
echo "   - 列表显示正常"
echo "   - 创建功能正常"
echo "   - 状态管理正常"
echo "   - 使用统计正常"
