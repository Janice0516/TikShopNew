#!/bin/bash

# TikTok Shop Render 部署脚本
# 此脚本用于自动化部署到Render平台

echo "🚀 开始部署 TikTok Shop 到 Render..."

# 检查Git状态
echo "📋 检查Git状态..."
if [ -n "$(git status --porcelain)" ]; then
    echo "❌ 有未提交的更改，请先提交所有更改"
    git status
    exit 1
fi

echo "✅ Git状态正常"

# 推送代码到GitHub
echo "📤 推送代码到GitHub..."
git push origin main

if [ $? -eq 0 ]; then
    echo "✅ 代码推送成功"
else
    echo "❌ 代码推送失败"
    exit 1
fi

echo ""
echo "🎉 部署准备完成！"
echo ""
echo "📋 接下来请在Render控制台完成以下步骤："
echo ""
echo "1️⃣ 后端API部署："
echo "   - 服务名称: tiktokshop-api"
echo "   - 类型: Web Service"
echo "   - 根目录: ecommerce-backend"
echo "   - 构建命令: npm install && npm run build"
echo "   - 启动命令: npm run start:prod"
echo ""
echo "2️⃣ 环境变量配置："
echo "   NODE_ENV=production"
echo "   DB_TYPE=postgres"
echo "   DB_HOST=dpg-d0j8q8h2s78s73fq8hpg-a.oregon-postgres.render.com"
echo "   DB_PORT=5432"
echo "   DB_USERNAME=tiktokshop_slkz_user"
echo "   DB_PASSWORD=U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn"
echo "   DB_DATABASE=tiktokshop_slkz"
echo "   JWT_SECRET=56dcb52ecafb60675a58d3472d7af4077f491c32e477372349f82f5ef3b12e4d7ff367b77c5f05bb0969843d1fbc3a647a69633dc6614d87ceea2d55c0ba31d6"
echo "   PORT=10000"
echo ""
echo "3️⃣ 前端应用部署："
echo "   - 商家后台: tiktokshop-merchant (Static Site, 根目录: merchant)"
echo "   - 管理后台: tiktokshop-admin (Static Site, 根目录: admin)"
echo "   - 用户端: tiktokshop-user (Static Site, 根目录: user-app)"
echo ""
echo "🔗 部署完成后访问地址："
echo "   - 后端API: https://tiktokshop-api.onrender.com"
echo "   - 商家后台: https://tiktokshop-merchant.onrender.com"
echo "   - 管理后台: https://tiktokshop-admin.onrender.com"
echo "   - 用户端: https://tiktokshop-user.onrender.com"
echo ""
echo "📚 详细配置请参考: RENDER_CONFIG.md"
