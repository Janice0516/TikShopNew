# Render PostgreSQL 数据库连接问题排查指南

## 🚨 当前问题
PostgreSQL数据库连接被"意外终止" - `Connection terminated unexpectedly`

## 🔍 问题诊断步骤

### 步骤1: 检查数据库服务状态
1. 进入Render控制台
2. 找到PostgreSQL数据库服务
3. 检查服务状态：
   - ✅ **Running** - 服务正常运行
   - ❌ **Starting** - 服务正在启动（等待）
   - ❌ **Failed** - 服务启动失败
   - ❌ **Stopped** - 服务已停止

### 步骤2: 检查数据库连接信息
确认环境变量设置正确：
```bash
DB_HOST=dpg-d0j8q8h2s78s73fq8hpg-a.oregon-postgres.render.com
DB_PORT=5432
DB_USERNAME=tiktokshop_slkz_user
DB_PASSWORD=U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn
DB_DATABASE=tiktokshop_slkz
```

### 步骤3: 测试数据库连接
在Render Shell中运行：
```bash
cd ecommerce-backend
npm run test:db
```

### 步骤4: 初始化数据库
如果数据库不存在，运行：
```bash
npm run init:db
```

## 🛠️ 常见问题解决方案

### 问题1: 数据库服务未启动
**症状**: `ECONNREFUSED` 错误
**解决**: 
1. 重启PostgreSQL服务
2. 等待服务完全启动（可能需要几分钟）

### 问题2: 数据库不存在
**症状**: `3D000` 错误
**解决**: 
1. 运行 `npm run init:db`
2. 或手动创建数据库

### 问题3: 认证失败
**症状**: `28P01` 错误
**解决**: 
1. 检查用户名和密码
2. 确认用户权限

### 问题4: SSL连接问题
**症状**: SSL相关错误
**解决**: 
1. 确认SSL配置正确
2. 检查证书设置

## 📋 手动数据库操作

### 使用Render Shell连接数据库
1. 进入PostgreSQL服务
2. 点击"Shell"按钮
3. 运行以下命令：

```sql
-- 检查数据库是否存在
SELECT datname FROM pg_database WHERE datname = 'tiktokshop_slkz';

-- 如果不存在，创建数据库
CREATE DATABASE tiktokshop_slkz;

-- 检查用户权限
SELECT usename FROM pg_user WHERE usename = 'tiktokshop_slkz_user';

-- 授予用户权限
GRANT ALL PRIVILEGES ON DATABASE tiktokshop_slkz TO tiktokshop_slkz_user;
```

## 🔧 临时解决方案

如果数据库问题持续存在，可以考虑：

### 方案1: 使用内存数据库
临时使用SQLite进行测试：
```typescript
// 在database.config.ts中添加SQLite配置
if (dbType === 'sqlite') {
  return {
    type: 'sqlite',
    database: ':memory:',
    entities: [...],
    synchronize: true,
  };
}
```

### 方案2: 重新创建数据库服务
1. 删除现有PostgreSQL服务
2. 创建新的PostgreSQL服务
3. 更新环境变量

## 📞 联系支持

如果问题仍然存在：
1. 查看Render服务状态页面
2. 联系Render技术支持
3. 提供详细的错误日志

## 🎯 预期结果

修复后应该看到：
- ✅ 数据库连接成功
- ✅ 应用完全启动
- ✅ API服务可访问
- ✅ 所有端点正常工作
