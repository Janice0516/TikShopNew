# 🚀 快速启动指南

欢迎使用供货型电商平台！本指南将帮助你快速搭建开发环境并启动项目。

## 📋 前置要求

在开始之前，请确保你的电脑已安装以下软件：

```bash
Node.js >= 18.x      # 运行环境
MySQL >= 8.0         # 数据库
Redis >= 7.0         # 缓存
Git                  # 版本控制
```

### 安装检查

```bash
# 检查Node.js版本
node -v
# 应该显示: v18.x.x 或更高

# 检查npm版本
npm -v

# 检查MySQL是否运行
mysql --version

# 检查Redis是否运行
redis-cli ping
# 应该返回: PONG
```

---

## 🛠️ 第一步：初始化数据库

### 1.1 创建数据库

```bash
# 登录MySQL
mysql -u root -p

# 创建数据库（复制以下SQL执行）
CREATE DATABASE ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 退出
exit;
```

### 1.2 导入数据库表结构

```bash
cd /Users/admin/Documents/TikTokShop

# 导入表结构
mysql -u root -p ecommerce < database/schema.sql

# 导入初始化数据
mysql -u root -p ecommerce < database/init_data.sql
```

### 1.3 验证数据库

```bash
mysql -u root -p ecommerce

# 查看所有表
SHOW TABLES;

# 应该看到15张表：
# user, user_address, merchant, category, platform_product,
# product_sku, merchant_product, cart, order_main, order_item,
# order_logistics, after_sale, fund_flow, withdraw,
# system_config, admin

# 查看初始化数据
SELECT * FROM category;        # 19条分类数据
SELECT * FROM platform_product; # 10条测试商品
SELECT * FROM admin;            # 1个管理员账号

exit;
```

---

## ⚙️ 第二步：配置后端

### 2.1 进入后端目录

```bash
cd /Users/admin/Documents/TikTokShop/ecommerce-backend
```

### 2.2 安装依赖

```bash
# 安装npm包（第一次需要5-10分钟）
npm install

# 如果安装慢，可以使用国内镜像
npm install --registry=https://registry.npmmirror.com
```

### 2.3 配置环境变量

```bash
# 创建.env文件
cp .env.example .env

# 使用编辑器打开.env文件
nano .env
# 或
code .env
```

**修改以下关键配置：**

```env
# 数据库配置（重要！）
DB_HOST=localhost
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=你的MySQL密码
DB_DATABASE=ecommerce

# Redis配置
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=你的Redis密码（如果有）

# JWT密钥（重要！生产环境务必修改）
JWT_SECRET=your_super_secret_key_change_it_in_production
JWT_EXPIRES_IN=7d
```

保存并退出（Ctrl+X，然后Y，然后Enter）

### 2.4 启动后端服务

```bash
# 开发模式启动（热重载）
npm run start:dev

# 成功启动后，你应该看到：
# ================================================
# 🚀 应用启动成功！
# ================================================
# 📝 API地址: http://localhost:3000/api
# 📚 文档地址: http://localhost:3000/api/docs
# 🌍 环境: development
# ================================================
```

### 2.5 测试API接口

打开浏览器访问：**http://localhost:3000/api/docs**

你应该能看到Swagger API文档界面！🎉

---

## 🧪 第三步：测试API

### 3.1 测试用户注册

使用Postman或curl测试：

```bash
# 1. 发送验证码（暂时跳过验证）
curl -X POST http://localhost:3000/api/user/send-code \
  -H "Content-Type: application/json" \
  -d '{"phone":"13800138001"}'

# 2. 注册用户
curl -X POST http://localhost:3000/api/user/register \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "13800138001",
    "password": "123456",
    "code": "123456"
  }'

# 成功返回：
# {
#   "code": 200,
#   "message": "success",
#   "data": {
#     "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
#     "userInfo": {
#       "id": 2,
#       "phone": "13800138001",
#       "nickname": "用户8001"
#     }
#   }
# }
```

### 3.2 测试用户登录

```bash
curl -X POST http://localhost:3000/api/user/login \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "13800138001",
    "password": "123456"
  }'
```

### 3.3 测试获取个人信息（需要Token）

