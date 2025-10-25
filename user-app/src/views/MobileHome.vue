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
    
    <!-- Bottom Navigation -->
    <MobileBottomNav />
    
    <!-- Loading State -->
    <MobileLoading 
      :loading="isLoading"
      type="spinner"
      message="加载中..."
    />
    
    <!-- Error State -->
    <MobileError 
      :show="hasError"
      type="network"
      title="网络错误"
      message="无法连接到服务器，请检查网络连接"
      @retry="handleRetry"
    />
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage } from 'element-plus'
import MobileHeader from '@/components/layout/MobileHeader.vue'
import MobileCategories from '@/components/sections/MobileCategories.vue'
import MobileProductGrid from '@/components/products/MobileProductGrid.vue'
import MobileBottomBanner from '@/components/layout/MobileBottomBanner.vue'
import MobileBottomNav from '@/components/layout/MobileBottomNav.vue'
import MobileLoading from '@/components/common/MobileLoading.vue'
import MobileError from '@/components/common/MobileError.vue'
import { categoryApi, productApi } from '@/api'
import { useCategories } from '@/composables/useCategories'
import { useTopDeals } from '@/composables/useTopDeals'
import { usePopularItems } from '@/composables/usePopularItems'
import type { Category } from '@/composables/useCategories'
import type { TopDealProduct } from '@/composables/useTopDeals'
import type { PopularItem } from '@/composables/usePopularItems'

const router = useRouter()

// 状态管理
const isLoading = ref(false)
const hasError = ref(false)

// 使用 composables
const { categories, loadCategories } = useCategories()
const { topDeals, loading: topDealsLoading, loadTopDeals } = useTopDeals()
const { popularItems, loading: popularItemsLoading, loadPopularItems } = usePopularItems()

// 事件处理
const handleTabChange = (tabId: string) => {
  console.log('Tab changed to:', tabId)
  // 可以根据 tabId 筛选商品
}

const handleCategoryClick = (category: Category) => {
  console.log('Category clicked:', category)
  router.push(`/mobile/category/${category.id}`)
}

const handleProductClick = (product: TopDealProduct | PopularItem) => {
  console.log('Product clicked:', product)
  router.push(`/mobile/product/${product.id}`)
}

const handleRetry = () => {
  hasError.value = false
  loadData()
}

const loadData = async () => {
  try {
    isLoading.value = true
    hasError.value = false
    
    // 使用 composables 加载数据
    await Promise.all([
      loadCategories(),
      loadTopDeals(),
      loadPopularItems()
    ])
    
    console.log('移动端首页数据加载完成:', {
      categories: categories.value.length,
      topDeals: topDeals.value.length,
      popularItems: popularItems.value.length
    })
  } catch (error: any) {
    console.error('数据加载失败:', error)
    hasError.value = true
    ElMessage.error('数据加载失败')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  console.log('Mobile home page mounted')
  loadData()
})
</script>

<style scoped lang="scss">
.mobile-home {
  min-height: 100vh;
  background: #000;
  color: #fff;
  padding-bottom: 160px; // 为底部横幅和导航栏留出空间
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
