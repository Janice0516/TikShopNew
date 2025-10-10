# 🛍️ User App (用户端)

**International E-commerce Platform - User Mobile App**

多语言用户购物端 - 支持英文、中文、马来文

---

## 🌍 Multi-language Support (多语言支持)

### Supported Languages

- 🇬🇧 **English** - Full support
- 🇨🇳 **中文** - 完整支持
- 🇲🇾 **Bahasa Melayu** - Sokongan penuh

---

## 🚀 Quick Start (快速开始)

### 1. Install Dependencies (安装依赖)

```bash
npm install
```

### 2. Run Development (运行开发)

#### H5 浏览器版本
```bash
npm run dev:h5
```
访问: http://localhost:5173

#### 微信小程序
```bash
npm run dev:mp-weixin
```
然后用微信开发者工具打开 `dist/dev/mp-weixin` 目录

#### 支付宝小程序
```bash
npm run dev:mp-alipay
```

#### APP (iOS/Android)
```bash
npm run dev:app
```

---

## 📱 Platform Support (平台支持)

| 平台 | 状态 | 说明 |
|------|------|------|
| H5 | ✅ | 浏览器/手机浏览器 |
| 微信小程序 | ✅ | WeChat Mini Program |
| 支付宝小程序 | ✅ | Alipay Mini Program |
| APP | ✅ | iOS & Android |

---

## 📋 Features (功能特性)

### ✅ Completed (已完成)

1. **Multi-language System (多语言系统)** ✅
   - 🌐 vue-i18n 完整配置
   - 🔄 动态语言切换
   - 💾 语言偏好保存
   - 3种语言完整翻译

2. **Project Structure (项目结构)** ✅
   - 页面配置
   - TabBar 导航
   - 路由配置
   - 语言文件

### ⏳ Under Development (开发中)

1. **Home Page (首页)**
   - [ ] 商品轮播
   - [ ] 热销商品
   - [ ] 分类导航
   - [ ] 搜索功能

2. **Product (商品)**
   - [ ] 商品列表
   - [ ] 商品详情
   - [ ] 商品搜索
   - [ ] 加入购物车

3. **Cart (购物车)**
   - [ ] 购物车列表
   - [ ] 数量修改
   - [ ] 删除商品
   - [ ] 结算

4. **Order (订单)**
   - [ ] 订单列表
   - [ ] 订单详情
   - [ ] 支付
   - [ ] 确认收货

5. **Profile (个人中心)**
   - [ ] 用户登录
   - [ ] 个人信息
   - [ ] 地址管理
   - [ ] 语言设置

---

## 💻 Tech Stack (技术栈)

```json
{
  "Framework": "Uni-app (Vue 3 + TypeScript)",
  "Build Tool": "Vite",
  "i18n": "Vue I18n",
  "UI": "uni-ui",
  "State Management": "Pinia",
  "HTTP": "uni.request",
  "Platform": "H5 + WeChat + Alipay + APP"
}
```

---

## 📁 Project Structure (项目结构)

```
user-app/
├── src/
│   ├── locale/                    # 🌍 国际化
│   │   ├── index.ts              # i18n配置
│   │   ├── en.json               # 🇬🇧 英文
│   │   ├── zh.json               # 🇨🇳 中文
│   │   └── ms.json               # 🇲🇾 马来文
│   │
│   ├── pages/                     # 页面
│   │   ├── index/                # 首页
│   │   ├── category/             # 分类
│   │   ├── cart/                 # 购物车
│   │   ├── profile/              # 个人中心
│   │   ├── product/              # 商品详情
│   │   ├── order/                # 订单
│   │   └── login/                # 登录
│   │
│   ├── components/                # 组件
│   │   └── LanguageSwitcher/     # 语言切换器
│   │
│   ├── static/                    # 静态资源
│   │   └── tabbar/               # TabBar图标
│   │
│   ├── stores/                    # 状态管理
│   │   └── user.ts               # 用户状态
│   │
│   ├── utils/                     # 工具函数
│   │   ├── request.ts            # 请求封装
│   │   └── currency.ts           # 货币工具
│   │
│   ├── App.vue                    # 根组件
│   ├── main.ts                    # 入口文件
│   └── pages.json                 # 页面配置
│
├── package.json
├── vite.config.ts
├── tsconfig.json
└── README.md
```

---

## 🌐 i18n Usage (国际化使用)

### In Template (模板中)

```vue
<template>
  <view>
    <!-- Direct translation -->
    <text>{{ $t('common.loading') }}</text>
    
    <!-- With parameters -->
    <text>{{ $t('product.price') }}: $99.99</text>
  </view>
</template>
```

### In Script (脚本中)

```typescript
<script setup lang="ts">
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

// Get translation
const message = t('common.success')

// Switch language
locale.value = 'zh'  // 中文
locale.value = 'en'  // English
locale.value = 'ms'  // Bahasa Melayu
</script>
```

---

## 💵 Currency (货币)

