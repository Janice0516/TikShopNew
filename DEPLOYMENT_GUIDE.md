# 🚀 TikShop 部署指南

## 📋 项目概览

TikShop 是一个完整的电商平台，包含以下组件：

- **后端API服务** (NestJS + MySQL) - 端口 3000
- **管理后台** (Vue.js + Element Plus) - 端口 5175  
- **商家端** (Vue.js + Element Plus) - 端口 5174
- **用户商城** (Vue.js + Element Plus) - 端口 3001

## ✅ 当前状态检查

### 服务状态
所有服务都已正常运行：
```bash
pm2 status
```

### API测试
```bash
# 健康检查
curl http://localhost:3000/api/health

# 公开分类接口
curl http://localhost:3000/api/public-categories

# 用户端访问
curl http://localhost:3001
```

## 🛠️ 环境要求

### 系统要求
- **操作系统**: Linux (Ubuntu 20.04+ 推荐)
- **Node.js**: 18+ (当前使用 20.19.0)
- **MySQL**: 8.0+
- **PM2**: 进程管理工具
- **内存**: 最少 2GB RAM
- **存储**: 最少 10GB 可用空间

### 端口要求
- **3000**: 后端API服务
- **3001**: 用户商城
- **5174**: 商家端
- **5175**: 管理后台
- **3306**: MySQL数据库
- **6379**: Redis (可选)

## 📦 快速部署

### 1. 环境准备

```bash
# 更新系统
sudo apt update && sudo apt upgrade -y

# 安装Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs

# 安装PM2
sudo npm install -g pm2

# 安装MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation
```

### 2. 数据库配置

```bash
# 登录MySQL
sudo mysql -u root -p

# 创建数据库和用户
CREATE DATABASE ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tikshop'@'localhost' IDENTIFIED BY 'TikShop_MySQL_#2025!9pQwXz';
GRANT ALL PRIVILEGES ON ecommerce.* TO 'tikshop'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# 导入数据库结构
cd /root/TikShop
mysql -u tikshop -p'TikShop_MySQL_#2025!9pQwXz' ecommerce < database/schema.sql
mysql -u tikshop -p'TikShop_MySQL_#2025!9pQwXz' ecommerce < database/init_data.sql
```

### 3. 项目配置

```bash
# 进入项目目录
cd /root/TikShop

# 安装根目录依赖
npm install

# 安装后端依赖
cd ecommerce-backend
npm install

# 安装前端依赖
cd ../admin && npm install
cd ../merchant && npm install  
cd ../user-app && npm install
```

### 4. 环境变量配置

后端环境变量 (`ecommerce-backend/.env`):
```env
NODE_ENV=development
PORT=3000

DB_HOST=127.0.0.1
DB_PORT=3306
DB_USERNAME=tikshop
DB_PASSWORD=TikShop_MySQL_#2025!9pQwXz
DB_DATABASE=ecommerce

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=

JWT_SECRET=super_secret_key
JWT_EXPIRES_IN=7d

UPLOAD_PATH=./uploads
MAX_FILE_SIZE=10485760
```

### 5. 启动服务

```bash
# 使用PM2启动所有服务
cd /root/TikShop
./start-all.sh

# 或者手动启动
pm2 start ecosystem.config.js
```

## 🌐 访问地址

| 服务 | 地址 | 说明 |
|------|------|------|
| 🔧 API服务 | http://localhost:3000/api | NestJS后端API |
| 📚 API文档 | http://localhost:3000/api/docs | Swagger文档 |
| 🖥️ 管理后台 | http://localhost:5175 | 平台管理界面 |
| 🏪 商家端 | http://localhost:5174 | 商家管理界面 |
| 📱 用户商城 | http://localhost:3001 | 用户购物界面 |

## 🔑 测试账户

### 管理员账户
- **用户名**: admin
- **密码**: 123456

### 商家账户
- **用户名**: merchant001
- **密码**: password123

### 用户账户
- **手机号**: 13800138000
- **密码**: 123456

## 🔧 管理命令

### PM2管理
```bash
# 查看状态
pm2 status

# 查看日志
pm2 logs

# 重启服务
pm2 restart all

# 停止服务
pm2 stop all

# 删除服务
pm2 delete all

# 监控面板
pm2 monit
```

