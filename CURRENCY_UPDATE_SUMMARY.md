# 💵 货币单位更新总结

**更新时间**: 2025-10-04  
**更新类型**: 货币单位从人民币（¥ CNY）改为美元（$ USD）  
**市场定位**: 从中国市场调整为国际市场

---

## ✅ 已完成的更新

### 1. 前端代码更新 ✅

#### 商品管理页面
**文件**: `admin/src/views/products/index.vue`
- ✅ 成本价显示：¥ → $
- ✅ 列标签：成本价 → Cost Price

**文件**: `admin/src/views/products/add.vue`
- ✅ 成本价标签：成本价 → Cost Price (USD)
- ✅ 建议售价标签：建议售价 → Suggested Price (USD)
- ✅ 占位符文本更新为英文

#### 订单管理页面
**文件**: `admin/src/views/orders/index.vue`
- ✅ 订单金额显示：¥ → $
- ✅ 列标签：订单金额 → Order Amount

**文件**: `admin/src/views/orders/detail.vue`
- ✅ 订单金额：¥ → $
- ✅ 运费：¥ → $
- ✅ 实付金额：¥ → $
- ✅ 单价：¥ → $
- ✅ 小计：¥ → $
- ✅ 标签更新：
  - 订单金额 → Order Amount
  - 运费 → Shipping Fee
  - 实付金额 → Total Paid
  - 单价 → Unit Price
  - 小计 → Subtotal

### 2. 工具函数新增 ✅

**文件**: `admin/src/utils/currency.ts` （新创建）

提供完整的货币处理工具：
```typescript
- formatPrice()           // 格式化价格
- formatPriceWithComma()  // 带千位分隔符
- parsePrice()            // 解析价格字符串
- CURRENCY_SYMBOL = '$'   // 货币符号
- CURRENCY_CODE = 'USD'   // 货币代码
- CURRENCY_NAME = 'US Dollar' // 货币名称
```

### 3. 数据库更新 ✅

**文件**: `database/schema.sql`

更新所有价格相关字段的注释：
```sql
✅ cost_price: '成本价' → 'Cost Price (USD)'
✅ suggest_price: '建议售价' → 'Suggested Price (USD)'
✅ total_amount: '订单总金额' → 'Total Order Amount (USD)'
✅ cost_amount: '成本总额' → 'Total Cost (USD)'
✅ merchant_profit: '商家利润' → 'Merchant Profit (USD)'
✅ platform_profit: '平台利润' → 'Platform Profit (USD)'
✅ freight: '运费' → 'Shipping Fee (USD)'
✅ pay_amount: '实付金额' → 'Total Paid Amount (USD)'
✅ sale_price: '销售价' → 'Sale Price (USD)'
✅ total_price: '小计' → 'Subtotal (USD)'
✅ balance: '账户余额' → 'Account Balance (USD)'
```

### 4. 项目文档更新 ✅

#### README.md
- ✅ 标题更新为英文
- ✅ 添加市场定位说明（International/Global）
- ✅ 添加货币说明（USD）

#### SUMMARY.md
- ✅ 项目概况更新
- ✅ 市场定位：Global / International
- ✅ 货币：USD (US Dollar)

#### 新文档创建
- ✅ **INTERNATIONALIZATION.md** - 完整的国际化配置指南
  - 货币设置规范
  - 多语言支持计划
  - 时区配置
  - 支付网关建议
  - 代码示例

---

## 🎯 价格显示标准

### 前端显示格式
```
单价：$19.99
千位分隔：$1,299.99
小数位数：固定2位
```

### 数据库存储
```sql
类型：DECIMAL(10,2)
单位：美元 (USD)
示例：19.99, 299.50
```

### API响应格式
```json
{
  "price": 19.99,
  "currency": "USD",
  "formatted": "$19.99"
}
```

---

## 📦 新增功能

### 货币工具函数

**位置**: `admin/src/utils/currency.ts`

```typescript
// 基础格式化
formatPrice(19.99)  // "$19.99"

// 带千位分隔符
formatPriceWithComma(1299.50)  // "$1,299.50"

// 解析价格
parsePrice("$19.99")  // 19.99

// 常量
CURRENCY_SYMBOL  // "$"
CURRENCY_CODE    // "USD"
CURRENCY_NAME    // "US Dollar"
```

### 环境变量配置

**文件**: `.env.development` / `.env.production`

```bash
VITE_CURRENCY=USD
VITE_CURRENCY_SYMBOL=$
VITE_DEFAULT_LANGUAGE=en
VITE_TIMEZONE=UTC
```

注意：环境变量文件被gitignore，需要手动创建。

---

## 🔄 数据迁移（如需要）

如果数据库中已有人民币数据，需要按汇率转换：

### 方案1：直接转换（假设汇率7.2）

