# 供货型电商平台项目文档

## 📋 项目概述

### 项目简介
供货型B2B2C电商平台，平台提供统一的商品库和供应链，商家入驻后可一键上架商品，设置利润空间，平台负责供货、发货和售后支持。

### 项目定位
- **规模**: 小型轻量级电商平台
- **目标用户**: 中小商家、个人创业者
- **业务模式**: B2B2C供货型
- **技术定位**: 快速开发、低成本、易维护

### 核心优势
- 🚀 **快速上线**: 2-3个月MVP版本
- 💰 **低成本**: 单服务器启动，月成本 < ¥500
- 📦 **统一供货**: 降低商家库存风险
- 💼 **灵活定价**: 商家自主设置利润空间
- 🔧 **易维护**: 单体架构，技术栈统一

---

## 🎯 业务流程

### 1. 角色定义

#### 用户端（买家）
- 浏览商品、搜索筛选
- 购物车管理
- 下单支付
- 订单跟踪
- 售后申请

#### 商家端（卖家）
- 注册入驻、资质审核
- 从平台商品库选品上架
- 设置销售价格（成本价 + 利润）
- 订单管理
- 财务管理、提现

#### 平台端（运营方）
- 商品库管理（对接供应商）
- 商家审核管理
- 订单监控
- 财务结算
- 售后处理

#### 供应链端
- 供货入库
- 订单发货
- 物流跟踪

### 2. 核心业务流程

```mermaid
graph LR
    A[用户浏览商品] --> B[加入购物车]
    B --> C[提交订单]
    C --> D[在线支付]
    D --> E[平台接收订单]
    E --> F[通知供应商发货]
    F --> G[物流配送]
    G --> H[用户确认收货]
    H --> I[商家收益结算]
```

### 3. 资金流转

```
用户支付 → 平台收款
  ├─ 成本价 → 供应商
  ├─ 利润 → 商家账户
  └─ 平台抽成（可选）
  
商家账户余额 → 提现申请 → 平台审核 → 打款到银行卡
```

---

## 🏗️ 技术架构

### 技术选型

#### 后端技术栈
```yaml
框架: NestJS 10.x (Node.js)
语言: TypeScript 5.x
数据库: MySQL 8.0
缓存: Redis 7.0
ORM: TypeORM
认证: JWT + Passport.js
API文档: Swagger
定时任务: @nestjs/schedule
文件上传: Multer
参数验证: class-validator
日志: Winston
```

**选择理由**：
- ✅ 开发效率高，2-3个月快速上线
- ✅ TypeScript类型安全，减少运行时错误
- ✅ NestJS架构清晰，适合团队协作
- ✅ 生态丰富，第三方库完善
- ✅ 性能满足中小规模（日订单 < 5000）

#### 前端技术栈

**用户端（H5 + 小程序）**
```yaml
框架: Uni-app (Vue3)
UI组件: uView UI 3.x
状态管理: Pinia
HTTP: uni.request
构建: HBuilderX / Vite
```

**管理后台（商家端 + 平台端）**
```yaml
框架: Vue 3.3+
语言: TypeScript
构建工具: Vite 5.x
UI组件: Element Plus 2.x
状态管理: Pinia
路由: Vue Router 4.x
HTTP客户端: Axios
图表: ECharts
```

#### 数据库设计
```yaml
主数据库: MySQL 8.0
  - 用户数据
  - 商品数据
  - 订单数据
  - 财务数据

缓存: Redis 7.0
  - Token存储
  - 热点数据缓存
  - 分布式锁
  - 消息队列（简单任务）

文件存储:
  - 开发: 本地存储
  - 生产: 阿里云OSS / 腾讯云COS
```

#### 第三方服务
```yaml
支付:
  - 微信支付（扫码、H5、小程序）
  - 支付宝支付（扫码、H5）

物流:
  - 快递鸟API（物流查询）

通信:
  - 阿里云短信（验证码、通知）
  - 邮件通知（可选）

存储:
  - 阿里云OSS（图片、视频）
```

### 系统架构图

```
┌─────────────────────────────────────────────┐
│         Nginx (反向代理 + 负载均衡)           │
│         + SSL证书 + 静态资源                  │
└─────────────┬───────────────────────────────┘
              │
    ┌─────────┼─────────┐
    │                   │
┌───▼────┐         ┌───▼────┐
│ 用户端  │         │ 管理端  │
│ H5/小程序│         │ 后台    │
└────────┘         └────────┘
    │                   │
    └─────────┬─────────┘
              │
    ┌─────────▼─────────┐
    │   NestJS 后端服务  │
    │  (单体应用部署)     │
    └─────────┬─────────┘
              │
    ┌─────────┼─────────┐
    │         │         │
┌───▼───┐ ┌──▼──┐ ┌───▼────┐
│ MySQL │ │Redis│ │ 文件存储│
└───────┘ └─────┘ └────────┘
```

---

## 📊 数据库设计

### ER关系图

```
用户 1:N 订单
用户 1:N 地址
用户 1:N 购物车

商家 1:N 商家商品
商家 1:N 订单
商家 1:1 账户

平台商品 1:N 商家商品
平台商品 1:N SKU
平台商品 N:1 分类

订单 1:N 订单明细
订单 1:1 物流信息
订单 1:N 售后申请
```

### 核心数据表（15张）

#### 1. 用户模块