**Default Currency**: USD ($)

All prices are displayed in USD:
- `$19.99`
- `$1,299.50`

---

## 📱 Pages Overview (页面概览)

### Tab Pages (底部导航页面)
- `/pages/index/index` - 首页 (Home)
- `/pages/category/category` - 分类 (Category)
- `/pages/cart/cart` - 购物车 (Cart)
- `/pages/profile/profile` - 个人中心 (Profile)

### Sub Pages (子页面)
- `/pages/product/detail` - 商品详情
- `/pages/order/list` - 订单列表
- `/pages/login/login` - 登录

---

## 🎨 UI Design (UI设计)

### Color Scheme (配色方案)
- Primary: `#409EFF`
- Success: `#67C23A`
- Warning: `#E6A23C`
- Danger: `#F56C6C`
- Text: `#333333`
- Border: `#EBEEF5`

### TabBar Icons (底部导航图标)
需要准备以下图标:
- `home.png` / `home-active.png`
- `category.png` / `category-active.png`
- `cart.png` / `cart-active.png`
- `profile.png` / `profile-active.png`

---

## 🔧 Configuration (配置)

### API Base URL

在 `utils/request.ts` 中配置:

```typescript
const baseURL = 'http://localhost:3000/api'
```

### Language Storage (语言存储)

使用 `uni.setStorageSync` 存储用户语言偏好:

```typescript
uni.setStorageSync('user-locale', 'en')
```

---

## 📦 Build (构建)

### Production Build (生产构建)

```bash
# H5
npm run build:h5

# 微信小程序
npm run build:mp-weixin

# 支付宝小程序
npm run build:mp-alipay

# APP
npm run build:app
```

构建后的文件在 `dist/build/` 目录

---

## 🎯 Development Roadmap (开发路线图)

### Phase 1: Foundation ✅
- [x] Multi-language setup
- [x] Project structure
- [x] Page configuration
- [x] TabBar navigation

### Phase 2: Core Features ⏳
- [ ] Home page
- [ ] Product list & detail
- [ ] Shopping cart
- [ ] Checkout
- [ ] Order management

### Phase 3: User Features ⏳
- [ ] User login/register
- [ ] Profile management
- [ ] Address management
- [ ] Payment integration

### Phase 4: Enhancement 📋
- [ ] Search
- [ ] Favorites
- [ ] Reviews
- [ ] Push notifications

---

## 🌟 Key Features (核心特性)

### 1. Cross-platform (跨平台)
- ✅ One codebase, multiple platforms
- ✅ H5 + Mini Programs + APP
- ✅ Native performance

### 2. Multi-language (多语言)
- ✅ 3 languages support
- ✅ Dynamic switching
- ✅ Persistent storage

### 3. Modern UI (现代化界面)
- ✅ Responsive design
- ✅ Touch-friendly
- ✅ Smooth animations

### 4. Type Safety (类型安全)
- ✅ TypeScript
- ✅ Strong typing
- ✅ IDE support

---

## 📖 Language Modules (语言模块)

用户端包含以下翻译模块:

- `tabBar` - 底部导航
- `common` - 通用词汇
- `home` - 首页
- `product` - 商品
- `cart` - 购物车
- `order` - 订单
- `profile` - 个人中心
- `login` - 登录
- `register` - 注册
- `address` - 地址
- `checkout` - 结算
- `message` - 消息提示

**总计**: 150+ 翻译键

---

## 🔗 API Integration (API集成)

### Request Example (请求示例)

```typescript
import { request } from '@/utils/request'

// Get products
const getProducts = () => {
  return request({
    url: '/products',
    method: 'GET'
  })
}

// Add to cart
const addToCart = (data: any) => {
  return request({
    url: '/cart',
    method: 'POST',
    data
  })
}
```

---

## 🐛 Common Issues (常见问题)

### Q1: 语言切换后小程序重启？

**A**: 正常现象，小程序语言切换需要重启生效。

### Q2: H5和小程序样式不一致？

**A**: 使用 uni-app 提供的条件编译处理平台差异。

### Q3: 如何调试小程序？

**A**: 使用对应平台的开发者工具（微信开发者工具/支付宝小程序开发者工具）。

---

## 📱 Test on Devices (设备测试)

### H5 Testing
```bash
npm run dev:h5
```
使用手机浏览器访问开发服务器地址

### Mini Program Testing
1. 运行 `npm run dev:mp-weixin`
2. 打开微信开发者工具
3. 导入项目 `dist/dev/mp-weixin`

---

## 🎉 Ready to Use! (可以使用了！)

The user app is ready with multi-language support!

用户端已准备就绪，支持多语言！

Aplikasi pengguna sudah siap dengan sokongan pelbagai bahasa!

---

**Last Updated**: 2025-10-04  
**Version**: 1.0.0  
**Languages**: English, 中文, Bahasa Melayu  
**Currency**: USD ($)  
**Platforms**: H5, WeChat, Alipay, APP