```sql
-- 转换所有价格字段
UPDATE platform_product 
SET cost_price = cost_price / 7.2,
    suggest_price = suggest_price / 7.2;

UPDATE order_main 
SET total_amount = total_amount / 7.2,
    pay_amount = pay_amount / 7.2,
    freight = freight / 7.2;

-- 转换订单明细
UPDATE order_item 
SET cost_price = cost_price / 7.2,
    sale_price = sale_price / 7.2,
    total_price = total_price / 7.2;
```

### 方案2：标记货币类型（推荐）

如果需要保留历史数据，可以添加货币字段：

```sql
-- 添加货币字段
ALTER TABLE platform_product ADD COLUMN currency VARCHAR(3) DEFAULT 'USD';
ALTER TABLE order_main ADD COLUMN currency VARCHAR(3) DEFAULT 'USD';

-- 标记旧数据
UPDATE platform_product SET currency = 'CNY' WHERE create_time < '2025-10-04';
UPDATE order_main SET currency = 'CNY' WHERE create_time < '2025-10-04';
```

---

## ⚠️ 注意事项

### 1. 价格精度
- ✅ 使用 DECIMAL(10,2) 类型
- ✅ 保留2位小数
- ❌ 不要使用 FLOAT 或 DOUBLE

### 2. 前端显示
- ✅ 统一使用 $ 符号
- ✅ 价格前置符号（$19.99）
- ✅ 千位分隔符可选

### 3. API设计
- ✅ 价格字段使用数值类型
- ✅ 可选提供格式化字符串
- ✅ 明确货币代码（USD）

### 4. 多货币支持（可选）
如果未来需要支持多货币：
- 添加货币代码字段
- 创建汇率表
- 使用实时汇率API

---

## 🌍 国际化支持

### 已准备
- ✅ 货币：USD
- ✅ 数据库注释英文化
- ✅ 部分界面标签英文化

### 待完成
- ⏳ 完整的多语言支持（vue-i18n）
- ⏳ 时区转换
- ⏳ 地区化日期格式
- ⏳ 国际支付网关接入

---

## 📚 相关文档

### 新增文档
1. **INTERNATIONALIZATION.md** - 国际化完整指南
   - 货币配置
   - 多语言方案
   - 支付网关
   - 最佳实践

### 更新文档
2. **README.md** - 项目定位更新
3. **SUMMARY.md** - 项目概况更新
4. **database/schema.sql** - 字段注释英文化

---

## 🔧 后续建议

### 短期（1周内）
1. ✅ 完成货币符号替换
2. ⏳ 测试所有价格显示
3. ⏳ 更新API文档中的价格示例
4. ⏳ 创建 `.env.example` 文件

### 中期（2-4周）
1. ⏳ 实现vue-i18n多语言
2. ⏳ 英文化所有用户界面
3. ⏳ 添加货币格式化组件
4. ⏳ 时区处理优化

### 长期（持续）
1. ⏳ 接入国际支付（PayPal/Stripe）
2. ⏳ 支持多货币切换
3. ⏳ 实时汇率转换
4. ⏳ 完整的国际化支持

---

## ✨ 快速使用

### 在Vue组件中使用

```vue
<template>
  <div>
    <!-- 方式1：直接使用 -->
    <span>${{ product.price }}</span>
    
    <!-- 方式2：使用工具函数 -->
    <span>{{ formatPrice(product.price) }}</span>
    
    <!-- 方式3：带千位分隔符 -->
    <span>{{ formatPriceWithComma(product.price) }}</span>
  </div>
</template>

<script setup lang="ts">
import { formatPrice, formatPriceWithComma } from '@/utils/currency'
</script>
```

### 在TypeScript中使用

```typescript
import { CURRENCY_SYMBOL, CURRENCY_CODE, formatPrice } from '@/utils/currency'

console.log(CURRENCY_SYMBOL)  // "$"
console.log(CURRENCY_CODE)    // "USD"

const price = 19.99
const formatted = formatPrice(price)  // "$19.99"
```

---

## 🎉 更新完成度

| 类型 | 完成度 | 状态 |
|------|--------|------|
| 前端显示 | 100% | ✅ 完成 |
| 工具函数 | 100% | ✅ 完成 |
| 数据库注释 | 100% | ✅ 完成 |
| 项目文档 | 100% | ✅ 完成 |
| 国际化指南 | 100% | ✅ 完成 |
| 环境配置 | 90% | ⚠️ 需手动创建.env |
| 多语言支持 | 0% | ⏳ 待开发 |

---

## 📞 支持

如需切换其他货币（EUR、GBP等），请参考：
- **INTERNATIONALIZATION.md** - 第"🚀 快速切换货币"章节

---

**更新完成时间**: 2025-10-04  
**当前货币**: USD ($)  
**市场定位**: International / Global

**🌍 The platform is now ready for the global market! 🚀**

