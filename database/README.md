# 数据库初始化说明

## 使用步骤

### 1. 创建数据库并导入表结构

```bash
# 方式一：命令行导入
mysql -u root -p < schema.sql

# 方式二：登录MySQL后执行
mysql -u root -p
source /path/to/schema.sql
```

### 2. 导入初始化数据

```bash
mysql -u root -p < init_data.sql
```

### 3. 验证数据

```bash
mysql -u root -p ecommerce

# 查看所有表
SHOW TABLES;

# 查看初始化数据
SELECT * FROM category;
SELECT * FROM platform_product;
SELECT * FROM admin;
```

## 默认账号

### 平台管理员
- 用户名: `admin`
- 密码: `admin123`

### 测试用户（用户端）
- 手机号: `13800138000`
- 密码: `123456`

### 测试商家（商家端）
- 用户名: `merchant001`
- 密码: `123456`

## 数据库表说明

| 表名 | 说明 | 记录数 |
|------|------|--------|
| user | 用户表 | 1（测试） |
| user_address | 用户地址 | 0 |
| merchant | 商家表 | 1（测试） |
| category | 商品分类 | 19 |
| platform_product | 平台商品库 | 10 |
| product_sku | 商品SKU | 0 |
| merchant_product | 商家商品 | 0 |
| cart | 购物车 | 0 |
| order_main | 订单主表 | 0 |
| order_item | 订单明细 | 0 |
| order_logistics | 订单物流 | 0 |
| after_sale | 售后申请 | 0 |
| fund_flow | 资金流水 | 0 |
| withdraw | 提现申请 | 0 |
| system_config | 系统配置 | 10 |
| admin | 管理员 | 1 |

## 注意事项

1. **密码加密**: 所有密码都是BCrypt加密，测试账号的密码已加密
2. **字符集**: 使用utf8mb4，支持emoji等特殊字符
3. **时间字段**: 自动维护create_time和update_time
4. **索引**: 已对常用查询字段添加索引

## 数据库备份

```bash
# 备份整个数据库
mysqldump -u root -p ecommerce > backup_$(date +%Y%m%d).sql

# 备份特定表
mysqldump -u root -p ecommerce user merchant order_main > backup_core_$(date +%Y%m%d).sql

# 恢复备份
mysql -u root -p ecommerce < backup_20251004.sql
```

## 修改密码

如果需要修改默认测试账号的密码，使用BCrypt在线工具生成新密码的hash值：
https://bcrypt-generator.com/

然后更新数据库：
```sql
UPDATE admin SET password = '新的bcrypt密码' WHERE username = 'admin';
```

