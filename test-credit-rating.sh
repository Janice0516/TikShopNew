#!/bin/bash

echo "🔧 测试信用评级系统..."

# 登录获取token
echo "1. 登录获取token..."
LOGIN_RESPONSE=$(curl -s -X POST https://tiktokbusines.store/api/admin/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}')

echo "登录响应: $LOGIN_RESPONSE"

# 提取token
TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*"' | cut -d'"' -f4)
echo "Token: $TOKEN"

if [ -z "$TOKEN" ]; then
    echo "❌ 登录失败，无法获取token"
    exit 1
fi

echo "✅ 登录成功"

# 测试信用评级统计API
echo "2. 测试信用评级统计API..."
STATS_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" https://tiktokbusines.store/api/credit-rating/stats)
echo "统计响应: $STATS_RESPONSE"

# 测试操作记录API
echo "3. 测试操作记录API..."
OPERATIONS_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" https://tiktokbusines.store/api/credit-rating/operations)
echo "操作记录响应: $OPERATIONS_RESPONSE"

# 测试信用评级列表API
echo "4. 测试信用评级列表API..."
LIST_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" https://tiktokbusines.store/api/credit-rating)
echo "列表响应: $LIST_RESPONSE"

echo "================================"
echo "✅ 信用评级系统测试完成"
