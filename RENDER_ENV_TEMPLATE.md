# Render环境变量配置模板

## 后端API环境变量

```
NODE_ENV=production
DB_TYPE=postgres
DB_HOST=[PostgreSQL Host from Render]
DB_PORT=5432
DB_USERNAME=[PostgreSQL Username from Render]
DB_PASSWORD=[PostgreSQL Password from Render]
DB_DATABASE=tiktokshop
JWT_SECRET=your-super-secret-jwt-key-change-this-in-production
PORT=10000
```

## 前端环境变量

### 商家后台
```
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
```

### 管理后台
```
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
```

### 用户前端
```
VITE_API_BASE_URL=https://tiktokshop-api.onrender.com/api
```

## 获取PostgreSQL连接信息

1. 在Render控制台选择你的PostgreSQL数据库
2. 点击"Connect"按钮
3. 复制以下信息：
   - Host
   - Port
   - Username
   - Password
   - Database

## 安全注意事项

1. **JWT_SECRET**: 使用强密码，至少32个字符
2. **数据库密码**: 使用强密码
3. **不要提交**: 这些环境变量到代码仓库
4. **定期更换**: 生产环境的密钥
