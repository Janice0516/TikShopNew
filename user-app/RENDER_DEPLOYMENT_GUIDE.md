# 🚀 User App Render 部署指南

## 📋 部署步骤

### 1. 准备项目
确保项目已推送到GitHub仓库：`https://github.com/Janice0516/TikShop.git`

### 2. 登录Render
访问 [Render Dashboard](https://dashboard.render.com) 并登录

### 3. 创建新服务
1. 点击 **"New +"** 按钮
2. 选择 **"Static Site"** 类型

### 4. 配置服务
**基本信息**：
- **Name**: `tikshop-user-app`
- **Repository**: `https://github.com/Janice0516/TikShop.git`
- **Branch**: `main`
- **Root Directory**: `user-app`

**构建设置**：
- **Build Command**: `npm ci && npm run build`
- **Publish Directory**: `dist`

**环境变量**（可选）：
- `NODE_ENV`: `production`
- `VITE_API_BASE_URL`: `https://tiktokshop-api.onrender.com`

### 5. 高级设置
**Headers**（安全配置）：
```
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
Referrer-Policy: strict-origin-when-cross-origin
```

**Routes**（SPA支持）：
```
/* -> /index.html (200)
```

### 6. 部署
1. 点击 **"Create Static Site"**
2. 等待构建完成（约2-3分钟）
3. 获取部署URL

## 🔧 本地测试

### 构建测试
```bash
cd user-app
npm ci
npm run build
npm run preview
```

### 检查构建产物
```bash
ls -la dist/
# 应该包含 index.html, assets/, _redirects
```

## 🌐 访问地址

部署完成后，您将获得：
- **生产URL**: `https://tikshop-user-app.onrender.com`
- **API地址**: `https://tiktokshop-api.onrender.com`

## 📱 功能测试

### 测试页面
1. **首页**: `/` - 商品列表
2. **登录**: `/login` - 用户登录
3. **注册**: `/register` - 用户注册
4. **购物车**: `/cart` - 购物车管理
5. **订单**: `/orders` - 订单列表
6. **个人中心**: `/profile` - 用户信息

### 测试账户
- **手机号**: `13800138000`
- **密码**: `123456`

## 🛠️ 故障排除

### 常见问题

1. **构建失败**
   - 检查Node.js版本（需要20.19.0+）
   - 确保所有依赖已安装
   - 检查TypeScript错误

2. **路由404错误**
   - 确保`_redirects`文件存在
   - 检查Vue Router配置

3. **API请求失败**
   - 检查API代理配置
   - 确认Render API服务正常运行

4. **样式问题**
   - 检查SCSS编译
   - 确认Element Plus正确加载

### 调试命令
```bash
# 本地构建测试
npm run build

# 检查构建产物
ls -la dist/

# 本地预览
npm run preview

# 检查API连接
curl https://tiktokshop-api.onrender.com/api/health
```

## 📊 性能优化

### 构建优化
- ✅ 代码分割（vendor, element）
- ✅ 资源压缩
- ✅ 静态资源缓存
- ✅ Gzip压缩

### 运行时优化
- ✅ 懒加载路由
- ✅ 图片优化
- ✅ API请求缓存

## 🔄 更新部署

### 自动部署
- 推送到`main`分支自动触发部署
- 构建时间：约2-3分钟
- 零停机时间更新

### 手动部署
1. 在Render Dashboard点击"Manual Deploy"
2. 选择要部署的提交
3. 等待构建完成

## 📈 监控

### 访问统计
- Render Dashboard提供基本访问统计
- 可集成Google Analytics

### 错误监控
- 浏览器控制台错误
- 网络请求失败
- 页面加载性能

## 🎯 成功指标

部署成功后应该看到：
- ✅ 首页正常加载
- ✅ 商品列表显示
- ✅ 登录功能正常
- ✅ 购物车功能正常
- ✅ 路由跳转正常
- ✅ API请求成功

---

## 📞 支持

如果遇到问题：
1. 检查Render构建日志
2. 查看浏览器控制台错误
3. 测试API连接状态
4. 参考本指南故障排除部分

**部署完成后，您的TikTok Shop用户商城将在Render上运行！** 🎉
