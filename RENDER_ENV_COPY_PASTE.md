# Render部署环境变量

## 复制以下内容到Render Dashboard的环境变量设置中：

```
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
VITE_APP_TITLE=TikTok Shop
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=production
VITE_DEBUG=false
```

## 设置步骤：

1. 登录 Render Dashboard
2. 选择用户前端服务
3. 点击 "Environment" 或 "Settings" → "Environment Variables"
4. 逐个添加上述环境变量
5. 点击 "Save Changes"

## 环境变量说明：

- `VITE_API_BASE_URL`: API服务地址，指向Render上的后端API
- `VITE_APP_TITLE`: 应用标题，显示在浏览器标签页
- `VITE_APP_VERSION`: 应用版本号
- `VITE_NODE_ENV`: 环境类型，生产环境设为production
- `VITE_DEBUG`: 调试模式，生产环境设为false

## 验证设置：

设置完成后，服务会自动重新部署。可以通过以下方式验证：

1. 查看构建日志，确认环境变量被正确读取
2. 访问部署的应用，检查网络请求是否指向正确的API地址
3. 查看浏览器控制台，确认没有API连接错误
