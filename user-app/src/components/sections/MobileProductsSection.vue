<template>
  <div class="products-section">
    <h2 class="section-title">Savings for you</h2>
    
    <div class="products-container">
      <div class="products-scroll">
        <div 
          v-for="product in products" 
          :key="product.id"
          class="product-card"
          @click="goToProduct(product)"
        >
          <div class="product-image-container">
            <img :src="product.image" :alt="product.name" class="product-image" />
            
            <!-- 促销标签 -->
            <div v-if="product.badge" class="product-badge">
              {{ product.badge }}
            </div>
            
            <!-- 折扣标签 -->
            <div v-if="product.discount" class="discount-badge">
              {{ product.discount }}%
            </div>
            
            <!-- 库存状态 -->
            <div v-if="product.stock === 0" class="out-of-stock">
              Out of Stock
            </div>
          </div>
          
          <div class="product-info">
            <h3 class="product-name">{{ product.name }}</h3>
            
            <!-- 品牌信息 -->
            <div v-if="product.brand" class="product-brand">
              {{ product.brand }}
            </div>
            
            <div class="product-rating">
              <span class="rating-stars">★</span>
              <span class="rating-score">{{ product.rating.toFixed(1) }}</span>
              <span class="sales-count">{{ formatSales(product.sales) }} sold</span>
            </div>
            
            <div class="product-pricing">
              <div class="current-price">RM{{ product.price.toFixed(2) }}</div>
              <div v-if="product.originalPrice && product.originalPrice > product.price" class="original-price">
                RM{{ product.originalPrice.toFixed(2) }}
              </div>
            </div>
            
            <!-- 商家信息 -->
            <div v-if="product.merchantName" class="merchant-info">
              by {{ product.merchantName }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'

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
  discount?: number
}

const props = defineProps<{
  products: Product[]
}>()

const router = useRouter()

const goToProduct = (product: Product) => {
  router.push(`/product/${product.id}`)
}

const formatSales = (sales: number): string => {
  if (sales >= 1000) {
    return (sales / 1000).toFixed(1) + 'K'
  }
  return sales.toString()
}
</script>

<style scoped lang="scss">
.products-section {
  background: #000;
  padding: 20px 0;
  
  .section-title {
    font-size: 20px;
    font-weight: bold;
    color: #fff;
    margin-bottom: 16px;
    padding: 0 16px;
  }
  
  .products-container {
    position: relative;
    
    .products-scroll {
      display: flex;
      gap: 16px;
      overflow-x: auto;
      padding: 0 16px;
      
      &::-webkit-scrollbar {
        display: none;
      }
    }
  }
}

.product-card {
  flex-shrink: 0;
  width: 160px;
  background: #1a1a1a;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.3s ease;
  
  &:hover {
    transform: translateY(-2px);
  }
  
  .product-image-container {
    position: relative;
    width: 100%;
    height: 160px;
    overflow: hidden;
    
    .product-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }
    
    .product-badge {
      position: absolute;
      top: 8px;
      left: 8px;
      background: rgba(0, 0, 0, 0.8);
      color: #fff;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 10px;
      font-weight: 500;
      z-index: 2;
    }
    
    .discount-badge {
      position: absolute;
      top: 8px;
      right: 8px;
      background: #ff0050;
      color: #fff;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 10px;
      font-weight: bold;
      z-index: 2;
    }
    
    .out-of-stock {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(0, 0, 0, 0.8);
      color: #fff;
      padding: 8px 12px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: 500;
      z-index: 2;
    }
  }
  
  .product-info {
    padding: 12px;
    
    .product-name {
      font-size: 14px;
      font-weight: 500;
      color: #fff;
      margin-bottom: 6px;
      line-height: 1.3;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      min-height: 36px;
    }
    
    .product-brand {
      font-size: 12px;
      color: #999;
      margin-bottom: 6px;
    }
    
    .product-rating {
      display: flex;
      align-items: center;
      gap: 4px;
      margin-bottom: 8px;
      
      .rating-stars {
        color: #ffa500;
        font-size: 12px;
      }
      
      .rating-score {
        font-size: 12px;
        color: #fff;
      }
      
      .sales-count {
        font-size: 12px;
        color: #666;
      }
    }
    
    .product-pricing {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 6px;
      
      .current-price {
        font-size: 16px;
        font-weight: bold;
        color: #ff0050;
      }
      
      .original-price {
        font-size: 12px;
        color: #666;
        text-decoration: line-through;
      }
    }
    
    .merchant-info {
      font-size: 11px;
      color: #666;
      margin-top: 4px;
    }
  }
  
  &:hover .product-image {
    transform: scale(1.05);
  }
}

// 响应式设计
@media (max-width: 480px) {
  .product-card {
    width: 140px;
    
    .product-image-container {
      height: 140px;
    }
    
    .product-info {
      padding: 10px;
      
      .product-name {
        font-size: 13px;
        min-height: 32px;
      }
      
      .product-pricing {
        .current-price {
          font-size: 14px;
        }
      }
    }
  }
}

@media (min-width: 769px) {
  .products-section {
    display: none;
  }
}
</style>
