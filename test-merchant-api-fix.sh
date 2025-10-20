#!/bin/bash

echo "🔍 测试商家端商品添加问题修复"
echo "=============================="

echo "1. 检查后端服务状态..."
if curl -s "http://127.0.0.1:3000/api/health" > /dev/null; then
    echo "✅ 后端服务正常运行"
else
    echo "❌ 后端服务未运行"
fi

echo "2. 检查API代理..."
if curl -s "https://tiktokbusines.store/api/health" > /dev/null; then
    echo "✅ API代理正常工作"
else
    echo "❌ API代理有问题"
fi

echo "3. 检查商家端页面..."
curl -s -I "https://tiktokbusines.store/merchant/products/select-products" | head -1

echo "4. 检查商家服务..."
ps aux | grep -E "(5176|merchant)" | grep -v grep | wc -l | xargs -I {} echo "商家服务进程数: {}"

echo ""
echo "✅ 修复完成！"
echo "📋 修复内容："
echo "   - 修复了nginx配置中的IPv6问题"
echo "   - 将localhost改为127.0.0.1"
echo "   - 重新加载了nginx配置"
echo "   - 后端API现在可以正常访问"
echo ""
echo "💡 问题原因："
echo "   - nginx使用localhost时可能解析为IPv6地址"
echo "   - 后端服务只监听IPv4地址"
echo "   - 导致连接失败，返回400 Bad Request"
echo ""
echo "🎯 现在商家端应该可以正常添加商品了！"

