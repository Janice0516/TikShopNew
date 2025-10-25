#!/bin/bash

echo "🔍 用户应用静态资源问题诊断报告"
echo "=============================="
echo ""

echo "📊 服务状态："
echo "============"
pm2 status | grep user-app

echo ""

echo "🌐 本地访问测试："
echo "==============="
echo "用户应用JS文件:"
curl -I http://localhost:5173/assets/index-BQSZoc03.js 2>/dev/null || echo "❌ 本地JS文件访问失败"

echo ""
echo "用户应用CSS文件:"
curl -I http://localhost:5173/assets/index-LyVmV0no.css 2>/dev/null || echo "❌ 本地CSS文件访问失败"

echo ""

echo "🌐 域名访问测试："
echo "==============="
echo "用户应用JS文件:"
curl -I https://tiktokbusines.store/assets/index-BQSZoc03.js 2>/dev/null || echo "❌ 域名JS文件访问失败"

echo ""
echo "用户应用CSS文件:"
curl -I https://tiktokbusines.store/assets/index-LyVmV0no.css 2>/dev/null || echo "❌ 域名CSS文件访问失败"

echo ""

echo "📁 构建文件检查："
echo "==============="
ls -la /root/TikShop/user-app/dist/assets/ | grep -E "(index-BQSZoc03|index-LyVmV0no)" || echo "❌ 构建文件不存在"

echo ""

echo "📋 问题分析："
echo "============"
echo "1. 用户应用服务状态: $(pm2 list | grep user-app | awk '{print $10}')"
echo "2. 本地JS文件访问: $(curl -I http://localhost:5173/assets/index-BQSZoc03.js 2>/dev/null | head -1 | grep -q "200" && echo "成功" || echo "失败")"
echo "3. 本地CSS文件访问: $(curl -I http://localhost:5173/assets/index-LyVmV0no.css 2>/dev/null | head -1 | grep -q "200" && echo "成功" || echo "失败")"
echo "4. 域名JS文件访问: $(curl -I https://tiktokbusines.store/assets/index-BQSZoc03.js 2>/dev/null | head -1 | grep -q "200" && echo "成功" || echo "失败")"
echo "5. 域名CSS文件访问: $(curl -I https://tiktokbusines.store/assets/index-LyVmV0no.css 2>/dev/null | head -1 | grep -q "200" && echo "成功" || echo "失败")"
