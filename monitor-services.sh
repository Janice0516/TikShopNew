#!/bin/bash

# TikShop 服务监控脚本
# 用于监控服务状态和自动重启

echo "📊 === TikShop 服务监控 ==="

# 检查服务状态
check_service() {
    local service_name=$1
    local port=$2
    local url=$3
    
    if netstat -tlnp | grep -q ":$port "; then
        local status=$(curl -s -o /dev/null -w '%{http_code}' "$url" 2>/dev/null)
        if [ "$status" = "200" ]; then
            echo "✅ $service_name: 运行正常 (端口 $port)"
            return 0
        else
            echo "⚠️  $service_name: 端口监听但响应异常 (HTTP $status)"
            return 1
        fi
    else
        echo "❌ $service_name: 未运行 (端口 $port 未监听)"
        return 1
    fi
}

# 重启前端服务
restart_frontend() {
    local service_name=$1
    local port=$2
    local dir=$3
    
    echo "🔄 重启 $service_name..."
    pkill -f "serve -s dist -l $port" 2>/dev/null || true
    sleep 2
    cd "$dir" && serve -s dist -l "$port" > "../logs/${service_name}-manual.log" 2>&1 &
    sleep 3
    echo "✅ $service_name 重启完成"
}

# 检查所有服务
echo "🔍 检查服务状态..."
backend_ok=$(check_service "后端API" 3000 "http://localhost:3000/api/test/status")
admin_ok=$(check_service "管理后台" 5177 "http://localhost:5177/")
merchant_ok=$(check_service "商家后台" 5176 "http://localhost:5176/")
user_ok=$(check_service "用户应用" 3001 "http://localhost:3001/")

# 重启有问题的服务
if [ $admin_ok -ne 0 ]; then
    restart_frontend "admin" 5177 "admin"
fi

if [ $merchant_ok -ne 0 ]; then
    restart_frontend "merchant" 5176 "merchant"
fi

# 检查PM2服务
echo -e "\n📋 PM2服务状态:"
pm2 status

# 系统资源使用情况
echo -e "\n💻 系统资源:"
echo "内存使用: $(free -h | grep '^Mem:' | awk '{print $3"/"$2}')"
echo "磁盘使用: $(df -h / | tail -1 | awk '{print $3"/"$2" ("$5")"}')"
echo "CPU负载: $(uptime | awk -F'load average:' '{print $2}')"

# 检查日志文件大小
echo -e "\n📝 日志文件大小:"
find logs/ -name "*.log" -exec ls -lh {} \; 2>/dev/null | awk '{print $5, $9}' || echo "无日志文件"

echo -e "\n✅ 监控完成"
