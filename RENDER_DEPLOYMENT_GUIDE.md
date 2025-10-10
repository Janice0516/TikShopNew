# Render部署指南

## 🚀 部署步骤

### 1. 创建PostgreSQL数据库

1. 登录Render控制台
2. 点击 "New +" → "PostgreSQL"
3. 选择免费计划
4. 设置数据库名称：`tiktokshop`
5. 记录数据库连接信息

### 2. 部署后端API

1. 点击 "New +" → "Web Service"
2. 连接GitHub仓库：`https://github.com/Janice0516/TikShop.git`
3. 配置如下：

**基本信息：**
- Name: `tiktokshop-api`
- Environment: `Node`
- Region: `Oregon (US West)`
- Branch: `main`
- Root Directory: `ecommerce-backend`

**构建和部署：**
- Build Command: `npm install && npm run build`
- Start Command: `npm run start:prod`

**环境变量：**
```
NODE_ENV=production
DB_HOST=[PostgreSQL Host]
DB_PORT=5432
DB_USERNAME=[PostgreSQL Username]
DB_PASSWORD=[PostgreSQL Password]
DB_DATABASE=tiktokshop
REDIS_URL=[Redis URL] (可选，使用内存缓存)
JWT_SECRET=your-super-secret-jwt-key-here
PORT=10000
```

### 3. 部署商家后台

1. 点击 "New +" → "Static Site"
2. 连接GitHub仓库：`https://github.com/Janice0516/TikShop.git`
3. 配置如下：

**基本信息：**
- Name: `tiktokshop-merchant`
- Branch: `main`
- Root Directory: `merchant`

**构建设置：**
- Build Command: `npm install && npm run build`
- Publish Directory: `dist`

**环境变量：**
```
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
```

### 4. 部署管理后台

1. 点击 "New +" → "Static Site"
2. 连接GitHub仓库：`https://github.com/Janice0516/TikShop.git`
3. 配置如下：

**基本信息：**
- Name: `tiktokshop-admin`
- Branch: `main`
- Root Directory: `admin`

**构建设置：**
- Build Command: `npm install && npm run build`
- Publish Directory: `dist`

**环境变量：**
```
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
```

### 5. 部署用户前端

1. 点击 "New +" → "Static Site"
2. 连接GitHub仓库：`https://github.com/Janice0516/TikShop.git`
3. 配置如下：

**基本信息：**
- Name: `tiktokshop-user`
- Branch: `main`
- Root Directory: `user-app`

**构建设置：**
- Build Command: `npm install && npm run build`
- Publish Directory: `dist`

**环境变量：**
```
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
```

## 📋 部署后配置

### 数据库初始化

1. 获取PostgreSQL连接信息
2. 使用数据库管理工具连接
3. 执行 `database/postgresql_migration.sql` 脚本

### 环境变量更新

部署完成后，需要更新前端的环境变量：

1. **商家后台**：更新 `VITE_API_BASE_URL` 为实际的API地址
2. **管理后台**：更新 `VITE_API_BASE_URL` 为实际的API地址
3. **用户前端**：更新 `VITE_API_BASE_URL` 为实际的API地址

## 🔗 访问地址

部署完成后，你将获得以下访问地址：

- **API**: `https://tiktokshop-api.onrender.com`
- **商家后台**: `https://tiktokshop-merchant.onrender.com`
- **管理后台**: `https://tiktokshop-admin.onrender.com`
- **用户前端**: `https://tiktokshop-user.onrender.com`

## 🔑 测试账号

- **管理员**: admin / admin123
- **商家**: merchant001 / merchant123
- **用户**: 13800138000 / 123456

## ⚠️ 注意事项

1. **免费额度限制**：
   - Web Service: 750小时/月
   - PostgreSQL: 1GB存储
   - Static Site: 100GB带宽

2. **冷启动**：
   - 免费服务在无活动时会休眠
   - 首次访问可能需要等待几秒钟

3. **数据库连接**：
   - 确保数据库连接信息正确
   - 检查防火墙设置

4. **环境变量**：
   - 所有敏感信息都通过环境变量配置
   - 不要将密钥提交到代码仓库

## 🚀 优化建议

1. **升级到付费计划**以获得更好的性能
2. **使用CDN**加速静态资源加载
3. **设置监控**监控服务状态
4. **定期备份**数据库数据
