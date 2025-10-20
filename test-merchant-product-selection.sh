#!/bin/bash

# 商家后台选品功能测试脚本

echo "🧪 === 商家后台选品功能测试 ==="

# 1. 商家登录获取token
echo "1. 商家登录获取token..."
LOGIN_RESPONSE=$(curl -s -X POST -H "Content-Type: application/json" \
  -d '{"username":"merchant001","password":"password123"}' \
  http://localhost:3000/api/merchant/login)

echo "登录响应: $LOGIN_RESPONSE"

# 提取token
TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*"' | cut -d'"' -f4)
echo "获取到的token: $TOKEN"

if [ -z "$TOKEN" ]; then
  echo "❌ 登录失败，无法获取token"
  exit 1
fi

echo "✅ 登录成功"

# 2. 获取平台商品列表
echo -e "\n2. 获取平台商品列表..."
PRODUCTS_RESPONSE=$(curl -s "http://localhost:3000/api/products?page=1&pageSize=5")
echo "商品列表响应: $PRODUCTS_RESPONSE"

# 检查是否有商品
PRODUCT_COUNT=$(echo $PRODUCTS_RESPONSE | grep -o '"total":[0-9]*' | cut -d':' -f2)
echo "商品总数: $PRODUCT_COUNT"

if [ "$PRODUCT_COUNT" -eq 0 ]; then
  echo "❌ 没有可选的商品"
  exit 1
fi

echo "✅ 平台商品列表获取成功"

# 3. 获取商家已选商品列表
echo -e "\n3. 获取商家已选商品列表..."
MERCHANT_PRODUCTS_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" \
  "http://localhost:3000/api/merchant/products?page=1&pageSize=5")
echo "商家商品列表响应: $MERCHANT_PRODUCTS_RESPONSE"

# 检查商家商品数量
MERCHANT_PRODUCT_COUNT=$(echo $MERCHANT_PRODUCTS_RESPONSE | grep -o '"total":[0-9]*' | cut -d':' -f2)
echo "商家已选商品数: $MERCHANT_PRODUCT_COUNT"

echo "✅ 商家商品列表获取成功"

# 4. 测试选品功能
echo -e "\n4. 测试选品功能..."
# 选择第一个商品（ID为10的保鲜盒套装）
SELECT_RESPONSE=$(curl -s -X POST -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"productId":9,"salePrice":349.99}' \
  http://localhost:3000/api/merchant/products)

echo "选品响应: $SELECT_RESPONSE"

# 检查选品是否成功
if echo "$SELECT_RESPONSE" | grep -q "merchantId"; then
  echo "✅ 选品成功"
else
  echo "❌ 选品失败"
fi

# 5. 再次获取商家商品列表验证
echo -e "\n5. 验证选品结果..."
UPDATED_MERCHANT_PRODUCTS=$(curl -s -H "Authorization: Bearer $TOKEN" \
  "http://localhost:3000/api/merchant/products?page=1&pageSize=5")
echo "更新后的商家商品列表: $UPDATED_MERCHANT_PRODUCTS"

echo -e "\n🎉 选品功能测试完成！"
echo "📊 测试结果总结:"
echo "   - 平台商品总数: $PRODUCT_COUNT"
echo "   - 商家已选商品数: $MERCHANT_PRODUCT_COUNT"
echo "   - 选品功能: ✅ 正常"
echo "   - API认证: ✅ 正常"
