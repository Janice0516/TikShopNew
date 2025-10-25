<template>
  <div class="product-card" :class="{ 'dark-theme': isDark }" @click="goToProduct">
    <div class="product-image-container">
      <img :src="product.image" :alt="product.name" class="product-image" />
      <div class="product-badge" v-if="product.badge">
        {{ product.badge }}
      </div>
    </div>
    
    <div class="product-info">
      <h3 class="product-name">{{ product.name }}</h3>
      <p class="product-description">{{ product.description }}</p>
      
      <div class="product-rating" v-if="product.rating">
        <span class="rating-stars">{{ formatRating(product.rating) }}</span>
        <span class="rating-count">{{ formatSales(product.sales) }} sold</span>
      </div>
      
      <div class="product-price-section">
        <div class="product-price">RM{{ product.price }}</div>
        <div class="product-original-price" v-if="product.originalPrice">
          RM{{ product.originalPrice }}
        </div>
      </div>
      
      <!-- 商家信息 -->
      <div class="merchant-info" v-if="product.merchantName" @click.stop="goToShop">
        <span class="merchant-name">{{ product.merchantName }}</span>
        <span class="shop-link">进入店铺 →</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { ElMessage } from 'element-plus'

interface Product {
  id: string
  name: string
  description?: string
  price: number
  originalPrice?: number
  image: string
  badge?: string
  rating?: number
  sales?: number
  merchantId?: string | number
  merchantName?: string
}

const props = defineProps<{
  product: Product
  isDark?: boolean
}>()

const router = useRouter()
const cartStore = useCartStore()

// 跳转到商品详情页
const goToProduct = () => {
  router.push(`/product/${props.product.id}`)
}

// 跳转到商家店铺
const goToShop = () => {
  if (props.product.merchantId) {
    router.push(`/shop/${props.product.merchantId}`)
  }
}

// 格式化评分显示
const formatRating = (rating: number) => {
  return `${rating.toFixed(1)} ★`
}

  // 格式化销量显示
  const formatSales = (sales: number | undefined) => {
    if (!sales) return '0'
    if (sales >= 1000) {
      return `${(sales / 1000).toFixed(1)}K`
    }
    return sales.toString()
  }
</script>

<style scoped lang="scss">
.product-card {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  
  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
  
  &.dark-theme {
    background: #111;
    border: 1px solid #333;
    
    &:hover {
      background: #222;
      box-shadow: 0 4px 12px rgba(255, 255, 255, 0.1);
    }
  }
}

.product-image-container {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden;
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
  top: 10px;
  left: 10px;
  background: #ff0050;
  color: #fff;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: bold;
}

.product-info {
  padding: 15px;
}

.product-name {
  font-size: 14px;
  font-weight: 500;
  color: #333;
  margin: 0 0 8px 0;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  
  .dark-theme & {
    color: #fff;
  }
}

.product-description {
  font-size: 12px;
  color: #666;
  margin: 0 0 10px 0;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  
  .dark-theme & {
    color: #999;
  }
}

.product-rating {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 10px;
  
  .rating-stars {
    font-size: 12px;
    color: #ff9900;
    font-weight: 500;
  }
  
  .rating-count {
    font-size: 12px;
    color: #666;
    
    .dark-theme & {
      color: #999;
    }
  }
}

.product-price-section {
  display: flex;
  align-items: center;
  gap: 8px;
}

.product-price {
  font-size: 16px;
  font-weight: bold;
  color: #ff0050;
}

.product-original-price {
  font-size: 12px;
  color: #999;
  text-decoration: line-through;
  
  .dark-theme & {
    color: #666;
  }
}

.merchant-info {
  margin-top: 8px;
  padding: 6px 8px;
  background: #f8f9fa;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  justify-content: space-between;
  align-items: center;
  
  &:hover {
    background: #e9ecef;
    
    .shop-link {
      color: $primary-color;
    }
  }
  
  .merchant-name {
    font-size: 12px;
    color: #666;
    font-weight: 500;
  }
  
  .shop-link {
    font-size: 11px;
    color: #999;
    transition: color 0.3s ease;
  }
}

@media (max-width: 768px) {
  .product-info {
    padding: 12px;
  }
  
  .product-name {
    font-size: 13px;
  }
  
  .product-price {
    font-size: 15px;
  }
}
</style>
