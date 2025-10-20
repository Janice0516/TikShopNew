#!/bin/bash

echo "🔧 测试信用评级系统完整性..."
echo "================================"

# 登录获取token
echo "1. 登录获取token..."
LOGIN_RESPONSE=$(curl -s -X POST https://tiktokbusines.store/api/admin/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}')

TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
    echo "❌ 登录失败，无法获取token"
    exit 1
fi

echo "✅ 登录成功"

# 测试信用评级统计API
echo "2. 测试信用评级统计API..."
STATS_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" https://tiktokbusines.store/api/credit-rating/dashboard-stats)
echo "统计响应: $STATS_RESPONSE"

# 检查统计API是否成功
if echo "$STATS_RESPONSE" | grep -q '"code":200'; then
    echo "✅ 统计API工作正常"
else
    echo "❌ 统计API失败"
fi

# 测试操作记录API
echo "3. 测试操作记录API..."
OPERATIONS_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" https://tiktokbusines.store/api/credit-rating/operations)
echo "操作记录响应: $OPERATIONS_RESPONSE"

# 检查操作记录API是否成功
if echo "$OPERATIONS_RESPONSE" | grep -q '"code":200'; then
    echo "✅ 操作记录API工作正常"
else
    echo "❌ 操作记录API失败"
fi

# 测试信用评级列表API
echo "4. 测试信用评级列表API..."
LIST_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" https://tiktokbusines.store/api/credit-rating)
echo "列表响应: $LIST_RESPONSE"

# 检查列表API是否成功
if echo "$LIST_RESPONSE" | grep -q '"code":200'; then
    echo "✅ 列表API工作正常"
else
    echo "❌ 列表API失败"
fi

echo "================================"
echo "✅ 信用评级系统测试完成"

# 显示统计信息
echo ""
echo "📊 系统状态:"
echo "   - 总评级数: $(echo $STATS_RESPONSE | grep -o '"totalRatings":[0-9]*' | cut -d: -f2)"
echo "   - 平均分数: $(echo $STATS_RESPONSE | grep -o '"averageScore":[0-9.]*' | cut -d: -f2)"
echo "   - AAA级商户: $(echo $STATS_RESPONSE | grep -o '"aaaCount":[0-9]*' | cut -d: -f2)"
echo "   - 操作记录数: $(echo $OPERATIONS_RESPONSE | grep -o '"total":[0-9]*' | cut -d: -f2)"
