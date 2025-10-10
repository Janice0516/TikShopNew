# Ecommerce Backend

供货型电商平台后端服务

## 技术栈

- **框架**: NestJS 10.x
- **语言**: TypeScript 5.x
- **数据库**: MySQL 8.0 + TypeORM
- **缓存**: Redis 7.0
- **认证**: JWT
- **文档**: Swagger

## 快速开始

### 1. 安装依赖

```bash
npm install
```

### 2. 配置环境变量

```bash
cp .env.example .env
# 编辑.env文件，配置数据库等信息
```

### 3. 初始化数据库

```bash
# 创建数据库
mysql -u root -p
CREATE DATABASE ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# 导入表结构
mysql -u root -p ecommerce < ../database/schema.sql

# 导入初始化数据
mysql -u root -p ecommerce < ../database/init_data.sql
```

### 4. 启动服务

```bash
# 开发模式
npm run start:dev

# 生产模式
npm run build
npm run start:prod
```

### 5. 访问API文档

http://localhost:3000/api/docs

## 项目结构

```
src/
├── main.ts                 # 应用入口
├── app.module.ts           # 根模块
├── config/                 # 配置文件
│   ├── database.config.ts
│   ├── redis.config.ts
│   └── jwt.config.ts
├── common/                 # 公共模块
│   ├── decorators/        # 装饰器
│   ├── filters/           # 异常过滤器
│   ├── guards/            # 守卫
│   ├── interceptors/      # 拦截器
│   └── pipes/             # 管道
├── modules/               # 业务模块
│   ├── user/             # 用户模块
│   ├── auth/             # 认证模块
│   ├── merchant/         # 商家模块
│   ├── product/          # 商品模块
│   ├── cart/             # 购物车模块
│   ├── order/            # 订单模块
│   └── upload/           # 文件上传模块
└── shared/               # 共享模块
```

## 可用脚本

```bash
# 开发
npm run start:dev

# 构建
npm run build

# 生产运行
npm run start:prod

# 格式化代码
npm run format

# 代码检查
npm run lint

# 测试
npm run test

# 测试覆盖率
npm run test:cov
```

## 默认账号

### 平台管理员
- 用户名: admin
- 密码: admin123

### 测试用户
- 手机号: 13800138000
- 密码: 123456

### 测试商家
- 用户名: merchant001
- 密码: 123456

## 环境变量说明

查看 `.env.example` 文件了解所有配置项。

关键配置：
- `DB_*` - 数据库配置
- `REDIS_*` - Redis配置
- `JWT_SECRET` - JWT密钥（生产环境务必修改）
- `UPLOAD_PATH` - 文件上传路径

## API接口

所有接口返回格式：

```json
{
  "code": 200,
  "message": "success",
  "data": {},
  "timestamp": 1696435200000
}
```

状态码：
- 200 - 成功
- 400 - 参数错误
- 401 - 未授权
- 403 - 无权限
- 404 - 资源不存在
- 500 - 服务器错误

## 开发指南

### 创建新模块

```bash
# 使用NestJS CLI
nest g module modules/example
nest g controller modules/example
nest g service modules/example
```

### 创建实体

```typescript
import { Entity, Column, PrimaryGeneratedColumn } from 'typeorm';

@Entity('table_name')
export class Example {
  @PrimaryGeneratedColumn()
  id: number;

  @Column()
  name: string;
}
```

### 使用JWT认证

```typescript
import { UseGuards } from '@nestjs/common';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@UseGuards(JwtAuthGuard)
@Get('profile')
async getProfile(@Request() req) {
  return req.user;
}
```

## 部署

### PM2部署

```bash
# 安装PM2
npm install -g pm2

# 启动
pm2 start dist/main.js --name ecommerce-api

# 查看状态
pm2 status

# 查看日志
pm2 logs ecommerce-api

# 重启
pm2 restart ecommerce-api

# 停止
pm2 stop ecommerce-api
```

### Docker部署

```bash
# 构建镜像
docker build -t ecommerce-backend .

# 运行容器
docker run -d -p 3000:3000 --name ecommerce-api ecommerce-backend
```

## 测试

```bash
# 单元测试
npm run test

# e2e测试
npm run test:e2e

# 测试覆盖率
npm run test:cov
```

## License

MIT

