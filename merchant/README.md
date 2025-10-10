# 🏪 Merchant Platform (商家端)

**International E-commerce Platform - Merchant Portal**

多语言商家管理平台 - 支持英文、中文、马来文

---

## 🌍 Multi-language Support (多语言支持)

### Supported Languages

- 🇬🇧 **English** - Full support
- 🇨🇳 **中文** - 完整支持
- 🇲🇾 **Bahasa Melayu** - Sokongan penuh

语言切换位置：
- 登录页右上角
- 系统顶部导航栏
- 设置页面

---

## 🚀 Quick Start (快速开始)

### 1. Install Dependencies (安装依赖)

```bash
npm install
```

### 2. Start Development Server (启动开发服务器)

```bash
npm run dev
```

访问: http://localhost:5174

### 3. Test Login (测试登录)

- **Username**: merchant001
- **Password**: 123456

---

## 📋 Features (功能特性)

### ✅ Completed (已完成)

1. **Multi-language System (多语言系统)**
   - 🌐 i18n 完整配置
   - 🔄 动态语言切换
   - 💾 语言偏好保存

2. **Authentication (认证系统)**
   - 登录页面
   - 注册页面
   - JWT Token管理

3. **Dashboard (控制台)**
   - 数据概览
   - 统计卡片
   - 欢迎信息

4. **Layout (布局)**
   - 响应式侧边栏
   - 多级菜单
   - 顶部导航
   - 语言切换器

### ⏳ Under Development (开发中)

1. **Product Management (商品管理)**
   - 我的商品
   - 从平台选品
   - 设置价格
   - 库存管理

2. **Order Management (订单管理)**
   - 待处理订单
   - 全部订单
   - 订单详情
   - 发货操作

3. **Financial Management (财务管理)**
   - 收益统计
   - 提现申请
   - 交易记录

4. **Shop Management (店铺管理)**
   - 店铺信息
   - 店铺装修

---

## 💻 Tech Stack (技术栈)

```json
{
  "Framework": "Vue 3.5 + TypeScript",
  "Build Tool": "Vite 7",
  "UI Library": "Element Plus 2.11",
  "Router": "Vue Router 4",
  "State Management": "Pinia 3",
  "i18n": "Vue I18n 9",
  "HTTP Client": "Axios",
  "Language": "TypeScript 5.9"
}
```

---

## 📁 Project Structure (项目结构)

```
merchant/
├── src/
│   ├── i18n/                      # 国际化配置
│   │   ├── index.ts              # i18n主配置
│   │   └── locales/              # 语言文件
│   │       ├── en.json           # 英文
│   │       ├── zh.json           # 中文
│   │       └── ms.json           # 马来文
│   │
│   ├── components/                # 公共组件
│   │   └── LanguageSwitcher.vue  # 语言切换器
│   │
│   ├── views/                     # 页面视图
│   │   ├── login/                # 登录
│   │   ├── register/             # 注册
│   │   ├── dashboard/            # 控制台
│   │   ├── products/             # 商品管理
│   │   ├── orders/               # 订单管理
│   │   ├── finance/              # 财务管理
│   │   ├── shop/                 # 店铺管理
│   │   └── settings/             # 设置
│   │
│   ├── layouts/                   # 布局组件
│   │   └── index.vue             # 主布局
│   │
│   ├── router/                    # 路由配置
│   │   └── index.ts
│   │
│   ├── stores/                    # 状态管理
│   │   └── merchant.ts           # 商家状态
│   │
│   ├── styles/                    # 全局样式
│   │   └── index.css
│   │
│   ├── App.vue                    # 根组件
│   └── main.ts                    # 入口文件
│
├── package.json
├── vite.config.ts
├── tsconfig.json
└── README.md
```

---

## 🌐 i18n Configuration (国际化配置)

### Adding New Language (添加新语言)

1. 在 `src/i18n/locales/` 创建新语言文件（如 `jp.json`）
2. 复制 `en.json` 的结构
3. 翻译所有文本
4. 在 `src/i18n/index.ts` 导入并注册

```typescript
import jp from './locales/jp.json'

const i18n = createI18n({
  messages: {
    en,
    zh,
    ms,
    jp  // 新增
  }
})

export const languages = [
  ...
  { code: 'jp', name: '日本語', flag: '🇯🇵' }
]
```

### Using i18n in Components (在组件中使用)

```vue
<template>
  <!-- 模板中 -->
  <div>{{ $t('common.welcome') }}</div>
  
  <!-- 插值 -->
  <div>{{ $t('validation.minLength', { min: 6 }) }}</div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

// 在脚本中
console.log(t('common.success'))

// 切换语言
locale.value = 'zh'
</script>
```

