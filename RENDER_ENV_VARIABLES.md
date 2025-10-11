# Render环境变量配置指南

## 🎯 用户前端环境变量

在Render上部署用户前端时，需要设置以下环境变量：

### 📱 必需的环境变量

| 变量名 | 值 | 说明 |
|--------|-----|------|
| `VITE_API_BASE_URL` | `https://tiktokshop-api.onrender.com/api` | API服务地址 |
| `VITE_APP_TITLE` | `TikTok Shop` | 应用标题 |
| `VITE_APP_VERSION` | `1.0.0` | 应用版本 |
| `VITE_NODE_ENV` | `production` | 环境类型 |

### 🔧 可选的环境变量

| 变量名 | 值 | 说明 |
|--------|-----|------|
| `VITE_APP_DESCRIPTION` | `TikTok风格的电商平台` | 应用描述 |
| `VITE_APP_AUTHOR` | `TikTok Shop Team` | 应用作者 |
| `VITE_DEBUG` | `false` | 调试模式 |

## 🚀 设置步骤

### 方法1: Render Dashboard

1. 登录 [Render Dashboard](https://dashboard.render.com)
2. 选择用户前端服务
3. 点击 **Environment** 或 **Settings** → **Environment Variables**
4. 添加上述环境变量
5. 点击 **Save Changes**

### 方法2: Render CLI

```bash
# 安装Render CLI
npm install -g @render/cli

# 登录
render auth login

# 设置环境变量
render env set VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api --service your-service-name
render env set VITE_APP_TITLE="TikTok Shop" --service your-service-name
render env set VITE_APP_VERSION=1.0.0 --service your-service-name
render env set VITE_NODE_ENV=production --service your-service-name
```

### 方法3: 批量设置脚本

```bash
# 使用提供的脚本
chmod +x setup-render-env.sh
./setup-render-env.sh
```

## 🔍 验证环境变量

设置完成后，可以通过以下方式验证：

1. **查看服务日志**：
   - 在Render Dashboard中查看服务日志
   - 确认环境变量已正确加载

2. **检查构建输出**：
   - 查看构建日志中的环境变量信息
   - 确认API地址正确

3. **测试API连接**：
   - 在浏览器中访问部署的应用
   - 检查网络请求是否指向正确的API地址

## ⚠️ 注意事项

1. **环境变量前缀**：
   - 所有客户端环境变量必须以 `VITE_` 开头
   - 只有 `VITE_` 开头的变量才能在客户端代码中访问

2. **敏感信息**：
   - 不要在环境变量中存储敏感信息（如API密钥）
   - 客户端环境变量对所有用户可见

3. **服务重启**：
   - 修改环境变量后，服务会自动重新部署
   - 重新部署可能需要几分钟时间

4. **变量值格式**：
   - 字符串值不需要引号
   - 布尔值使用 `true` 或 `false`
   - 数字值直接输入数字

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
