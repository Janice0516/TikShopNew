#!/bin/bash

echo "🔐 测试商家登录API"
echo "=================="

# 测试不同的密码
echo "测试密码: 123456"
curl -s -X POST http://localhost:3000/api/merchant/login \
  -H "Content-Type: application/json" \
  -d '{"username":"merchant001","password":"123456"}'

echo ""

echo "测试密码: password123"
curl -s -X POST http://localhost:3000/api/merchant/login \
  -H "Content-Type: application/json" \
  -d '{"username":"merchant001","password":"password123"}'

echo ""

echo "测试密码: merchant001"
curl -s -X POST http://localhost:3000/api/merchant/login \
  -H "Content-Type: application/json" \
  -d '{"username":"merchant001","password":"merchant001"}'

echo ""

echo "测试其他商家账号:"
echo "测试 merchant1"
curl -s -X POST http://localhost:3000/api/merchant/login \
  -H "Content-Type: application/json" \
  -d '{"username":"merchant1","password":"123456"}'

echo ""

echo "测试 merchant2"
curl -s -X POST http://localhost:3000/api/merchant/login \
  -H "Content-Type: application/json" \
  -d '{"username":"merchant2","password":"123456"}'

echo ""

echo "✅ 登录测试完成！"
