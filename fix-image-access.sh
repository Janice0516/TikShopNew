#!/bin/bash

echo "🔧 修复商品图片访问问题："
echo "========================"
echo ""

# 1. 检查uploads目录
echo "📋 1. 检查uploads目录："
ls -la /root/TikShop/uploads/

echo ""

# 2. 检查uploads/images目录
echo "📋 2. 检查uploads/images目录："
ls -la /root/TikShop/uploads/images/ | head -5

echo ""

# 3. 检查Nginx配置
echo "📋 3. 检查Nginx配置："
grep -A 10 -B 5 "location /uploads" /etc/nginx/sites-available/tikshop

echo ""

# 4. 测试图片访问
echo "🧪 4. 测试图片访问："
echo "测试本地图片访问:"
curl -I http://localhost:3000/uploads/images/e51cd3e39cf5d8b8433410410c9653b2fb.jpg 2>/dev/null || echo "本地图片访问失败"

echo ""
echo "测试域名图片访问:"
curl -I https://tiktokbusines.store/uploads/images/e51cd3e39cf5d8b8433410410c9653b2fb.jpg 2>/dev/null || echo "域名图片访问失败"

echo ""

# 5. 检查文件权限
echo "📋 5. 检查文件权限："
ls -la /root/TikShop/uploads/images/e51cd3e39cf5d8b8433410410c9653b2fb.jpg 2>/dev/null || echo "图片文件不存在"

echo ""

# 6. 检查Nginx访问日志
echo "📋 6. 检查Nginx访问日志："
tail -5 /var/log/nginx/access.log | grep -i "uploads\|image"

echo ""