**用户表 (user)**
```sql
CREATE TABLE `user` (
  `id` BIGINT PRIMARY KEY AUTO_INCREMENT COMMENT '用户ID',
  `phone` VARCHAR(11) UNIQUE NOT NULL COMMENT '手机号',
  `password` VARCHAR(100) NOT NULL COMMENT '密码（BCrypt加密）',
  `nickname` VARCHAR(50) DEFAULT NULL COMMENT '昵称',
  `avatar` VARCHAR(255) DEFAULT NULL COMMENT '头像URL',
  `gender` TINYINT DEFAULT 0 COMMENT '性别 0未知 1男 2女',
  `status` TINYINT DEFAULT 1 COMMENT '状态 1正常 0禁用',
  `last_login_time` DATETIME DEFAULT NULL COMMENT '最后登录时间',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_phone` (`phone`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';
```

**用户地址表 (user_address)**
```sql
CREATE TABLE `user_address` (
  `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
  `user_id` BIGINT NOT NULL COMMENT '用户ID',
  `receiver_name` VARCHAR(50) NOT NULL COMMENT '收货人姓名',
  `phone` VARCHAR(11) NOT NULL COMMENT '收货电话',
  `province` VARCHAR(50) NOT NULL COMMENT '省份',
  `city` VARCHAR(50) NOT NULL COMMENT '城市',
  `district` VARCHAR(50) NOT NULL COMMENT '区/县',
  `detail_address` VARCHAR(255) NOT NULL COMMENT '详细地址',
  `is_default` TINYINT DEFAULT 0 COMMENT '是否默认 1是 0否',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户地址表';
```

#### 2. 商家模块

**商家表 (merchant)**
```sql
CREATE TABLE `merchant` (
  `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
  `username` VARCHAR(50) UNIQUE NOT NULL COMMENT '登录账号',
  `password` VARCHAR(100) NOT NULL COMMENT '密码',
  `merchant_name` VARCHAR(100) NOT NULL COMMENT '商家名称',
  `contact_name` VARCHAR(50) COMMENT '联系人',
  `contact_phone` VARCHAR(11) COMMENT '联系电话',
  `business_license` VARCHAR(255) COMMENT '营业执照图片URL',
  `id_card_front` VARCHAR(255) COMMENT '身份证正面',
  `id_card_back` VARCHAR(255) COMMENT '身份证反面',
  `status` TINYINT DEFAULT 0 COMMENT '状态 0待审核 1已通过 2已拒绝 3已禁用',
  `reject_reason` VARCHAR(255) COMMENT '拒绝原因',
  `shop_name` VARCHAR(100) COMMENT '店铺名称',
  `shop_logo` VARCHAR(255) COMMENT '店铺Logo',
  `shop_banner` TEXT COMMENT '店铺Banner（JSON数组）',
  `shop_description` VARCHAR(500) COMMENT '店铺简介',
  `balance` DECIMAL(10,2) DEFAULT 0.00 COMMENT '账户余额',
  `frozen_amount` DECIMAL(10,2) DEFAULT 0.00 COMMENT '冻结金额',
  `total_income` DECIMAL(10,2) DEFAULT 0.00 COMMENT '累计收入',
  `total_withdraw` DECIMAL(10,2) DEFAULT 0.00 COMMENT '累计提现',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_username` (`username`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商家表';
```

#### 3. 商品模块

**商品分类表 (category)**
```sql
CREATE TABLE `category` (
  `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
  `parent_id` BIGINT DEFAULT 0 COMMENT '父级ID，0为顶级分类',
  `name` VARCHAR(50) NOT NULL COMMENT '分类名称',
  `level` TINYINT DEFAULT 1 COMMENT '层级 1一级 2二级 3三级',
  `sort` INT DEFAULT 0 COMMENT '排序',
  `icon` VARCHAR(255) COMMENT '分类图标',
  `status` TINYINT DEFAULT 1 COMMENT '状态 1启用 0禁用',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_parent_id` (`parent_id`),
  INDEX `idx_status_sort` (`status`, `sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品分类表';
```

**平台商品库表 (platform_product)**
```sql
CREATE TABLE `platform_product` (
  `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
  `product_no` VARCHAR(50) UNIQUE COMMENT '商品编号',
  `name` VARCHAR(200) NOT NULL COMMENT '商品名称',
  `category_id` BIGINT NOT NULL COMMENT '分类ID',
  `brand` VARCHAR(100) COMMENT '品牌',
  `main_image` VARCHAR(255) NOT NULL COMMENT '主图',
  `images` TEXT COMMENT '轮播图（JSON数组）',
  `video` VARCHAR(255) COMMENT '商品视频',
  `cost_price` DECIMAL(10,2) NOT NULL COMMENT '平台成本价',
  `suggest_price` DECIMAL(10,2) COMMENT '建议售价',
  `stock` INT DEFAULT 0 COMMENT '总库存',
  `sales` INT DEFAULT 0 COMMENT '总销量',
  `description` TEXT COMMENT '商品详情（富文本）',
  `status` TINYINT DEFAULT 1 COMMENT '状态 1上架 0下架',
  `sort` INT DEFAULT 0 COMMENT '排序',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_category_id` (`category_id`),
  INDEX `idx_status_sort` (`status`, `sort`),
  INDEX `idx_product_no` (`product_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='平台商品库表';
```

**商品SKU表 (product_sku)**
```sql
CREATE TABLE `product_sku` (
  `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
  `product_id` BIGINT NOT NULL COMMENT '商品ID',
  `sku_no` VARCHAR(50) UNIQUE COMMENT 'SKU编号',
  `sku_name` VARCHAR(100) COMMENT '规格名称（如：红色-XL）',
  `specs` VARCHAR(255) COMMENT '规格属性（JSON：{"颜色":"红色","尺寸":"XL"}）',
  `cost_price` DECIMAL(10,2) NOT NULL COMMENT '成本价',
  `stock` INT DEFAULT 0 COMMENT '库存',
  `sku_image` VARCHAR(255) COMMENT 'SKU图片',
  `status` TINYINT DEFAULT 1 COMMENT '状态 1启用 0禁用',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_product_id` (`product_id`),
  INDEX `idx_sku_no` (`sku_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品SKU表';
```

**商家商品表 (merchant_product)**
```sql
CREATE TABLE `merchant_product` (
  `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
  `merchant_id` BIGINT NOT NULL COMMENT '商家ID',
  `platform_product_id` BIGINT NOT NULL COMMENT '平台商品ID',
  `sale_price` DECIMAL(10,2) NOT NULL COMMENT '销售价格',
  `profit_margin` DECIMAL(10,2) NOT NULL COMMENT '利润空间（加价金额）',
  `profit_rate` DECIMAL(5,2) COMMENT '利润率（%）',
  `stock` INT DEFAULT 0 COMMENT '可售库存（关联平台库存）',
  `sales` INT DEFAULT 0 COMMENT '销量',
  `status` TINYINT DEFAULT 1 COMMENT '状态 1上架 0下架',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_merchant_id` (`merchant_id`),
  INDEX `idx_platform_product_id` (`platform_product_id`),
  UNIQUE KEY `uk_merchant_product` (`merchant_id`, `platform_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商家商品表';
```

#### 4. 购物车模块

**购物车表 (cart)**
```sql
CREATE TABLE `cart` (
  `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
  `user_id` BIGINT NOT NULL COMMENT '用户ID',
  `merchant_id` BIGINT NOT NULL COMMENT '商家ID',
  `product_id` BIGINT NOT NULL COMMENT '商品ID（平台商品）',
  `sku_id` BIGINT COMMENT 'SKU ID',
  `quantity` INT DEFAULT 1 COMMENT '数量',
  `selected` TINYINT DEFAULT 1 COMMENT '是否选中 1是 0否',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_user_id` (`user_id`),
  UNIQUE KEY `uk_user_product_sku` (`user_id`, `product_id`, `sku_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='购物车表';
```

#### 5. 订单模块

**订单主表 (order_main)**
```sql
CREATE TABLE `order_main` (
  `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
  `order_no` VARCHAR(32) UNIQUE NOT NULL COMMENT '订单号',
  `user_id` BIGINT NOT NULL COMMENT '用户ID',
  `merchant_id` BIGINT NOT NULL COMMENT '商家ID',
  `total_amount` DECIMAL(10,2) NOT NULL COMMENT '订单总额',
  `cost_amount` DECIMAL(10,2) NOT NULL COMMENT '成本金额',
  `merchant_profit` DECIMAL(10,2) NOT NULL COMMENT '商家利润',
  `platform_profit` DECIMAL(10,2) DEFAULT 0.00 COMMENT '平台利润',
  `freight` DECIMAL(10,2) DEFAULT 0.00 COMMENT '运费',
  `discount_amount` DECIMAL(10,2) DEFAULT 0.00 COMMENT '优惠金额',
  `pay_amount` DECIMAL(10,2) NOT NULL COMMENT '实付金额',
  
  `receiver_name` VARCHAR(50) NOT NULL COMMENT '收货人',
  `receiver_phone` VARCHAR(11) NOT NULL COMMENT '收货电话',
  `receiver_province` VARCHAR(50) NOT NULL,
  `receiver_city` VARCHAR(50) NOT NULL,
  `receiver_district` VARCHAR(50) NOT NULL,
  `receiver_address` VARCHAR(255) NOT NULL COMMENT '详细地址',
  
  `order_status` TINYINT DEFAULT 1 COMMENT '订单状态 1待付款 2待发货 3待收货 4已完成 5已取消 6售后中',
  `pay_status` TINYINT DEFAULT 0 COMMENT '支付状态 0未支付 1已支付 2已退款',
  `pay_type` TINYINT COMMENT '支付方式 1微信 2支付宝',
  `pay_time` DATETIME COMMENT '支付时间',
  `transaction_id` VARCHAR(100) COMMENT '第三方支付交易号',
  
  `ship_time` DATETIME COMMENT '发货时间',
  `receive_time` DATETIME COMMENT '收货时间',
  `finish_time` DATETIME COMMENT '完成时间',
  `cancel_time` DATETIME COMMENT '取消时间',
  `cancel_reason` VARCHAR(255) COMMENT '取消原因',
  
  `buyer_message` VARCHAR(255) COMMENT '买家留言',
  `remark` VARCHAR(500) COMMENT '备注',
  
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  INDEX `idx_order_no` (`order_no`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_merchant_id` (`merchant_id`),
  INDEX `idx_order_status` (`order_status`),
  INDEX `idx_pay_status` (`pay_status`),
  INDEX `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单主表';
```

**订单明细表 (order_item)**
```sql
CREATE TABLE `order_item` (
  `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
  `order_id` BIGINT NOT NULL COMMENT '订单ID',
  `product_id` BIGINT NOT NULL COMMENT '商品ID',
  `sku_id` BIGINT COMMENT 'SKU ID',
  `product_name` VARCHAR(200) NOT NULL COMMENT '商品名称',
  `product_image` VARCHAR(255) NOT NULL COMMENT '商品图片',
  `sku_name` VARCHAR(100) COMMENT '规格名称',
  `quantity` INT NOT NULL COMMENT '数量',
  `cost_price` DECIMAL(10,2) NOT NULL COMMENT '成本价',
  `sale_price` DECIMAL(10,2) NOT NULL COMMENT '销售价',
  `total_price` DECIMAL(10,2) NOT NULL COMMENT '小计',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单明细表';
```

**订单物流表 (order_logistics)**
```sql
CREATE TABLE `order_logistics` (
  `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
  `order_id` BIGINT NOT NULL COMMENT '订单ID',
  `logistics_company` VARCHAR(50) COMMENT '物流公司',
  `logistics_code` VARCHAR(50) COMMENT '物流公司编码',
  `tracking_number` VARCHAR(50) COMMENT '快递单号',
  `ship_time` DATETIME COMMENT '发货时间',
  `receive_time` DATETIME COMMENT '收货时间',
  `logistics_status` TINYINT DEFAULT 0 COMMENT '物流状态 0未发货 1运输中 2派送中 3已签收',
  `logistics_info` TEXT COMMENT '物流信息（JSON）',
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_order_id` (`order_id`),
  INDEX `idx_tracking_number` (`tracking_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单物流表';
```

#### 6. 售后模块

**售后申请表 (after_sale)**
```sql
CREATE TABLE `after_sale` (
  `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
  `after_sale_no` VARCHAR(32) UNIQUE COMMENT '售后单号',
  `order_id` BIGINT NOT NULL COMMENT '订单ID',
  `order_no` VARCHAR(32) NOT NULL COMMENT '订单号',
  `user_id` BIGINT NOT NULL COMMENT '用户ID',
  `merchant_id` BIGINT NOT NULL COMMENT '商家ID',
  `type` TINYINT NOT NULL COMMENT '类型 1仅退款 2退货退款 3换货',
  `reason` VARCHAR(255) NOT NULL COMMENT '退款原因',
  `description` VARCHAR(500) COMMENT '问题描述',
  `images` TEXT COMMENT '凭证图片（JSON数组）',
  `refund_amount` DECIMAL(10,2) NOT NULL COMMENT '退款金额',
  `status` TINYINT DEFAULT 0 COMMENT '状态 0待审核 1已同意 2已拒绝 3退款中 4已完成',
  `reject_reason` VARCHAR(255) COMMENT '拒绝原因',
  `return_logistics_company` VARCHAR(50) COMMENT '退货物流公司',
  `return_tracking_number` VARCHAR(50) COMMENT '退货快递单号',
  `refund_time` DATETIME COMMENT '退款时间',
  `remark` VARCHAR(500) COMMENT '备注',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_after_sale_no` (`after_sale_no`),
  INDEX `idx_order_id` (`order_id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_merchant_id` (`merchant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='售后申请表';
```

#### 7. 财务模块

**资金流水表 (fund_flow)**
```sql
CREATE TABLE `fund_flow` (
  `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
  `flow_no` VARCHAR(32) UNIQUE COMMENT '流水号',
  `merchant_id` BIGINT NOT NULL COMMENT '商家ID',
  `order_id` BIGINT COMMENT '关联订单ID',
  `type` TINYINT NOT NULL COMMENT '类型 1订单收入 2提现支出 3退款支出 4冻结 5解冻',
  `amount` DECIMAL(10,2) NOT NULL COMMENT '金额',
  `balance_before` DECIMAL(10,2) NOT NULL COMMENT '变动前余额',
  `balance_after` DECIMAL(10,2) NOT NULL COMMENT '变动后余额',
  `description` VARCHAR(255) COMMENT '说明',
  `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_merchant_id` (`merchant_id`),
  INDEX `idx_order_id` (`order_id`),
  INDEX `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='资金流水表';
```

**提现申请表 (withdraw)**
```sql
CREATE TABLE `withdraw` (
  `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
  `withdraw_no` VARCHAR(32) UNIQUE COMMENT '提现单号',
  `merchant_id` BIGINT NOT NULL COMMENT '商家ID',
  `amount` DECIMAL(10,2) NOT NULL COMMENT '提现金额',
  `bank_name` VARCHAR(50) NOT NULL COMMENT '银行名称',
  `bank_account` VARCHAR(50) NOT NULL COMMENT '银行账号',
  `account_name` VARCHAR(50) NOT NULL COMMENT '开户姓名',
  `status` TINYINT DEFAULT 0 COMMENT '状态 0待审核 1已通过 2已拒绝 3已打款',
  `reject_reason` VARCHAR(255) COMMENT '拒绝原因',
  `fee` DECIMAL(10,2) DEFAULT 0.00 COMMENT '手续费',
  `actual_amount` DECIMAL(10,2) COMMENT '实际到账金额',
  `apply_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '申请时间',
  `audit_time` DATETIME COMMENT '审核时间',
  `transfer_time` DATETIME COMMENT '打款时间',
  `remark` VARCHAR(500) COMMENT '备注',
  INDEX `idx_merchant_id` (`merchant_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='提现申请表';
```

#### 8. 系统配置

**系统配置表 (system_config)**
```sql
CREATE TABLE `system_config` (
  `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
  `config_key` VARCHAR(50) UNIQUE NOT NULL COMMENT '配置键',
  `config_value` TEXT NOT NULL COMMENT '配置值',
  `config_type` VARCHAR(20) DEFAULT 'string' COMMENT '类型 string/number/json/boolean',
  `description` VARCHAR(255) COMMENT '说明',
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统配置表';
```

---

## 🔌 API设计

### API规范

#### 请求格式
```
BASE_URL: https://api.yourdomain.com
Content-Type: application/json
Authorization: Bearer {token}
```

#### 响应格式
```typescript
// 成功响应
{
  "code": 200,
  "message": "success",
  "data": {
    // 业务数据
  },
  "timestamp": 1696435200000
}

// 失败响应
{
  "code": 400,
  "message": "参数错误",
  "data": null,
  "timestamp": 1696435200000
}

// 分页响应
{
  "code": 200,
  "message": "success",
  "data": {
    "list": [],
    "total": 100,
    "page": 1,
    "pageSize": 10
  }
}
```

#### 状态码
```
200 - 成功
400 - 参数错误
401 - 未授权
403 - 无权限
404 - 资源不存在
500 - 服务器错误
```

### 核心API接口

#### 1. 用户端API

**用户认证**
```
POST /api/user/register          # 注册
POST /api/user/login             # 登录
POST /api/user/logout            # 登出
POST /api/user/send-code         # 发送验证码
GET  /api/user/profile           # 获取个人信息
PUT  /api/user/profile           # 更新个人信息
```

**商品相关**
```
GET  /api/products               # 商品列表（分页、搜索、筛选）
GET  /api/products/:id           # 商品详情
GET  /api/categories             # 商品分类
GET  /api/products/recommend     # 推荐商品
```

**购物车**
```
GET    /api/cart                 # 购物车列表
POST   /api/cart                 # 添加到购物车
PUT    /api/cart/:id             # 更新购物车
DELETE /api/cart/:id             # 删除购物车项
DELETE /api/cart/batch           # 批量删除
```

**订单**
```
POST   /api/orders               # 创建订单
GET    /api/orders               # 订单列表
GET    /api/orders/:id           # 订单详情
PUT    /api/orders/:id/cancel    # 取消订单
PUT    /api/orders/:id/confirm   # 确认收货
GET    /api/orders/:id/logistics # 物流信息
```

**地址**
```
GET    /api/addresses            # 地址列表
POST   /api/addresses            # 添加地址
PUT    /api/addresses/:id        # 更新地址
DELETE /api/addresses/:id        # 删除地址
PUT    /api/addresses/:id/default # 设为默认
```

**售后**
```
POST   /api/after-sales          # 申请售后
GET    /api/after-sales          # 售后列表
GET    /api/after-sales/:id      # 售后详情
PUT    /api/after-sales/:id/cancel # 取消售后
```

#### 2. 商家端API

**商家认证**
```
POST /api/merchant/register      # 商家入驻
POST /api/merchant/login         # 商家登录
GET  /api/merchant/profile       # 商家信息
PUT  /api/merchant/profile       # 更新信息
PUT  /api/merchant/shop          # 更新店铺信息
```

**商品管理**
```
GET  /api/merchant/platform-products      # 平台商品库
POST /api/merchant/products               # 上架商品
GET  /api/merchant/products               # 我的商品
PUT  /api/merchant/products/:id           # 更新商品
PUT  /api/merchant/products/:id/price     # 修改价格
PUT  /api/merchant/products/:id/status    # 上下架
DELETE /api/merchant/products/:id         # 删除商品
```

**订单管理**
```
GET  /api/merchant/orders                 # 订单列表
GET  /api/merchant/orders/:id             # 订单详情
PUT  /api/merchant/orders/:id/ship        # 发货
GET  /api/merchant/orders/statistics      # 订单统计
```

**财务管理**
```
GET  /api/merchant/finance/overview       # 财务概览
GET  /api/merchant/finance/flow           # 资金流水
POST /api/merchant/finance/withdraw       # 申请提现
GET  /api/merchant/finance/withdraw       # 提现记录
```

**售后管理**
```
GET  /api/merchant/after-sales            # 售后列表
GET  /api/merchant/after-sales/:id        # 售后详情
PUT  /api/merchant/after-sales/:id/agree  # 同意售后
PUT  /api/merchant/after-sales/:id/reject # 拒绝售后
```

**数据统计**
```
GET  /api/merchant/dashboard              # 数据面板
GET  /api/merchant/statistics/sales       # 销售统计
GET  /api/merchant/statistics/products    # 商品统计
```

#### 3. 平台端API

**商家管理**
```
GET  /api/admin/merchants                 # 商家列表
GET  /api/admin/merchants/:id             # 商家详情
PUT  /api/admin/merchants/:id/audit       # 审核商家
PUT  /api/admin/merchants/:id/status      # 启用/禁用
```

**商品库管理**
```
POST /api/admin/products                  # 添加商品
GET  /api/admin/products                  # 商品列表
GET  /api/admin/products/:id              # 商品详情
PUT  /api/admin/products/:id              # 更新商品
DELETE /api/admin/products/:id            # 删除商品
PUT  /api/admin/products/:id/status       # 上下架
```

**分类管理**
```
POST /api/admin/categories                # 添加分类
GET  /api/admin/categories                # 分类列表
PUT  /api/admin/categories/:id            # 更新分类
DELETE /api/admin/categories/:id          # 删除分类
```

**订单管理**
```
GET  /api/admin/orders                    # 订单列表
GET  /api/admin/orders/:id                # 订单详情
GET  /api/admin/orders/statistics         # 订单统计
```

**财务管理**
```
GET  /api/admin/finance/overview          # 财务概览
GET  /api/admin/finance/settlement        # 结算统计
GET  /api/admin/withdraw                  # 提现申请列表
PUT  /api/admin/withdraw/:id/audit        # 审核提现
```

**售后管理**
```
GET  /api/admin/after-sales               # 售后列表
GET  /api/admin/after-sales/:id           # 售后详情
PUT  /api/admin/after-sales/:id/handle    # 处理售后
```

**系统配置**
```
GET  /api/admin/config                    # 配置列表
PUT  /api/admin/config                    # 更新配置
```

**数据统计**
```
GET  /api/admin/dashboard                 # 数据大盘
GET  /api/admin/statistics/sales          # 销售统计
GET  /api/admin/statistics/merchants      # 商家统计
GET  /api/admin/statistics/users          # 用户统计
```

#### 4. 公共API

**支付相关**
```
POST /api/payment/create                  # 创建支付
POST /api/payment/notify                  # 支付回调
GET  /api/payment/query/:orderNo          # 查询支付状态
```

**文件上传**
```
POST /api/upload/image                    # 上传图片
POST /api/upload/video                    # 上传视频
POST /api/upload/file                     # 上传文件
```

**物流相关**
```
GET  /api/logistics/query                 # 查询物流
GET  /api/logistics/companies             # 物流公司列表
```

---

## 📁 项目目录结构

### 后端项目结构 (NestJS)

```
ecommerce-backend/
├── src/
│   ├── main.ts                           # 应用入口
│   ├── app.module.ts                     # 根模块
│   │
│   ├── modules/                          # 业务模块
│   │   ├── user/                        # 用户模块
│   │   │   ├── user.controller.ts
│   │   │   ├── user.service.ts
│   │   │   ├── user.module.ts
│   │   │   ├── entities/
│   │   │   │   └── user.entity.ts
│   │   │   └── dto/
│   │   │       ├── register.dto.ts
│   │   │       ├── login.dto.ts
│   │   │       └── update-profile.dto.ts
│   │   │
│   │   ├── merchant/                    # 商家模块
│   │   │   ├── merchant.controller.ts
│   │   │   ├── merchant.service.ts
│   │   │   ├── merchant.module.ts
│   │   │   ├── entities/
│   │   │   │   └── merchant.entity.ts
│   │   │   └── dto/
│   │   │
│   │   ├── product/                     # 商品模块
│   │   │   ├── product.controller.ts
│   │   │   ├── product.service.ts
│   │   │   ├── product.module.ts
│   │   │   ├── entities/
│   │   │   │   ├── product.entity.ts
│   │   │   │   ├── sku.entity.ts
│   │   │   │   └── category.entity.ts
│   │   │   └── dto/
│   │   │
│   │   ├── cart/                        # 购物车模块
│   │   │   ├── cart.controller.ts
│   │   │   ├── cart.service.ts
│   │   │   ├── cart.module.ts
│   │   │   ├── entities/
│   │   │   └── dto/
│   │   │
│   │   ├── order/                       # 订单模块
│   │   │   ├── order.controller.ts
│   │   │   ├── order.service.ts
│   │   │   ├── order.module.ts
│   │   │   ├── entities/
│   │   │   │   ├── order.entity.ts
│   │   │   │   ├── order-item.entity.ts
│   │   │   │   └── logistics.entity.ts
│   │   │   └── dto/
│   │   │
│   │   ├── payment/                     # 支付模块
│   │   │   ├── payment.controller.ts
│   │   │   ├── payment.service.ts
│   │   │   ├── payment.module.ts
│   │   │   ├── wechat-pay.service.ts
│   │   │   └── alipay.service.ts
│   │   │
│   │   ├── after-sale/                  # 售后模块
│   │   │   ├── after-sale.controller.ts
│   │   │   ├── after-sale.service.ts
│   │   │   ├── after-sale.module.ts
│   │   │   ├── entities/
│   │   │   └── dto/
│   │   │
│   │   ├── finance/                     # 财务模块
│   │   │   ├── finance.controller.ts
│   │   │   ├── finance.service.ts
│   │   │   ├── finance.module.ts
│   │   │   ├── entities/
│   │   │   │   ├── fund-flow.entity.ts
│   │   │   │   └── withdraw.entity.ts
│   │   │   └── dto/
│   │   │
│   │   ├── admin/                       # 平台管理模块
│   │   │   ├── admin.controller.ts
│   │   │   ├── admin.service.ts
│   │   │   ├── admin.module.ts
│   │   │   └── dto/
│   │   │
│   │   └── upload/                      # 文件上传模块
│   │       ├── upload.controller.ts
│   │       ├── upload.service.ts
│   │       └── upload.module.ts
│   │
│   ├── common/                          # 公共模块
│   │   ├── decorators/                 # 装饰器
│   │   │   ├── auth.decorator.ts       # 认证装饰器
│   │   │   ├── roles.decorator.ts      # 角色装饰器
│   │   │   └── user.decorator.ts       # 用户装饰器
│   │   │
│   │   ├── filters/                    # 异常过滤器
│   │   │   ├── http-exception.filter.ts
│   │   │   └── all-exception.filter.ts
│   │   │
│   │   ├── guards/                     # 守卫
│   │   │   ├── auth.guard.ts           # 认证守卫
│   │   │   └── roles.guard.ts          # 角色守卫
│   │   │
│   │   ├── interceptors/               # 拦截器
│   │   │   ├── transform.interceptor.ts # 响应转换
│   │   │   └── logging.interceptor.ts   # 日志拦截
│   │   │
│   │   ├── pipes/                      # 管道
│   │   │   └── validation.pipe.ts      # 参数验证
│   │   │
│   │   ├── utils/                      # 工具函数
│   │   │   ├── crypto.util.ts          # 加密工具
│   │   │   ├── date.util.ts            # 日期工具
│   │   │   └── number.util.ts          # 数字工具
│   │   │
│   │   └── constants/                  # 常量
│   │       ├── error-code.ts           # 错误码
│   │       └── business.ts             # 业务常量
│   │
│   ├── config/                          # 配置
│   │   ├── database.config.ts          # 数据库配置
│   │   ├── redis.config.ts             # Redis配置
│   │   ├── jwt.config.ts               # JWT配置
│   │   ├── upload.config.ts            # 上传配置
│   │   └── payment.config.ts           # 支付配置
│   │
│   └── shared/                          # 共享模块
│       ├── redis/                      # Redis模块
│       │   ├── redis.module.ts
│       │   └── redis.service.ts
│       │
│       ├── database/                   # 数据库模块
│       │   └── database.module.ts
│       │
│       └── logger/                     # 日志模块
│           ├── logger.module.ts
│           └── logger.service.ts
│
├── test/                                # 测试文件
├── uploads/                             # 上传文件目录（开发环境）
├── logs/                                # 日志目录
│
├── .env                                 # 环境变量
├── .env.example                         # 环境变量示例
├── .gitignore
├── package.json
├── tsconfig.json
├── nest-cli.json
└── README.md
```

### 前端项目结构

#### 用户端 (Uni-app)

```
user-app/
├── pages/                               # 页面
│   ├── index/                          # 首页
│   │   └── index.vue
│   ├── category/                       # 分类
│   │   └── category.vue
│   ├── cart/                           # 购物车
│   │   └── cart.vue
│   ├── product/                        # 商品详情
│   │   └── detail.vue
│   ├── order/                          # 订单
│   │   ├── confirm.vue                # 确认订单
│   │   ├── list.vue                   # 订单列表
│   │   └── detail.vue                 # 订单详情
│   ├── user/                           # 个人中心
│   │   ├── index.vue                  # 个人中心首页
│   │   ├── address.vue                # 地址管理
│   │   └── profile.vue                # 个人信息
│   └── login/                          # 登录注册
│       └── login.vue
│
├── components/                          # 组件
│   ├── product-card/                   # 商品卡片
│   ├── order-card/                     # 订单卡片
│   └── empty/                          # 空状态
│
├── api/                                 # API接口
│   ├── user.ts                         # 用户接口
│   ├── product.ts                      # 商品接口
│   ├── cart.ts                         # 购物车接口
│   ├── order.ts                        # 订单接口
│   └── request.ts                      # 请求封装
│
├── store/                               # 状态管理
│   ├── index.ts
│   ├── user.ts                         # 用户状态
│   └── cart.ts                         # 购物车状态
│
├── utils/                               # 工具函数
│   ├── auth.ts                         # 认证工具
│   ├── format.ts                       # 格式化工具
│   └── validate.ts                     # 验证工具
│
├── static/                              # 静态资源
│   ├── images/
│   └── icons/
│
├── uni_modules/                         # uni-app插件
├── App.vue                              # 应用入口
├── main.ts                              # 主文件
├── manifest.json                        # 应用配置
├── pages.json                           # 页面配置
├── uni.scss                             # 全局样式
└── package.json
```

#### 管理后台 (Vue3)

```
admin/
├── src/
│   ├── views/                           # 页面视图
│   │   ├── login/                      # 登录页
│   │   │   └── index.vue
│   │   │
│   │   ├── merchant/                   # 商家端页面
│   │   │   ├── dashboard/             # 数据面板
│   │   │   │   └── index.vue
│   │   │   ├── product/               # 商品管理
│   │   │   │   ├── platform-list.vue  # 平台商品库
│   │   │   │   ├── my-list.vue        # 我的商品
│   │   │   │   └── add.vue            # 添加商品
│   │   │   ├── order/                 # 订单管理
│   │   │   │   ├── list.vue
│   │   │   │   └── detail.vue
│   │   │   ├── finance/               # 财务管理
│   │   │   │   ├── overview.vue       # 财务概览
│   │   │   │   ├── flow.vue           # 资金流水
│   │   │   │   └── withdraw.vue       # 提现申请
│   │   │   ├── after-sale/            # 售后管理
│   │   │   │   ├── list.vue
│   │   │   │   └── detail.vue
│   │   │   └── shop/                  # 店铺管理
│   │   │       └── index.vue
│   │   │
│   │   └── platform/                   # 平台端页面
│   │       ├── dashboard/             # 数据大盘
│   │       │   └── index.vue
│   │       ├── product/               # 商品库管理
│   │       │   ├── list.vue
│   │       │   ├── add.vue
│   │       │   └── category.vue
│   │       ├── merchant/              # 商家管理
│   │       │   ├── list.vue
│   │       │   ├── audit.vue          # 审核
│   │       │   └── detail.vue
│   │       ├── order/                 # 订单管理
│   │       │   ├── list.vue
│   │       │   └── detail.vue
│   │       ├── finance/               # 财务管理
│   │       │   ├── overview.vue
│   │       │   ├── settlement.vue     # 结算
│   │       │   └── withdraw.vue       # 提现审核
│   │       ├── after-sale/            # 售后管理
│   │       │   ├── list.vue
│   │       │   └── detail.vue
│   │       └── system/                # 系统设置
│   │           ├── config.vue         # 系统配置
│   │           └── admin.vue          # 管理员
│   │
│   ├── components/                      # 组件
│   │   ├── Upload/                     # 上传组件
│   │   ├── Editor/                     # 富文本编辑器
│   │   ├── Charts/                     # 图表组件
│   │   └── Table/                      # 表格组件
│   │
│   ├── api/                             # API接口
│   │   ├── merchant.ts
│   │   ├── product.ts
│   │   ├── order.ts
│   │   ├── finance.ts
│   │   └── request.ts                  # 请求封装
│   │
│   ├── router/                          # 路由
│   │   ├── index.ts                    # 路由配置
│   │   ├── merchant.ts                 # 商家路由
│   │   └── platform.ts                 # 平台路由
│   │
│   ├── store/                           # 状态管理
│   │   ├── index.ts
│   │   ├── user.ts
│   │   └── app.ts
│   │
│   ├── utils/                           # 工具函数
│   │   ├── request.ts                  # 请求工具
│   │   ├── auth.ts                     # 认证工具
│   │   ├── validate.ts                 # 验证工具
│   │   └── format.ts                   # 格式化工具
│   │
│   ├── styles/                          # 样式
│   │   ├── index.scss                  # 全局样式
│   │   └── variables.scss              # 变量
│   │
│   ├── assets/                          # 静态资源
│   │   ├── images/
│   │   └── icons/
│   │
│   ├── App.vue                          # 根组件
│   └── main.ts                          # 入口文件
│
├── public/                              # 公共文件
├── .env.development                     # 开发环境变量
├── .env.production                      # 生产环境变量
├── index.html
├── package.json
├── tsconfig.json
├── vite.config.ts
└── README.md
```

---

## 🚀 开发部署

### 开发环境配置

#### 环境要求
```
Node.js >= 18.x
MySQL >= 8.0
Redis >= 7.0
```

#### 后端开发

1. **安装依赖**
```bash
cd ecommerce-backend
npm install
```

2. **配置环境变量**
```bash
# .env
NODE_ENV=development
PORT=3000

# 数据库配置
DB_HOST=localhost
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=your_password
DB_DATABASE=ecommerce

# Redis配置
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=

# JWT配置
JWT_SECRET=your_jwt_secret
JWT_EXPIRES_IN=7d

# 文件上传
UPLOAD_PATH=./uploads
MAX_FILE_SIZE=10485760

# 支付配置（微信支付）
WECHAT_APP_ID=
WECHAT_MCH_ID=
WECHAT_API_KEY=

# 支付配置（支付宝）
ALIPAY_APP_ID=
ALIPAY_PRIVATE_KEY=
ALIPAY_PUBLIC_KEY=

# 阿里云短信
ALIYUN_ACCESS_KEY_ID=
ALIYUN_ACCESS_KEY_SECRET=
SMS_SIGN_NAME=
SMS_TEMPLATE_CODE=

# OSS配置
OSS_REGION=
OSS_ACCESS_KEY_ID=
OSS_ACCESS_KEY_SECRET=
OSS_BUCKET=
```

3. **初始化数据库**
```bash
# 创建数据库
mysql -u root -p
CREATE DATABASE ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 运行SQL脚本
mysql -u root -p ecommerce < database/schema.sql
mysql -u root -p ecommerce < database/init_data.sql
```

4. **启动开发服务器**
```bash
npm run start:dev

# 访问
# API: http://localhost:3000
# 文档: http://localhost:3000/api/docs
```

#### 前端开发

**用户端（Uni-app）**
```bash
cd user-app
npm install

# H5开发
npm run dev:h5

# 微信小程序开发
npm run dev:mp-weixin

# 构建
npm run build:h5
npm run build:mp-weixin
```

**管理后台**
```bash
cd admin
npm install

# 启动开发服务器
npm run dev

# 访问: http://localhost:5173

# 构建
npm run build
```

### 生产部署

#### 服务器配置（推荐）
```
阿里云 ECS / 腾讯云 CVM
- CPU: 4核
- 内存: 8GB
- 硬盘: 100GB SSD
- 带宽: 5Mbps
- 系统: Ubuntu 22.04 LTS
```

#### 部署步骤

1. **安装基础环境**
```bash
# 更新系统
sudo apt update && sudo apt upgrade -y

# 安装Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# 安装MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# 安装Redis
sudo apt install -y redis-server
sudo systemctl enable redis-server

# 安装Nginx
sudo apt install -y nginx
sudo systemctl enable nginx

# 安装PM2（进程管理器）
sudo npm install -g pm2
```

2. **部署后端**
```bash
# 上传代码
cd /var/www
git clone your-repo.git ecommerce-backend
cd ecommerce-backend

# 安装依赖
npm install --production

# 配置环境变量
nano .env

# 构建
npm run build

# 使用PM2启动
pm2 start dist/main.js --name ecommerce-api
pm2 save
pm2 startup
```

3. **配置Nginx**
```bash
sudo nano /etc/nginx/sites-available/ecommerce

# 配置内容
server {
    listen 80;
    server_name api.yourdomain.com;
    
    # 重定向到HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name api.yourdomain.com;
    
    # SSL证书
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;
    
    # API代理
    location / {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_cache_bypass $http_upgrade;
    }
    
    # 上传文件大小限制
    client_max_body_size 10M;
}

# 前端静态文件
server {
    listen 80;
    server_name www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name www.yourdomain.com;
    
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;
    
    root /var/www/admin/dist;
    index index.html;
    
    location / {
        try_files $uri $uri/ /index.html;
    }
    
    # 静态资源缓存
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 7d;
        add_header Cache-Control "public, immutable";
    }
}

# 启用配置
sudo ln -s /etc/nginx/sites-available/ecommerce /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

4. **部署前端**
```bash
# 管理后台
cd /var/www/admin
npm install
npm run build

# 用户端H5
cd /var/www/user-app
npm install
npm run build:h5

# 上传到服务器或CDN
```

5. **配置SSL证书（Let's Encrypt 免费）**
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d api.yourdomain.com -d www.yourdomain.com
```

6. **数据库备份**
```bash
# 创建备份脚本
sudo nano /root/backup.sh

#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/mysql"
mkdir -p $BACKUP_DIR
mysqldump -u root -p'password' ecommerce | gzip > $BACKUP_DIR/ecommerce_$DATE.sql.gz
# 删除7天前的备份
find $BACKUP_DIR -name "*.gz" -mtime +7 -delete

# 添加执行权限
sudo chmod +x /root/backup.sh

# 添加定时任务（每天凌晨3点备份）
crontab -e
0 3 * * * /root/backup.sh
```

---

## 📈 开发计划与里程碑

### 第一阶段：MVP核心功能（1.5个月）

**Week 1-2: 基础搭建**
- [x] 项目初始化
- [x] 数据库设计与创建
- [x] 后端基础架构搭建
- [x] 前端基础架构搭建
- [x] 用户注册登录（用户端）
- [x] 商家注册登录（商家端）
- [x] 平台管理员登录（平台端）

**Week 3-4: 商品模块**
- [x] 平台商品库管理（增删改查）
- [x] 商品分类管理
- [x] SKU管理
- [x] 商家商品上架（从平台库选品）
- [x] 价格设置（成本价 + 利润）
- [x] 用户端商品展示
- [x] 商品搜索筛选

**Week 5-6: 购物车与订单**
- [x] 购物车功能
- [x] 地址管理
- [x] 订单创建流程
- [x] 支付集成（微信、支付宝）
- [x] 订单状态管理
- [x] 商家订单管理

**Week 7: 物流与售后**
- [x] 物流信息录入
- [x] 物流跟踪（快递鸟API）
- [x] 确认收货
- [x] 售后申请（退款/退货）
- [x] 售后审核流程

**Week 8: 测试与优化**
- [x] 全流程测试
- [x] Bug修复
- [x] 性能优化
- [x] 安全测试

### 第二阶段：完善功能（1个月）

**Week 9-10: 财务模块**
- [ ] 资金流水记录
- [ ] 商家收益统计
- [ ] 提现申请
- [ ] 提现审核
- [ ] 财务报表

**Week 11: 数据统计**
- [ ] 商家数据面板
- [ ] 平台数据大盘
- [ ] 销售统计
- [ ] 商品统计
- [ ] 订单统计

**Week 12: 上线准备**
- [ ] 服务器部署
- [ ] 域名配置
- [ ] SSL证书
- [ ] 性能压测
- [ ] 监控配置
- [ ] 正式上线

### 第三阶段：运营功能（持续迭代）

**功能规划**
- [ ] 优惠券系统
- [ ] 营销活动（秒杀、拼团）
- [ ] 会员体系
- [ ] 积分系统
- [ ] 消息推送
- [ ] 客服系统（IM）
- [ ] 评价系统
- [ ] 商品推荐算法
- [ ] 数据分析优化

---

## 💰 成本预算

### 服务器成本（月）

**初期配置（支持1000-2000日活）**
```
云服务器: 4核8G + 100GB
- 阿里云/腾讯云: ¥300/月（3年付）
- 或新用户优惠: ¥100/月

MySQL + Redis（服务器自建）: ¥0
对象存储OSS: ¥20/月（100GB + 流量）
CDN: ¥50/月（100GB流量）
域名: ¥50/年
SSL证书: ¥0（Let's Encrypt免费）

月成本合计: ¥370-470
```

**中期配置（支持5000-10000日活）**
```
云服务器: 8核16G
- ¥600/月

负载均衡SLB: ¥100/月
RDS MySQL: ¥300/月
Redis: ¥200/月
OSS + CDN: ¥200/月

月成本合计: ¥1400
```

### 第三方服务成本

```
支付手续费: 0.6%（按交易额）
短信服务: ¥0.03-0.05/条
物流API: ¥0（快递鸟免费版够用）
```

### 开发成本

**技术团队配置**
```
全栈开发 × 1-2人: 2-3个月
UI设计 × 1人: 1个月（兼职）

估算成本: 5-15万（含人力）
```

### 总成本估算

```
首年成本:
- 开发成本: 5-15万（一次性）
- 服务器: ¥500 × 12 = ¥6000
- 第三方服务: ¥5000（估算）
- 其他: ¥5000

合计: 6.6-17.6万
```

---

## ⚡ 性能指标

### 目标性能
```
并发用户: 1000人在线
日订单量: 3000-5000单
响应时间: < 500ms（API）
页面加载: < 2s（首屏）
数据库QPS: 1000+
Redis QPS: 10000+
```

### 优化策略

**1. 缓存优化**
```typescript
// 商品信息缓存（5分钟）
@Cacheable('product', 300)
async getProduct(id: number) {
  return await this.productRepository.findOne(id);
}

// 分类数据缓存（30分钟）
@Cacheable('categories', 1800)
async getCategories() {
  return await this.categoryRepository.find();
}

// 热门商品缓存（1小时）
@Cacheable('hot_products', 3600)
async getHotProducts() {
  return await this.productRepository.find({
    where: { status: 1 },
    order: { sales: 'DESC' },
    take: 20
  });
}
```

**2. 数据库优化**
```sql
-- 创建必要索引
CREATE INDEX idx_user_phone ON user(phone);
CREATE INDEX idx_order_status ON order_main(order_status, create_time);
CREATE INDEX idx_product_category ON platform_product(category_id, status);

-- 慢查询监控
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1;
```

**3. 接口优化**
```typescript
// 订单创建异步处理
@Post('create')
async createOrder(@Body() dto: CreateOrderDto) {
  // 创建订单
  const order = await this.orderService.create(dto);
  
  // 异步处理（发送通知、更新库存等）
  await this.queue.add('order-created', { orderId: order.id });
  
  return order;
}
```

---

## 🔐 安全措施

### 1. 认证与授权
```typescript
// JWT认证
@UseGuards(JwtAuthGuard)
@Get('profile')
async getProfile(@User() user) {
  return user;
}

// 角色权限
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
@Get('admin/users')
async getUsers() {
  return await this.userService.findAll();
}
```

### 2. 密码加密
```typescript
import * as bcrypt from 'bcrypt';

// 注册时加密
const hashedPassword = await bcrypt.hash(password, 10);

// 登录时验证
const isMatch = await bcrypt.compare(password, user.password);
```

### 3. 接口限流
```typescript
// Redis + 装饰器实现限流
@RateLimit(10, 60) // 1分钟最多10次
@Post('send-code')
async sendCode(@Body() dto: SendCodeDto) {
  // ...
}
```

### 4. SQL注入防护
```typescript
// TypeORM自动防护
await this.userRepository.findOne({
  where: { phone: dto.phone }
});

// 禁止直接拼接SQL
```

### 5. XSS防护
```typescript
// 前端输出转义
import { escape } from 'lodash';
const safeText = escape(userInput);

// 后端验证
@IsString()
@IsNotEmpty()
@MaxLength(100)
content: string;
```

---

## 📚 技术文档

### 开发规范

**代码规范**
- 使用ESLint + Prettier统一代码风格
- 遵循TypeScript最佳实践
- 代码注释清晰完整

**命名规范**
```typescript
// 文件命名: kebab-case
user.controller.ts
order.service.ts

// 类命名: PascalCase
class UserController {}

// 函数/变量: camelCase
async getUser() {}
const userName = '';

// 常量: UPPER_SNAKE_CASE
const MAX_FILE_SIZE = 10485760;
```

**Git规范**
```bash
# 分支管理
main        # 生产环境
dev         # 开发环境
feature/*   # 功能分支
hotfix/*    # 紧急修复

# 提交规范
feat: 新功能
fix: 修复bug
docs: 文档更新
style: 代码格式（不影响功能）
refactor: 重构
test: 测试
chore: 构建/工具

# 示例
git commit -m "feat: 添加商品搜索功能"
git commit -m "fix: 修复订单支付回调bug"
```

### API文档

- 使用Swagger自动生成
- 访问地址: `http://localhost:3000/api/docs`
- 包含所有接口说明、参数、响应示例

---

## 📞 技术支持

### 参考资源

**官方文档**
- NestJS: https://docs.nestjs.com
- Vue3: https://cn.vuejs.org
- Uni-app: https://uniapp.dcloud.net.cn
- TypeORM: https://typeorm.io
- Element Plus: https://element-plus.org

**开源项目参考**
- nest-admin: https://github.com/nest-boot/nest-boot
- vue3-element-admin: https://github.com/youlaitech/vue3-element-admin
- uni-shop: https://github.com/yinbigben/uni-shop

**社区资源**
- Stack Overflow
- Segmentfault
- 掘金
- 思否

---

## 📝 附录

### 常见问题FAQ

**Q1: 如何处理高并发订单？**
A: 使用Redis分布式锁 + 消息队列异步处理 + 库存预扣除

**Q2: 支付回调如何保证幂等性？**
A: 使用订单号作为唯一标识，支付前检查订单状态

**Q3: 如何防止超卖？**
A: Redis库存预扣 + 数据库库存二次校验 + 乐观锁

**Q4: 大量图片如何优化？**
A: 使用OSS对象存储 + CDN加速 + 图片压缩 + WebP格式

**Q5: 如何监控系统运行状态？**
A: PM2监控 + 日志记录 + 告警通知（钉钉/邮件）

### 更新日志

**v1.0.0 (2025-10-04)**
- 初始版本
- 完成核心功能开发
- 用户端、商家端、平台端基础功能

---

## 📄 许可证

本项目采用 MIT 许可证

---

**文档版本**: v1.0  
**最后更新**: 2025-10-04  
**维护人员**: [您的名字]

---

**项目启动检查清单**

开发前准备:
- [ ] 安装Node.js 18+
- [ ] 安装MySQL 8.0
- [ ] 安装Redis 7.0
- [ ] 克隆项目代码
- [ ] 配置环境变量
- [ ] 初始化数据库
- [ ] 安装依赖包

开发中:
- [ ] 遵循代码规范
- [ ] 编写单元测试
- [ ] 提交前自测
- [ ] Code Review

上线前:
- [ ] 全流程测试
- [ ] 性能测试
- [ ] 安全扫描
- [ ] 备份数据
- [ ] 配置监控

---

**联系方式**

如有问题，请联系:
- 邮箱: your-email@example.com
- 微信: your-wechat
- 电话: 138-xxxx-xxxx

