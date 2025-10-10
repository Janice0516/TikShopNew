# 🧪 API接口测试指南

本文档提供完整的API测试流程，帮助你快速测试所有功能。

---

## 📋 测试前准备

### 1. 启动后端服务

```bash
cd /Users/admin/Documents/TikTokShop/ecommerce-backend
npm run start:dev
```

### 2. 访问Swagger文档

http://localhost:3000/api/docs

---

## 🧪 完整测试流程

### 第一步：用户注册登录

#### 1.1 用户注册

```bash
curl -X POST http://localhost:3000/api/user/register \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "13900139001",
    "password": "123456",
    "code": "123456"
  }'
```

**返回示例**:
```json
{
  "code": 200,
  "message": "success",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "userInfo": {
      "id": 2,
      "phone": "13900139001",
      "nickname": "用户9001"
    }
  }
}
```

> **保存Token**: 将返回的token保存，后续请求需要使用

#### 1.2 用户登录

```bash
curl -X POST http://localhost:3000/api/user/login \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "13900139001",
    "password": "123456"
  }'
```

---

### 第二步：浏览商品

#### 2.1 获取商品列表

```bash
# 不带参数（默认第1页，10条）
curl http://localhost:3000/api/products

# 带分页
curl "http://localhost:3000/api/products?page=1&pageSize=5"

# 按分类筛选
curl "http://localhost:3000/api/products?categoryId=6"

# 搜索商品
curl "http://localhost:3000/api/products?keyword=T恤"
```

#### 2.2 获取商品详情

```bash
curl http://localhost:3000/api/products/1
```

#### 2.3 获取分类列表

```bash
curl http://localhost:3000/api/products/categories
```

---

### 第三步：加入购物车

#### 3.1 添加商品到购物车

```bash
curl -X POST http://localhost:3000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "productId": 1,
    "quantity": 2
  }'
```

> **替换YOUR_TOKEN**: 使用第一步获取的token

#### 3.2 查看购物车

```bash
curl http://localhost:3000/api/cart \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### 3.3 更新购物车数量

```bash
curl -X PATCH http://localhost:3000/api/cart/1/quantity \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"quantity": 3}'
```

---

### 第四步：创建订单

#### 4.1 创建订单

```bash
curl -X POST http://localhost:3000/api/orders \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "addressId": 1,
    "items": [
      {
        "productId": 1,
        "quantity": 2
      },
      {
        "productId": 2,
        "quantity": 1
      }
    ],
    "buyerMessage": "请尽快发货"
  }'
```

**返回示例**:
```json
{
  "code": 200,
  "message": "success",
  "data": {
    "orderId": 1,
    "orderNo": "20251004152030123456",
    "payAmount": 164.00
  }
}
```

#### 4.2 模拟支付（开发环境）

```bash
curl -X POST http://localhost:3000/api/orders/1/pay \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

### 第五步：订单管理

#### 5.1 查看订单列表

```bash
# 全部订单
curl http://localhost:3000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN"

# 按状态筛选（1待付款 2待发货 3待收货 4已完成）
curl "http://localhost:3000/api/orders?orderStatus=2" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### 5.2 查看订单详情

```bash
curl http://localhost:3000/api/orders/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### 5.3 取消订单

```bash
curl -X PATCH http://localhost:3000/api/orders/1/cancel \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"reason": "不想要了"}'
```

#### 5.4 确认收货

```bash
curl -X PATCH http://localhost:3000/api/orders/1/confirm \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### 5.5 订单数量统计

```bash
curl http://localhost:3000/api/orders/count \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

### 第六步：商家端功能

#### 6.1 商家注册

```bash
curl -X POST http://localhost:3000/api/merchant/register \
  -H "Content-Type: application/json" \
  -d '{
    "username": "merchant002",
    "password": "123456",
    "merchantName": "优品小店2号",
    "contactName": "李四",
    "contactPhone": "13900139002",
    "businessLicense": "/uploads/license.jpg"
  }'
```

#### 6.2 商家登录

```bash
curl -X POST http://localhost:3000/api/merchant/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "merchant001",
    "password": "123456"
  }'
```

> **保存商家Token**: 商家的token和用户的token不同

