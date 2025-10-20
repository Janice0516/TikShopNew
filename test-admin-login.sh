#!/bin/bash

echo "🔐 测试管理员账户登录..."
echo "================================"

# 测试账户信息
USERNAME="admin001"
PASSWORD="cadWcxIpzglf"

echo "测试账户: $USERNAME"
echo "密码: $PASSWORD"
echo ""

# 测试登录
echo "正在测试登录..."
RESPONSE=$(curl -s -X POST https://tiktokbusines.store/api/admin/login \
  -H "Content-Type: application/json" \
  -d "{\"username\":\"$USERNAME\",\"password\":\"$PASSWORD\"}")

echo "登录响应:"
echo "$RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$RESPONSE"

echo ""
echo "================================"

# 检查是否登录成功
if echo "$RESPONSE" | grep -q "token"; then
    echo "✅ 登录测试成功！"
    echo "💡 可以使用此账户访问管理后台"
else
    echo "❌ 登录测试失败"
    echo "请检查账户信息或服务状态"
fi
