# TikTok Shop - 用户商城

## 🎯 项目简介

这是TikTok Shop的用户商城前端，采用现代化的Vue.js技术栈构建，提供完整的电商购物体验。

## 🚀 技术栈

- **Vue 3** - 渐进式JavaScript框架
- **TypeScript** - 类型安全的JavaScript
- **Vite** - 下一代前端构建工具
- **Element Plus** - Vue 3 UI组件库
- **Pinia** - Vue状态管理
- **Vue Router** - Vue官方路由
- **SCSS** - CSS预处理器

## 🎨 设计特色

- **TikTok风格**: 深色主题 + TikTok红色配色
- **响应式设计**: 完美适配桌面和移动端
- **现代交互**: 流畅的动画和用户体验
- **商品展示**: TikTok风格的商品卡片设计

## 📱 功能特性

### 用户功能
- ✅ 用户注册/登录
- ✅ 商品浏览/搜索
- ✅ 分类筛选
- ✅ 购物车管理
- ✅ 订单创建/管理
- ✅ 个人中心

### 界面特色
- ✅ TikTok风格顶部导航
- ✅ 圆形分类图标
- ✅ 商品卡片展示
- ✅ 搜索功能
- ✅ 应用推广横幅

## 🔧 开发环境

### 环境要求
- Node.js >= 20.19.0
- npm >= 10.9.0

### 安装依赖
```bash
npm install
```

### 启动开发服务器
```bash
npm run dev
```
访问: http://localhost:3001

### 构建生产版本
```bash
npm run build-only
```

### 预览构建结果
```bash
npm run preview
```

## 🌐 API对接

项目已配置与后端API对接：
- **后端地址**: http://localhost:3000
- **API代理**: `/api` → 后端服务
- **认证方式**: JWT Token
- **数据格式**: JSON

### API接口
- 用户认证: `/api/auth/*`
- 商品管理: `/api/products/*`
- 分类管理: `/api/categories/*`
- 购物车: `/api/cart/*`
- 订单管理: `/api/orders/*`
- 轮播图: `/api/banners/*`

## 📁 项目结构

```
user-app/
├── src/
│   ├── api/           # API接口定义
│   ├── components/     # 可复用组件
│   ├── router/         # 路由配置
│   ├── stores/         # Pinia状态管理
│   ├── styles/         # 全局样式
│   ├── views/          # 页面组件
│   └── main.ts         # 应用入口
├── public/             # 静态资源
├── dist/               # 构建输出
└── package.json        # 项目配置
```

## 🎯 页面路由

- `/` - 首页
- `/login` - 登录页
- `/register` - 注册页
- `/product/:id` - 商品详情
- `/category/:id` - 分类页面
- `/search` - 搜索结果
- `/cart` - 购物车
- `/order` - 订单确认
- `/orders` - 订单列表
- `/profile` - 个人中心

## 🚀 部署

### 构建项目
```bash
npm run build-only
```

### 部署平台推荐
1. **Vercel** - 最简单，免费
2. **Netlify** - 功能丰富
3. **Render** - 全栈支持

详细部署指南请参考: `DEPLOYMENT_GUIDE.md`

## 🔄 项目替换说明

此项目已替换原有的UniApp商城：
- ✅ 原UniApp项目已备份
- ✅ 新Vue.js项目已部署到位
- ✅ API接口已配置对接
- ✅ 功能完整，体验更佳

## 📞 技术支持

如有问题，请检查：
1. Node.js版本是否符合要求
2. 依赖是否正确安装
3. API服务是否正常运行
4. 网络连接是否正常

---

**TikTok Shop - 您的购物首选平台** 🛍️