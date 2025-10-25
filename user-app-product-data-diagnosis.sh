#!/bin/bash

echo "🔍 用户应用商品数据加载问题诊断"
echo "=============================="
echo ""

echo "📊 服务状态："
echo "============"
pm2 status | grep user-app

echo ""

echo "🌐 API测试："
echo "==========="
echo "商品7详情API:"
curl -s "https://tiktokbusines.store/api/products/7" | jq '.name, .costPrice, .mainImage' 2>/dev/null || echo "❌ 商品7详情API测试失败"

echo ""
echo "商品110详情API:"
curl -s "https://tiktokbusines.store/api/products/110" | jq '.name, .costPrice, .mainImage' 2>/dev/null || echo "❌ 商品110详情API测试失败"

echo ""

echo "🌐 页面访问测试："
echo "==============="
echo "用户应用首页:"
curl -I https://tiktokbusines.store/ 2>/dev/null || echo "❌ 用户应用首页测试失败"

echo ""

echo "📋 问题分析："
echo "============"
echo "1. 用户应用服务状态: $(pm2 list | grep user-app | awk '{print $10}')"
echo "2. 商品7 API状态: $(curl -s "https://tiktokbusines.store/api/products/7" | jq -r '.name' 2>/dev/null | grep -q "undefined" && echo "异常" || echo "正常")"
echo "3. 商品110 API状态: $(curl -s "https://tiktokbusines.store/api/products/110" | jq -r '.name' 2>/dev/null | grep -q "undefined" && echo "异常" || echo "正常")"
echo "4. 用户应用页面状态: $(curl -I https://tiktokbusines.store/ 2>/dev/null | head -1 | grep -q "200" && echo "正常" || echo "异常")"
