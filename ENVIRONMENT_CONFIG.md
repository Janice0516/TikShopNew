# 环境配置说明

## 开发环境配置

在 `ecommerce-backend` 目录下创建 `.env` 文件：

```bash
# 开发环境配置
NODE_ENV=development
DB_TYPE=mysql
DB_HOST=localhost
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=ecommerce

# JWT配置
JWT_SECRET=your-development-jwt-secret-key

# Redis配置（可选）
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=
```

## 生产环境配置（Render）

在Render的环境变量中设置：

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

## 数据库类型说明

- **开发环境**: 默认使用MySQL（需要本地MySQL服务）
- **生产环境**: 使用PostgreSQL（Render提供的数据库）

## 切换数据库类型

如果需要强制使用特定数据库类型，设置 `DB_TYPE` 环境变量：
- `DB_TYPE=mysql` - 使用MySQL
- `DB_TYPE=postgres` - 使用PostgreSQL
