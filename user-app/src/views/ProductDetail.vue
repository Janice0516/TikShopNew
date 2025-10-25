<template>
  <div class="tiktok-shop">
    <!-- TikTok Shop Header -->
    <TikTokHeader />

    <div class="main-layout">
      <!-- Left Sidebar -->
      <TikTokSidebar />

      <!-- Main Content -->
      <main class="main-content">
        <!-- Breadcrumbs -->
        <BreadcrumbNavigation :breadcrumbs="breadcrumbs" />

        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
          <div class="loading-spinner"></div>
          <p>Loading product details...</p>
        </div>
        
        <!-- Error State -->
        <div v-else-if="error" class="error-container">
          <h2>Error</h2>
          <p>{{ error }}</p>
          <button @click="loadProductData" class="retry-btn">Retry</button>
        </div>
        
        <!-- Product Display Area -->
        <div v-else class="product-display">
              <!-- Product Image Section -->
              <ProductImageGallery 
                :images="allImages" 
                :product-name="translatedProduct.name || product.name || 'Product'"
                :initial-index="currentImageIndex"
                @image-change="handleImageChange"
              />

          <!-- Product Details Section -->
          <div class="product-details-section">
            <!-- Discount Badge -->
            <div class="discount-badge" v-if="discountPercentage > 0">
              -{{ discountPercentage }}%
            </div>

            <!-- Price Section -->
            <div class="price-section">
              <div class="current-price">RM{{ formatPrice(product.salePrice || 0) }}</div>
              <div class="original-price" v-if="product.originalPrice && product.originalPrice > product.salePrice">
                RM{{ formatPrice(product.originalPrice) }}
              </div>
            </div>

            <!-- Shipping Info -->
            <div class="shipping-info">
              <span class="shipping-icon">🚚</span>
              <span class="shipping-text">From RM4.90 shipping on this order</span>
            </div>

            <!-- Product Title -->
            <h1 class="product-title">{{ translatedProduct.name }}</h1>

            <!-- Seller Summary -->
            <div class="seller-summary">
              <span class="seller-label">Sold by</span>
              <span class="seller-name">{{ merchant.shopInfo?.name || t('common.defaultShop') }}</span>
              <div class="rating-sales">
                <span class="rating">⭐ {{ merchant.shopInfo?.rating || 4.8 }} ({{ merchant.stats?.totalCustomers || 74 }})</span>
                <span class="sales">{{ merchant.stats?.totalSales || 249 }} sold</span>
              </div>
            </div>

            <!-- Product Variants -->
            <ProductVariants 
              :variants="product.variants || []"
              :variant-type="variantType"
              :base-price="product.price"
              :initial-selection="selectedVariantIndex"
              @variant-change="handleVariantChange"
              @variant-select="handleVariantSelect"
            />

            <!-- Seller Profile -->
            <SellerProfile :seller="sellerData" />

              <!-- Action Buttons -->
              <ActionButtons 
                :product-id="product.id || route.params.id"
                :product-name="translatedProduct.name"
                :variant-id="selectedVariantIndex"
                @add-to-cart="handleAddToCart"
                @buy-now="handleBuyNow"
                @share="handleShare"
              />
          </div>
        </div>

        <!-- Bottom Navigation -->
        <div class="bottom-navigation">
          <div class="nav-tabs">
            <button class="nav-tab active">Product Details</button>
            <button class="nav-tab">User Reviews</button>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'
import { useProductTranslations } from '@/utils/productTranslations'
import { productApi, shopApi } from '@/api'
import { useUserStore } from '@/stores/user'
import { useCartStore } from '@/stores/cart'
import TikTokHeader from '@/components/layout/TikTokHeader.vue'
import TikTokSidebar from '@/components/layout/TikTokSidebar.vue'
import BreadcrumbNavigation from '@/components/navigation/BreadcrumbNavigation.vue'
import ProductImageGallery from '@/components/product/ProductImageGallery.vue'
import ProductVariants from '@/components/product/ProductVariants.vue'
import SellerProfile from '@/components/product/SellerProfile.vue'
import ActionButtons from '@/components/product/ActionButtons.vue'

const route = useRoute()
const router = useRouter()
const { t } = useI18n()
const { getTranslatedProduct } = useProductTranslations()
const userStore = useUserStore()
const cartStore = useCartStore()

// 响应式数据
const product = ref<any>({})
const merchant = ref<any>({})
const loading = ref(true)
const error = ref('')

const currentImageIndex = ref(0)
const selectedVariantIndex = ref(0)

