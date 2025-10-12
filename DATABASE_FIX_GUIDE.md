# 数据库修复指南

## 问题诊断
管理后台显示"No Data"和"服务器错误"的原因是：
- **数据库缺少关键表** - `merchant`、`merchant_withdrawal`、`merchant_recharge` 表不存在
- **所有商家相关API返回500错误** - "relation 'merchant' does not exist"

## 解决方案

### 方法1：通过Render数据库管理界面（推荐）

1. **登录Render Dashboard**
   - 访问 https://dashboard.render.com
   - 找到 `TikShop-db` 数据库服务

2. **打开数据库管理界面**
   - 点击数据库服务
   - 找到 "Connect" 或 "Database" 选项
   - 选择 "Connect External" 或 "Open Database"

3. **执行SQL脚本**
   - 复制 `create-database-tables.sql` 文件中的所有内容
   - 在SQL查询界面中粘贴并执行
   - 确认所有表创建成功

### 方法2：通过pgAdmin（如果可用）

1. **连接数据库**
   - 主机: `dpg-ctatjh5u0jms738shh30-a.oregon-postgres.render.com`
   - 端口: `5432`
   - 数据库: `tikshop`
   - 用户名: `tikshop_user`
   - 密码: `xNye4k92dtzXqa9rPkLRW04Au74ZK6Yv`

2. **执行SQL脚本**
   - 在pgAdmin中打开SQL查询工具
   - 粘贴并执行 `create-database-tables.sql` 内容

### 方法3：通过命令行（如果连接成功）

```bash
psql "postgresql://tikshop_user:xNye4k92dtzXqa9rPkLRW04Au74ZK6Yv@dpg-ctatjh5u0jms738shh30-a.oregon-postgres.render.com/tikshop" -f create-database-tables.sql
```

## 验证修复

执行SQL脚本后，测试以下API：

1. **商家登录**
   ```bash
   curl -X POST https://tiktokshop-api.onrender.com/api/merchant/login \
     -H "Content-Type: application/json" \
     -d '{"username":"merchant001","password":"password123"}'
   ```

2. **提现列表**
   ```bash
   curl https://tiktokshop-api.onrender.com/api/withdrawal/list
   ```

3. **管理后台**
   - 访问 https://tikshop-admin.onrender.com
   - 登录管理后台
   - 检查提现管理页面是否显示数据

## 预期结果

修复后应该看到：
- ✅ 商家登录成功
- ✅ 提现列表API返回数据
- ✅ 管理后台显示提现记录
- ✅ 不再有"服务器错误"和"No Data"

## 测试账号

创建了以下测试账号：
- **商家001**: `merchant001` / `password123`
- **商家002**: `merchant002` / `password123`  
- **商家003**: `merchant003` / `password123`

每个商家都有：
- 初始余额
- 提现记录
- 充值记录
