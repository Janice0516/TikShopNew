#!/bin/bash

# TikShop 服务启动脚本
# 用于统一启动所有服务

echo "🚀 === TikShop 服务启动脚本 ==="

# 检查是否在正确的目录
if [ ! -f "ecosystem.config.js" ]; then
    echo "❌ 错误: 请在TikShop根目录运行此脚本"
    exit 1
fi

# 创建日志目录
mkdir -p logs

# 停止所有现有服务
echo "🛑 停止现有服务..."
pm2 stop all 2>/dev/null || true
pm2 delete all 2>/dev/null || true

# 停止手动启动的serve服务
pkill -f "serve -s dist" 2>/dev/null || true

# 启动后端和用户应用服务
echo "🔧 启动后端服务..."
pm2 start ecosystem.config.js --only backend-api,user-app

# 等待后端服务启动
echo "⏳ 等待后端服务启动..."
sleep 10

# 手动启动前端服务（因为PM2配置有问题）
echo "🎨 启动前端服务..."
cd admin && serve -s dist -l 5177 > ../logs/admin-manual.log 2>&1 &
ADMIN_PID=$!
cd ../merchant && serve -s dist -l 5176 > ../logs/merchant-manual.log 2>&1 &
MERCHANT_PID=$!
cd ..

# 等待前端服务启动
echo "⏳ 等待前端服务启动..."
sleep 5

# 检查服务状态
echo "📊 检查服务状态..."
pm2 status

echo "🔍 检查端口监听..."
netstat -tlnp | grep -E ":(3000|3001|5176|5177)" || echo "部分端口未监听"

# 测试服务
echo "🧪 测试服务..."
echo "后端API: $(curl -s -o /dev/null -w '%{http_code}' http://localhost:3000/api/test/status)"
echo "管理后台: $(curl -s -o /dev/null -w '%{http_code}' http://localhost:5177/)"
echo "商家后台: $(curl -s -o /dev/null -w '%{http_code}' http://localhost:5176/)"
echo "用户应用: $(curl -s -o /dev/null -w '%{http_code}' http://localhost:3001/)"

echo "✅ 服务启动完成!"
echo "📱 访问地址:"
echo "   管理后台: http://202.146.222.134/admin/"
echo "   商家后台: http://202.146.222.134/merchant/"
echo "   用户应用: http://202.146.222.134/"
echo "   API文档: http://202.146.222.134/api/docs"

# 保存PID到文件
echo $ADMIN_PID > logs/admin.pid
echo $MERCHANT_PID > logs/merchant.pid

echo "💾 服务PID已保存到 logs/ 目录"
