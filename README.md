# TikTok Shop - 完整的电商平台

一个功能完整的TikTok风格电商平台，包含用户端、商家端、管理端和API后端。

## 🚀 项目特性

- **用户端**: TikTok风格的购物体验，支持商品浏览、购物车、订单管理
- **商家端**: 完整的商家管理后台，支持商品管理、订单处理、财务管理
- **管理端**: 平台管理后台，支持用户管理、商家管理、分类管理、信用评级
- **API后端**: NestJS + TypeORM + MySQL + Redis 的完整后端服务

## 📁 项目结构

```
TikTokShop/
├── ecommerce-backend/     # NestJS 后端API
├── user-app/             # UniApp 用户端应用
├── merchant/             # Vue.js 商家后台
├── admin/                # Vue.js 管理后台
├── .gitignore
└── README.md
```

## 🛠 技术栈

### 后端
- **框架**: NestJS
- **数据库**: MySQL + TypeORM
- **缓存**: Redis
- **认证**: JWT
- **文档**: Swagger

### 前端
- **用户端**: UniApp (Vue.js)
- **商家端**: Vue.js + Element Plus
- **管理端**: Vue.js + Element Plus
- **构建工具**: Vite

## 🚀 快速开始

### 环境要求
- Node.js >= 16.0.0
- MySQL >= 8.0
- Redis >= 6.0

### 安装依赖

```bash
# 后端
cd ecommerce-backend
npm install

# 用户端
cd user-app
npm install

# 商家端
cd merchant
npm install

# 管理端
cd admin
npm install
```

### 配置环境变量

在 `ecommerce-backend` 目录下创建 `.env` 文件：

```env
# 数据库配置
DB_HOST=localhost
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=your_password
DB_DATABASE=tiktokshop

# Redis配置
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=

# JWT配置
JWT_SECRET=your_jwt_secret

# 其他配置
NODE_ENV=development
PORT=3000
```

### 启动服务

```bash
# 启动后端API
cd ecommerce-backend
npm run start:dev

# 启动用户端 (端口: 5173)
cd user-app
npm run dev

# 启动商家端 (端口: 5174)
cd merchant
npm run dev

# 启动管理端 (端口: 5177)
cd admin
npm run dev
```

## 📱 访问地址

- **用户端**: http://localhost:5173
- **商家端**: http://localhost:5174
- **管理端**: http://localhost:5177
- **API文档**: http://localhost:3000/api/docs

## 🔑 测试账号

### 管理员账号
- 用户名: `admin`
- 密码: `admin123`

### 商家账号
- 用户名: `merchant001`
- 密码: `merchant123`

### 用户账号
- 手机号: `13800138000`
- 密码: `123456`

## ✨ 主要功能

### 用户端功能
- 🛍️ 商品浏览和搜索
- 🛒 购物车管理
- 📦 订单管理
- 💳 支付功能
- 👤 用户中心

### 商家端功能
- 📊 数据统计仪表盘
- 🛍️ 商品管理
- 📦 订单管理
- 💰 财务管理
- 🏪 店铺管理
- ⭐ 信用评级查看

### 管理端功能
- 📊 平台数据统计
- 👥 用户管理
- 🏪 商家管理
- 📦 订单管理
- 🏷️ 分类管理
- ⭐ 信用评级系统
- 💰 提现管理

## 🔧 开发说明

### 数据库迁移
```bash
cd ecommerce-backend
npm run migration:run
```

### 代码格式化
```bash
npm run format
```

### 代码检查
```bash
npm run lint
```

## 📄 许可证

MIT License

## 🤝 贡献

欢迎提交 Issue 和 Pull Request！

## 📞 联系方式

如有问题，请通过以下方式联系：
- GitHub Issues: [项目Issues页面]
- Email: janice0516@example.com

---

⭐ 如果这个项目对你有帮助，请给个Star支持一下！