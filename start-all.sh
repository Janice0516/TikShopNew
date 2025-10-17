#!/bin/bash

echo "🚀 启动电商平台所有服务..."
echo ""

# 检查PM2是否安装
if ! command -v pm2 &> /dev/null; then
    echo "❌ PM2未安装，正在安装..."
    npm install -g pm2
fi

# 创建日志目录
mkdir -p logs

# 停止现有服务
echo "🔧 停止现有服务..."
pm2 delete all 2>/dev/null || echo "无现有服务"

# 启动所有服务
echo "🚀 启动所有服务..."
pm2 start ecosystem.config.js

# 显示状态
echo ""
echo "�� 服务状态:"
pm2 status

echo ""
echo "🌐 访问地址:"
echo "   🔧 API文档: http://localhost:3000/api/docs"
echo "   🖥️ 管理后台: http://localhost:5175"
echo "   🏪 商家端: http://localhost:5174"
echo "   📱 用户端: http://localhost:3001 (Vue.js商城)"
echo ""
echo "🔑 测试账户:"
echo "   👤 管理员: admin / 123456"
echo "   👤 用户: 13800138000 / 123456"
echo ""
echo "📝 管理命令:"
echo "   pm2 status    - 查看状态"
echo "   pm2 logs      - 查看日志"
echo "   pm2 monit     - 监控面板"
echo "   pm2 stop all  - 停止所有服务"
