<template>
  <div class="mobile-product-detail">
    <!-- Mobile Header -->
    <div class="mobile-header">
      <button class="back-btn" @click="goBack">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <h1 class="page-title">商品详情</h1>
      <button class="share-btn">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M8.684 13.342C8.886 12.938 9 12.482 9 12C9 11.518 8.886 11.062 8.684 10.658M21 12C21 16.418 17.418 20 13 20C11.895 20 10.8 19.7 9.8 19.1L5 20L6.9 15.2C6.3 14.2 6 13.1 6 12C6 7.582 9.582 4 14 4C18.418 4 22 7.582 22 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p>加载中...</p>
    </div>
    
    <!-- Error State -->
    <div v-else-if="error" class="error-container">
      <h2>错误</h2>
      <p>{{ error }}</p>
      <button @click="loadProductData" class="retry-btn">重试</button>
    </div>
    
    <!-- Product Content -->
    <div v-else class="product-content">
      <!-- Product Images -->
      <div class="product-images">
        <div class="image-gallery">
          <div 
            v-for="(image, index) in allImages" 
            :key="index"
            class="image-item"
            :class="{ active: index === currentImageIndex }"
            @click="currentImageIndex = index"
          >
            <img :src="image" :alt="`${product.name} ${index + 1}`" />
          </div>
        </div>
        <div class="main-image">
          <img :src="allImages[currentImageIndex]" :alt="product.name" />
          <div class="image-indicator">
            {{ currentImageIndex + 1 }} / {{ allImages.length }}
          </div>
        </div>
      </div>

      <!-- Product Info -->
      <div class="product-info">
        <!-- Discount Badge -->
        <div class="discount-badge" v-if="discountPercentage > 0">
          -{{ discountPercentage }}%
        </div>

        <!-- Price -->
        <div class="price-section">
          <div class="current-price">RM{{ formatPrice(product.salePrice || 0) }}</div>
          <div class="original-price" v-if="product.originalPrice && product.originalPrice > product.salePrice">
            RM{{ formatPrice(product.originalPrice) }}
          </div>
        </div>

        <!-- Product Title -->
        <h1 class="product-title">{{ translatedProduct.name }}</h1>

        <!-- Shipping Info -->
        <div class="shipping-info">
          <span class="shipping-icon">🚚</span>
          <span class="shipping-text">此订单运费从 RM4.90 起</span>
        </div>

        <!-- Seller Info -->
        <div class="seller-info">
          <div class="seller-header">
            <span class="seller-label">销售商家</span>
            <span class="seller-name">{{ merchant.shopInfo?.name || '优品小店' }}</span>
          </div>
          <div class="seller-stats">
            <span class="rating">⭐ {{ merchant.shopInfo?.rating || 4.8 }} ({{ merchant.stats?.totalCustomers || 74 }})</span>
            <span class="sales">{{ merchant.stats?.totalSales || 249 }} 已售</span>
          </div>
        </div>

        <!-- Product Variants -->
        <div class="variants-section" v-if="product.variants && product.variants.length > 0">
          <ProductVariants 
            :variants="product.variants"
            :variant-type="variantType"
            :base-price="product.price"
            :initial-selection="selectedVariantIndex"
            @variant-change="handleVariantChange"
            @variant-select="handleVariantSelect"
          />
        </div>

        <!-- Seller Profile -->
        <div class="seller-profile-section">
          <SellerProfile :seller="sellerData" />
        </div>
      </div>
    </div>

    <!-- Bottom Action Bar -->
    <div class="bottom-action-bar">
      <button class="add-to-cart-btn" @click="handleAddToCart">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
          <path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.5 5.1 16.5H17M17 13V19C17 19.6 16.6 20 16 20H8C7.4 20 7 19.6 7 19V13H17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        加入购物车
      </button>
      <button class="buy-now-btn" @click="handleBuyNow">
        立即购买
      </button>
    </div>

    <!-- Bottom Navigation -->
    <MobileBottomNav />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ElMessage } from 'element-plus'
