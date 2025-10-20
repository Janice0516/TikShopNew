#!/bin/bash

# 订单列表问题调试脚本

echo "🔍 订单列表问题调试"
echo "=================="

# 获取测试token
echo "1. 获取测试token..."
LOGIN_RESPONSE=$(curl -s -X POST -H "Content-Type: application/json" \
  -d '{"username":"testmerchant","password":"123456"}' \
  "https://tiktokbusines.store/api/merchant/login")

TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
  echo "❌ 无法获取token"
  exit 1
fi

echo "✅ Token获取成功: ${TOKEN:0:20}..."

# 测试不同的订单API端点
echo ""
echo "2. 测试订单统计API..."
STATS_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "https://tiktokbusines.store/api/orders/count")
echo "订单统计响应: $STATS_RESPONSE"

echo ""
echo "3. 测试订单列表API（带参数）..."
ORDERS_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "https://tiktokbusines.store/api/orders?page=1&pageSize=10")
echo "订单列表响应: $ORDERS_RESPONSE"

echo ""
echo "4. 测试订单列表API（不带参数）..."
ORDERS_SIMPLE=$(curl -s -H "Authorization: Bearer $TOKEN" "https://tiktokbusines.store/api/orders")
echo "订单列表响应（无参数）: $ORDERS_SIMPLE"

echo ""
echo "5. 检查数据库表是否存在..."
# 这里可以添加数据库检查逻辑

echo ""
echo "🔍 调试完成"
echo "=========="
echo "订单统计API: $(echo $STATS_RESPONSE | grep -q 'waitPay' && echo '✅ 正常' || echo '❌ 异常')"
echo "订单列表API: $(echo $ORDERS_RESPONSE | grep -q 'list' && echo '✅ 正常' || echo '❌ 异常')"
