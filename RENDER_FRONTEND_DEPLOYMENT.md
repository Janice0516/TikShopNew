# Render前端服务部署指南

## 🚀 部署商家后台 (Merchant Backend)

### 1. 创建新的Web Service
- 进入Render控制台
- 点击 "New +" → "Static Site"
- 连接GitHub仓库：`https://github.com/Janice0516/TikShop.git`

### 2. 配置商家后台
```bash
# 基本信息
Name: tiktokshop-merchant
Branch: main
Root Directory: merchant

# 构建配置
Build Command: npm install && npm run build
Publish Directory: dist

# 环境变量
VITE_API_BASE_URL: https://tiktokshop-api.onrender.com/api
```

### 3. 部署设置
- **Framework**: Vite
- **Node Version**: 18.x
- **Auto-Deploy**: Yes

---

## 🚀 部署管理后台 (Admin Backend)

### 1. 创建新的Web Service
- 点击 "New +" → "Static Site"
- 连接GitHub仓库：`https://github.com/Janice0516/TikShop.git`

### 2. 配置管理后台
```bash
# 基本信息
Name: tiktokshop-admin
Branch: main
Root Directory: admin

# 构建配置
Build Command: npm install && npm run build
Publish Directory: dist

# 环境变量
VITE_API_BASE_URL: https://tiktokshop-api.onrender.com/api
```

### 3. 部署设置
- **Framework**: Vite
- **Node Version**: 18.x
- **Auto-Deploy**: Yes

---

## 🚀 部署用户前端 (User App) - UniApp

### 1. 创建新的Web Service
- 点击 "New +" → "Static Site"
- 连接GitHub仓库：`https://github.com/Janice0516/TikShop.git`

### 2. 配置用户前端
```bash
# 基本信息
Name: tiktokshop-user
Branch: main
Root Directory: user-app

# 构建配置 (UniApp特殊配置)
Build Command: npm install && npm run build:h5
Publish Directory: dist/build/h5

# 环境变量
VITE_API_BASE_URL: https://tiktokshop-api.onrender.com/api
```

### 3. 部署设置
- **Framework**: Custom
- **Node Version**: 18.x
- **Auto-Deploy**: Yes

### 4. UniApp特殊说明
- UniApp使用 `build:h5` 命令构建H5版本
- 构建输出目录是 `dist/build/h5`
- 需要确保UniApp CLI已正确安装

---

## 📋 部署检查清单

### ✅ 商家后台部署检查
- [ ] 创建Static Site服务
- [ ] 设置Root Directory为 `merchant`
- [ ] 配置Build Command: `npm install && npm run build`
- [ ] 设置Publish Directory: `dist`
- [ ] 添加环境变量 `VITE_API_BASE_URL`
- [ ] 等待构建完成
- [ ] 测试访问商家后台

### ✅ 管理后台部署检查
- [ ] 创建Static Site服务
- [ ] 设置Root Directory为 `admin`
- [ ] 配置Build Command: `npm install && npm run build`
- [ ] 设置Publish Directory: `dist`
- [ ] 添加环境变量 `VITE_API_BASE_URL`
- [ ] 等待构建完成
- [ ] 测试访问管理后台

### ✅ 用户前端部署检查
- [ ] 创建Static Site服务
- [ ] 设置Root Directory为 `user-app`
- [ ] 配置Build Command: `npm install && npm run build`
- [ ] 设置Publish Directory: `dist`
- [ ] 添加环境变量 `VITE_API_BASE_URL`
- [ ] 等待构建完成
- [ ] 测试访问用户前端

---

## 🌐 预期访问地址

部署完成后，你将获得以下访问地址：

- **API服务**: `https://tiktokshop-api.onrender.com/api`
- **商家后台**: `https://tiktokshop-merchant.onrender.com`
- **管理后台**: `https://tiktokshop-admin.onrender.com`
- **用户前端**: `https://tiktokshop-user.onrender.com`

---

## 🔧 测试账号

### 商家后台测试账号
- **用户名**: merchant001
- **密码**: 123456

### 管理后台测试账号
- **用户名**: admin
- **密码**: admin123

---

## 📝 注意事项

1. **构建时间**: 每个前端服务首次构建可能需要3-5分钟
2. **环境变量**: 确保所有服务都设置了正确的API地址
3. **依赖安装**: 如果构建失败，检查package.json中的依赖
4. **缓存问题**: 如果遇到缓存问题，可以手动触发重新部署

---

## 🎯 部署成功标志

- ✅ 所有服务状态显示 "Live"
- ✅ 可以正常访问各个前端页面
- ✅ 商家后台可以正常登录
- ✅ 管理后台可以正常登录
- ✅ 用户前端可以正常浏览商品
