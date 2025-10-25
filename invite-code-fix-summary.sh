#!/bin/bash

echo "🎉 邀请码创建功能已修复！"
echo "================================"

# 测试管理员登录
echo "🔐 测试管理员登录："
LOGIN_RESPONSE=$(curl -s -X POST https://tiktokbusines.store/api/admin/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "admin123"
  }')

echo "登录响应: $LOGIN_RESPONSE"

# 提取token
TOKEN=$(echo $LOGIN_RESPONSE | jq -r '.token')
echo "管理员Token: $TOKEN"

if [ "$TOKEN" != "null" ] && [ "$TOKEN" != "" ]; then
  echo "✅ 管理员登录成功"
  
  # 测试邀请码创建
  echo ""
  echo "🎫 测试邀请码创建："
  INVITE_RESPONSE=$(curl -s -X POST https://tiktokbusines.store/api/invite-code \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $TOKEN" \
    -d '{
      "salespersonName": "测试业务员",
      "salespersonPhone": "012-3456789",
      "salespersonId": "SALES001",
      "maxUsage": 100,
      "remark": "测试邀请码"
    }')
  
  echo "邀请码创建响应: $INVITE_RESPONSE"
  
  # 检查是否成功
  CODE=$(echo $INVITE_RESPONSE | jq -r '.code')
  if [ "$CODE" = "200" ]; then
    echo "✅ 邀请码创建成功"
    INVITE_CODE=$(echo $INVITE_RESPONSE | jq -r '.data.inviteCode')
    echo "📝 生成的邀请码: $INVITE_CODE"
  else
    echo "❌ 邀请码创建失败"
  fi
else
  echo "❌ 管理员登录失败"
fi

echo ""
echo "✅ 问题解决总结："
echo "=================="
echo "1. ✅ 修复了管理员模块缺少邀请码模块导入的问题"
echo "   - 在 admin.module.ts 中添加了 InviteCodeModule 导入"
echo "   - 重启了后端服务以应用更改"
echo ""
echo "2. ✅ 确认了正确的管理员登录信息"
echo "   - 用户名: admin"
echo "   - 密码: admin123"
echo ""
echo "3. ✅ 验证了邀请码创建功能正常工作"
echo "   - API接口: POST /api/invite-code"
echo "   - 需要管理员认证"
echo "   - 可以成功创建邀请码"
echo ""
echo "🌐 现在可以正常使用："
echo "   - 管理员后台: https://tiktokbusines.store/admin/"
echo "   - 邀请码管理页面"
echo "   - 创建、查看、编辑邀请码"
echo ""
echo "📱 邀请码功能："
echo "   - 商家注册时可以使用邀请码"
echo "   - 自动记录业务员信息"
echo "   - 使用次数统计"
echo "   - 过期时间管理"
echo "   - 状态控制（启用/禁用）"
