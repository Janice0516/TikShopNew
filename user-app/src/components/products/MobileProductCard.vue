<template>
  <div class="mobile-product-card" @click="handleClick">
    <div class="product-image-container">
      <img :src="product.image" :alt="translatedProduct.name" class="product-image" />
      
      <!-- Store Badge -->
      <div v-if="product.merchantName" class="store-badge">
        {{ product.merchantName }} Store
      </div>
      
      <!-- Quality Badges -->
      <div class="quality-badges">
        <div class="badge fast-delivery">Fast Delivery</div>
        <div class="badge high-quality">High Quality</div>
      </div>
    </div>
    
    <div class="product-info">
      <h3 class="product-name">{{ translatedProduct.name }}</h3>
      
      <div class="product-rating">
        <div class="stars">
          <span v-for="i in 5" :key="`star-${i}`" class="star" :class="{ filled: i <= Math.floor(product.rating) }">
            ★
          </span>
        </div>
        <span class="rating-value">{{ product.rating.toFixed(1) }}</span>
      </div>
      
      <div class="product-sales">
        {{ formatSales(product.sales) }} sold
      </div>
      
      <div class="product-price">
        <span class="currency">RM</span>
        <span class="price">{{ product.price.toFixed(2) }}</span>
        <span v-if="product.originalPrice" class="original-price">
          RM{{ product.originalPrice.toFixed(2) }}
        </span>
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
  originalPrice?: number | null
  image: string
  rating: number
  sales: number
  stock: number
  brand?: string
  merchantName?: string
}

const props = defineProps<{
  product: Product
}>()

// 使用商品翻译功能
const { getTranslatedProduct } = useProductTranslations()
const translatedProduct = computed(() => getTranslatedProduct(props.product))

const emit = defineEmits<{
  click: [product: Product]
}>()

const handleClick = () => {
  emit('click', props.product)
}

const formatSales = (sales: number): string => {
  if (sales >= 1000) {
    return (sales / 1000).toFixed(1) + 'K'
  }
  return sales.toString()
}
</script>

<style scoped lang="scss">
.mobile-product-card {
  background: #1a1a1a; // Dark background to match TikTok Shop
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  color: #fff; // White text for dark background
  flex-shrink: 0; /* 防止收缩 */
  width: 160px; /* 固定宽度，适合横向滚动 */
  
  /* 触屏优化 */
  -webkit-tap-highlight-color: transparent;
  touch-action: manipulation;
  
  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
  }
  
  &:active {
    transform: scale(0.98);
    transition: transform 0.1s ease;
  }
}

.product-image-container {
  position: relative;
  width: 100%;
  aspect-ratio: 1;
  overflow: hidden;
  background: #f5f5f5;
  
  .product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }
  
  .store-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    background: rgba(0, 0, 0, 0.7);
    color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 500;
    z-index: 2;
  }
  
  .quality-badges {
    position: absolute;
    bottom: 8px;
    left: 8px;
    right: 8px;
    display: flex;
    gap: 4px;
    z-index: 2;
    
    .badge {
      background: rgba(0, 0, 0, 0.8);
      color: #fff;
      padding: 2px 6px;
      border-radius: 3px;
      font-size: 9px;
      font-weight: 500;
      
      &.fast-delivery {
        background: rgba(34, 197, 94, 0.9);
      }
      
      &.high-quality {
        background: rgba(59, 130, 246, 0.9);
      }
    }
  }
}

.product-info {
  padding: 12px;
  
  .product-name {
    font-size: 14px;
    font-weight: 500;
    color: #fff; // White text for dark background
    margin: 0 0 8px 0;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 36px;
  }
  
  .product-rating {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 6px;
    
    .stars {
      display: flex;
      gap: 1px;
      
      .star {
        color: #d1d5db;
        font-size: 12px;
        
        &.filled {
          color: #fbbf24;
        }
      }
    }
    
    .rating-value {
      font-size: 12px;
      color: #ccc; // Light gray for dark background
      font-weight: 500;
    }
  }
  
  .product-sales {
    font-size: 12px;
    color: #ccc; // Light gray for dark background
    margin-bottom: 8px;
  }
  
  .product-price {
    display: flex;
    align-items: baseline;
    gap: 2px;
    
    .currency {
      font-size: 12px;
      color: #ef4444;
      font-weight: 600;
    }
    
    .price {
      font-size: 16px;
      color: #ef4444;
      font-weight: 700;
    }
    
    .original-price {
      font-size: 12px;
      color: #999; // Lighter gray for dark background
      text-decoration: line-through;
      margin-left: 4px;
    }
  }
}

// 响应式设计
@media (max-width: 480px) {
  .product-info {
    padding: 10px;
    
    .product-name {
      font-size: 13px;
      min-height: 32px;
    }
    
    .product-rating {
      margin-bottom: 4px;
      
      .stars .star {
        font-size: 11px;
      }
      
      .rating-value {
        font-size: 11px;
      }
    }
    
    .product-sales {
      font-size: 11px;
      margin-bottom: 6px;
    }
    
    .product-price {
      .currency {
        font-size: 11px;
      }
      
      .price {
        font-size: 15px;
      }
      
      .original-price {
        font-size: 11px;
      }
    }
  }
}
</style>
