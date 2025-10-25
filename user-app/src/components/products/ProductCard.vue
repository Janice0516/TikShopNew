<template>
  <div 
    class="product-card"
    @click="handleProductClick"
  >
    <!-- 商品图片 -->
    <div class="product-image-container">
      <img :src="product.image" :alt="translatedProduct.name" class="product-image" />
      
      <!-- 库存状态 -->
      <div v-if="product.stock === 0" class="product-out-of-stock">
        Out of Stock
      </div>
      
      <!-- 促销标签 -->
      <div v-if="product.badge" class="product-badge">
        {{ product.badge }}
      </div>
    </div>
    
    <!-- 商品信息 -->
    <div class="product-info">
      <h3 class="product-name">{{ translatedProduct.name }}</h3>
      
      <!-- 品牌信息 -->
      <div v-if="product.brand" class="product-brand">
        {{ product.brand }}
      </div>
      
      <!-- 评分和销量 -->
      <div class="product-rating">
        <div class="rating-stars">
          <span class="stars">{{ formatStars(product.rating || 0) }}</span>
          <span class="rating-value">{{ (product.rating || 0).toFixed(1) }}</span>
        </div>
        <div class="sales-count">
          {{ formatSales(product.sales || 0) }} sold
        </div>
      </div>
      
      <!-- 价格信息 -->
      <div class="product-pricing">
        <div class="current-price">
          RM{{ formatPrice(product.price || 0) }}
        </div>
        <div v-if="product.originalPrice && product.originalPrice > 0" class="original-price">
          RM{{ formatPrice(product.originalPrice) }}
        </div>
      </div>
      
      <!-- 商家信息 -->
      <div v-if="product.merchantName" class="merchant-info">
        by {{ product.merchantName }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
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
  product: Product
}

const props = defineProps<Props>()

// 使用商品翻译功能
const { getTranslatedProduct } = useProductTranslations()
const translatedProduct = computed(() => getTranslatedProduct(props.product))

const emit = defineEmits<{
  productClick: [product: Product]
}>()

const handleProductClick = () => {
  emit('productClick', props.product)
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
</script>

<style scoped lang="scss">
.product-card {
  width: 100%;
  background: #fff;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
  gap: 12px;
  
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
  border-radius: 8px;
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

.product-out-of-stock {
  position: absolute;
  top: 8px;
  left: 8px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.product-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  background: #ff0050;
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.product-info {
  padding: 0 4px;
  display: flex;
  flex-direction: column;
  gap: 8px;
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
}

.product-brand {
  font-size: 12px;
  color: #666;
  font-weight: 500;
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
}

.current-price {
  font-size: 16px;
  font-weight: 600;
  color: #ff0050;
}

.original-price {
  font-size: 14px;
  color: #999;
  text-decoration: line-through;
}

.merchant-info {
  font-size: 12px;
  color: #666;
  font-style: italic;
}
</style>
