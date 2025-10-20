<template>
  <div class="mobile-home">
    <!-- Mobile Header -->
    <MobileHeader @tab-change="handleTabChange" />
    
    <!-- Categories Section -->
    <MobileCategories 
      :categories="categories" 
      @category-click="handleCategoryClick" 
    />
    
    <!-- Top Deals Section -->
    <MobileProductGrid
      title="Savings for you"
      :products="topDeals"
      :loading="topDealsLoading"
      @product-click="handleProductClick"
    />
    
    <!-- Popular Items Section -->
    <MobileProductGrid
      title="Popular items"
      :products="popularItems"
      :loading="popularItemsLoading"
      @product-click="handleProductClick"
    />
    
    <!-- Bottom Banner -->
    <MobileBottomBanner />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import MobileHeader from '@/components/layout/MobileHeader.vue'
import MobileCategories from '@/components/sections/MobileCategories.vue'
import MobileProductGrid from '@/components/products/MobileProductGrid.vue'
import MobileBottomBanner from '@/components/layout/MobileBottomBanner.vue'
import { useCategories } from '@/composables/useCategories'
import { useTopDeals } from '@/composables/useTopDeals'
import { usePopularItems } from '@/composables/usePopularItems'
import type { Category } from '@/composables/useCategories'
import type { TopDealProduct } from '@/composables/useTopDeals'
import type { PopularItem } from '@/composables/usePopularItems'

const router = useRouter()

// 使用 composables
const { categories } = useCategories()
const { topDeals, loading: topDealsLoading } = useTopDeals()
const { popularItems, loading: popularItemsLoading } = usePopularItems()

// 事件处理
const handleTabChange = (tabId: string) => {
  console.log('Tab changed to:', tabId)
  // 可以根据 tabId 筛选商品
}

const handleCategoryClick = (category: Category) => {
  console.log('Category clicked:', category)
  router.push(`/category/${category.id}`)
}

const handleProductClick = (product: TopDealProduct | PopularItem) => {
  console.log('Product clicked:', product)
  router.push(`/product/${product.id}`)
}

onMounted(() => {
  // 页面加载完成后的初始化逻辑
  console.log('Mobile home page mounted')
})
</script>

<style scoped lang="scss">
.mobile-home {
  min-height: 100vh;
  background: #000;
  color: #fff;
  padding-bottom: 80px; // 为底部横幅留出空间
}

// 全局移动端样式
:global(body) {
  background: #000;
  color: #fff;
  margin: 0;
  padding: 0;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

:global(*) {
  box-sizing: border-box;
}

// 滚动条样式
:global(::-webkit-scrollbar) {
  width: 4px;
}

:global(::-webkit-scrollbar-track) {
  background: rgba(255, 255, 255, 0.1);
}

:global(::-webkit-scrollbar-thumb) {
  background: rgba(255, 0, 80, 0.5);
  border-radius: 2px;
}

:global(::-webkit-scrollbar-thumb:hover) {
  background: rgba(255, 0, 80, 0.7);
}

// 响应式设计
@media (max-width: 768px) {
  .mobile-home {
    padding-bottom: 70px;
  }
}

@media (max-width: 480px) {
  .mobile-home {
    padding-bottom: 60px;
  }
}
</style>
