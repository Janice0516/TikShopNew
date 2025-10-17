#!/bin/bash

# GitHub上传脚本
echo "🚀 开始上传TikTok Shop Vue.js项目到GitHub..."

# 设置GitHub认证
echo "📝 设置GitHub认证..."
git config --global credential.helper store

# 创建认证文件
echo "ghp_mE1o04cutmoc6PDqnQz3sCFlU6EnifRCn" > ~/.git-credentials

# 设置远程仓库URL
echo "🔗 设置远程仓库..."
git remote set-url origin https://ghp_mE1o04cutmoc6PDqnQz3sCFlU6EnifRCn@github.com/Janice0516/tikshop-vue.git

# 推送代码
echo "📤 推送代码到GitHub..."
git push -u origin main

echo "✅ 上传完成！"
echo "🌐 仓库地址: https://github.com/Janice0516/tikshop-vue"
