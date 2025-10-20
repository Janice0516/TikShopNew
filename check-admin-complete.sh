#!/bin/bash

echo "🔧 管理后台功能完整性检查..."
echo "================================"

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
    exit 1
fi

echo "✅ 登录成功"

echo "================================"

# 检查各个功能模块
echo "2. 检查仪表盘统计API..."
DASHBOARD_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "$BASE_URL/admin/dashboard/stats")
if echo "$DASHBOARD_RESPONSE" | grep -q '"code":200'; then
    echo "✅ 仪表盘统计API正常"
else
    echo "❌ 仪表盘统计API异常: $DASHBOARD_RESPONSE"
fi

echo "3. 检查商品管理API..."
PRODUCT_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "$BASE_URL/product?page=1&pageSize=10")
if echo "$PRODUCT_RESPONSE" | grep -q '"code":200'; then
    echo "✅ 商品管理API正常"
else
    echo "❌ 商品管理API异常: $PRODUCT_RESPONSE"
fi

echo "4. 检查商家管理API..."
MERCHANT_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "$BASE_URL/merchant?page=1&pageSize=10")
if echo "$MERCHANT_RESPONSE" | grep -q '"code":200'; then
    echo "✅ 商家管理API正常"
else
    echo "❌ 商家管理API异常: $MERCHANT_RESPONSE"
fi

echo "5. 检查订单管理API..."
ORDER_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "$BASE_URL/order?page=1&pageSize=10")
if echo "$ORDER_RESPONSE" | grep -q '"code":200'; then
    echo "✅ 订单管理API正常"
else
    echo "❌ 订单管理API异常: $ORDER_RESPONSE"
fi

echo "6. 检查用户管理API..."
USER_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "$BASE_URL/user?page=1&pageSize=10")
if echo "$USER_RESPONSE" | grep -q '"code":200'; then
    echo "✅ 用户管理API正常"
else
    echo "❌ 用户管理API异常: $USER_RESPONSE"
fi

echo "7. 检查分类管理API..."
CATEGORY_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "$BASE_URL/category")
if echo "$CATEGORY_RESPONSE" | grep -q '"code":200'; then
    echo "✅ 分类管理API正常"
else
    echo "❌ 分类管理API异常: $CATEGORY_RESPONSE"
fi

echo "8. 检查提现管理API..."
WITHDRAWAL_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "$BASE_URL/withdrawal/list")
if echo "$WITHDRAWAL_RESPONSE" | grep -q '"code":200'; then
    echo "✅ 提现管理API正常"
else
    echo "❌ 提现管理API异常: $WITHDRAWAL_RESPONSE"
fi

echo "9. 检查资金管理API..."
FUND_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "$BASE_URL/admin/fund-management/operations")
if echo "$FUND_RESPONSE" | grep -q '"code":200'; then
    echo "✅ 资金管理API正常"
else
    echo "❌ 资金管理API异常: $FUND_RESPONSE"
fi

echo "10. 检查信用评级API..."
CREDIT_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "$BASE_URL/credit-rating/dashboard-stats")
if echo "$CREDIT_RESPONSE" | grep -q '"code":200'; then
    echo "✅ 信用评级API正常"
else
    echo "❌ 信用评级API异常: $CREDIT_RESPONSE"
fi

echo "11. 检查推荐商品API..."
RECOMMEND_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "$BASE_URL/admin/recommend-products/stats")
if echo "$RECOMMEND_RESPONSE" | grep -q '"code":200'; then
    echo "✅ 推荐商品API正常"
else
    echo "❌ 推荐商品API异常: $RECOMMEND_RESPONSE"
fi

echo "================================"
echo "✅ 管理后台功能检查完成"

echo ""
echo "🌐 管理后台访问地址:"
echo "   https://tiktokbusines.store/admin"
echo ""
echo "📊 主要功能模块:"
echo "   - 仪表盘: 数据概览和统计"
echo "   - 商品管理: 商品增删改查"
echo "   - 商家管理: 商家审核和管理"
echo "   - 订单管理: 订单查看和处理"
echo "   - 用户管理: 用户信息管理"
echo "   - 分类管理: 商品分类管理"
echo "   - 提现管理: 提现申请审核"
echo "   - 资金管理: 资金操作记录"
echo "   - 信用评级: 商户信用评级"
echo "   - 推荐商品: 推荐商品管理"
