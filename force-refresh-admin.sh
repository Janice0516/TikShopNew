#!/bin/bash

echo "🔄 强制刷新管理员前端："
echo "====================="

# 1. 重启管理员前端服务
echo "📱 1. 重启管理员前端服务："
pm2 restart admin-frontend

# 2. 等待服务启动
echo "⏳ 2. 等待服务启动..."
sleep 3

# 3. 检查服务状态
echo "📊 3. 检查服务状态："
pm2 status admin-frontend

# 4. 添加版本号
echo "🔢 4. 添加版本号："
VERSION=$(date +%s)
if [ -f "/root/TikShop/admin/dist/index.html" ]; then
  cp /root/TikShop/admin/dist/index.html /root/TikShop/admin/dist/index.html.bak
  sed -i "s|assets/|assets/?v=$VERSION|g" /root/TikShop/admin/dist/index.html
  echo "✅ 已添加版本号: $VERSION"
fi

echo ""
echo "🌐 现在请："
echo "1. 强制刷新页面: Ctrl+Shift+R (Windows/Linux) 或 Cmd+Shift+R (Mac)"
echo "2. 或者清除浏览器缓存"
echo "3. 或者使用无痕模式"
echo "4. 访问: https://tiktokbusines.store/admin/"
echo "5. 登录: admin / admin123"
echo "6. 进入邀请码管理页面"
echo ""
echo "如果还有问题，请检查浏览器控制台的Network标签页"
echo "看看API请求是否包含Authorization头"