import MobileBottomNav from '@/components/layout/MobileBottomNav.vue'
import ProductVariants from '@/components/product/ProductVariants.vue'
import SellerProfile from '@/components/product/SellerProfile.vue'
import { productApi } from '@/api'
import { useProductTranslations } from '@/composables/useProductTranslations'

const route = useRoute()
const router = useRouter()
const { getTranslatedProduct } = useProductTranslations()

// 响应式数据
const loading = ref(true)
const error = ref('')
const product = ref<any>({})
const merchant = ref<any>({})
const currentImageIndex = ref(0)
const selectedVariantIndex = ref(0)

// 计算属性
const translatedProduct = computed(() => {
  return getTranslatedProduct(product.value)
})

const allImages = computed(() => {
  if (!product.value.images) return []
  try {
    const images = typeof product.value.images === 'string' 
      ? JSON.parse(product.value.images) 
      : product.value.images
    return Array.isArray(images) ? images : []
  } catch {
    return []
  }
})

const variantType = computed(() => {
  if (!product.value.variants || product.value.variants.length === 0) return 'none'
  return product.value.variants[0]?.type || 'color'
})

const discountPercentage = computed(() => {
  if (product.value.originalPrice && product.value.salePrice && product.value.originalPrice > product.value.salePrice) {
    return Math.round(((product.value.originalPrice - product.value.salePrice) / product.value.originalPrice) * 100)
  }
  return 0
})

const sellerData = computed(() => {
  return {
    id: merchant.value.id,
    name: merchant.value.shopInfo?.name || '优品小店',
    avatar: merchant.value.shopInfo?.avatar || 'https://via.placeholder.com/60x60/409EFF/ffffff?text=店',
    rating: merchant.value.shopInfo?.rating || 4.8,
    totalCustomers: merchant.value.stats?.totalCustomers || 74,
    totalSales: merchant.value.stats?.totalSales || 249,
    joinDate: merchant.value.joinDate || '2023-01-01',
    description: merchant.value.description || '优质商家，值得信赖'
  }
})

// 方法
const formatPrice = (price: number) => {
  return price.toFixed(2)
}

const goBack = () => {
  router.back()
}

const handleImageChange = (index: number) => {
  currentImageIndex.value = index
}

const handleVariantChange = (variant: any) => {
  console.log('Variant changed:', variant)
}

const handleVariantSelect = (variant: any) => {
  selectedVariantIndex.value = variant.index
  console.log('Variant selected:', variant)
}

const handleAddToCart = () => {
  ElMessage.success('已加入购物车')
}

const handleBuyNow = () => {
  ElMessage.success('跳转到结算页面')
}

const loadProductData = async () => {
  try {
    loading.value = true
    error.value = ''
    
    const productId = route.params.id
    console.log('Loading product data for ID:', productId)
    
    const response = await productApi.getProductDetail(productId as string)
    console.log('Product API response:', response)
    
    if (response && response.id) {
      product.value = response
      
      // 模拟商家数据
      merchant.value = {
        id: response.merchantId || 1,
        shopInfo: {
          name: response.merchantName || '优品小店',
          avatar: 'https://via.placeholder.com/60x60/409EFF/ffffff?text=店',
          rating: 4.8
        },
        stats: {
          totalCustomers: 74,
          totalSales: 249
        },
        joinDate: '2023-01-01',
        description: '优质商家，值得信赖'
      }
      
      console.log('Product data loaded successfully:', product.value)
    } else {
      throw new Error('No product data received')
    }
  } catch (err: any) {
    console.error('Failed to load product data:', err)
    error.value = err.message || '加载商品数据失败'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  console.log('MobileProductDetail mounted!')
  loadProductData()
})
</script>

<style scoped lang="scss">
.mobile-product-detail {
  min-height: 100vh;
  background: #000;
  color: #fff;
  padding-bottom: 160px; // 为底部导航栏留出空间
}

