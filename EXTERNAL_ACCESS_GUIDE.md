# 🌐 TikShop 外部访问配置指南

## 📋 当前配置状态

✅ **服务器信息**
- **服务器IP**: 202.146.222.134
- **Nginx状态**: 正常运行
- **防火墙**: 已开放80/443端口
- **所有服务**: 正常运行

## 🌐 外部访问地址

| 服务 | 外部访问地址 | 状态 |
|------|-------------|------|
| 🔧 **API服务** | http://202.146.222.134/api | ✅ 正常 |
| 📚 **API文档** | http://202.146.222.134/api/docs | ✅ 正常 |
| 🖥️ **管理后台** | http://202.146.222.134/admin | ✅ 正常 |
| 🏪 **商家端** | http://202.146.222.134/merchant | ✅ 正常 |
| 📱 **用户商城** | http://202.146.222.134 | ✅ 正常 |

## 🔑 测试账户

### 管理员账户
- **用户名**: admin
- **密码**: 123456
- **访问地址**: http://202.146.222.134/admin

### 商家账户
- **用户名**: merchant001
- **密码**: password123
- **访问地址**: http://202.146.222.134/merchant

### 用户账户
- **手机号**: 13800138000
- **密码**: 123456
- **访问地址**: http://202.146.222.134

## 🛠️ 技术配置详情

### Nginx反向代理配置
```nginx
# 配置文件位置: /etc/nginx/sites-available/tikshop

# API服务 (端口3000)
location /api/ {
    proxy_pass http://localhost:3000;
}

# 管理后台 (端口5177)
location /admin/ {
    proxy_pass http://localhost:5177/;
}

# 商家端 (端口5176)
location /merchant/ {
    proxy_pass http://localhost:5176/;
}

# 用户商城 (端口3001)
location / {
    proxy_pass http://localhost:3001/;
}
```

### 服务端口映射
| 服务 | 内部端口 | 外部路径 |
|------|----------|----------|
| 后端API | 3000 | /api/ |
| 管理后台 | 5177 | /admin/ |
| 商家端 | 5176 | /merchant/ |
| 用户商城 | 3001 | / |

## 🔧 管理命令

### Nginx管理
```bash
# 检查Nginx状态
systemctl status nginx

# 重新加载配置
systemctl reload nginx

# 重启Nginx
systemctl restart nginx

# 查看Nginx日志
tail -f /var/log/nginx/tikshop_access.log
tail -f /var/log/nginx/tikshop_error.log
```

### PM2服务管理
```bash
# 查看服务状态
pm2 status

# 重启所有服务
pm2 restart all

# 查看服务日志
pm2 logs

# 监控面板
pm2 monit
```

## 🔒 安全配置

### 当前安全设置
- ✅ 防火墙已配置
- ✅ 安全头已设置
- ✅ 文件上传限制 (10MB)
- ✅ Gzip压缩已启用

### 建议的安全增强
1. **SSL证书配置** (HTTPS)
2. **访问频率限制**
3. **IP白名单** (可选)
4. **定期安全更新**

## 📊 性能优化

### 已启用的优化
- ✅ Gzip压缩
- ✅ 静态文件缓存
- ✅ 连接池优化
- ✅ 超时设置

### 监控指标
```bash
# 检查服务器负载
htop

# 检查内存使用
free -h

# 检查磁盘空间
df -h

# 检查网络连接
netstat -tlnp
```

## 🚨 故障排除

### 常见问题解决

#### 1. 502 Bad Gateway
```bash
# 检查后端服务状态
pm2 status

# 重启服务
pm2 restart all

# 检查端口监听
netstat -tlnp | grep -E ":(3000|3001|5176|5177)"
```

#### 2. 无法访问外部地址
```bash
# 检查防火墙状态
ufw status

# 检查Nginx状态
systemctl status nginx

# 检查端口80监听
netstat -tlnp | grep :80
```

#### 3. API接口无响应
```bash
# 检查后端日志
pm2 logs ecommerce-api

# 测试本地API
curl http://localhost:3000/api/health

# 测试外部API
curl http://202.146.222.134/api/health
```

## 🌍 域名配置 (可选)

### 1. 购买域名
- 在域名注册商购买域名
- 将域名A记录指向服务器IP: 202.146.222.134

### 2. 配置SSL证书
```bash
# 安装Certbot
apt install certbot python3-certbot-nginx -y

# 获取SSL证书
certbot --nginx -d your-domain.com

# 自动续期
crontab -e
# 添加: 0 12 * * * /usr/bin/certbot renew --quiet
```

### 3. 更新Nginx配置
```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.com;
    
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    
    # 其他配置...
}
```

## 📱 移动端访问

### 响应式设计
- ✅ 所有前端都支持移动端
- ✅ 自适应布局
- ✅ 触摸友好界面

### 移动端测试
```bash
# 使用curl模拟移动端请求
curl -H "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)" \
     http://202.146.222.134/
```

## 🔄 备份和恢复

### 数据库备份
```bash
# 备份数据库
mysqldump -u tikshop -p'TikShop_MySQL_#2025!9pQwXz' ecommerce > backup_$(date +%Y%m%d).sql

# 恢复数据库
mysql -u tikshop -p'TikShop_MySQL_#2025!9pQwXz' ecommerce < backup_20251018.sql
```

### 文件备份
```bash
# 备份上传文件
tar -czf uploads_backup_$(date +%Y%m%d).tar.gz /root/TikShop/ecommerce-backend/uploads/

# 备份配置文件
tar -czf config_backup_$(date +%Y%m%d).tar.gz /etc/nginx/sites-available/tikshop
```

## 📈 监控和日志

### 系统监控
```bash
# 实时监控
htop

# 磁盘使用
df -h

# 内存使用
free -h

# 网络连接
ss -tuln
```

### 应用监控
```bash
# PM2监控
pm2 monit

# Nginx访问日志
tail -f /var/log/nginx/tikshop_access.log

# 应用错误日志
pm2 logs --err
```

## 🎯 下一步建议

### 1. 立即可以做的
- ✅ 测试所有外部访问地址
- ✅ 使用测试账户登录各个系统
- ✅ 验证核心功能是否正常

### 2. 短期优化
- 🔄 配置SSL证书 (HTTPS)
- 🔄 设置域名解析
- 🔄 配置自动备份

### 3. 长期规划
- 📊 性能监控和优化
- 🔒 安全加固
- 📱 移动端优化
- 🚀 负载均衡 (如需要)

## 📞 技术支持

### 快速检查命令
```bash
# 一键检查所有服务状态
cd /root/TikShop && echo "=== 服务状态检查 ===" && \
pm2 status && echo -e "\n=== Nginx状态 ===" && \
systemctl status nginx --no-pager -l && echo -e "\n=== 外部访问测试 ===" && \
curl -s http://202.146.222.134/api/health
```

### 联系方式
- **项目维护者**: Admin
- **服务器IP**: 202.146.222.134
- **项目地址**: https://github.com/Janice0516/TikShop

---

## ✅ 部署完成确认

请确认以下项目都已正常工作：

- [ ] 外部API访问正常
- [ ] 管理后台可以登录
- [ ] 商家端可以登录
- [ ] 用户商城可以访问
- [ ] 所有功能测试通过

**🎉 恭喜！你的TikShop电商平台已经成功开放给外部访问！**

---

最后更新：2025-10-18
