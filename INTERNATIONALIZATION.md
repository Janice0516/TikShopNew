# 🌍 国际化配置说明

## 项目定位

**目标市场**: 国际市场  
**主要货币**: 美元 (USD)  
**语言**: 英文为主，支持多语言扩展

---

## 💵 货币设置

### 当前货币配置

```json
{
  "currency": "USD",
  "symbol": "$",
  "name": "US Dollar",
  "decimal_places": 2
}
```

### 价格显示规范

#### 前端显示
- **格式**: `$99.99`
- **千位分隔符**: `$1,299.99`
- **小数位数**: 固定2位小数

#### 数据库存储
- **类型**: `DECIMAL(10,2)`
- **单位**: 美元 (USD)
- **示例**: 
  - 成本价: `19.99`
  - 售价: `29.99`
  - 订单金额: `199.99`

#### API返回
```json
{
  "cost_price": 19.99,
  "sale_price": 29.99,
  "currency": "USD"
}
```

---

## 🗣️ 语言支持

### 当前支持语言
- ✅ **英语** (English) - 主要语言
- 🔲 **中文** (Chinese) - 待扩展
- 🔲 **西班牙语** (Spanish) - 待扩展
- 🔲 **法语** (French) - 待扩展

### 前端国际化

#### 使用工具
建议使用 `vue-i18n` 进行多语言支持

#### 安装
```bash
npm install vue-i18n
```

#### 配置示例
```typescript
// src/i18n/index.ts
import { createI18n } from 'vue-i18n'
import en from './locales/en.json'
import zh from './locales/zh.json'

const i18n = createI18n({
  locale: 'en', // 默认语言
  fallbackLocale: 'en',
  messages: {
    en,
    zh
  }
})

export default i18n
```

#### 语言文件示例
```json
// locales/en.json
{
  "product": {
    "name": "Product Name",
    "price": "Price",
    "stock": "Stock"
  },
  "order": {
    "total": "Total Amount",
    "shipping": "Shipping Fee"
  }
}

// locales/zh.json
{
  "product": {
    "name": "商品名称",
    "price": "价格",
    "stock": "库存"
  },
  "order": {
    "total": "订单金额",
    "shipping": "运费"
  }
}
```

---

## 🌐 地区设置

### 时区
- **默认时区**: UTC
- **显示格式**: 转换为用户本地时间

### 日期格式
- **美国格式**: MM/DD/YYYY
- **国际格式**: YYYY-MM-DD
- **时间格式**: 24小时制或12小时制（可配置）

### 示例
```typescript
// 格式化日期
const formatDate = (date: Date, locale: string = 'en-US') => {
  return new Intl.DateTimeFormat(locale, {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}
```

---

## 📦 物流配置

### 国际运输
- ✅ 支持全球配送
- ✅ 多种运输方式
  - 标准运输 (Standard Shipping)
  - 快速运输 (Express Shipping)
  - 经济运输 (Economy Shipping)

### 运费计算
```typescript
// 基于重量和目的地计算运费
interface ShippingRate {
  country: string;
  basePrice: number; // USD
  perKg: number;     // USD per kg
}

const calculateShipping = (
  weight: number, 
  country: string, 
  method: string
): number => {
  // 运费计算逻辑
  const rate = getShippingRate(country, method)
  return rate.basePrice + (weight * rate.perKg)
}
```

---

## 💳 支付网关

### 支持的支付方式

#### 国际支付
- 🔲 **PayPal** - 待接入
- 🔲 **Stripe** - 待接入
- 🔲 **Square** - 待接入

#### 信用卡
- 🔲 Visa
- 🔲 MasterCard
- 🔲 American Express

#### 数字钱包
- 🔲 Apple Pay
- 🔲 Google Pay

### 货币转换
如果需要支持多货币，建议使用实时汇率API：
- Open Exchange Rates
- CurrencyLayer
- Fixer.io

---

## 🛠️ 配置文件

### 环境变量
```bash
# .env
VITE_CURRENCY=USD
VITE_CURRENCY_SYMBOL=$
VITE_DEFAULT_LANGUAGE=en
VITE_TIMEZONE=UTC
```