---

## 💵 Currency (货币)

**Default Currency**: USD ($)

所有价格显示格式:
- `$19.99`
- `$1,299.50` (带千位分隔符)

配置文件: `src/i18n/locales/*.json`
```json
{
  "currency": {
    "usd": "USD",
    "symbol": "$"
  }
}
```

---

## 🎨 UI Components (UI组件)

使用 Element Plus 组件库:

```vue
<el-button type="primary">{{ $t('common.submit') }}</el-button>
<el-table :data="tableData">...</el-table>
<el-form>...</el-form>
```

文档: https://element-plus.org

---

## 🔗 API Integration (API集成)

后端API地址配置在 `vite.config.ts`:

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

## 🧪 Build & Deploy (构建与部署)

### Development (开发)

```bash
npm run dev
```

### Build (构建)

```bash
npm run build
```

生成的文件在 `dist/` 目录

### Preview (预览)

```bash
npm run preview
```

---

## 📱 Pages Overview (页面概览)

### Public Pages (公开页面)
- `/login` - 登录页 (支持3种语言)
- `/register` - 注册页 (支持3种语言)

### Protected Pages (需要登录)
- `/dashboard` - 控制台
- `/products/my-products` - 我的商品
- `/products/select-products` - 选品上架
- `/orders/pending` - 待处理订单
- `/orders/all` - 全部订单
- `/finance/earnings` - 收益统计
- `/finance/withdraw` - 提现管理
- `/shop` - 店铺管理
- `/settings` - 设置

---

## 🎯 Development Roadmap (开发路线图)

### Phase 1: Foundation ✅
- [x] Multi-language i18n setup
- [x] Authentication pages
- [x] Layout & Navigation
- [x] Language switcher

### Phase 2: Core Features ⏳
- [ ] Product management
- [ ] Order processing
- [ ] Financial dashboard
- [ ] Shop settings

### Phase 3: Enhancement 📋
- [ ] Data visualization (Charts)
- [ ] Real-time notifications
- [ ] Advanced filtering
- [ ] Export functions

---

## 🌟 Key Features (核心特性)

### 1. Multi-language (多语言)
- ✅ Vue I18n integration
- ✅ 3 languages support
- ✅ Dynamic language switching
- ✅ Persistent language preference

### 2. Modern UI (现代化界面)
- ✅ Element Plus components
- ✅ Responsive design
- ✅ Beautiful gradients
- ✅ Smooth animations

### 3. Type Safety (类型安全)
- ✅ TypeScript throughout
- ✅ Strong typing
- ✅ IDE intellisense

### 4. Developer Experience (开发体验)
- ✅ Hot Module Replacement
- ✅ Fast Vite build
- ✅ Clear code structure

---

## 📖 Language Files (语言文件)

每个语言文件包含以下模块:

- `common` - 通用词汇
- `nav` - 导航菜单
- `login` - 登录相关
- `register` - 注册相关
- `dashboard` - 控制台
- `products` - 商品管理
- `orders` - 订单管理
- `finance` - 财务管理
- `shop` - 店铺管理
- `settings` - 设置
- `currency` - 货币
- `validation` - 验证信息
- `message` - 提示消息

---

## 🔧 Configuration (配置)

### Port (端口)
- Merchant Platform: `5174`
- Admin Platform: `5173`
- Backend API: `3000`

### Environment Variables (环境变量)

创建 `.env` 文件:

```env
VITE_API_BASE_URL=http://localhost:3000/api
VITE_APP_TITLE=Merchant Platform
```

---

## 👥 User Roles (用户角色)

1. **Merchant (商家)**
   - 选品上架
   - 设置价格
   - 处理订单
   - 财务管理

2. **Platform Admin (平台管理员)**
   - 使用 `admin` 项目
   - 审核商家
   - 管理商品库

3. **End User (终端用户)**
   - 使用用户端APP (待开发)
   - 浏览购买商品

---

## 📞 Support (支持)

### Documentation (文档)
- Project root: `PROJECT.md`
- API docs: `API_TEST.md`
- i18n guide: This file

### Language Switch (切换语言)
点击右上角的语言选择器:
- 🇬🇧 English
- 🇨🇳 中文
- 🇲🇾 Bahasa Melayu

---

## 🎉 Ready to Use! (可以使用了！)

The merchant platform is ready with full multi-language support!

商家端已准备就绪，完整支持多语言！

Platform peniaga sudah siap dengan sokongan pelbagai bahasa penuh!

---

**Last Updated**: 2025-10-04  
**Version**: 1.0.0  
**Languages**: English, 中文, Bahasa Melayu  
**Currency**: USD ($)
