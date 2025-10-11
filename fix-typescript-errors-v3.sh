#!/bin/bash

# 修复商家后台TypeScript错误的脚本 - 第三版

echo "🔧 修复商家后台TypeScript编译错误 - 第三版..."

# 修复credit-rating/index.vue中的res问题
echo "修复credit-rating/index.vue中的res问题..."
sed -i '' 's/await getMerchantCurrentRating()/const res = await getMerchantCurrentRating()/g' merchant/src/views/credit-rating/index.vue
sed -i '' 's/await getMerchantRatingHistory(params)/const res = await getMerchantRatingHistory(params)/g' merchant/src/views/credit-rating/index.vue

# 修复finance/earnings.vue中的res问题
echo "修复finance/earnings.vue中的res问题..."
sed -i '' 's/await getFinanceStats()/const res = await getFinanceStats()/g' merchant/src/views/finance/earnings.vue
sed -i '' 's/await getFundFlow(params)/const res = await getFundFlow(params)/g' merchant/src/views/finance/earnings.vue

# 修复finance/withdraw.vue中的res问题
echo "修复finance/withdraw.vue中的res问题..."
sed -i '' 's/await getWithdrawHistory(params)/const res = await getWithdrawHistory(params)/g' merchant/src/views/finance/withdraw.vue

# 修复shop/index.vue中的res问题
echo "修复shop/index.vue中的res问题..."
sed -i '' 's/await uploadShopImage(file)/const res = await uploadShopImage(file)/g' merchant/src/views/shop/index.vue

# 修复withdrawal/index.vue中的类型比较问题
echo "修复withdrawal/index.vue中的类型比较问题..."
sed -i '' 's/withdrawForm.amount < minWithdrawalAmount/withdrawForm.amount < minWithdrawalAmount.value/g' merchant/src/views/withdrawal/index.vue

echo "✅ TypeScript错误修复完成！"
