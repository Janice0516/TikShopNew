# TikTok Shop 前端模块化架构

## 📋 概述

基于 [TikTok Shop](https://www.tiktok.com/shop/my) 的设计，将前端商城完全模块化，实现分类横向滚动、商品垂直网格布局的现代化电商界面。

## 🏗️ 模块化结构

### 1. 布局模块 (Layout Components)

#### `AppHeader.vue`
- **功能**: 顶部导航栏
- **特性**: 
  - 响应式设计
  - 固定定位
  - 品牌 Logo 展示
  - 用户操作按钮

#### `AppSidebar.vue`
- **功能**: 左侧边栏导航
- **特性**:
  - 固定定位
  - 导航菜单
  - 用户登录区域
  - 页脚链接

### 2. 功能模块 (Section Components)

#### `CategorySection.vue`
- **功能**: 分类横向滚动区域
- **特性**:
  - 水平滚动支持
  - 滚动箭头控制
  - 分类图标映射
  - 点击事件处理

#### `ProductCarousel.vue`
- **功能**: 商品横向轮播组件
- **特性**:
  - 横向滚动轮播
  - 左右滚动按钮
  - 商品卡片展示
  - 促销标签支持
  - 评分和销量显示
  - 价格信息展示

### 3. 商品模块 (Product Components)

#### `ProductCard.vue`
- **功能**: 单个商品卡片
- **特性**:
  - 商品图片展示
  - 价格信息显示
  - 评分和销量
  - 商家信息
  - 库存状态
  - 促销标签

#### `ProductGrid.vue`
- **功能**: 商品网格容器
- **特性**:
  - 响应式网格布局
  - 分页支持
  - 加载状态
  - 空状态处理
  - 商品卡片渲染

### 4. 业务逻辑模块 (Composables)

#### `useCategories.ts`
- **功能**: 分类数据管理
- **特性**:
  - API 数据获取
  - 分类图标映射
  - 错误处理
  - 备用数据

#### `useProducts.ts`
- **功能**: 商品数据管理
- **特性**:
  - 商品列表获取
  - 分类筛选
  - 搜索功能
  - 分页管理
  - 数据格式化

#### `useTopDeals.ts`
- **功能**: Top Deals 数据管理
- **特性**:
  - 折扣商品筛选
  - 促销标签生成
  - 高销量商品筛选
  - 价格对比计算

#### `usePopularItems.ts`
- **功能**: Popular Items 数据管理
- **特性**:
  - 热门商品筛选
  - 高评分商品筛选
  - 分类商品展示
  - 品牌信息管理

## 🎯 设计特点

### 参考 TikTok Shop 设计
- **分类区域**: 横向滚动，圆形图标
- **Top Deals 区域**: 横向轮播，折扣商品展示
- **Popular Items 区域**: 横向轮播，热门商品展示
- **商品区域**: 垂直网格布局，卡片式设计
- **响应式**: 移动端适配
- **交互**: 悬停效果，点击反馈

### 模块化优势
1. **可维护性**: 每个模块职责单一
2. **可复用性**: 组件可在不同页面使用
3. **可测试性**: 独立模块便于单元测试
4. **可扩展性**: 新功能易于添加

## 📁 文件结构

```
src/
├── components/
│   ├── layout/
│   │   ├── AppHeader.vue      # 顶部导航栏
│   │   └── AppSidebar.vue     # 左侧边栏
│   ├── sections/
│   │   └── CategorySection.vue # 分类区域
│   ├── products/
│   │   ├── ProductCard.vue    # 商品卡片
│   │   └── ProductGrid.vue    # 商品网格
│   └── index.ts               # 组件导出
├── composables/
│   ├── useCategories.ts       # 分类逻辑
│   ├── useProducts.ts         # 商品逻辑
│   └── index.ts               # 导出文件
├── views/
│   ├── HomeModular.vue        # 模块化首页
│   └── Home.vue               # 原始首页（保留）
└── router/
    └── index.ts               # 路由配置
```

## 🚀 使用方法

### 1. 导入组件
```typescript
import { AppHeader, AppSidebar, CategorySection, ProductGrid } from '@/components'
```

### 2. 使用 Composables
```typescript
import { useCategories, useProducts } from '@/composables'

const { categories, loading: categoriesLoading } = useCategories()
const { products, loading: productsLoading, loadProducts } = useProducts()
```

### 3. 页面结构
```vue
<template>
  <div class="tiktok-shop">
    <AppHeader />
    <div class="main-layout">
      <AppSidebar />
      <main class="main-content">
        <CategorySection 
          :categories="categories"
          @category-click="handleCategoryClick"
        />
        <ProductGrid
          :products="products"
          :loading="loading"
          @product-click="handleProductClick"
        />
      </main>
    </div>
  </div>
</template>
```

## 🔧 配置说明

### 路由配置
- `/` - 模块化首页 (HomeModular.vue)
- `/home-original` - 原始首页 (Home.vue)

### 样式配置
- 使用 SCSS 预处理器
- 响应式断点: 768px
- 颜色主题: TikTok 品牌色

### API 配置
- 分类接口: `/api/shop/categories`
- 商品接口: `/api/shop/products`
- 支持分页、筛选、搜索

## 📱 响应式设计

### 桌面端 (>768px)
- 侧边栏固定显示
- 商品网格多列布局
- 分类横向滚动

### 移动端 (≤768px)
- 侧边栏隐藏
- 商品网格单列布局
- 分类紧凑显示

## 🎨 视觉效果

### 分类区域
- 圆形图标设计
- 平滑滚动动画
- 悬停提升效果

### 商品卡片
- 卡片阴影效果
- 图片悬停缩放
- 价格突出显示
- 评分星级显示

## 🔄 数据流

1. **页面加载** → 调用 `useCategories()` 和 `useProducts()`
2. **分类点击** → 触发 `loadProductsByCategory()`
3. **商品点击** → 路由跳转到商品详情
4. **分页操作** → 调用 `loadProducts()` 更新数据

## 🛠️ 开发建议

### 添加新模块
1. 在 `components/` 下创建对应目录
2. 实现组件逻辑和样式
3. 在 `components/index.ts` 中导出
4. 编写对应的 composable（如需要）

### 修改现有模块
1. 保持接口稳定
2. 更新类型定义
3. 测试相关功能
4. 更新文档

## 📊 性能优化

- 组件懒加载
- 图片懒加载
- 虚拟滚动（大量数据时）
- 缓存策略
- 代码分割

## 🧪 测试策略

- 单元测试：每个 composable
- 组件测试：关键 UI 组件
- 集成测试：页面级功能
- E2E 测试：用户流程

---

**注意**: 此架构完全参考 TikTok Shop 的设计理念，实现了现代化的模块化前端架构，便于维护和扩展。
