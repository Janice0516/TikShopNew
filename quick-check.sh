#!/bin/bash

# TikShop 快速检查脚本
# 快速检查所有服务状态和页面访问

set -e

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 检查端口
check_port() {
    local port=$1
    if lsof -i :$port >/dev/null 2>&1; then
        local pid=$(lsof -ti :$port)
        echo -e "${GREEN}✓${NC} 端口 $port: 运行中 (PID: $pid)"
        return 0
    else
        echo -e "${RED}✗${NC} 端口 $port: 未运行"
        return 1
    fi
}

# 检查页面访问
check_page() {
    local url=$1
    local name=$2
    
    local status=$(curl -s -o /dev/null -w "%{http_code}" "$url" 2>/dev/null || echo "000")
    
    case $status in
        200|301|302)
            echo -e "${GREEN}✓${NC} $name: 正常 ($status)"
            ;;
        404)
            echo -e "${RED}✗${NC} $name: 404错误"
            ;;
        502|503)
            echo -e "${RED}✗${NC} $name: 服务错误 ($status)"
            ;;
        *)
            echo -e "${RED}✗${NC} $name: 连接失败 ($status)"
            ;;
    esac
}

# 主检查函数
main() {
    echo "🔍 TikShop 项目状态检查"
    echo "=========================="
    echo
    
    log_info "检查服务端口..."
    check_port 3000  # 后端API
    check_port 3001  # 用户端
    check_port 5176  # 商家后台
    check_port 5177  # Admin后台
    echo
    
    log_info "检查页面访问..."
    check_page "https://tiktokbusines.store/" "用户端主页"
    check_page "https://tiktokbusines.store/login" "用户端登录"
    check_page "https://tiktokbusines.store/merchant/" "商家后台"
    check_page "https://tiktokbusines.store/admin/" "Admin后台"
    check_page "https://tiktokbusines.store/api/docs" "API文档"
    echo
    
    log_info "检查静态资源..."
    check_page "https://tiktokbusines.store/merchant/assets/" "商家后台静态资源"
    check_page "https://tiktokbusines.store/admin/assets/" "Admin后台静态资源"
    echo
    
    log_info "检查完成！"
    echo
    echo "💡 如果发现问题，可以运行:"
    echo "   ./auto-fix.sh          # 自动修复"
    echo "   ./manage-services.sh restart  # 重启所有服务"
}

# 执行主函数
main "$@"
