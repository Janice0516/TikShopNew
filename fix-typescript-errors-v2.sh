#!/bin/bash

# 修复商家后台TypeScript错误的脚本 - 第二版

echo "🔧 修复商家后台TypeScript编译错误 - 第二版..."

# 修复所有文件中的res变量问题
echo "修复res变量问题..."

# 修复orders/pending.vue
sed -i '' 's/await getMerchantOrders(params)/const res = await getMerchantOrders(params)/g' merchant/src/views/orders/pending.vue

# 修复products/my-products.vue
sed -i '' 's/await getMerchantProducts(params)/const res = await getMerchantProducts(params)/g' merchant/src/views/products/my-products.vue

# 修复products/select-products.vue
sed -i '' 's/await getProducts(params)/const res = await getProducts(params)/g' merchant/src/views/products/select-products.vue
sed -i '' 's/await getCategories()/const res = await getCategories()/g' merchant/src/views/products/select-products.vue

# 修复shop/index.vue
sed -i '' 's/await getShopInfo()/const res = await getShopInfo()/g' merchant/src/views/shop/index.vue
sed -i '' 's/await updateShopInfo(shopForm)/const res = await updateShopInfo(shopForm)/g' merchant/src/views/shop/index.vue
sed -i '' 's/await updateShopAnnouncement(announcementForm)/const res = await updateShopAnnouncement(announcementForm)/g' merchant/src/views/shop/index.vue
sed -i '' 's/await uploadShopImage(file)/const res = await uploadShopImage(file)/g' merchant/src/views/shop/index.vue

# 修复withdrawal/index.vue
sed -i '' 's/await getWithdrawHistory(params)/const res = await getWithdrawHistory(params)/g' merchant/src/views/withdrawal/index.vue
sed -i '' 's/await getWithdrawHistory(params)/const res = await getWithdrawHistory(params)/g' merchant/src/views/withdrawal/index.vue

# 修复orders/all.vue中的res问题
sed -i '' 's/await getMerchantOrders({ page: 1, pageSize: 1000 })/const res = await getMerchantOrders({ page: 1, pageSize: 1000 })/g' merchant/src/views/orders/all.vue
sed -i '' 's/await getMerchantOrders(params)/const res = await getMerchantOrders(params)/g' merchant/src/views/orders/all.vue

# 修复orders/detail.vue中的res问题
sed -i '' 's/await getOrderDetail(parseInt(orderId))/const res = await getOrderDetail(parseInt(orderId))/g' merchant/src/views/orders/detail.vue

# 修复withdraw.vue中的minWithdrawalAmount问题
sed -i '' 's/minWithdrawalAmount = res.data.data.minWithdrawalAmount/minWithdrawalAmount.value = res.data.data.minWithdrawalAmount/g' merchant/src/views/finance/withdraw.vue

echo "✅ TypeScript错误修复完成！"
