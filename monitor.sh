#!/bin/bash

echo "📊 电商平台服务监控..."
echo ""

# 显示服务状态
echo "🔍 服务状态:"
pm2 status

echo ""
echo "📈 资源使用情况:"
pm2 monit --no-daemon
