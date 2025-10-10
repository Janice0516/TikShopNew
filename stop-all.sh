#!/bin/bash

echo "🛑 停止电商平台所有服务..."
echo ""

# 停止所有PM2服务
pm2 stop all
pm2 delete all

echo "✅ 所有服务已停止"