### 配置文件
```typescript
// src/config/i18n.config.ts
export default {
  currency: {
    code: 'USD',
    symbol: '$',
    decimals: 2
  },
  language: {
    default: 'en',
    supported: ['en', 'zh', 'es', 'fr']
  },
  timezone: {
    default: 'UTC',
    display: 'America/New_York'
  }
}
```

---

## 📊 数据库字段说明

### 价格相关字段
所有价格字段统一使用美元 (USD) 存储：

| 表名 | 字段名 | 类型 | 说明 |
|-----|--------|------|------|
| platform_product | cost_price | DECIMAL(10,2) | 成本价（美元） |
| platform_product | suggest_price | DECIMAL(10,2) | 建议售价（美元） |
| order_main | total_amount | DECIMAL(10,2) | 订单金额（美元） |
| order_main | freight | DECIMAL(10,2) | 运费（美元） |
| order_item | sale_price | DECIMAL(10,2) | 销售价（美元） |

---

## 🔄 货币转换（可选）

如果未来需要支持多货币，可以添加转换表：

```sql
CREATE TABLE `currency_rate` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `from_currency` VARCHAR(3) NOT NULL COMMENT '源货币代码',
  `to_currency` VARCHAR(3) NOT NULL COMMENT '目标货币代码',
  `rate` DECIMAL(10,6) NOT NULL COMMENT '汇率',
  `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_currency_pair` (`from_currency`, `to_currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='货币汇率表';

-- 示例数据
INSERT INTO currency_rate (from_currency, to_currency, rate) VALUES
('USD', 'EUR', 0.92),
('USD', 'GBP', 0.79),
('USD', 'CNY', 7.24),
('USD', 'JPY', 149.50);
```

---

## 📝 代码示例

### 货币格式化工具
```typescript
// src/utils/currency.ts
export function formatPrice(
  price: number, 
  currency: string = 'USD'
): string {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: currency
  }).format(price)
}

// 使用示例
formatPrice(19.99)  // "$19.99"
formatPrice(1299.50) // "$1,299.50"
```

### API响应示例
```typescript
// 商品价格响应
{
  "id": 1,
  "name": "Product Name",
  "price": {
    "amount": 29.99,
    "currency": "USD",
    "formatted": "$29.99"
  },
  "shipping": {
    "amount": 5.99,
    "currency": "USD",
    "formatted": "$5.99"
  }
}
```

---

## 🎯 最佳实践

### 1. 价格精度
- 始终使用 `DECIMAL` 类型存储价格
- 避免使用 `FLOAT` 或 `DOUBLE`
- 保留2位小数

### 2. 货币符号
- 前端显示使用本地化格式
- 数据库只存储数值
- API返回同时包含数值和格式化字符串

### 3. 时区处理
- 数据库存储使用UTC时间
- API返回ISO 8601格式
- 前端转换为用户本地时间

### 4. 多语言
- 使用i18n库管理翻译
- 避免硬编码文本
- 所有用户可见文本都应支持翻译

---

## 🚀 快速切换货币（开发指南）

如果将来需要切换货币（如切换到欧元EUR），按以下步骤操作：

### 1. 更新配置
```typescript
// src/utils/currency.ts
export const CURRENCY_SYMBOL = '€'  // 改为 €
export const CURRENCY_CODE = 'EUR'  // 改为 EUR
```

### 2. 全局替换
```bash
# 替换所有 $ 为 €
find ./src -type f -name "*.vue" -exec sed -i '' 's/\$/€/g' {} +
```

### 3. 更新文档
更新所有文档中的货币说明

### 4. 数据迁移（如需要）
如果需要转换现有数据：
```sql
-- 按汇率转换价格（示例：USD转EUR，汇率0.92）
UPDATE platform_product SET cost_price = cost_price * 0.92;
UPDATE platform_product SET suggest_price = suggest_price * 0.92;
```

---

## 📚 相关资源

- [ISO 4217 货币代码](https://www.iso.org/iso-4217-currency-codes.html)
- [Vue i18n 文档](https://vue-i18n.intlify.dev/)
- [Intl.NumberFormat MDN](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/NumberFormat)
- [Stripe 国际化最佳实践](https://stripe.com/docs/currencies)

---

**最后更新**: 2025-10-04  
**货币**: USD ($)  
**市场定位**: Global / International

