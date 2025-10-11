# Render 部署配置

## 🚀 后端API部署配置

### 服务配置
- **服务名称**: `tiktokshop-api`
- **类型**: Web Service
- **环境**: Node.js
- **区域**: Oregon (US West)
- **分支**: main
- **根目录**: `ecommerce-backend`

### 构建配置
- **构建命令**: `npm install && npm run build`
- **启动命令**: `npm run start:prod`

### 环境变量
```bash
NODE_ENV=production
DB_TYPE=postgres
DB_HOST=dpg-d0j8q8h2s78s73fq8hpg-a.oregon-postgres.render.com
DB_PORT=5432
DB_USERNAME=tiktokshop_slkz_user
DB_PASSWORD=U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn
DB_DATABASE=tiktokshop_slkz
JWT_SECRET=56dcb52ecafb60675a58d3472d7af4077f491c32e477372349f82f5ef3b12e4d7ff367b77c5f05bb0969843d1fbc3a647a69633dc6614d87ceea2d55c0ba31d6
PORT=10000
```

## 🎨 前端部署配置

### 商家后台
- **服务名称**: `tiktokshop-merchant`
- **类型**: Static Site
- **根目录**: `merchant`
- **构建命令**: `npm install && npm run build`
- **发布目录**: `dist`

### 管理后台
- **服务名称**: `tiktokshop-admin`
- **类型**: Static Site
- **根目录**: `admin`
- **构建命令**: `npm install && npm run build`
- **发布目录**: `dist`

### 用户端
- **服务名称**: `tiktokshop-user`
- **类型**: Static Site
- **根目录**: `user-app`
- **构建命令**: `npm install && npm run build`
- **发布目录**: `dist`

## 📋 部署检查清单

### 后端API
- [x] TypeScript编译错误已修复
- [x] PostgreSQL驱动已安装
- [x] @nestjs/cli已移动到dependencies
- [x] 环境变量配置已准备
- [x] 数据库连接信息已配置

### 前端应用
- [ ] 商家后台API地址配置
- [ ] 管理后台API地址配置
- [ ] 用户端API地址配置

## 🔗 访问地址

部署完成后，各服务的访问地址：
- **后端API**: `https://tiktokshop-api.onrender.com`
- **商家后台**: `https://tiktokshop-merchant.onrender.com`
- **管理后台**: `https://tiktokshop-admin.onrender.com`
- **用户端**: `https://tiktokshop-user.onrender.com`