```bash
# 替换YOUR_TOKEN为上一步返回的token
curl -X GET http://localhost:3000/api/user/profile \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3.4 使用Swagger测试（更方便）

1. 打开 http://localhost:3000/api/docs
2. 找到"用户模块"
3. 点击 POST /api/user/register
4. 点击"Try it out"
5. 填写参数，点击"Execute"
6. 查看响应

---

## 🎨 第四步：启动前端（可选）

### 4.1 管理后台（暂未创建）

管理后台需要Vue3项目，我们会在后续创建。

### 4.2 用户端H5（暂未创建）

用户端需要Uni-app项目，我们会在后续创建。

---

## 📝 常见问题

### Q1: npm install 很慢怎么办？

```bash
# 使用国内镜像
npm install --registry=https://registry.npmmirror.com

# 或者永久设置
npm config set registry https://registry.npmmirror.com
```

### Q2: MySQL连接失败？

检查：
1. MySQL服务是否启动
2. 用户名密码是否正确
3. 数据库是否已创建
4. .env文件配置是否正确

```bash
# 重启MySQL
# Mac:
brew services restart mysql
# Windows:
net stop MySQL80
net start MySQL80
```

### Q3: Redis连接失败？

```bash
# 启动Redis
# Mac:
brew services start redis
# Windows:
redis-server

# 测试连接
redis-cli ping
```

### Q4: 端口3000被占用？

```bash
# Mac/Linux 查找占用端口的进程
lsof -i :3000
kill -9 PID

# Windows
netstat -ano | findstr :3000
taskkill /PID xxxx /F

# 或者修改.env中的PORT
PORT=3001
```

### Q5: TypeORM连接数据库报错？

确认数据库配置：
```bash
# 登录MySQL测试
mysql -u root -p -h localhost -P 3306

# 查看是否有ecommerce数据库
SHOW DATABASES;

# 确认字符集
SHOW CREATE DATABASE ecommerce;
```

---

## 📚 下一步

恭喜！🎉 后端已经成功启动。

### 接下来你可以：

1. ✅ **学习项目结构**
   - 查看 `src/modules/user` 了解模块结构
   - 查看 `src/common` 了解公共组件

2. ✅ **开发新功能**
   - 参考用户模块，创建商家模块
   - 创建商品模块
   - 创建订单模块

3. ✅ **查看API文档**
   - http://localhost:3000/api/docs
   - 测试所有接口

4. ✅ **开始前端开发**
   - 创建Vue3管理后台
   - 创建Uni-app用户端

---

## 🛠️ 开发工具推荐

### 必备工具
- **VS Code** - 代码编辑器
- **Postman** - API测试
- **Navicat** - 数据库管理
- **Another Redis Desktop Manager** - Redis管理

### VS Code插件
- ESLint
- Prettier
- GitLens
- Thunder Client（API测试）

### 浏览器插件
- JSON Viewer
- Vue DevTools

---

## 📖 学习资源

### 官方文档
- [NestJS中文文档](https://docs.nestjs.cn)
- [TypeORM文档](https://typeorm.io)
- [Vue3文档](https://cn.vuejs.org)

### 视频教程
- B站搜索"NestJS教程"
- B站搜索"电商项目实战"

---

## 💬 获取帮助

遇到问题？

1. 查看项目文档
   - PROJECT.md - 完整项目文档
   - RECOMMENDATIONS.md - 开发建议

2. 查看代码注释
   - 所有代码都有详细注释

3. 查看错误日志
   - 后端日志在终端
   - 数据库日志在MySQL日志文件

4. Google搜索错误信息

---

## ✅ 启动检查清单

在开始开发前，请确认：

- [ ] Node.js版本 >= 18
- [ ] MySQL已安装并运行
- [ ] Redis已安装并运行
- [ ] 数据库ecommerce已创建
- [ ] 数据库表已导入（15张表）
- [ ] 初始化数据已导入
- [ ] npm依赖已安装
- [ ] .env文件已配置
- [ ] 后端服务已启动
- [ ] API文档可以访问
- [ ] 用户注册接口测试通过

---

**准备好了吗？让我们开始开发吧！🚀**

如有问题，随时查看文档或提问。

---

最后更新：2025-10-04