#### 6.3 查看商家订单

```bash
curl http://localhost:3000/api/orders \
  -H "Authorization: Bearer MERCHANT_TOKEN"
```

#### 6.4 商家发货

```bash
curl -X PATCH http://localhost:3000/api/orders/1/ship \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer MERCHANT_TOKEN" \
  -d '{
    "logisticsCompany": "顺丰速运",
    "trackingNumber": "SF1234567890"
  }'
```

---

## 📱 使用Swagger测试（推荐）

### 1. 打开Swagger

http://localhost:3000/api/docs

### 2. 登录获取Token

1. 找到 "POST /api/user/register" 或 "POST /api/user/login"
2. 点击 "Try it out"
3. 填写参数
4. 点击 "Execute"
5. 复制返回的token

### 3. 设置Authorization

1. 点击页面右上角的 "Authorize" 按钮
2. 在弹窗中输入：`Bearer YOUR_TOKEN`（注意Bearer后有空格）
3. 点击 "Authorize"
4. 点击 "Close"

### 4. 测试需要认证的接口

现在可以测试所有需要Token的接口了！

---

## 🧪 完整测试场景

### 场景1：用户购物流程

```
1. 注册/登录
2. 浏览商品列表
3. 查看商品详情
4. 添加到购物车
5. 查看购物车
6. 创建订单
7. 模拟支付
8. 查看订单
9. 确认收货
```

### 场景2：商家订单处理

```
1. 商家登录
2. 查看待发货订单
3. 发货（录入快递单号）
4. 查看已发货订单
```

### 场景3：订单取消

```
1. 用户登录
2. 创建订单（不支付）
3. 取消订单
4. 验证库存恢复
```

---

## 📊 订单状态说明

| 状态值 | 状态名称 | 说明 | 可执行操作 |
|-------|---------|------|-----------|
| 1 | 待付款 | 订单已创建，等待支付 | 支付、取消 |
| 2 | 待发货 | 已支付，等待商家发货 | 发货（商家） |
| 3 | 待收货 | 已发货，等待用户确认 | 确认收货 |
| 4 | 已完成 | 交易完成 | 申请售后 |
| 5 | 已取消 | 订单已取消 | - |

---

## 🔧 常见问题

### Q1: Token过期怎么办？

重新登录获取新的Token

### Q2: 创建订单时提示库存不足？

查看商品详情，确认库存是否充足

### Q3: 测试支付接口？

目前使用模拟支付，直接调用 `POST /api/orders/:id/pay` 即可

### Q4: 如何测试并发？

使用JMeter或Postman的Collection Runner进行压力测试

---

## 🎯 性能测试

### 使用Apache Bench

```bash
# 测试商品列表接口（100个请求，10个并发）
ab -n 100 -c 10 http://localhost:3000/api/products

# 测试登录接口
ab -n 100 -c 10 -p login.json -T application/json \
  http://localhost:3000/api/user/login
```

### 使用Postman Collection Runner

1. 在Postman中创建Collection
2. 添加所有测试接口
3. 点击 "Run Collection"
4. 设置迭代次数和延迟
5. 运行测试

---

## 📝 测试检查清单

### 用户模块
- [ ] 用户注册
- [ ] 用户登录
- [ ] 获取个人信息
- [ ] Token过期处理

### 商品模块
- [ ] 商品列表（分页）
- [ ] 商品搜索
- [ ] 商品筛选
- [ ] 商品详情
- [ ] 分类列表

### 购物车模块
- [ ] 添加商品
- [ ] 查看购物车
- [ ] 更新数量
- [ ] 删除商品
- [ ] 清空购物车

### 订单模块
- [ ] 创建订单
- [ ] 订单列表
- [ ] 订单详情
- [ ] 模拟支付
- [ ] 取消订单
- [ ] 确认收货
- [ ] 订单统计

### 商家模块
- [ ] 商家注册
- [ ] 商家登录
- [ ] 查看订单
- [ ] 发货操作

---

## 🎉 测试完成

恭喜！如果所有测试都通过，说明后端核心功能已经完整实现。

下一步：
1. 继续开发支付模块
2. 创建前端项目
3. 部署测试

---

**最后更新**: 2025-10-04

