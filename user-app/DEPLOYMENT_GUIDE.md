# 🚀 TikTok Shop Vue.js 项目部署指南

## 📋 部署选项对比

| 平台 | 类型 | 免费额度 | 部署难度 | 推荐指数 |
|------|------|----------|----------|----------|
| **Vercel** | 静态托管 | 100GB/月 | ⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Netlify** | 静态托管 | 100GB/月 | ⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Render** | 全栈托管 | 750小时/月 | ⭐⭐⭐ | ⭐⭐⭐⭐ |
| **GitHub Pages** | 静态托管 | 1GB存储 | ⭐⭐ | ⭐⭐⭐ |

## 🎯 推荐方案：Vercel (最简单)

### 1. 准备工作

**确保项目已上传到GitHub**:
- ✅ 仓库地址: https://github.com/Janice0516/TikShop
- ✅ 项目已包含所有必要文件
- ✅ package.json 配置正确

### 2. Vercel部署步骤

#### 步骤1: 注册Vercel
1. 访问 [vercel.com](https://vercel.com)
2. 点击 "Sign Up" 注册账户
3. 选择 "Continue with GitHub" 使用GitHub登录

#### 步骤2: 导入项目
1. 登录后点击 "New Project"
2. 选择 "Import Git Repository"
3. 找到 `Janice0516/TikShop` 仓库
4. 点击 "Import"

#### 步骤3: 配置项目
```json
{
  "Framework Preset": "Vite",
  "Root Directory": "./",
  "Build Command": "npm run build",
  "Output Directory": "dist",
  "Install Command": "npm install"
}
```

#### 步骤4: 环境变量 (可选)
如果需要连接后端API，添加环境变量：
```
VITE_API_BASE_URL=https://tikshop-backend.onrender.com
```

#### 步骤5: 部署
1. 点击 "Deploy" 按钮
2. 等待构建完成 (约2-3分钟)
3. 获得部署URL: `https://tikshop-xxx.vercel.app`

### 3. 自定义域名 (可选)
1. 在Vercel项目设置中添加自定义域名
2. 配置DNS记录指向Vercel
3. 自动获得SSL证书

## 🌐 方案二：Netlify部署

### 1. 注册Netlify
1. 访问 [netlify.com](https://netlify.com)
2. 使用GitHub登录

### 2. 部署配置
```yaml
# netlify.toml (可选配置文件)
[build]
  publish = "dist"
  command = "npm run build"

[[redirects]]
  from = "/*"
  to = "/index.html"
  status = 200
```

### 3. 部署步骤
1. 点击 "New site from Git"
2. 选择GitHub → TikShop仓库
3. 设置构建命令: `npm run build`
4. 设置发布目录: `dist`
5. 点击 "Deploy site"

## 🔧 方案三：Render部署

### 1. 创建render.yaml配置
```yaml
# render.yaml
services:
  - type: web
    name: tikshop-frontend
    env: static
    buildCommand: npm install && npm run build
    staticPublishPath: ./dist
    envVars:
      - key: NODE_ENV
        value: production
```

### 2. Render部署步骤
1. 访问 [render.com](https://render.com)
2. 连接GitHub账户
3. 选择TikShop仓库
4. 选择 "Static Site"
5. 配置构建命令和发布目录
6. 部署

## 📱 移动端优化部署

### 1. PWA支持 (可选)
```json
// package.json 添加
{
  "scripts": {
    "build:pwa": "vite build && vite build --mode pwa"
  }
}
```

### 2. 移动端适配
项目已包含响应式设计，自动适配移动端。

## 🔧 本地构建测试

### 1. 构建项目
```bash
cd /Users/admin/Documents/tikshop-web
npm install
npm run build
```

### 2. 预览构建结果
```bash
npm run preview
```

### 3. 检查构建文件
```bash
ls -la dist/
```

## 🌍 生产环境配置

### 1. 环境变量
```bash
# .env.production
VITE_API_BASE_URL=https://tikshop-backend.onrender.com
VITE_APP_TITLE=TikTok Shop
VITE_APP_DESCRIPTION=您的购物首选平台
```

### 2. 构建优化
```typescript
// vite.config.ts 生产配置
export default defineConfig({
  build: {
    outDir: 'dist',
    assetsDir: 'assets',
    sourcemap: false,
    minify: 'terser',
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['vue', 'vue-router', 'pinia'],
          element: ['element-plus']
        }
      }
    }
  }
})
```

## 🚀 快速部署脚本

### 1. 创建部署脚本
```bash
#!/bin/bash
# deploy.sh

echo "🚀 开始部署TikTok Shop项目..."

# 构建项目
echo "📦 构建项目..."
npm run build

# 检查构建结果
if [ -d "dist" ]; then
    echo "✅ 构建成功！"
    echo "📁 构建文件:"
    ls -la dist/
else
    echo "❌ 构建失败！"
    exit 1
fi

echo "🎉 项目已准备好部署！"
echo "📤 请将dist文件夹上传到您的托管平台"
```

### 2. 运行部署脚本
```bash
chmod +x deploy.sh
./deploy.sh
```

## 🔍 部署后检查

### 1. 功能测试
- ✅ 页面加载正常
- ✅ 路由跳转正常
- ✅ 响应式布局正常
- ✅ API调用正常 (如果配置了后端)

### 2. 性能检查
- ✅ 页面加载速度
- ✅ 资源压缩情况
- ✅ 缓存配置

### 3. SEO优化
- ✅ 页面标题设置
- ✅ Meta标签配置
- ✅ 结构化数据

## 🆘 常见问题

### 1. 构建失败
```bash
# 清除缓存重新安装
rm -rf node_modules package-lock.json
npm install
npm run build
```

### 2. 路由404问题
确保配置了SPA重定向规则：
```yaml
# netlify.toml
[[redirects]]
  from = "/*"
  to = "/index.html"
  status = 200
```

### 3. API调用失败
检查环境变量配置和CORS设置。

## 🎯 推荐部署流程

1. **首选**: Vercel (最简单，免费，自动HTTPS)
2. **备选**: Netlify (功能丰富，免费额度大)
3. **高级**: Render (全栈支持，可扩展)

## 📞 需要帮助？

如果在部署过程中遇到任何问题，请提供：
1. 错误信息截图
2. 部署平台名称
3. 具体操作步骤

我会帮您解决部署问题！🚀
