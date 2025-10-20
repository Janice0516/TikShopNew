# TikTok Shop 数据库连接说明

## 🔗 数据库连接架构

### 📊 连接流程
```
前端服务 → API服务 → PostgreSQL数据库
```

### 🎯 关键点说明
- **前端服务** (商家后台、管理后台、用户前端) **不直接连接数据库**
- **API服务** 是唯一直接连接数据库的服务
- **前端服务** 通过HTTP请求调用API服务来获取数据

---

## 🔧 数据库连接配置

### 1. API服务数据库连接 ✅ (已完成)
```bash
# 环境变量配置 (已在Render API服务中设置)
NODE_ENV=production
DB_TYPE=postgres
DB_HOST=dpg-d3kgpsd6ubrc73dvbjm0-a
DB_PORT=5432
DB_USERNAME=postgres
DB_PASSWORD=[你的数据库密码]
DB_DATABASE=tiktokshop_slkz
JWT_SECRET=[你的JWT密钥]
PORT=10000
```

### 2. 前端服务API连接配置
```bash
# 环境变量配置 (需要在每个前端服务中设置)
VITE_API_BASE_URL=http://localhost:3000/api
```

---

## 📋 前端服务配置检查

### ✅ 商家后台 (Merchant Backend)
- **API地址**: `http://localhost:3000/api`
- **配置文件**: `merchant/src/utils/request.ts`
- **环境变量**: `VITE_API_BASE_URL`

### ✅ 管理后台 (Admin Backend)  
- **API地址**: `http://localhost:3000/api`
- **配置文件**: `admin/src/utils/request.ts`
- **环境变量**: `VITE_API_BASE_URL`

### ✅ 用户前端 (User App)
- **API地址**: `http://localhost:3000/api`
- **配置文件**: `user-app/src/utils/request.ts`
- **环境变量**: `VITE_API_BASE_URL`

---

## 🔍 数据库连接验证

### 1. 检查API服务状态
访问: `http://localhost:3000/api/test/health`

预期响应:
```json
{
  "code": 200,
  "message": "服务正常",
  "data": {
    "status": "running",
    "timestamp": "2025-01-11T12:00:00.000Z"
  }
}
```

### 2. 检查数据库连接
访问: `http://localhost:3000/api/products`

预期响应:
```json
{
  "code": 200,
  "message": "获取商品列表成功",
  "data": {
    "list": [...],
    "total": 100,
    "page": 1,
    "pageSize": 10
  }
}
```

---

## 🛠️ 前端服务部署时的数据库连接设置

### 在Render控制台设置环境变量

#### 商家后台环境变量:
```bash
VITE_API_BASE_URL=http://localhost:3000/api
```

#### 管理后台环境变量:
```bash
VITE_API_BASE_URL=http://localhost:3000/api
```

#### 用户前端环境变量:
```bash
VITE_API_BASE_URL=http://localhost:3000/api
```

---

## 🔧 前端代码中的数据库访问

### 示例：商家后台获取商品列表
```typescript
// merchant/src/api/product.ts
import request from '@/utils/request'

export function getProducts(params: any) {
  return request({
    url: '/products',
    method: 'get',
    params
  })
}
```

### 示例：管理后台获取用户列表
```typescript
// admin/src/api/user.ts
import request from '@/utils/request'

export function getUserList(params: any) {
  return request({
    url: '/admin/users',
    method: 'get',
    params
  })
}
```

### 示例：用户前端获取商品
```typescript
// user-app/src/api/product.ts
export function getProducts() {
  return uni.request({
    url: 'http://localhost:3000/api/products',
    method: 'GET'
  })
}
```

---

## 🚨 常见问题排查

### 1. 前端无法获取数据
**检查步骤:**
- ✅ API服务是否正常运行
- ✅ 前端环境变量是否正确设置
- ✅ API地址是否正确
- ✅ 网络连接是否正常

### 2. API服务连接数据库失败
**检查步骤:**
- ✅ 数据库服务是否运行
- ✅ 数据库连接信息是否正确
- ✅ 网络连接是否正常
- ✅ 数据库权限是否正确

### 3. 跨域问题
**解决方案:**
- API服务已配置CORS允许所有来源
- 前端请求使用HTTPS协议

---

## 📊 数据库连接状态监控

### API服务日志检查
在Render控制台查看API服务日志，确认:
- ✅ 数据库连接成功
- ✅ 所有模块正常加载
- ✅ 路由映射完成

### 前端服务日志检查
在Render控制台查看前端服务日志，确认:
- ✅ 构建成功
- ✅ 环境变量正确加载
- ✅ API请求正常发送

---

## 🎯 总结

1. **数据库连接**: 只有API服务直接连接数据库
2. **前端访问**: 前端通过API服务间接访问数据库
3. **配置要点**: 确保所有前端服务都设置了正确的API地址
4. **验证方法**: 通过API健康检查和数据接口测试

**当前状态**: API服务已成功连接数据库 ✅
**下一步**: 部署前端服务并配置API连接
