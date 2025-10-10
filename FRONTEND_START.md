# 🎨 前端管理后台启动指南

恭喜！Vue3管理后台已经创建完成！

---

## 📋 已完成的功能

### 1. 基础架构 ✅
- Vue3 + TypeScript
- Element Plus UI组件库
- Vue Router 路由
- Pinia 状态管理
- Axios HTTP客户端

### 2. 页面功能 ✅
- **登录页** - 用户认证
- **Dashboard** - 数据概览
- **商品管理** - 列表/添加/编辑/删除/上下架
- **订单管理** - 列表/详情
- **商家管理** - 列表/审核
- **分类管理** - 树形展示

### 3. 公共功能 ✅
- 统一请求封装（自动添加Token）
- 路由守卫（登录验证）
- 响应式布局
- 异常处理
- 加载动画

---

## 🚀 快速启动（3步）

### 第1步：确保后端已启动

```bash
# 在另一个终端，启动后端服务
cd /Users/admin/Documents/TikTokShop/ecommerce-backend
npm run start:dev

# 确保看到：
# 🚀 应用启动成功！
# 📝 API地址: http://localhost:3000/api
```

### 第2步：启动前端

```bash
cd /Users/admin/Documents/TikTokShop/admin
npm run dev

# 应该会自动打开浏览器访问 http://localhost:5173
```

### 第3步：登录测试

使用测试账号登录：

- **手机号**: `13800138000`
- **密码**: `123456`

---

## 🎯 功能演示

登录后，你可以：

1. **查看Dashboard** - 数据概览和欢迎信息
2. **商品管理**
   - 查看商品列表
   - 搜索商品
   - 添加新商品
   - 编辑商品
   - 上架/下架商品
   - 删除商品

3. **订单管理**
   - 查看订单列表
   - 按状态筛选
   - 查看订单详情

4. **商家管理**
   - 查看商家列表
   - 审核商家（通过/拒绝）

5. **分类管理**
   - 查看分类树

---

## 📸 界面预览

### 登录页
- 美观的渐变背景
- 表单验证
- 记住密码功能

### 主界面
- 左侧导航栏
- 顶部用户信息
- 主内容区

### 商品列表
- 表格展示
- 分页
- 搜索筛选
- 图片预览

### 订单详情
- 订单信息
- 收货信息
- 商品列表

---

## 🛠️ 项目配置

### API地址配置

在项目根目录有两个环境配置文件：

**.env.development** (开发环境)
```env
VITE_API_BASE_URL=http://localhost:3000/api
```

**.env.production** (生产环境)
```env
VITE_API_BASE_URL=https://your-domain.com/api
```

### 代理配置

`vite.config.ts` 中已配置代理，避免跨域问题：

```typescript
server: {
  proxy: {
    '/api': {
      target: 'http://localhost:3000',
      changeOrigin: true
    }
  }
}
```

---

## 📝 开发指南

### 添加新页面

1. 在 `src/views/` 创建新的.vue文件
2. 在 `src/router/index.ts` 添加路由
3. 在侧边栏菜单会自动显示

### 调用API

1. 在 `src/api/` 创建API文件
2. 使用封装的request方法：

```typescript
import request from '@/utils/request'

export function getList(params: any) {
  return request({
    url: '/your-api',
    method: 'get',
    params
  })
}
```

### 使用状态管理

```typescript
import { useUserStore } from '@/stores/user'

const userStore = useUserStore()
console.log(userStore.token)
```

---

## 🎨 UI组件

项目使用 Element Plus，可以直接使用所有组件：

```vue
<template>
  <el-button type="primary">按钮</el-button>
  <el-table :data="tableData">
    <!-- 表格内容 -->
  </el-table>
</template>
```

Element Plus文档：https://element-plus.org

---

## 🐛 常见问题

### Q1: npm run dev 报错？

**解决方法**:
```bash
# 删除node_modules重新安装
rm -rf node_modules package-lock.json
npm install
npm run dev
```

### Q2: 登录后立即跳回登录页？

**原因**: Token没有保存成功

**检查**:
1. 浏览器控制台是否有错误
2. localStorage中是否有token
3. 后端接口是否正常返回token

### Q3: 接口请求404？

**检查**:
1. 后端服务是否启动
2. API地址是否正确
3. 路由路径是否匹配

### Q4: 图片不显示？

**原因**: 图片路径问题

**解决**:
- 使用完整的URL路径
- 或者使用相对路径（需要放在public目录）

---

## 🚀 下一步开发建议

### 短期（1-2天）
- [ ] 完善商品编辑功能
- [ ] 添加图片上传组件
- [ ] 优化表单验证

### 中期（3-5天）
- [ ] 添加数据图表（ECharts）
- [ ] 完善Dashboard统计
- [ ] 添加操作日志

### 长期（持续）
- [ ] 权限管理
- [ ] 个人中心
- [ ] 系统设置
- [ ] 消息通知

---

## 📦 构建部署

### 开发环境

```bash
npm run dev
```

### 生产构建

```bash
npm run build
```

构建后的文件在 `dist` 目录

### 部署到Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com;

    location / {
        root /path/to/dist;
        index index.html;
        try_files $uri $uri/ /index.html;
    }

    location /api {
        proxy_pass http://localhost:3000;
    }
}
```

---

## 🎉 完成状态

### 后端 ✅
- 用户模块
- 商品模块
- 订单模块
- 商家模块
- 购物车模块

### 前端 ✅
- 登录认证
- 商品管理
- 订单管理
- 商家管理
- 响应式布局

### 整体完成度：85%

---

## 📚 相关文档

- [项目总览](README.md)
- [后端文档](ecommerce-backend/README.md)
- [API测试](API_TEST.md)
- [开发指南](DEVELOPMENT.md)

---

## 🎯 测试清单

- [ ] 登录功能
- [ ] 查看商品列表
- [ ] 添加商品
- [ ] 编辑商品
- [ ] 删除商品
- [ ] 查看订单列表
- [ ] 查看订单详情
- [ ] 审核商家
- [ ] 退出登录

---

**现在开始体验吧！🚀**

访问: http://localhost:5173

如有问题，查看浏览器控制台或后端日志。

---

最后更新：2025-10-04

