# 统一翻译管理系统

## 📋 概述

本项目实现了完全统一的翻译管理系统，支持中文、英文、马来文三种语言，覆盖前端用户界面、管理后台、商家后台和后端API。

## 🏗️ 系统架构

```
/root/TikShop/
├── shared-translations/          # 统一翻译库
│   ├── locales/                 # 翻译文件
│   │   ├── zh-CN.json          # 中文翻译
│   │   ├── en-US.json          # 英文翻译
│   │   └── ms-MY.json          # 马来文翻译
│   ├── constants/               # 常量定义
│   │   └── messages.ts         # 错误消息常量
│   ├── utils/                  # 工具函数
│   │   └── translations.ts     # 翻译工具函数
│   ├── config.ts               # 配置文件
│   └── index.ts               # 入口文件
├── user-app/                   # 用户前端
├── admin/                      # 管理后台
├── merchant/                   # 商家后台
└── ecommerce-backend/          # 后端API
```

## 🚀 功能特性

### ✅ 已实现功能

1. **统一翻译文件**: 所有模块使用相同的翻译键值对
2. **多语言支持**: 中文、英文、马来文
3. **状态常量**: 统一的商品状态、订单状态等
4. **错误消息**: 标准化的错误消息处理
5. **工具函数**: 便捷的翻译工具函数
6. **类型安全**: TypeScript类型定义

### 🔧 核心组件

#### 1. 翻译文件结构
```json
{
  "common": {
    "status": {
      "active": "上架",
      "inactive": "下架",
      "pending": "待处理"
    }
  },
  "errors": {
    "productNotFound": "商品不存在",
    "stockInsufficient": "库存不足"
  },
  "messages": {
    "success": {
      "productAdded": "商品添加成功"
    }
  }
}
```

#### 2. 错误消息常量
```typescript
export const ERROR_MESSAGES = {
  PRODUCT_NOT_FOUND: 'errors.productNotFound',
  STOCK_INSUFFICIENT: 'errors.stockInsufficient',
  // ...
} as const;
```

#### 3. 翻译工具函数
```typescript
// 基础翻译
t('common.status.active', 'zh-CN') // "上架"

// 状态翻译
translateStatus(1, 'zh-CN') // "上架"

// 错误翻译
translateError('productNotFound', 'zh-CN') // "商品不存在"
```

## 📖 使用方法

### 前端使用

#### Vue组件中
```vue
<template>
  <div>
    <el-tag :type="row.status === 1 ? 'success' : 'danger'">
      {{ row.status === 1 ? $t('common.status.active') : $t('common.status.inactive') }}
    </el-tag>
  </div>
</template>
```

#### JavaScript中
```typescript
import { t, translateStatus } from '@/shared-translations';

// 基础翻译
const text = t('common.status.active', 'zh-CN');

// 状态翻译
const statusText = translateStatus(1, 'zh-CN');
```

### 后端使用

#### 服务中
```typescript
import { ERROR_MESSAGES } from '../../../shared-translations/constants/messages';

// 抛出错误
throw new HttpException(ERROR_MESSAGES.PRODUCT_NOT_FOUND, HttpStatus.NOT_FOUND);
```

## 🔄 迁移指南

### 从硬编码迁移

#### 迁移前
```typescript
// 硬编码
throw new HttpException('商品不存在', HttpStatus.NOT_FOUND);
```

#### 迁移后
```typescript
// 使用常量
throw new HttpException(ERROR_MESSAGES.PRODUCT_NOT_FOUND, HttpStatus.NOT_FOUND);
```

### 状态显示迁移

#### 迁移前
```vue
<template>
  <el-tag>{{ row.status === 1 ? '上架' : '下架' }}</el-tag>
</template>
```

#### 迁移后
```vue
<template>
  <el-tag>{{ row.status === 1 ? $t('common.status.active') : $t('common.status.inactive') }}</el-tag>
</template>
```

## 🌍 语言支持

### 支持的语言

| 语言代码 | 语言名称 | 状态 |
|---------|---------|------|
| zh-CN   | 简体中文 | ✅ 完成 |
| en-US   | English | ✅ 完成 |
| ms-MY   | Bahasa Melayu | ✅ 完成 |

### 添加新语言

1. 在 `shared-translations/locales/` 创建新的翻译文件
2. 在 `shared-translations/utils/translations.ts` 中添加语言支持
3. 更新 `shared-translations/config.ts` 中的语言配置

## 🔧 维护指南

### 添加新的翻译键

1. 在 `shared-translations/locales/` 的所有语言文件中添加新键
2. 在 `shared-translations/constants/messages.ts` 中添加常量（如需要）
3. 更新相关组件使用新的翻译键

### 更新翻译内容

1. 直接编辑 `shared-translations/locales/` 中的翻译文件
2. 确保所有语言文件同步更新
3. 测试翻译显示效果

## 📊 系统优势

### ✅ 统一性
- 所有模块使用相同的翻译键
- 统一的错误消息处理
- 一致的状态显示

### ✅ 可维护性
- 集中管理所有翻译内容
- 易于添加新语言
- 类型安全的常量定义

### ✅ 可扩展性
- 支持动态添加新语言
- 灵活的翻译工具函数
- 模块化的架构设计

### ✅ 开发效率
- 减少重复的翻译工作
- 统一的开发规范
- 便捷的工具函数

## 🚀 部署说明

### 构建和部署

1. **用户前端**
```bash
cd /root/TikShop/user-app
npm run build
pm2 restart tikshop-user
```

2. **管理后台**
```bash
cd /root/TikShop/admin
npm run build
pm2 restart tikshop-admin
```

3. **商家后台**
```bash
cd /root/TikShop/merchant
npm run build
pm2 restart tikshop-merchant
```

4. **后端API**
```bash
cd /root/TikShop/ecommerce-backend
npm run build
pm2 restart tikshop-backend
```

## 📝 注意事项

1. **翻译键命名**: 使用点号分隔的层级结构，如 `common.status.active`
2. **常量使用**: 优先使用预定义的常量，避免硬编码
3. **语言同步**: 添加新翻译时确保所有语言文件同步更新
4. **类型安全**: 使用TypeScript类型定义确保类型安全

## 🔍 故障排除

### 常见问题

1. **翻译不显示**: 检查翻译键是否正确，语言文件是否存在
2. **类型错误**: 确保导入了正确的类型定义
3. **构建失败**: 检查翻译文件的JSON格式是否正确

### 调试方法

1. 使用浏览器开发者工具检查翻译键
2. 查看控制台错误信息
3. 检查翻译文件的JSON格式

---

**统一翻译管理系统** - 让多语言开发更简单、更高效！


