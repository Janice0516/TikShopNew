# 🚀 Render部署环境变量快速配置

## 📋 三个前端应用的环境变量

### 1. 用户前端 (TikTok Shop)
```
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
VITE_APP_TITLE=TikTok Shop
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=production
VITE_DEBUG=false
```

### 2. 商家后台 (Merchant Dashboard)
```
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
VITE_APP_TITLE=TikTok Shop Merchant
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=production
VITE_DEBUG=false
```

### 3. 管理后台 (Admin Dashboard)
```
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
VITE_APP_TITLE=TikTok Shop Admin
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=production
VITE_DEBUG=false
```

## 🔧 设置步骤

1. **登录Render Dashboard**
   - 访问：https://dashboard.render.com
   - 登录你的账户

2. **选择服务**
   - 找到对应的前端服务（用户前端/商家后台/管理后台）
   - 点击服务名称

3. **进入环境变量设置**
   - 点击左侧菜单的 **"Environment"**
   - 或点击 **"Settings"** → **"Environment Variables"**

4. **添加环境变量**
   - 点击 **"Add Environment Variable"**
   - 根据上述配置添加对应的环境变量

5. **保存并重新部署**
   - 点击 **"Save Changes"**
   - 服务将自动重新部署

## 📊 环境变量对比表

| 应用 | VITE_APP_TITLE | 用途 |
|------|----------------|------|
| 用户前端 | TikTok Shop | 用户购物界面 |
| 商家后台 | TikTok Shop Merchant | 商家管理平台 |
| 管理后台 | TikTok Shop Admin | 平台管理后台 |

## ✅ 验证设置

设置完成后，可以通过以下方式验证：

1. **检查构建日志**：确认环境变量被正确读取
2. **测试API连接**：访问应用并检查网络请求
3. **查看控制台**：确认没有API连接错误
4. **检查页面标题**：确认应用标题正确显示

## 🚀 部署顺序建议

1. **API服务** - 确保后端API正常运行
2. **用户前端** - 部署用户购物界面
3. **商家后台** - 部署商家管理平台
4. **管理后台** - 部署平台管理后台

## ⚠️ 重要提醒

- 所有环境变量必须以 `VITE_` 开头
- 修改环境变量后服务会自动重新部署
- 重新部署可能需要2-5分钟
- 确保API服务地址正确且可访问
- 每个服务都需要单独设置环境变量
