#!/bin/bash

# TikShop 项目服务管理脚本
# 用于启动、停止、重启所有服务

set -e

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 项目根目录
PROJECT_ROOT="/root/TikShop"

# 服务配置
declare -A SERVICES=(
    ["backend"]="ecommerce-backend:3000:node dist/main.js"
    ["user"]="user-app:3001:npm run dev"
    ["merchant"]="merchant:5176:npx serve -s dist -p 5176"
    ["admin"]="admin:5177:npx serve -s dist -p 5177"
)

# 日志函数
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

# 检查端口是否被占用
check_port() {
    local port=$1
    if lsof -i :$port >/dev/null 2>&1; then
        return 0  # 端口被占用
    else
        return 1  # 端口空闲
    fi
}

# 杀死占用端口的进程
kill_port() {
    local port=$1
    local pids=$(lsof -ti :$port)
    if [ ! -z "$pids" ]; then
        log_warning "端口 $port 被占用，正在清理..."
        echo $pids | xargs kill -9 2>/dev/null || true
        sleep 2
    fi
}

# 启动单个服务
start_service() {
    local service_name=$1
    local service_config=${SERVICES[$service_name]}
    
    if [ -z "$service_config" ]; then
        log_error "未知服务: $service_name"
        return 1
    fi
    
    IFS=':' read -r dir port command <<< "$service_config"
    local service_dir="$PROJECT_ROOT/$dir"
    
    log_info "启动服务: $service_name (端口: $port)"
    
    # 检查服务目录是否存在
    if [ ! -d "$service_dir" ]; then
        log_error "服务目录不存在: $service_dir"
        return 1
    fi
    
    # 清理端口
    kill_port $port
    
    # 启动服务
    cd "$service_dir"
    
    if [ "$service_name" = "backend" ]; then
        # 后端服务需要先构建
        log_info "构建后端服务..."
        npm run build
        nohup $command > /tmp/tikshop-$service_name.log 2>&1 &
    elif [ "$service_name" = "user" ]; then
        # 用户端开发服务
        nohup $command > /tmp/tikshop-$service_name.log 2>&1 &
    else
        # 前端服务需要先构建
        log_info "构建前端服务: $service_name..."
        npm run build
        nohup $command > /tmp/tikshop-$service_name.log 2>&1 &
    fi
    
    # 等待服务启动
    sleep 3
    
    # 检查服务是否启动成功
    if check_port $port; then
        log_success "服务 $service_name 启动成功 (PID: $(lsof -ti :$port))"
    else
        log_error "服务 $service_name 启动失败"
        return 1
    fi
}

# 停止单个服务
stop_service() {
    local service_name=$1
    local service_config=${SERVICES[$service_name]}
    
    if [ -z "$service_config" ]; then
        log_error "未知服务: $service_name"
        return 1
    fi
    
    IFS=':' read -r dir port command <<< "$service_config"
    
    log_info "停止服务: $service_name (端口: $port)"
    
    if check_port $port; then
        kill_port $port
        log_success "服务 $service_name 已停止"
    else
        log_warning "服务 $service_name 未运行"
    fi
}

# 重启单个服务
restart_service() {
    local service_name=$1
    log_info "重启服务: $service_name"
    stop_service $service_name
    sleep 2
    start_service $service_name
}

# 检查所有服务状态
check_status() {
    log_info "检查所有服务状态..."
    echo
    
    for service_name in "${!SERVICES[@]}"; do
        local service_config=${SERVICES[$service_name]}
        IFS=':' read -r dir port command <<< "$service_config"
        
        if check_port $port; then
            local pid=$(lsof -ti :$port)
            log_success "$service_name: 运行中 (端口: $port, PID: $pid)"
        else
            log_error "$service_name: 未运行 (端口: $port)"
        fi
    done
}

# 启动所有服务
start_all() {
    log_info "启动所有服务..."
    
    # 按依赖顺序启动
    start_service "backend"
    start_service "user"
    start_service "merchant"
    start_service "admin"
    
    echo
    log_success "所有服务启动完成！"
    check_status
}

# 停止所有服务
stop_all() {
    log_info "停止所有服务..."
    
    for service_name in "${!SERVICES[@]}"; do
        stop_service $service_name
    done
    
    echo
    log_success "所有服务已停止！"
}

# 重启所有服务
restart_all() {
    log_info "重启所有服务..."
    stop_all
    sleep 3
    start_all
}

# 显示帮助信息
show_help() {
    echo "TikShop 项目服务管理脚本"
    echo
    echo "用法: $0 [命令] [服务名]"
    echo
    echo "命令:"
    echo "  start [服务名]    启动服务 (不指定服务名则启动所有服务)"
    echo "  stop [服务名]     停止服务 (不指定服务名则停止所有服务)"
    echo "  restart [服务名]   重启服务 (不指定服务名则重启所有服务)"
    echo "  status           检查所有服务状态"
    echo "  logs [服务名]     查看服务日志"
    echo "  help             显示帮助信息"
    echo
    echo "可用服务:"
    for service_name in "${!SERVICES[@]}"; do
        local service_config=${SERVICES[$service_name]}
        IFS=':' read -r dir port command <<< "$service_config"
        echo "  $service_name (端口: $port)"
    done
}

# 查看服务日志
show_logs() {
    local service_name=$1
    
    if [ -z "$service_name" ]; then
        log_error "请指定服务名"
        show_help
        return 1
    fi
    
    local log_file="/tmp/tikshop-$service_name.log"
    
    if [ -f "$log_file" ]; then
        log_info "显示服务 $service_name 的日志 (最后50行):"
        tail -50 "$log_file"
    else
        log_error "日志文件不存在: $log_file"
    fi
}

# 主函数
main() {
    local command=$1
    local service_name=$2
    
    case $command in
        "start")
            if [ -z "$service_name" ]; then
                start_all
            else
                start_service $service_name
            fi
            ;;
        "stop")
            if [ -z "$service_name" ]; then
                stop_all
            else
                stop_service $service_name
            fi
            ;;
        "restart")
            if [ -z "$service_name" ]; then
                restart_all
            else
                restart_service $service_name
            fi
            ;;
        "status")
            check_status
            ;;
        "logs")
            show_logs $service_name
            ;;
        "help"|"--help"|"-h")
            show_help
            ;;
        *)
            log_error "未知命令: $command"
            show_help
            exit 1
            ;;
    esac
}

# 执行主函数
main "$@"
