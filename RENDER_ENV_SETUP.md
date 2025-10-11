# Render Environment Variables Template

## 必需的环境变量

在Render的Environment页面添加以下变量：

### 1. 基础配置
```
NODE_ENV=production
PORT=10000
```

### 2. 数据库配置
```
DB_TYPE=postgres
DB_HOST=[PostgreSQL Host from Render]
DB_PORT=5432
DB_USERNAME=[PostgreSQL Username from Render]
DB_PASSWORD=[PostgreSQL Password from Render]
DB_DATABASE=tiktokshop
```

### 3. JWT配置
```
JWT_SECRET=your-super-secret-jwt-key-change-this-in-production
```

## 如何获取PostgreSQL连接信息

1. 在Render控制台创建PostgreSQL服务
2. 在PostgreSQL服务页面找到连接信息
3. 复制以下信息：
   - Host (类似: dpg-xxxxxxxxx-a.oregon-postgres.render.com)
   - Username (类似: tiktokshop_user)
   - Password (类似: abc123def456)
   - Database (类似: tiktokshop)

## 添加环境变量的步骤

1. 在API服务页面点击 "Environment"
2. 点击 "+ Add" 按钮
3. 输入 Key 和 Value
4. 点击 "Save Changes"
5. 等待重新部署

## 验证部署

部署成功后，检查日志确认：
- ✅ 数据库连接成功
- ✅ 应用启动成功
- ✅ API服务可访问
