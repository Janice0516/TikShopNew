# 用户前端环境变量配置说明

## 环境变量配置

在 `user-app` 目录下创建 `.env` 文件，内容如下：

```bash
# API基础URL
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api

# 应用配置
VITE_APP_TITLE=TikTok Shop
VITE_APP_VERSION=1.0.0

# 开发环境配置
VITE_NODE_ENV=development
```

## 生产环境配置

在 `user-app` 目录下创建 `.env.production` 文件，内容如下：

```bash
# 生产环境API基础URL
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api

# 应用配置
VITE_APP_TITLE=TikTok Shop
VITE_APP_VERSION=1.0.0

# 生产环境配置
VITE_NODE_ENV=production
```

## 使用方法

1. 复制上述内容到对应的环境变量文件中
2. 重启开发服务器
3. 在代码中使用 `import.meta.env.VITE_API_BASE_URL` 访问环境变量

## 注意事项

- 所有环境变量必须以 `VITE_` 开头才能在客户端代码中访问
- `.env` 文件应该添加到 `.gitignore` 中，不要提交到版本控制
- 生产环境部署时，需要在部署平台设置相应的环境变量
