<template>
  <section class="mobile-products-section">
    <h2 class="section-title">{{ title }}</h2>
    
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p>Loading products...</p>
    </div>
    
    <div v-else-if="products.length === 0" class="empty-container">
      <p>No products found</p>
    </div>
    
    <div v-else class="products-container">
      <div class="products-scroll" ref="productsScrollRef">
        <MobileProductCard
          v-for="product in products"
          :key="product.id"
          :product="product"
          @click="handleProductClick"
        />
      </div>
      
      <!-- 滚动箭头 -->
      <button class="scroll-arrow left" @click="scrollProducts('left')" v-if="showLeftArrow">
        <el-icon><ArrowLeft /></el-icon>
      </button>
      <button class="scroll-arrow right" @click="scrollProducts('right')" v-if="showRightArrow">
        <el-icon><ArrowRight /></el-icon>
      </button>
    </div>
    
    <!-- Pagination for mobile -->
    <div v-if="showPagination && pagination.totalPages > 1" class="mobile-pagination">
      <el-pagination
        v-model:current-page="pagination.current"
        :total="pagination.total"
        :page-size="pagination.pageSize"
        layout="prev, pager, next"
        small
        @current-change="handlePageChange"
      />
    </div>
  </section>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, ref, onMounted, onUnmounted } from 'vue'
import { ArrowLeft, ArrowRight } from '@element-plus/icons-vue'
import MobileProductCard from '../products/MobileProductCard.vue'

interface Product {
  id: string | number
  name: string
  description?: string
  price: number
  originalPrice?: number | null
  image: string
  rating: number
  sales: number
  stock: number
  brand?: string
  merchantName?: string
}

interface Pagination {
  current: number
  pageSize: number
  total: number
  totalPages: number
}

const props = defineProps<{
  title: string
  products: Product[]
  loading?: boolean
  showPagination?: boolean
  pagination?: Pagination
}>()

const emit = defineEmits<{
  productClick: [product: Product]
  pageChange: [page: number]
}>()

const productsScrollRef = ref<HTMLElement | null>(null)
const showLeftArrow = ref(false)
const showRightArrow = ref(true)

const handleProductClick = (product: Product) => {
  emit('productClick', product)
}

const handlePageChange = (page: number) => {
  emit('pageChange', page)
}

const scrollProducts = (direction: 'left' | 'right') => {
  if (productsScrollRef.value) {
    const scrollAmount = 300 // 滚动距离
    if (direction === 'left') {
      productsScrollRef.value.scrollBy({ left: -scrollAmount, behavior: 'smooth' })
    } else {
      productsScrollRef.value.scrollBy({ left: scrollAmount, behavior: 'smooth' })
    }
  }
}

const updateArrowVisibility = () => {
  if (productsScrollRef.value) {
    const { scrollLeft, scrollWidth, clientWidth } = productsScrollRef.value
    showLeftArrow.value = scrollLeft > 0
    showRightArrow.value = scrollLeft < scrollWidth - clientWidth - 10
  }
}

onMounted(() => {
  if (productsScrollRef.value) {
    productsScrollRef.value.addEventListener('scroll', updateArrowVisibility)
    updateArrowVisibility()
  }
})

onUnmounted(() => {
  if (productsScrollRef.value) {
    productsScrollRef.value.removeEventListener('scroll', updateArrowVisibility)
  }
})
</script>

<style scoped lang="scss">
.mobile-products-section {
  background: #000;
  color: #fff;
  padding: 20px 0;
}

.section-title {
  font-size: 18px;
  font-weight: 700;
  color: #fff;
  margin: 0 0 16px 16px;
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 0;
  
  .loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-top: 3px solid #ff0050;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 16px;
  }
  
  p {
    color: rgba(255, 255, 255, 0.7);
    font-size: 14px;
  }
}

.empty-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 40px 0;
  
  p {
    color: rgba(255, 255, 255, 0.7);
    font-size: 14px;
  }
}

.products-container {
  position: relative;
  display: flex;
  align-items: center;
}

.products-scroll {
  display: flex;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch; /* iOS 平滑滚动 */
  scroll-behavior: smooth;
  padding: 0 16px;
  gap: 16px;
  
  /* 隐藏滚动条 */
  &::-webkit-scrollbar {
    display: none;
  }
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}

.scroll-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0, 0, 0, 0.7);
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  z-index: 10;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
  
  &:hover {
    background: rgba(255, 0, 80, 0.8);
    transform: translateY(-50%) scale(1.1);
  }
  
  &.left {
    left: 8px;
  }
  
  &.right {
    right: 8px;
  }
  
  .el-icon {
    font-size: 18px;
  }
}

.mobile-pagination {
  margin-top: 24px;
  display: flex;
  justify-content: center;
  
  :deep(.el-pagination) {
    --el-pagination-font-size: 14px;
    --el-pagination-button-color: #fff;
    --el-pagination-button-bg-color: rgba(255, 255, 255, 0.1);
    --el-pagination-button-border-color: rgba(255, 255, 255, 0.2);
    --el-pagination-hover-color: #ff0050;
    --el-pagination-bg-color: rgba(255, 255, 255, 0.1);
    --el-pagination-text-color: #fff;
  }
  
  :deep(.el-pagination .btn-prev),
  :deep(.el-pagination .btn-next) {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    color: #fff;
    
    &:hover {
      background: rgba(255, 0, 80, 0.2);
      border-color: #ff0050;
      color: #ff0050;
    }
  }
  
  :deep(.el-pagination .el-pager li) {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    color: #fff;
    
    &:hover {
      background: rgba(255, 0, 80, 0.2);
      border-color: #ff0050;
      color: #ff0050;
    }
    
    &.is-active {
      background: #ff0050;
      border-color: #ff0050;
      color: #fff;
    }
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

// 响应式设计
@media (max-width: 480px) {
  .mobile-products-section {
    padding: 16px 0;
  }
  
  .section-title {
    font-size: 16px;
    margin-bottom: 12px;
    margin-left: 12px;
  }
  
  .products-scroll {
    padding: 0 12px;
    gap: 12px;
  }
  
  .loading-container {
    padding: 30px 0;
    
    .loading-spinner {
      width: 32px;
      height: 32px;
    }
    
    p {
      font-size: 13px;
    }
  }
  
  .empty-container {
    padding: 30px 0;
    
    p {
      font-size: 13px;
    }
  }
  
  .scroll-arrow {
    width: 32px;
    height: 32px;
    
    .el-icon {
      font-size: 14px;
    }
    
    &.left {
      left: 6px;
    }
    
    &.right {
      right: 6px;
    }
  }
}
</style>
