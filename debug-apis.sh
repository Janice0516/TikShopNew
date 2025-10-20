#!/bin/bash

echo "🔧 调试订单和用户API问题..."
echo "=================================="

# API基础URL
BASE_URL="https://tiktokbusines.store/api"

# 登录获取token
echo "1. 登录获取管理员token..."
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/admin/login" \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}')

TOKEN=$(echo "$LOGIN_RESPONSE" | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
    echo "❌ 登录失败，无法获取token"
    echo "登录响应: $LOGIN_RESPONSE"
    exit 1
fi

echo "✅ 登录成功，Token: ${TOKEN:0:20}..."

echo "=================================="

# 测试订单API
echo "2. 测试订单API..."
ORDER_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "$BASE_URL/admin/orders?page=1&pageSize=5")
echo "订单API响应: $ORDER_RESPONSE"

echo "=================================="

# 测试用户API
echo "3. 测试用户API..."
USER_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "$BASE_URL/admin/users?page=1&pageSize=5")
echo "用户API响应: $USER_RESPONSE"

echo "=================================="

# 测试其他正常工作的API作为对比
echo "4. 测试商品API（应该正常）..."
PRODUCT_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "$BASE_URL/products?page=1&pageSize=5")
echo "商品API响应: $PRODUCT_RESPONSE"

echo "=================================="
echo "🔍 调试完成"