// 计算属性
const translatedProduct = computed(() => getTranslatedProduct(product.value))
const allImages = computed(() => {
  const images = []
  
  // 添加主图
  if (product.value.mainImage) {
    images.push(product.value.mainImage)
    console.log('Added main image:', product.value.mainImage)
  }
  
  // 处理副图数组
  if (product.value.images) {
    let imageArray = []
    
    // 如果是字符串，尝试解析JSON
    if (typeof product.value.images === 'string') {
      try {
        imageArray = JSON.parse(product.value.images)
        console.log('Parsed images JSON:', imageArray)
      } catch (e) {
        console.warn('Failed to parse images JSON:', product.value.images)
      }
    } else if (Array.isArray(product.value.images)) {
      imageArray = product.value.images
    }
    
    // 添加有效的副图
    if (Array.isArray(imageArray)) {
      imageArray.forEach(img => {
        if (img && typeof img === 'string' && img.trim()) {
          images.push(img.trim())
          console.log('Added additional image:', img.trim())
        }
      })
    }
  }
  
  console.log('Final images array:', images)
  console.log('Product data:', product.value)
  return images
})
const selectedVariant = computed(() => product.value.variants?.[selectedVariantIndex.value] || product.value.variants?.[0])

const discountPercentage = computed(() => {
  if (product.value.originalPrice && product.value.salePrice && product.value.originalPrice > product.value.salePrice) {
    return Math.round(((product.value.originalPrice - product.value.salePrice) / product.value.originalPrice) * 100)
  }
  return 0
})

// 变体类型
const variantType = computed(() => {
  // 根据变体数据判断类型
  if (product.value.variants && product.value.variants.length > 0) {
    const firstVariant = product.value.variants[0]
    if (firstVariant.color) return t('product.color')
    if (firstVariant.size) return t('product.size')
    if (firstVariant.material) return t('product.material')
    return t('product.option')
  }
  return t('product.color')
})

// 商家数据
const sellerData = computed(() => ({
  id: merchant.value.shopInfo?.id || 1,
  name: merchant.value.shopInfo?.name || t('common.defaultShop'),
  rating: merchant.value.shopInfo?.rating || 4.8,
  sales: merchant.value.stats?.totalSales || 249,
  followers: merchant.value.stats?.totalCustomers || 74
}))

// 面包屑数据
const breadcrumbs = computed(() => [
  { name: 'TikTok Shop', path: '/' },
  { name: product.value.category?.name || '分类', path: `/categories/${product.value.categoryId}` },
  { name: translatedProduct.value.name, path: '', current: true }
])

// 方法
const formatPrice = (price: number | string) => {
  const numPrice = typeof price === 'string' ? parseFloat(price) : price
  return isNaN(numPrice) ? '0.00' : numPrice.toFixed(2)
}

const formatSales = (sales: number) => {
  if (sales >= 1000) {
    return (sales / 1000).toFixed(1) + 'K'
  }
  return sales.toString()
}

const handleImageChange = (index: number) => {
  currentImageIndex.value = index
}

const handleVariantChange = (variant: any, index: number) => {
  selectedVariantIndex.value = index
  console.log('Variant changed:', variant, index)
  
  // 可以在这里更新价格、图片等
  if (variant.price && variant.price !== product.value.price) {
    product.value.price = variant.price
  }
}

const handleVariantSelect = (index: number) => {
  selectedVariantIndex.value = index
  console.log('Variant selected:', index)
}

const handleAddToCart = async (productId: string | number, variantId?: string | number, quantity?: number) => {
  try {
    const qty = quantity || 1
    console.log('Add to cart:', { productId, variantId, quantity: qty })
    
    // 检查用户是否登录
    if (!userStore.isLoggedIn) {
      ElMessage.warning('请先登录')
      router.push('/login')
      return
    }
    
    // 调用购物车API
    await cartStore.addToCart(String(productId), qty)
    
    ElMessage.success('商品已加入购物车')
    
    // 更新购物车数量显示
    await cartStore.fetchCart()
    
  } catch (error: any) {
    console.error('加入购物车失败:', error)
    ElMessage.error(error.message || '加入购物车失败，请重试')
  }
}

const handleShare = (productId: string | number, productName?: string) => {
  console.log('Product shared:', { productId, productName })
  // 可以在这里添加分享统计逻辑
}

const handleBuyNow = async (productId: string | number, variantId?: string | number, quantity?: number) => {
  try {
    if (!userStore.isLoggedIn) {
      ElMessage.warning('请先登录')
      router.push('/login')
      return
    }
    
    const qty = quantity || 1
    console.log('Buy now:', { productId, variantId, quantity: qty })
    
    // 立即购买：直接跳转到订单确认页面
    // 将商品信息作为查询参数传递
    const productData = {
      id: productId,
      quantity: qty,
      variantId: variantId
    }
    
    // 跳转到订单确认页面
    router.push({
      path: '/order/confirm',
      query: {
        product: JSON.stringify(productData),
        type: 'buy_now'
      }
    })
    
  } catch (error: any) {
    console.error('立即购买失败:', error)
    ElMessage.error(error.message || '立即购买失败，请重试')
  }
}

