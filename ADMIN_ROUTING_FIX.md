# 🎯 Admin路由跳转问题解决方案

## 📊 问题分析

**您遇到的问题**：
- 访问 `https://tiktokbusines.store/admin/` 时，URL显示为 `https://tiktokbusines.store/login`
- 虽然页面内容显示"Admin Login"，但URL路径不正确

**根本原因**：
1. **Vite base配置错误**：当 `base: './'` 时，Vue Router使用相对路径
2. **rolldown构建工具**：rolldown-vite在处理相对路径时可能导致路由重定向问题
3. **nginx静态文件服务**：admin使用静态文件服务，依赖正确的base路径

## 🔧 解决方案

### 1. 修复Vite配置
```typescript
// admin/vite.config.ts
export default defineConfig({
  base: '/admin/',  // ✅ 正确：绝对路径
  // base: './',    // ❌ 错误：相对路径会导致路由问题
})
```

### 2. 重新构建项目
```bash
cd /root/TikShop/admin && npm run build
cp -r /root/TikShop/admin/dist/* /www/wwwroot/tikshop-admin/
```

### 3. 验证修复结果
- ✅ `https://tiktokbusines.store/admin/login` → 200 (Admin登录页面)
- ✅ `https://tiktokbusines.store/admin/` → 200 (Admin首页)
- ✅ `https://tiktokbusines.store/admin/assets/` → 200 (静态资源)

## 🚀 技术原理

### Vue Router Base配置
```typescript
// admin/src/router/index.ts
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL), // 使用 /admin/
  routes
})
```

**当 `base: '/admin/'` 时**：
- Vue Router知道应用运行在 `/admin/` 路径下
- 路由重定向会正确跳转到 `/admin/login`
- 静态资源路径为 `/admin/assets/`

**当 `base: './'` 时**：
- Vue Router使用相对路径
- 可能导致重定向到根路径 `/login`
- rolldown构建工具可能无法正确处理相对路径

### Nginx配置
```nginx
# 管理后台
location /admin/ {
    alias /www/wwwroot/tikshop-admin/;
    index index.html;
    try_files $uri $uri/ @admin_fallback;
}

location @admin_fallback {
    rewrite ^/admin/(.*)$ /admin/index.html last;
}
```

## 📋 验证步骤

1. **访问admin根目录**：
   ```bash
   curl -I "https://tiktokbusines.store/admin/"
   # 应该返回200，并且不会重定向到/login
   ```

2. **检查静态资源**：
   ```bash
   curl -I "https://tiktokbusines.store/admin/assets/index-CQ4vL0WQ.js"
   # 应该返回200
   ```

3. **测试路由重定向**：
   - 访问 `https://tiktokbusines.store/admin/`
   - 如果未登录，应该重定向到 `https://tiktokbusines.store/admin/login`
   - **不应该**重定向到 `https://tiktokbusines.store/login`

## ⚠️ 重要提醒

**不要将base改回 `'./'`**：
- rolldown-vite + Vue Router + 相对路径 = 路由问题
- 静态文件服务需要绝对路径
- nginx配置依赖正确的base路径

**正确的配置**：
- Admin: `base: '/admin/'`
- Merchant: `base: '/merchant/'`
- User: `base: '/'` (根路径)

## 🎉 结果

**问题完全解决！** 现在：
- ✅ Admin后台正确重定向到 `/admin/login`
- ✅ 不会跳转到用户端的 `/login`
- ✅ 静态资源正确加载
- ✅ rolldown构建工具正常工作

**每个后台都有独立的登录页面**：
- 用户端: `https://tiktokbusines.store/login`
- Admin: `https://tiktokbusines.store/admin/login`
- Merchant: `https://tiktokbusines.store/merchant/login`
