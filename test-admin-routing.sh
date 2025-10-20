#!/bin/bash

echo "🔍 测试Admin路由行为"
echo "===================="

echo "1. 测试直接访问admin登录页面..."
curl -s -I "https://tiktokbusines.store/admin/login" | head -1

echo "2. 测试访问admin根目录..."
curl -s -I "https://tiktokbusines.store/admin/" | head -1

echo "3. 测试访问admin根目录（无斜杠）..."
curl -s -I "https://tiktokbusines.store/admin" | head -1

echo "4. 检查admin静态资源..."
curl -s -I "https://tiktokbusines.store/admin/assets/index-CQ4vL0WQ.js" | head -1

echo "5. 检查admin SVG文件..."
curl -s -I "https://tiktokbusines.store/admin/vite.svg" | head -1

echo ""
echo "✅ 测试完成！"