### 数据库管理
```bash
# 备份数据库
mysqldump -u tikshop -p'TikShop_MySQL_#2025!9pQwXz' ecommerce > backup.sql

# 恢复数据库
mysql -u tikshop -p'TikShop_MySQL_#2025!9pQwXz' ecommerce < backup.sql
```

## 🚨 故障排除

### 常见问题

#### 1. 端口被占用
```bash
# 查看端口占用
sudo netstat -tlnp | grep :3000
sudo netstat -tlnp | grep :5175

# 杀死进程
sudo kill -9 PID
```

#### 2. 数据库连接失败
```bash
# 检查MySQL状态
sudo systemctl status mysql

# 重启MySQL
sudo systemctl restart mysql

# 检查连接
mysql -u tikshop -p'TikShop_MySQL_#2025!9pQwXz' -h 127.0.0.1
```

#### 3. 前端服务无法访问
```bash
# 检查PM2状态
pm2 status

# 查看错误日志
pm2 logs admin-frontend
pm2 logs merchant-frontend
pm2 logs user-app

# 重启前端服务
pm2 restart admin-frontend
pm2 restart merchant-frontend
pm2 restart user-app
```

#### 4. API接口404错误
```bash
# 检查后端服务
pm2 logs ecommerce-api

# 测试API
curl http://localhost:3000/api/health
curl http://localhost:3000/api/public-categories
```

## 📊 性能优化

### 1. 数据库优化
```sql
-- 添加索引
ALTER TABLE products ADD INDEX idx_category_id (category_id);
ALTER TABLE orders ADD INDEX idx_user_id (user_id);
ALTER TABLE orders ADD INDEX idx_status (status);
```

### 2. PM2集群模式
```bash
# 修改ecosystem.config.js
# 将exec_mode改为cluster
# 增加instances数量
```

### 3. 缓存配置
```bash
# 安装Redis
sudo apt install redis-server -y

# 启动Redis
sudo systemctl start redis
sudo systemctl enable redis
```

## 🔒 安全配置

### 1. 防火墙设置
```bash
# 只开放必要端口
sudo ufw allow 22    # SSH
sudo ufw allow 80    # HTTP
sudo ufw allow 443  # HTTPS
sudo ufw enable
```

### 2. 数据库安全
```bash
# 修改默认密码
# 限制远程访问
# 定期备份
```

### 3. 应用安全
```bash
# 修改JWT密钥
# 启用HTTPS
# 配置CORS
```

## 📈 监控和日志

### 1. 日志管理
```bash
# 查看所有日志
pm2 logs

# 查看特定服务日志
pm2 logs ecommerce-api

# 日志轮转
pm2 install pm2-logrotate
pm2 set pm2-logrotate:max_size 10M
pm2 set pm2-logrotate:retain 30
```

### 2. 性能监控
```bash
# PM2监控面板
pm2 monit

# 系统监控
htop
iostat
```

## 🚀 生产环境部署

### 1. 使用Nginx反向代理
```nginx
# /etc/nginx/sites-available/tikshop
server {
    listen 80;
    server_name your-domain.com;

    location /api/ {
        proxy_pass http://localhost:3000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }

    location /admin/ {
        proxy_pass http://localhost:5175;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }

    location /merchant/ {
        proxy_pass http://localhost:5174;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }

    location / {
        proxy_pass http://localhost:3001;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

### 2. SSL证书配置
```bash
# 使用Let's Encrypt
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

### 3. 自动启动
```bash
# 设置PM2开机自启
pm2 startup
pm2 save
```

## 📞 技术支持

### 获取帮助
1. 查看项目文档
2. 检查错误日志
3. 查看PM2状态
4. 测试API接口

### 联系方式
- **项目维护者**: Admin
- **邮箱**: admin@example.com
- **项目地址**: https://github.com/Janice0516/TikShop

---

## ✅ 部署检查清单

在部署完成后，请确认：

- [ ] 所有服务状态为online
- [ ] API健康检查返回200
- [ ] 数据库连接正常
- [ ] 前端页面可以访问
- [ ] 测试账户可以登录
- [ ] 文件上传功能正常
- [ ] 日志没有错误信息

**恭喜！🎉 你的TikShop电商平台已经成功部署！**

---

最后更新：2025-10-18