.mobile-header {
  position: sticky;
  top: 0;
  z-index: 100;
  background: #000;
  border-bottom: 1px solid #333;
  padding: 12px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;

  .back-btn, .share-btn {
    background: none;
    border: none;
    color: #fff;
    padding: 8px;
    cursor: pointer;
    border-radius: 8px;
    transition: background-color 0.2s;

    &:hover {
      background: #333;
    }
  }

  .page-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
  }
}

.loading-container, .error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;

  .loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #333;
    border-top: 3px solid #ff0050;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 16px;
  }

  .retry-btn {
    background: #ff0050;
    color: #fff;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 16px;
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.product-content {
  .product-images {
    .image-gallery {
      display: flex;
      gap: 8px;
      padding: 16px;
      overflow-x: auto;
      
      .image-item {
        min-width: 60px;
        height: 60px;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border-color 0.2s;

        &.active {
          border-color: #ff0050;
        }

        img {
          width: 100%;
          height: 100%;
          object-fit: cover;
        }
      }
    }

    .main-image {
      position: relative;
      width: 100%;
      height: 300px;
      background: #111;

      img {
        width: 100%;
        height: 100%;
        object-fit: contain;
      }

      .image-indicator {
        position: absolute;
        bottom: 16px;
        right: 16px;
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
      }
    }
  }

  .product-info {
    padding: 20px 16px;

    .discount-badge {
      background: #ff0050;
      color: #fff;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: 600;
      display: inline-block;
      margin-bottom: 12px;
    }

    .price-section {
      margin-bottom: 16px;

      .current-price {
        font-size: 24px;
        font-weight: 700;
        color: #ff0050;
        margin-bottom: 4px;
      }

      .original-price {
        font-size: 16px;
        color: #666;
        text-decoration: line-through;
      }
    }

    .product-title {
      font-size: 18px;
      font-weight: 600;
      margin: 0 0 16px 0;
      line-height: 1.4;
    }

    .shipping-info {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 20px;
      padding: 12px;
      background: #111;
      border-radius: 8px;

      .shipping-icon {
        font-size: 16px;
      }

      .shipping-text {
        font-size: 14px;
        color: #ccc;
      }
    }

    .seller-info {
      margin-bottom: 20px;
      padding: 16px;
      background: #111;
      border-radius: 8px;

      .seller-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;

        .seller-label {
          font-size: 14px;
          color: #666;
        }

        .seller-name {
          font-size: 16px;
          font-weight: 600;
        }
      }

      .seller-stats {
        display: flex;
        gap: 16px;

        .rating, .sales {
          font-size: 14px;
          color: #ccc;
        }
      }
    }

    .variants-section, .seller-profile-section {
      margin-bottom: 20px;
    }
  }
}

.bottom-action-bar {
  position: fixed;
  bottom: 80px; // 在底部导航栏上方
  left: 0;
  right: 0;
  background: #000;
  border-top: 1px solid #333;
  padding: 16px;
  display: flex;
  gap: 12px;
  z-index: 50;

  .add-to-cart-btn, .buy-now-btn {
    flex: 1;
    padding: 16px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .add-to-cart-btn {
    background: #333;
    color: #fff;
    border: 1px solid #555;

    &:hover {
      background: #444;
    }
  }

  .buy-now-btn {
    background: #ff0050;
    color: #fff;
    border: none;

    &:hover {
      background: #e6004a;
    }
  }
}

// 移动端优化
@media (max-width: 768px) {
  .mobile-header {
    padding: 12px 16px;
  }

  .product-content {
    .product-images {
      .main-image {
        height: 250px;
      }
    }

    .product-info {
      padding: 16px;

      .price-section {
        .current-price {
          font-size: 22px;
        }
      }

      .product-title {
        font-size: 16px;
      }
    }
  }

  .bottom-action-bar {
    padding: 12px;
    
    .add-to-cart-btn, .buy-now-btn {
      padding: 14px;
      font-size: 14px;
    }
  }
}
</style>
