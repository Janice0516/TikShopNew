#!/bin/bash
set -e

echo "Building user-app for Render deployment..."

# 进入user-app目录
cd user-app

# 安装依赖
echo "Installing dependencies..."
npm ci

# 构建项目
echo "Building project..."
npm run build

echo "Build completed successfully!"
echo "Build output is in user-app/dist/"
