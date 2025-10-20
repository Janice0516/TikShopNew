# 环境变量模板文件

## 开发环境 (.env)
```bash
# 开发环境配置
VITE_API_BASE_URL=http://localhost:3000/api
VITE_APP_TITLE=TikTok Shop
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=development
VITE_DEBUG=true
```

## 生产环境 (.env.production)
```bash
# 生产环境配置
VITE_API_BASE_URL=http://localhost:3000/api
VITE_APP_TITLE=TikTok Shop
VITE_APP_VERSION=1.0.0
VITE_NODE_ENV=production
VITE_DEBUG=false
```

## 测试环境 (.env.test)
```bash
# 测试环境配置
VITE_API_BASE_URL=http://localhost:3000/api
VITE_APP_TITLE=TikTok Shop (Test)
VITE_APP_VERSION=1.0.0-test
VITE_NODE_ENV=test
VITE_DEBUG=true
```

## 本地环境 (.env.local)
```bash
# 本地开发环境配置
VITE_API_BASE_URL=http://localhost:3000/api
VITE_APP_TITLE=TikTok Shop (Local)
VITE_APP_VERSION=1.0.0-local
VITE_NODE_ENV=development
VITE_DEBUG=true
```

## Render环境变量设置

在Render Dashboard中设置以下环境变量：

| Key | Value |
|-----|-------|
| `VITE_API_BASE_URL` | `http://localhost:3000/api` |
| `VITE_APP_TITLE` | `TikTok Shop` |
| `VITE_APP_VERSION` | `1.0.0` |
| `VITE_NODE_ENV` | `production` |
| `VITE_DEBUG` | `false` |

## 使用方法

1. **本地开发**：
   ```bash
   cd user-app
   npm run dev
   ```

2. **生产构建**：
   ```bash
   cd user-app
   npm run build
   ```

3. **测试环境**：
   ```bash
   cd user-app
   npm run build --mode test
   ```

## 注意事项

- 所有环境变量必须以 `VITE_` 开头
- `.env.local` 文件优先级最高
- 生产环境建议使用 `.env.production`
- 敏感信息不要放在客户端环境变量中
