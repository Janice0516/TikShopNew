# Render PostgreSQL 数据库迁移指南

## 1. 创建PostgreSQL数据库

在Render控制台：
1. 点击 **"+ New"** → **"PostgreSQL"**
2. 选择 **"Free"** 计划
3. 数据库名称：`tiktokshop`
4. 记录连接信息

## 2. 设置环境变量

在API服务的Environment页面添加：

```bash
NODE_ENV=production
DB_TYPE=postgres
DB_HOST=[PostgreSQL Host from Render]
DB_PORT=5432
DB_USERNAME=[PostgreSQL Username from Render]
DB_PASSWORD=[PostgreSQL Password from Render]
DB_DATABASE=tiktokshop
JWT_SECRET=your-super-secret-jwt-key-here
PORT=10000
```

## 3. 数据库迁移

### 方法1：使用Render Shell
1. 在API服务页面点击 **"Shell"**
2. 运行以下命令：

```bash
# 进入项目目录
cd ecommerce-backend

# 运行数据库迁移
npm run migration:run
```

### 方法2：手动执行SQL
如果迁移命令不可用，可以手动执行SQL：

1. 在PostgreSQL服务页面点击 **"Connect"**
2. 使用提供的连接信息连接到数据库
3. 执行 `database/postgresql_migration.sql` 文件中的SQL语句

## 4. 验证部署

重新部署API服务后，检查日志确认：
- ✅ 数据库连接成功
- ✅ 应用启动成功
- ✅ API端点可访问

## 5. 常见问题

### ECONNREFUSED 错误
- 检查数据库服务是否正在运行
- 验证环境变量中的数据库连接信息
- 确保数据库服务在同一区域

### 权限错误
- 检查数据库用户名和密码
- 确保数据库用户有创建表的权限

### 连接超时
- 检查网络连接
- 尝试重启数据库服务
