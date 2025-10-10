# 🛍️ 商城前端虚拟数据演示

## 📋 概述

已为商城前端生成了完整的虚拟数据系统，让您可以直接查看排版和交互效果，无需依赖后端API。

## 🎯 功能特性

### ✅ 已完成的虚拟数据

1. **商品数据** (20个虚拟商品)
   - 商品名称、价格、图片
   - 品牌、分类、库存、销量
   - 评分、评论数、标签
   - 商品规格、多图展示

2. **分类数据** (6个主分类，24个子分类)
   - 手机数码、服装鞋帽、家用电器
   - 美妆护肤、食品饮料、运动户外
   - 带图标和层级结构

3. **购物车数据** (随机1-5个商品)
   - 商品信息、数量、选中状态
   - 支持增减数量操作

4. **订单数据** (15个虚拟订单)
   - 订单号、状态、总金额
   - 商品列表、收货地址
   - 时间信息（创建、支付、发货、送达）

5. **用户数据**
   - 用户信息、头像、等级
   - 积分、优惠券数量

6. **轮播图数据** (4张轮播图)
   - 图片、标题、跳转链接

## 🚀 使用方法

### 1. 访问演示页面

**方式一：从首页进入**
- 打开商城前端：http://localhost:5176
- 点击右下角红色"虚拟数据演示"按钮

**方式二：直接访问**
- 直接访问：http://localhost:5176/#/pages/demo

### 2. 演示页面功能

- **轮播图展示**：4张精美的轮播图
- **分类导航**：6个主分类，带图标
- **热门商品**：8个热门商品横向滚动
- **商品列表**：12个商品网格展示，支持排序
- **购物车**：显示购物车商品，支持数量调整
- **订单列表**：5个订单展示，支持状态查看

### 3. 交互功能

- ✅ 商品点击查看详情
- ✅ 分类点击筛选
- ✅ 购物车数量调整
- ✅ 订单状态查看
- ✅ 排序筛选功能

## 🔧 技术实现

### 虚拟数据生成器
```typescript
// 位置：/src/utils/mockData.ts
export const mockData = {
  generateProducts(count: number = 20)    // 生成商品数据
  generateCategories()                    // 生成分类数据
  generateCartItems(products: any[])      // 生成购物车数据
  generateOrders(count: number = 10)      // 生成订单数据
  generateAddresses()                     // 生成地址数据
  generateUser()                          // 生成用户数据
  generateBanners()                        // 生成轮播图数据
}
```

### 模拟API服务
```typescript
// 位置：/src/utils/mockApi.ts
export const mockApi = {
  // 用户相关
  login, register, getUserInfo, sendCode
  
  // 商品相关
  getProducts, getProductDetail, getCategories
  getHotProducts, getRecommendProducts
  
  // 购物车相关
  getCart, addToCart, updateCartItem, removeCartItem, clearCart
  
  // 订单相关
  createOrder, getOrders, getOrderDetail, cancelOrder, payOrder
  
  // 地址相关
  getAddresses, createAddress, updateAddress, deleteAddress, setDefaultAddress
  
  // 其他
  getBanners
}
```

### API切换机制
```typescript
// 位置：/src/api/index.ts
const USE_MOCK_DATA = true  // 控制是否使用虚拟数据

export function getProducts(params) {
  if (USE_MOCK_DATA) {
    return mockApi.getProducts(params)  // 使用虚拟数据
  }
  return request({ ... })               // 使用真实API
}
```

## 📱 页面展示

### 演示页面包含：

1. **顶部标题**：商城前端虚拟数据演示
2. **轮播图区域**：4张轮播图自动播放
3. **分类导航**：6个分类图标网格
4. **热门商品**：横向滚动的商品列表
5. **商品列表**：网格布局，支持排序筛选
6. **购物车**：商品列表，数量调整
7. **订单列表**：订单状态展示

## 🎨 设计特色

- **现代化UI**：渐变背景、圆角设计、阴影效果
- **响应式布局**：适配不同屏幕尺寸
- **交互动画**：点击反馈、悬停效果
- **数据丰富**：真实的商品信息和价格
- **状态管理**：完整的购物流程数据

## 🔄 切换真实API

如需切换到真实API，只需修改：

```typescript
// 在 /src/api/index.ts 中
const USE_MOCK_DATA = false  // 改为 false
```

## 📊 数据统计

- **商品数量**：20个虚拟商品
- **分类数量**：6个主分类，24个子分类
- **订单数量**：15个虚拟订单
- **轮播图**：4张轮播图
- **购物车**：1-5个随机商品

## 🎉 使用效果

现在您可以：
1. 直接查看商城前端的完整排版效果
2. 测试各种交互功能
3. 体验购物流程
4. 查看订单管理
5. 无需等待后端API开发完成

## 📝 注意事项

- 虚拟数据每次刷新都会重新生成
- 图片使用 Picsum 随机图片服务
- 价格和销量为随机生成
- 所有交互都有控制台日志输出

---

**🎯 现在就可以访问 http://localhost:5176 查看完整的商城前端效果！**
