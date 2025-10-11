# 🚀 全项目环境变量配置指南

## 📋 项目环境变量概览

本项目包含三个前端应用，每个都需要配置环境变量：

- **用户前端** (`user-app`) - TikTok风格的商城
- **商家后台** (`merchant`) - 商家管理平台
- **管理后台** (`admin`) - 平台管理后台

## 🔧 环境变量文件结构

每个项目都包含以下环境变量文件：

```
project/
├── .env                 # 开发环境
├── .env.production      # 生产环境
├── .env.test           # 测试环境
└── .env.local          # 本地环境
```

## 📱 用户前端环境变量

### 开发环境 (.env)
```bash
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
VITE_APP_TITLE=TikTok Shop
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=development
VITE_DEBUG=true
```

### 生产环境 (.env.production)
```bash
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
VITE_APP_TITLE=TikTok Shop
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=production
VITE_DEBUG=false
```

## 🏪 商家后台环境变量

### 开发环境 (.env)
```bash
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
VITE_APP_TITLE=TikTok Shop Merchant
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=development
VITE_DEBUG=true
```

### 生产环境 (.env.production)
```bash
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
VITE_APP_TITLE=TikTok Shop Merchant
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=production
VITE_DEBUG=false
```

## 🛠️ 管理后台环境变量

### 开发环境 (.env)
```bash
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
VITE_APP_TITLE=TikTok Shop Admin
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=development
VITE_DEBUG=true
```

### 生产环境 (.env.production)
```bash
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
VITE_APP_TITLE=TikTok Shop Admin
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=production
VITE_DEBUG=false
```

## 🚀 Render部署环境变量

### 用户前端部署
在Render Dashboard中设置：
```
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
VITE_APP_TITLE=TikTok Shop
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=production
VITE_DEBUG=false
```

### 商家后台部署
在Render Dashboard中设置：
```
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
VITE_APP_TITLE=TikTok Shop Merchant
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=production
VITE_DEBUG=false
```

### 管理后台部署
在Render Dashboard中设置：
```
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
VITE_APP_TITLE=TikTok Shop Admin
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=production
VITE_DEBUG=false
```

## 💻 本地开发

### 启动用户前端
```bash
cd user-app
npm run dev
```

### 启动商家后台
```bash
cd merchant
npm run dev
```

### 启动管理后台
```bash
cd admin
npm run dev
```

## 🏗️ 生产构建

### 构建用户前端
```bash
cd user-app
npm run build
```

### 构建商家后台
```bash
cd merchant
npm run build
```

### 构建管理后台
```bash
cd admin
npm run build
```

## 🔍 环境变量说明

| 变量名 | 说明 | 示例值 |
|--------|------|--------|
| `VITE_API_BASE_URL` | API服务地址 | `https://tiktokshop-api.onrender.com/api` |
| `VITE_APP_TITLE` | 应用标题 | `TikTok Shop` |
| `VITE_APP_VERSION` | 应用版本 | `1.0.0` |
| `VITE_NODE_ENV` | 环境类型 | `development/production/test` |
| `VITE_DEBUG` | 调试模式 | `true/false` |

## ⚠️ 注意事项

1. **环境变量前缀**：所有客户端环境变量必须以 `VITE_` 开头
2. **文件优先级**：`.env.local` > `.env.production` > `.env.test` > `.env`
3. **敏感信息**：不要在客户端环境变量中存储敏感信息
4. **API地址**：确保API服务地址正确且可访问
5. **重新部署**：修改环境变量后服务会自动重新部署

## 🛠️ 故障排除

### 环境变量未生效
1. 检查变量名是否正确（必须以 `VITE_` 开头）
2. 确认服务已重新部署
3. 查看构建日志确认变量被正确读取

### API连接失败
1. 检查 `VITE_API_BASE_URL` 是否正确
2. 确认API服务是否正常运行
3. 检查网络连接和CORS设置

### 构建失败
1. 检查环境变量值是否包含特殊字符
2. 确认所有必需的环境变量都已设置
3. 查看构建日志中的具体错误信息