// 加载商品数据函数
const loadProductData = async () => {
      try {
        loading.value = true
        error.value = ''
        
        const productId = route.params.id as string
        console.log('=== LOADING PRODUCT DATA ===')
        console.log('Product ID:', productId)
        console.log('Route params:', route.params)
        
        // 加载商品详情
        console.log('About to call productApi.getProductDetail...')
        const productResponse = await productApi.getProductDetail(productId)
        console.log('Product API response received:', productResponse)
    
    // 检查响应数据结构
    console.log('Raw productResponse:', productResponse)
    console.log('productResponse.data:', productResponse.data)
    
    // API直接返回商品数据，没有data包装
    if (productResponse && productResponse.id) {
      product.value = productResponse
      console.log('Product data loaded directly from response:', product.value)
    } else {
      console.warn('No product data received')
    }
    
    // 加载商家信息 (假设商家ID为1，实际应该从商品数据中获取)
    const merchantResponse = await shopApi.getMerchantShop('1')
    console.log('Merchant API response:', merchantResponse)
    
    if (merchantResponse.data) {
      merchant.value = merchantResponse.data
      console.log('Merchant data loaded:', merchant.value)
    }
    
  } catch (err: any) {
    console.error('Failed to load product data:', err)
    error.value = err.message || '加载商品数据失败'
      } finally {
        loading.value = false
      }
    }

// 生命周期
onMounted(() => {
  console.log('🚀 ProductDetail mounted!')
  // 加载商品数据
  loadProductData()
})
</script>

<style scoped lang="scss">
.tiktok-shop {
  min-height: 100vh;
  background: #fff;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

// Main Layout
.main-layout {
  display: flex;
  min-height: calc(100vh - 60px);
}

// Main Content
.main-content {
  flex: 1;
  padding: 20px 40px;
  max-width: calc(100vw - 240px);
}

// Loading and Error States
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  
  .loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #ff0050;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
  }
  
  p {
    color: #666;
    font-size: 16px;
  }
}

.error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
  
  h2 {
    color: #ff0050;
    margin-bottom: 16px;
  }
  
  p {
    color: #666;
    margin-bottom: 24px;
  }
  
  .retry-btn {
    background-color: #ff0050;
    color: #fff;
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    
    &:hover {
      background-color: #e6004a;
    }
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

// Product Display
.product-display {
  display: flex;
  gap: 40px;
  margin-bottom: 40px;
}

// Product Details Section
.product-details-section {
  flex: 1;
  max-width: 400px;
  
  .discount-badge {
    background: #ff0050;
    color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 12px;
    display: inline-block;
  }
  
  .price-section {
    margin-bottom: 12px;
    
    .current-price {
      font-size: 32px;
      font-weight: 700;
      color: #000;
      margin-bottom: 4px;
    }
    
    .original-price {
      font-size: 18px;
      color: #999;
      text-decoration: line-through;
    }
  }
  
  .shipping-info {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
    font-size: 14px;
    color: #666;
    
    .shipping-icon {
      font-size: 16px;
    }
  }
  
  .product-title {
    font-size: 20px;
    font-weight: 600;
    color: #000;
    line-height: 1.3;
    margin-bottom: 16px;
  }
  
  .seller-summary {
    margin-bottom: 24px;
    
    .seller-label {
      font-size: 14px;
      color: #666;
    }
    
    .seller-name {
      font-size: 14px;
      font-weight: 500;
      color: #000;
      margin-left: 4px;
    }
    
    .rating-sales {
      margin-top: 8px;
      font-size: 14px;
      color: #666;
      
      .rating {
        margin-right: 16px;
      }
    }
  }
  
}

// Bottom Navigation
.bottom-navigation {
  border-top: 1px solid #e5e5e5;
  padding-top: 20px;
  
  .nav-tabs {
    display: flex;
    gap: 32px;
    
    .nav-tab {
      background: none;
      border: none;
      padding: 12px 0;
      font-size: 16px;
      font-weight: 500;
      color: #666;
      cursor: pointer;
      border-bottom: 2px solid transparent;
      
      &.active {
        color: #000;
        border-bottom-color: #ff0050;
      }
      
      &:hover {
        color: #000;
      }
    }
  }
}

// Responsive Design
@media (max-width: 768px) {
  .main-layout {
    flex-direction: column;
  }
  
  .main-content {
    max-width: 100%;
    padding: 20px;
  }
  
  .product-display {
    flex-direction: column;
    gap: 20px;
  }
  
  .product-details-section {
    max-width: 100%;
  }
}
</style>