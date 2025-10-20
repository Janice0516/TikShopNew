#!/bin/bash

# TikShop 自动修复脚本
# 当出现白屏、404等问题时自动修复

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

# 项目根目录
PROJECT_ROOT="/root/TikShop"

# 检查并修复nginx配置
fix_nginx() {
    log_info "检查nginx配置..."
    
    if nginx -t 2>/dev/null; then
        log_success "nginx配置正常"
    else
        log_warning "nginx配置有误，正在修复..."
        
        # 备份当前配置
        cp /etc/nginx/sites-available/tikshop /etc/nginx/sites-available/tikshop.backup.$(date +%Y%m%d_%H%M%S)
        
        # 重新创建正确的配置
        cat > /etc/nginx/sites-available/tikshop << 'EOF'
# TikShop 电商平台 Nginx 配置
# 服务器IP: 202.146.222.134

# HTTP配置 - 重定向到HTTPS
server {
    listen 80;
    server_name tiktokbusines.store www.tiktokbusines.store;
    
    # 重定向到HTTPS
    return 301 https://$host$request_uri;
}

# HTTPS配置
server {
    listen 443 ssl http2;
    server_name tiktokbusines.store www.tiktokbusines.store;
    
    # SSL证书配置
    ssl_certificate /etc/letsencrypt/live/tiktokbusines.store/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/tiktokbusines.store/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;
    
    # 安全头设置
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline' 'unsafe-eval'" always;
    
    # 日志配置
    access_log /var/log/nginx/tiktokbusines_access.log;
    error_log /var/log/nginx/tiktokbusines_error.log;
    
    # 静态文件上传路径
    location /uploads/ {
        proxy_pass http://127.0.0.1:3000/uploads/;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
    
    # 后端API服务
    location /api/ {
        proxy_pass http://localhost:3000/api/;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
        proxy_read_timeout 300s;
        proxy_connect_timeout 75s;
    }
    
    # 商家后台静态资源
    location ^~ /merchant/assets/ {
        proxy_pass http://localhost:5176/assets/;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
    
    # 商家后台SVG文件
    location ^~ /merchant/vite.svg {
        proxy_pass http://localhost:5176/vite.svg;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
    
    # 商家后台
    location /merchant/ {
        proxy_pass http://localhost:5176/;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
    }
    
    # 管理后台静态资源
    location ~* ^/admin/assets/(.*\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot))$ {
        alias /www/wwwroot/tikshop-admin/assets/$1;
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # 管理后台SVG文件
    location ~* ^/admin/(.*\.svg)$ {
        alias /www/wwwroot/tikshop-admin/$1;
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # 管理后台
    location /admin/ {
        alias /www/wwwroot/tikshop-admin/;
        index index.html;
        try_files $uri $uri/ @admin_fallback;
    }
    
    location @admin_fallback {
        rewrite ^/admin/(.*)$ /admin/index.html last;
    }
    
    # 用户商城 (代理到开发服务器)
    location / {
        proxy_pass http://localhost:3001/;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
    }
    
    # 文件上传大小限制
    client_max_body_size 10M;
}
EOF
        
        # 重新加载nginx
        nginx -t && systemctl reload nginx
        log_success "nginx配置已修复并重新加载"
    fi
}

# 检查并修复前端配置
fix_frontend_configs() {
    log_info "检查前端配置文件..."
    
    # 修复merchant vite配置
    if [ -f "$PROJECT_ROOT/merchant/vite.config.ts" ]; then
        log_info "检查merchant vite配置..."
        if ! grep -q "base: './'" "$PROJECT_ROOT/merchant/vite.config.ts"; then
            log_warning "修复merchant vite配置..."
            sed -i "s|base: '/merchant/'|base: './'|g" "$PROJECT_ROOT/merchant/vite.config.ts"
        fi
    fi
    
    # 修复admin vite配置
    if [ -f "$PROJECT_ROOT/admin/vite.config.ts" ]; then
        log_info "检查admin vite配置..."
        if ! grep -q "base: './'" "$PROJECT_ROOT/admin/vite.config.ts"; then
            log_warning "修复admin vite配置..."
            sed -i "s|base: '/admin/'|base: './'|g" "$PROJECT_ROOT/admin/vite.config.ts"
        fi
    fi
    
    # 修复user vite配置
    if [ -f "$PROJECT_ROOT/user-app/vite.config.ts" ]; then
        log_info "检查user vite配置..."
        if ! grep -q "tiktokbusines.store" "$PROJECT_ROOT/user-app/vite.config.ts"; then
            log_warning "修复user vite配置..."
            # 添加allowedHosts配置
            sed -i '/host: true,/a\    allowedHosts: [\n      "localhost",\n      "127.0.0.1",\n      "tiktokbusines.store",\n      "www.tiktokbusines.store"\n    ],' "$PROJECT_ROOT/user-app/vite.config.ts"
        fi
    fi
    
    log_success "前端配置检查完成"
}

# 重建前端项目
rebuild_frontends() {
    log_info "重建前端项目..."
    
    # 重建merchant
    log_info "重建merchant前端..."
    cd "$PROJECT_ROOT/merchant"
    npm run build
    
    # 重建admin
    log_info "重建admin前端..."
    cd "$PROJECT_ROOT/admin"
    npm run build
    
    # 复制admin文件到nginx目录
    log_info "同步admin文件到nginx目录..."
    rm -rf /www/wwwroot/tikshop-admin/*
    cp -r "$PROJECT_ROOT/admin/dist/"* /www/wwwroot/tikshop-admin/
    
    log_success "前端项目重建完成"
}

# 重启所有服务
restart_services() {
    log_info "重启所有服务..."
    
    # 使用管理脚本重启
    cd "$PROJECT_ROOT"
    ./manage-services.sh restart
    
    log_success "所有服务已重启"
}

# 验证修复结果
verify_fix() {
    log_info "验证修复结果..."
    
    # 检查服务状态
    cd "$PROJECT_ROOT"
    ./manage-services.sh status
    
    echo
    log_info "测试页面访问..."
    
    # 测试各个页面
    local tests=(
        "https://tiktokbusines.store/ 用户端主页"
        "https://tiktokbusines.store/login 用户端登录"
        "https://tiktokbusines.store/merchant/ 商家后台"
        "https://tiktokbusines.store/admin/ Admin后台"
    )
    
    for test in "${tests[@]}"; do
        local url=$(echo $test | cut -d' ' -f1)
        local desc=$(echo $test | cut -d' ' -f2-)
        
        if curl -s -I "$url" | head -1 | grep -q "200\|301\|302"; then
            log_success "$desc: 正常"
        else
            log_error "$desc: 异常"
        fi
    done
}

# 主修复函数
main() {
    log_info "开始自动修复TikShop项目..."
    echo
    
    fix_nginx
    echo
    
    fix_frontend_configs
    echo
    
    rebuild_frontends
    echo
    
    restart_services
    echo
    
    verify_fix
    echo
    
    log_success "自动修复完成！"
    log_info "如果仍有问题，请检查日志: ./manage-services.sh logs [服务名]"
}

# 执行主函数
main "$@"
