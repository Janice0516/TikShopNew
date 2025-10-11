# 🚀 Render环境变量快速设置指南

## 📋 用户前端环境变量列表

在Render Dashboard中设置以下环境变量：

```
VITE_API_BASE_URL = https://tiktokshop-api.onrender.com/api
VITE_APP_TITLE = TikTok Shop
VITE_APP_VERSION = 1.0.0
VITE_NODE_ENV = production
```

## 🔧 设置步骤

### 1. 登录Render Dashboard
- 访问：https://dashboard.render.com
- 登录你的账户

### 2. 选择用户前端服务
- 找到用户前端服务（Static Site）
- 点击服务名称

### 3. 进入环境变量设置
- 点击左侧菜单的 **"Environment"**
- 或点击 **"Settings"** → **"Environment Variables"**

### 4. 添加环境变量
点击 **"Add Environment Variable"** 并添加：

| Key | Value |
|-----|-------|
| `VITE_API_BASE_URL` | `https://tiktokshop-api.onrender.com/api` |
| `VITE_APP_TITLE` | `TikTok Shop` |
| `VITE_APP_VERSION` | `1.0.0` |
| `VITE_NODE_ENV` | `production` |

### 5. 保存并重新部署
- 点击 **"Save Changes"**
- 服务将自动重新部署

## ✅ 验证设置

1. **检查构建日志**：确认环境变量被正确读取
2. **测试API连接**：访问应用并检查网络请求
3. **查看控制台**：确认没有API连接错误

## ⚠️ 重要提醒

- 所有环境变量必须以 `VITE_` 开头
- 修改环境变量后服务会自动重新部署
- 重新部署可能需要2-5分钟
- 确保API服务地址正确且可访问
