#!/bin/bash

# 商家后台功能测试脚本

echo "🏪 商家后台功能测试"
echo "=================="

# 测试商家登录
echo "1. 测试商家登录..."
LOGIN_RESPONSE=$(curl -s -X POST -H "Content-Type: application/json" \
  -d '{"username":"merchant41","password":"123456"}' \
  "https://tiktokbusines.store/api/merchant/login")

echo "登录响应: $LOGIN_RESPONSE"

# 提取token
TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
  echo "❌ 登录失败，无法获取token"
  echo "尝试其他商家账号..."
  
  # 尝试其他账号
  for i in {42..50}; do
    echo "尝试 merchant$i..."
    LOGIN_RESPONSE=$(curl -s -X POST -H "Content-Type: application/json" \
      -d "{\"username\":\"merchant$i\",\"password\":\"123456\"}" \
      "https://tiktokbusines.store/api/merchant/login")
    
    TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*"' | cut -d'"' -f4)
    if [ ! -z "$TOKEN" ]; then
      echo "✅ 成功登录 merchant$i"
      break
    fi
  done
fi

if [ -z "$TOKEN" ]; then
  echo "❌ 所有商家账号登录失败"
  exit 1
fi

echo "✅ 获取到token: ${TOKEN:0:20}..."

# 测试商家信息API
echo ""
echo "2. 测试商家信息API..."
PROFILE_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" \
  "https://tiktokbusines.store/api/merchant/profile")
echo "商家信息: $PROFILE_RESPONSE"

# 测试商品管理API
echo ""
echo "3. 测试商品管理API..."
PRODUCTS_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" \
  "https://tiktokbusines.store/api/merchant/products")
echo "商品列表: $PRODUCTS_RESPONSE"

# 测试控制台统计API
echo ""
echo "4. 测试控制台统计API..."
STATS_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" \
  "https://tiktokbusines.store/api/merchant/dashboard/stats")
echo "控制台统计: $STATS_RESPONSE"

# 测试订单管理API
echo ""
echo "5. 测试订单管理API..."
ORDERS_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" \
  "https://tiktokbusines.store/api/merchant/orders")
echo "订单列表: $ORDERS_RESPONSE"

echo ""
echo "🏪 商家后台功能测试完成"
