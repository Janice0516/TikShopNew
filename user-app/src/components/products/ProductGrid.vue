<template>
  <section class="products-section">
    <div class="section-header">
      <h2 class="section-title">{{ title }}</h2>
      <div v-if="showViewAll" class="view-all">
        <button @click="handleViewAll" class="view-all-btn">
          View all
        </button>
      </div>
    </div>
    
    <div class="products-container">
      <div v-if="loading" class="loading-state">
        <div class="loading-spinner"></div>
        <p>Loading products...</p>
      </div>
      
      <div v-else-if="products.length === 0" class="empty-state">
        <div class="empty-icon">📦</div>
        <p>No products found</p>
      </div>
      
      <div v-else class="products-grid">
        <ProductCard
          v-for="product in products"
          :key="product.id"
          :product="product"
          @product-click="handleProductClick"
        />
      </div>
    </div>
    
    <!-- 分页 -->
    <div v-if="showPagination && totalPages > 1" class="pagination">
      <button 
        class="pagination-btn"
        :disabled="currentPage <= 1"
        @click="handlePageChange(currentPage - 1)"
      >
        ← Previous
      </button>
      
      <div class="pagination-info">
        Page {{ currentPage }} of {{ totalPages }}
      </div>
      
      <button 
        class="pagination-btn"
        :disabled="currentPage >= totalPages"
        @click="handlePageChange(currentPage + 1)"
      >
        Next →
      </button>
    </div>
  </section>
</template>

<script setup lang="ts">
import ProductCard from './ProductCard.vue'

interface Product {
  id: string | number
  name: string
  description?: string
  price: number
  originalPrice?: number
  image: string
  rating: number
  sales: number
  stock: number
  brand?: string
  merchantName?: string
  badge?: string
}

interface Props {
  title: string
  products: Product[]
  loading?: boolean
  showViewAll?: boolean
  showPagination?: boolean
  currentPage?: number
  totalPages?: number
}

const props = withDefaults(defineProps<Props>(), {
  loading: false,
  showViewAll: false,
  showPagination: false,
  currentPage: 1,
  totalPages: 1
})

const emit = defineEmits<{
  productClick: [product: Product]
  viewAll: []
  pageChange: [page: number]
}>()

const handleProductClick = (product: Product) => {
  emit('productClick', product)
}

const handleViewAll = () => {
  emit('viewAll')
}

const handlePageChange = (page: number) => {
  if (page >= 1 && page <= props.totalPages) {
    emit('pageChange', page)
  }
}
</script>

<style scoped lang="scss">
.products-section {
  margin-bottom: 40px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-title {
  font-size: 20px;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.view-all {
  display: flex;
  align-items: center;
}

.view-all-btn {
  padding: 8px 16px;
  background: transparent;
  border: 1px solid #e5e5e5;
  border-radius: 6px;
  color: #666;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;

  &:hover {
    background: #f5f5f5;
    border-color: #ccc;
  }
}

.products-container {
  position: relative;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  color: #666;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #ff0050;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  color: #666;
}

.empty-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  overflow-x: hidden;
  overflow-y: visible;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  margin-top: 30px;
  padding: 20px 0;
}

.pagination-btn {
  padding: 10px 20px;
  background: #fff;
  border: 1px solid #e5e5e5;
  border-radius: 6px;
  color: #333;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;

  &:hover:not(:disabled) {
    background: #f5f5f5;
    border-color: #ccc;
  }

  &:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
}

.pagination-info {
  font-size: 14px;
  color: #666;
  font-weight: 500;
}

// 响应式设计
@media (max-width: 768px) {
  .products-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
  }
  
  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
  
  .pagination {
    flex-direction: column;
    gap: 12px;
  }
}
</style>
