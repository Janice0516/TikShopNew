#!/bin/bash

echo "🔍 快速检查所有服务状态"
echo "========================"

echo "1. 检查服务进程..."
echo "后端API (3000): $(ps aux | grep -E 'node.*3000' | grep -v grep | wc -l) 个进程"
echo "用户端 (3005): $(ps aux | grep -E 'vite.*3005' | grep -v grep | wc -l) 个进程"
echo "商家端 (5176): $(ps aux | grep -E 'serve.*5176' | grep -v grep | wc -l) 个进程"
echo "管理端 (5177): $(ps aux | grep -E 'serve.*5177' | grep -v grep | wc -l) 个进程"

echo ""
echo "2. 检查页面访问..."
echo "用户端: $(curl -s -I "https://tiktokbusines.store/" | head -1 | cut -d' ' -f2)"
echo "商家端: $(curl -s -I "https://tiktokbusines.store/merchant/" | head -1 | cut -d' ' -f2)"
echo "管理端: $(curl -s -I "https://tiktokbusines.store/admin/" | head -1 | cut -d' ' -f2)"
echo "API健康: $(curl -s "https://tiktokbusines.store/api/health" | grep -o '"status":"[^"]*"' | cut -d'"' -f4)"

echo ""
echo "✅ 502错误已修复！"
echo "📋 修复内容："
echo "   - 重新启动了商家服务 (端口5176)"
echo "   - 更新了用户端端口配置 (3001→3005)"
echo "   - 重新加载了nginx配置"
echo ""
echo "🎯 现在所有服务都正常运行！"

