# 电商管理后台

基于 Vue3 + TypeScript + Element Plus 的现代化管理后台系统

## 技术栈

- **框架**: Vue 3.5
- **语言**: TypeScript
- **构建工具**: Vite 7
- **UI组件库**: Element Plus
- **路由**: Vue Router 4
- **状态管理**: Pinia
- **HTTP客户端**: Axios

## 功能特性

### 已完成功能

- ✅ 用户认证（登录/登出）
- ✅ 响应式布局（侧边栏 + 顶部导航）
- ✅ 数据概览（Dashboard）
- ✅ 商品管理
  - 商品列表（分页、搜索、筛选）
  - 添加商品
  - 编辑商品
  - 上架/下架
  - 删除商品
- ✅ 订单管理
  - 订单列表（分页、搜索、筛选）
  - 订单详情
- ✅ 商家管理
  - 商家列表
  - 商家审核（通过/拒绝）
- ✅ 分类管理（展示）

### 待完善功能

- [ ] 数据统计图表
- [ ] 权限管理
- [ ] 操作日志
- [ ] 个人中心
- [ ] 文件上传组件
- [ ] 富文本编辑器

## 快速开始

### 1. 安装依赖

```bash
npm install
```

### 2. 启动开发服务器

```bash
npm run dev
```

访问: http://localhost:5173

### 3. 登录测试

使用以下测试账号登录：

- 手机号: `13800138000`
- 密码: `123456`

> 注意：需要先启动后端服务（ecommerce-backend）

## 项目结构

```
admin/
├── src/
│   ├── api/                  # API接口
│   │   ├── user.ts          # 用户相关
│   │   ├── product.ts       # 商品相关
│   │   ├── order.ts         # 订单相关
│   │   └── merchant.ts      # 商家相关
│   │
│   ├── assets/              # 静态资源
│   │
│   ├── components/          # 公共组件
│   │
│   ├── layouts/             # 布局组件
│   │   └── index.vue       # 主布局
│   │
│   ├── router/              # 路由配置
│   │   └── index.ts
│   │
│   ├── stores/              # 状态管理
│   │   └── user.ts         # 用户状态
│   │
│   ├── styles/              # 全局样式
│   │   └── index.css
│   │
│   ├── utils/               # 工具函数
│   │   └── request.ts      # HTTP请求封装
│   │
│   ├── views/               # 页面视图
│   │   ├── login/          # 登录页
│   │   ├── dashboard/      # 数据概览
│   │   ├── products/       # 商品管理
│   │   ├── orders/         # 订单管理
│   │   ├── merchants/      # 商家管理
│   │   └── categories/     # 分类管理
│   │
│   ├── App.vue             # 根组件
│   └── main.ts             # 入口文件
│
├── .env.development         # 开发环境变量
├── .env.production          # 生产环境变量
├── index.html
├── package.json
├── tsconfig.json
├── vite.config.ts
└── README.md
```

## 环境变量

### 开发环境 (.env.development)

```env
VITE_API_BASE_URL=http://localhost:3000/api
```

### 生产环境 (.env.production)

```env
VITE_API_BASE_URL=https://your-domain.com/api
```

## 构建部署

### 构建

```bash
npm run build
```

构建后的文件在 `dist` 目录

### 预览

```bash
npm run preview
```

## 开发规范

### 代码规范

- 使用 TypeScript 严格模式
- 组件使用 Composition API + `<script setup>`
- 遵循 Vue 3 最佳实践

### 命名规范

- 组件文件：PascalCase（如：`UserList.vue`）
- 工具函数：camelCase（如：`formatDate.ts`）
- 常量：UPPER_SNAKE_CASE（如：`API_BASE_URL`）

### Git提交规范

```
feat: 新功能
fix: 修复bug
docs: 文档更新
style: 代码格式
refactor: 重构
test: 测试
chore: 构建/工具
```

## 常见问题

### Q: 登录后提示401？

A: 检查后端服务是否启动，Token是否正确

### Q: 接口请求失败？

A: 检查`.env.development`中的API地址是否正确

### Q: 图片显示不了？

A: 确保图片URL路径正确，可以使用完整的URL

## License

MIT
