<template>
  <section class="product-carousel-section">
    <div class="section-header">
      <h2 class="section-title">{{ title }}</h2>
      <div class="section-actions">
        <button 
          class="scroll-btn scroll-left" 
          @click="scrollLeft"
          :disabled="!canScrollLeft"
        >
          ←
        </button>
        <button 
          class="scroll-btn scroll-right" 
          @click="scrollRight"
          :disabled="!canScrollRight"
        >
          →
        </button>
      </div>
    </div>
    
    <div class="carousel-container">
      <div 
        class="product-carousel" 
        ref="carouselContainer"
        @scroll="updateScrollState"
      >
        <div 
          v-for="product in products" 
          :key="product.id"
          class="product-card"
          @click="handleProductClick(product)"
        >
          <!-- 商品图片 -->
          <div class="product-image-container">
            <img :src="product.image" :alt="getTranslatedProduct(product).name" class="product-image" />
            
            <!-- 促销标签 -->
            <div v-if="product.badge" class="product-badge">
              {{ product.badge }}
            </div>
            
            <!-- 库存状态 -->
            <div v-if="product.stock === 0" class="product-out-of-stock">
              Out of Stock
            </div>
          </div>
          
          <!-- 商品信息 -->
          <div class="product-info">
            <!-- 品牌 -->
            <div v-if="product.brand" class="product-brand">
              {{ product.brand }}
            </div>
            
            <!-- 商品名称 -->
            <h3 class="product-name">{{ getTranslatedProduct(product).name }}</h3>
            
            <!-- 评分和销量 -->
            <div class="product-rating">
              <div class="rating-stars">
                <span class="stars">{{ formatStars(product.rating) }}</span>
                <span class="rating-value">{{ product.rating.toFixed(1) }}</span>
              </div>
              <div class="sales-count">
                {{ formatSales(product.sales) }} sold
              </div>
            </div>
            
            <!-- 价格信息 -->
            <div class="product-pricing">
              <div class="current-price">
                RM{{ formatPrice(product.price) }}
              </div>
              <div v-if="product.originalPrice" class="original-price">
                RM{{ formatPrice(product.originalPrice) }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useProductTranslations } from '@/utils/productTranslations'

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
}

const props = defineProps<Props>()

// 使用商品翻译功能
const { getTranslatedProduct } = useProductTranslations()

const emit = defineEmits<{
  productClick: [product: Product]
}>()

const carouselContainer = ref<HTMLElement>()
const scrollPosition = ref(0)
const containerWidth = ref(0)
const scrollWidth = ref(0)

const canScrollLeft = computed(() => scrollPosition.value > 0)
const canScrollRight = computed(() => scrollPosition.value < scrollWidth.value - containerWidth.value)

const handleProductClick = (product: Product) => {
  emit('productClick', product)
}

const scrollLeft = () => {
  if (carouselContainer.value) {
    const scrollAmount = 300
    carouselContainer.value.scrollBy({
      left: -scrollAmount,
      behavior: 'smooth'
    })
  }
}

const scrollRight = () => {
  if (carouselContainer.value) {
    const scrollAmount = 300
    carouselContainer.value.scrollBy({
      left: scrollAmount,
      behavior: 'smooth'
    })
  }
}

const updateScrollState = () => {
  if (carouselContainer.value) {
    scrollPosition.value = carouselContainer.value.scrollLeft
    containerWidth.value = carouselContainer.value.clientWidth
    scrollWidth.value = carouselContainer.value.scrollWidth
  }
}

const formatPrice = (price: number): string => {
  return price.toFixed(2)
}

const formatStars = (rating: number): string => {
  const fullStars = Math.floor(rating)
  const hasHalfStar = rating % 1 >= 0.5
  const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0)
  
  return '★'.repeat(fullStars) + 
         (hasHalfStar ? '☆' : '') + 
         '☆'.repeat(emptyStars)
}

const formatSales = (sales: number): string => {
  if (sales >= 1000) {
    return (sales / 1000).toFixed(1) + 'K'
  }
  return sales.toString()
}

onMounted(() => {
  if (carouselContainer.value) {
    carouselContainer.value.addEventListener('scroll', updateScrollState)
    updateScrollState()
  }
})

onUnmounted(() => {
  if (carouselContainer.value) {
    carouselContainer.value.removeEventListener('scroll', updateScrollState)
  }
})
</script>

<style scoped lang="scss">
.product-carousel-section {
  margin-bottom: 40px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-title {
  font-size: 24px;
  font-weight: 700;
  color: #333;
  margin: 0;
}

.section-actions {
  display: flex;
  gap: 8px;
}

.scroll-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #fff;
  border: 1px solid #e5e5e5;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 18px;
  color: #666;
  transition: all 0.2s;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);

  &:hover:not(:disabled) {
    background: #f8f9fa;
    border-color: #ff0050;
    color: #ff0050;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }

  &:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
}

.carousel-container {
  position: relative;
  overflow: hidden;
}

.product-carousel {
  display: flex;
  gap: 16px;
  overflow-x: auto;
  overflow-y: hidden;
  scroll-behavior: smooth;
  padding: 8px 0;
  scrollbar-width: none;
  -ms-overflow-style: none;

  &::-webkit-scrollbar {
    display: none;
  }
}

.product-card {
  min-width: 200px;
  width: 200px;
  background: #fff;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
  
  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }
}

.product-image-container {
  position: relative;
  width: 100%;
  aspect-ratio: 1;
  overflow: hidden;
  background: #f5f5f5;
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-image {
  transform: scale(1.05);
}

.product-badge {
  position: absolute;
  top: 8px;
  left: 8px;
  background: #ff0050;
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
  z-index: 2;
}

.product-out-of-stock {
  position: absolute;
  top: 8px;
  right: 8px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  z-index: 2;
}

.product-info {
  padding: 12px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  flex: 1;
}

.product-brand {
  font-size: 12px;
  color: #666;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.product-name {
  font-size: 14px;
  font-weight: 500;
  color: #333;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  margin: 0;
  min-height: 36px;
}

.product-rating {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.rating-stars {
  display: flex;
  align-items: center;
  gap: 4px;
}

.stars {
  color: #ffc107;
  font-size: 12px;
}

.rating-value {
  font-size: 12px;
  color: #666;
  font-weight: 500;
}

.sales-count {
  font-size: 12px;
  color: #666;
}

.product-pricing {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: auto;
}

.current-price {
  font-size: 16px;
  font-weight: 700;
  color: #ff0050;
}

.original-price {
  font-size: 14px;
  color: #999;
  text-decoration: line-through;
}

// 响应式设计
@media (max-width: 768px) {
  .product-card {
    min-width: 160px;
    width: 160px;
  }
  
  .section-title {
    font-size: 20px;
  }
  
  .scroll-btn {
    width: 36px;
    height: 36px;
    font-size: 16px;
  }
}
</style>
