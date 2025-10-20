<template>
  <div class="tiktok-shop">
    <!-- 顶部导航栏 -->
    <AppHeader />

    <div class="main-layout">
      <!-- 左侧边栏 -->
      <AppSidebar />

      <!-- 主内容区域 -->
      <main class="main-content">
        <!-- 分类区域 -->
        <section id="categories">
        <CategorySection
          :categories="categories"
          @category-click="handleCategoryClick"
        />
        </section>

        <!-- Top Deals 区域 -->
        <ProductCarousel
          title="Top deals for you"
          :products="topDeals"
          :loading="topDealsLoading"
          @product-click="handleProductClick"
        />

        <!-- Popular Items 区域 -->
        <ProductCarousel
          title="Popular items"
          :products="popularItems"
          :loading="popularItemsLoading"
          @product-click="handleProductClick"
        />

        <!-- 优惠商品区域 -->
        <section id="products">
        <ProductGrid
          title="Savings for you"
          :products="products"
          :loading="loading"
          :show-pagination="true"
          :current-page="pagination.current"
          :total-pages="pagination.totalPages"
          @product-click="handleProductClick"
          @page-change="handlePageChange"
        />
        </section>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AppHeader from '@/components/layout/AppHeader.vue'
import AppSidebar from '@/components/layout/AppSidebar.vue'
import CategorySection from '@/components/sections/CategorySection.vue'
import ProductCarousel from '@/components/sections/ProductCarousel.vue'
import ProductGrid from '@/components/products/ProductGrid.vue'
import { useCategories } from '@/composables/useCategories'
import { useProducts } from '@/composables/useProducts'
import { useTopDeals } from '@/composables/useTopDeals'
import { usePopularItems } from '@/composables/usePopularItems'
import type { Category, Product } from '@/composables/useProducts'

const router = useRouter()

// 检测是否为移动设备
const isMobile = () => {
  return window.innerWidth <= 768 || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
}

// 使用分类 composable
const { categories } = useCategories()

// 使用商品 composable
const { products, loading, pagination, loadProducts } = useProducts()

// 使用 Top Deals composable
const { topDeals, loading: topDealsLoading } = useTopDeals()

// 使用 Popular Items composable
const { popularItems, loading: popularItemsLoading } = usePopularItems()

// 处理分类点击
const handleCategoryClick = (category: Category) => {
  console.log('分类点击:', category)
  // 可以跳转到分类页面或筛选商品
  router.push(`/category/${category.id}`)
}

// 处理商品点击
const handleProductClick = (product: Product) => {
  console.log('商品点击:', product)
  router.push(`/product/${product.id}`)
}

// 处理分页变化
const handlePageChange = (page: number) => {
  loadProducts(page, pagination.value.pageSize)
}

// 页面加载时获取商品
onMounted(() => {
  // 检测移动设备并自动跳转
  if (isMobile()) {
    router.push('/mobile')
    return
  }
  
  loadProducts(1, 10)
})
</script>

<style scoped lang="scss">
.tiktok-shop {
  min-height: 100vh;
  background: #f8f9fa;
}

.main-layout {
  display: flex;
  min-height: calc(100vh - 60px);
  margin-top: 60px; // 为固定头部留出空间
}

.main-content {
  flex: 1;
  margin-left: 200px; // 为侧边栏留出空间
  padding: 20px;
  background: #f8f9fa;
  min-height: calc(100vh - 60px);
  overflow-y: auto;
}

/* 页脚锚点占位，供“更多”按钮滚动使用 */
:global(footer), #page-footer { min-height: 1px; }

// 响应式设计
@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
    padding: 16px;
  }
  
  .main-layout {
    flex-direction: column;
  }
}
</style>
